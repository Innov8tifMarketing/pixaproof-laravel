<?php

namespace Deployer;

require 'recipe/laravel.php';
require __DIR__.'/vendor/jothamlec/laravel-offsite-backup/recipe/offsite-backup.php';

set('application', 'Pixaproof');

$origin = trim((string) shell_exec('git -C '.escapeshellarg(__DIR__).' config --get remote.origin.url 2>/dev/null'));
set('repository', preg_match('#github\.com[:/](.+?)(?:\.git)?$#', $origin, $matches)
    ? "git@github.com:{$matches[1]}.git"
    : $origin);

set('keep_releases', 5);
set('php_version', '8.4');

set('default_timeout', 300);

set('writable_mode', 'acl');
set('writable_use_sudo', false);

set('forward_agent', false);

set('update_code_strategy', 'clone');
set('env', ['GIT_LFS_SKIP_SMUDGE' => '1']);

/*
 * Off-site backups (jothamlec/laravel-offsite-backup); see AGENTS.md (Backups). Secrets are
 * resolved locally with one `op inject` and written into shared/.env by `dep offsite:env prod`.
 * offsite_name is the existing folder in the bucket. The scheduler runs from
 * /etc/cron.d/pixaproof-laravel, so don't run `dep offsite:scheduler`.
 */
set('offsite_stages', ['prod']);
set('offsite_name', 'pixaproof');
set('offsite_secrets', [
    'BACKUP_ARCHIVE_PASSWORD' => 'op://Personal/dig73jz7gqe6svm4llq7naj4fy/archivePassword_pixaproof',
    'B2_ACCESS_KEY_ID' => 'op://Personal/dig73jz7gqe6svm4llq7naj4fy/keyID',
    'B2_SECRET_ACCESS_KEY' => 'op://Personal/dig73jz7gqe6svm4llq7naj4fy/applicationKey',
    'OFFSITE_BACKUP_HEARTBEAT_URL' => 'op://Personal/dig73jz7gqe6svm4llq7naj4fy/heartbeat_pixaproof',
]);
set('offsite_env_extra', [
    'B2_BUCKET' => 'cothinking-client-backups',
    'B2_REGION' => 'us-east-005',
    'B2_ENDPOINT' => 'https://s3.us-east-005.backblazeb2.com',
    'OFFSITE_BACKUP_CONNECTIONS' => 'sqlite',
    // backup:clean at 19:50, backup:run at 19:55, backup:monitor at 20:50 (UTC).
    'OFFSITE_BACKUP_TIME' => '19:50',
    'OFFSITE_BACKUP_TIMEZONE' => 'UTC',
    'OFFSITE_BACKUP_HEARTBEAT_FORMAT' => 'kuma',
]);

host('prod')
    ->setHostname('47.237.191.213')
    ->set('remote_user', 'deployer')
    ->setIdentityFile('~/.ssh/alicloud-innov8tif.pub')
    ->setSshArguments(['-o IdentitiesOnly=yes'])
    ->set('deploy_path', '/home/deployer/pixaproof-laravel')
    ->set('branch', 'main')
    ->set('labels', ['stage' => 'prod'])
    ->set('url', 'https://pixaproof.com');

add('shared_dirs', [
    'data/sqlite',
    'data/media',
    'data/backups',
    'cache/npm',
]);

add('writable_dirs', [
    'data/sqlite',
    'data/media',
]);

set('storage_links', [
    'storage' => 'storage/app/public',
    'media' => 'data/media',
]);

set('queue_worker_name', fn () => 'pixaproof-'.getStage().'-worker');

set('sqlite_path', '{{deploy_path}}/shared/data/sqlite/database.sqlite');
set('migrate_backup_path', '{{deploy_path}}/shared/data/backups');

set('migrate_backup_keep', 5);

set('verify_timeout', 15);
set('verify_retries', 5);
set('verify_retry_delay', 2);

function getStage(): string
{
    $labels = get('labels', []);

    return $labels['stage'] ?? 'unknown';
}

desc('Compare deploy/server/** against the live server and report drift');
task('server:diff', function () {
    $root = __DIR__.'/deploy/server';

    if (! is_dir($root)) {
        warning('No deploy/server directory; nothing to compare.');

        return;
    }

    $files = [];
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));

    foreach ($iterator as $file) {
        /** @var \SplFileInfo $file */
        if ($file->isFile() && $file->getFilename() !== 'README.md') {
            $files[] = $file->getPathname();
        }
    }

    sort($files);
    $drifted = 0;
    $missing = 0;

    foreach ($files as $localPath) {
        $remotePath = substr($localPath, strlen($root));
        $remote = run("cat {$remotePath} 2>/dev/null || true");
        $local = (string) file_get_contents($localPath);

        if (trim($remote) === '') {
            writeln("  <fg=yellow>MISSING</> {$remotePath} (not present on server)");
            $missing++;

            continue;
        }

        if (rtrim($remote) !== rtrim($local)) {
            writeln("  <fg=red>DRIFT</>   {$remotePath}");
            $drifted++;

            continue;
        }

        writeln("  <fg=green>ok</>      {$remotePath}");
    }

    writeln('');

    if ($drifted === 0 && $missing === 0) {
        info(count($files).' file(s) checked, server matches the repo.');

        return;
    }

    warning("{$drifted} drifted, {$missing} missing, of ".count($files).' checked.');
    writeln('  See deploy/server/README.md for how to apply changes safely.');
});

desc('Ensure SQLite database file exists');
task('db:ensure-sqlite', function () {
    $sqlitePath = get('sqlite_path');
    $sqliteDir = dirname($sqlitePath);

    if (test("[ -f {$sqlitePath} ]")) {
        info('SQLite database exists');

        return;
    }

    info('Creating SQLite database file...');
    run("mkdir -p {$sqliteDir}");
    run("touch {$sqlitePath}");
    run("chmod 664 {$sqlitePath}");
    info('SQLite database created');
});

desc('Backup SQLite database before migrations');
task('db:backup', function () {
    $sqlitePath = get('sqlite_path');
    $backupPath = get('migrate_backup_path');
    $keepBackups = get('migrate_backup_keep', 5);
    $timestamp = date('Y-m-d-His');
    $stage = getStage();

    if (! test("[ -f {$sqlitePath} ]")) {
        info('No database to backup');

        return;
    }

    $fileSize = run("stat -f%z {$sqlitePath} 2>/dev/null || stat -c%s {$sqlitePath} 2>/dev/null || echo '0'");
    if ((int) trim($fileSize) === 0) {
        info('Database is empty, skipping backup');

        return;
    }

    run("mkdir -p {$backupPath}");
    $backupFile = "{$backupPath}/database_{$stage}_{$timestamp}.sqlite";

    run("cp {$sqlitePath} {$backupFile}");
    $sizeKb = round((int) $fileSize / 1024, 2);
    info("Backup created: {$backupFile} ({$sizeKb} KB)");

    $backups = run("ls -1t {$backupPath}/database_{$stage}_*.sqlite 2>/dev/null || echo ''");
    $backupFiles = array_filter(explode("\n", trim($backups)));

    if (count($backupFiles) > $keepBackups) {
        $toRemove = array_slice($backupFiles, $keepBackups);
        foreach ($toRemove as $file) {
            run("rm -f {$file}");
        }
        info('Cleaned up '.count($toRemove).' old backup(s)');
    }
});

desc('List available database backups');
task('db:backups', function () {
    $backupPath = get('migrate_backup_path');
    $backups = run("ls -lh {$backupPath}/*.sqlite 2>/dev/null || echo 'No backups found'");
    writeln($backups);
});

desc('Restore database from backup');
task('db:restore', function () {
    $backupPath = get('migrate_backup_path');
    $stage = getStage();
    $sqlitePath = get('sqlite_path');

    $backups = run("ls -1t {$backupPath}/database_{$stage}_*.sqlite 2>/dev/null || echo ''");
    $backupFiles = array_filter(explode("\n", trim($backups)));

    if (empty($backupFiles)) {
        warning('No backups found');

        return;
    }

    writeln('Available backups:');
    foreach ($backupFiles as $i => $file) {
        $size = run("ls -lh {$file} | awk '{print \$5}'");
        writeln("  [{$i}] ".basename($file)." ({$size})");
    }

    $choice = ask('Enter backup number to restore:', '0');
    $selectedBackup = $backupFiles[(int) $choice] ?? null;

    if (! $selectedBackup) {
        warning('Invalid selection');

        return;
    }

    if (! askConfirmation('This will OVERWRITE the current database. Continue?', false)) {
        info('Restore cancelled');

        return;
    }

    if (test('[ -f {{deploy_path}}/current/artisan ]')) {
        run('cd {{deploy_path}}/current && {{bin/php}} artisan down --retry=60');
    }

    info('Restoring from: '.basename($selectedBackup));
    run("cp {$selectedBackup} {$sqlitePath}");
    info('Database restored');

    if (test('[ -f {{deploy_path}}/current/artisan ]')) {
        run('cd {{deploy_path}}/current && {{bin/php}} artisan up');
    }
});

desc('Run migrations safely with backup');
task('migrate:safe', function () {
    $status = run('cd {{release_path}} && {{bin/php}} artisan migrate:status --pending 2>&1 || echo "NO_PENDING"');

    if (str_contains($status, 'NO_PENDING') || str_contains($status, 'Nothing to migrate') || str_contains($status, 'No pending migrations')) {
        info('No pending migrations');

        return;
    }

    info('Pending migrations detected');

    try {
        invoke('db:backup');
    } catch (\Throwable $e) {
        warning('Backup failed: '.$e->getMessage());
        if (! askConfirmation('Continue without backup?', false)) {
            throw new \RuntimeException('Migration aborted: backup failed');
        }
    }

    info('Running migrations...');
    $output = run('cd {{release_path}} && {{bin/php}} artisan migrate --force 2>&1', timeout: 300);
    writeln($output);
    info('Migrations completed');
});

desc('Import the Statamic content on the first deploy (never overwrites control panel edits)');
task('content:import', function () {
    $output = run('cd {{release_path}} && {{bin/php}} artisan pixaproof:import-content --once 2>&1');
    writeln($output);
});

desc('Let PHP-FPM (www-data) write what the deploy user created under shared/storage');
task('storage:acl', function () {
    // The directories carry default ACLs for www-data, but PHP's mkdir(..., 0755) (Statamic's
    // stache-locks, tmp, …) sets the ACL mask to r-x, capping www-data at read-only: every request
    // then fails with "FlockStore directory … is not writable". Restore the mask on what deployer owns.
    run('find {{deploy_path}}/shared/storage -user deployer -type d -exec setfacl -m m::rwx -m d:m::rwx {} +');
    run('find {{deploy_path}}/shared/storage -user deployer -type f -exec setfacl -m m::rw {} +');
});

desc('Warm the Stache (Statamic\'s flat-file index: blueprints, fieldsets)');
task('statamic:stache:warm', function () {
    run('cd {{release_path}} && {{bin/php}} please stache:warm');
});

desc('Restart PHP-FPM');
task('php-fpm:restart', function () {
    $version = get('php_version', '8.4');

    run("sudo systemctl restart php{$version}-fpm");

    $maxAttempts = 10;
    for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
        $status = run("systemctl is-active php{$version}-fpm 2>/dev/null || echo 'inactive'");
        if (trim($status) === 'active') {
            info("PHP {$version}-FPM restarted");

            return;
        }
        run('sleep 0.5');
    }

    throw new \RuntimeException('PHP-FPM failed to restart');
});

desc('Restart queue workers via Supervisor');
task('queue:restart', function () {
    $workerName = get('queue_worker_name', '');

    if (empty($workerName)) {
        return;
    }

    $status = run("sudo supervisorctl status {$workerName}:* 2>&1 || echo 'NOT_FOUND'");

    if (str_contains($status, 'NOT_FOUND') || str_contains($status, 'no such')) {
        info("Queue worker {$workerName} not configured, reloading supervisor...");
        run('sudo supervisorctl reread');
        run('sudo supervisorctl update');

        return;
    }

    run("sudo supervisorctl restart {$workerName}:*");
    info('Queue workers restarted');
});

desc('Show queue worker status');
task('queue:status', function () {
    $workerName = get('queue_worker_name', '');

    if (empty($workerName)) {
        warning('queue_worker_name not set');

        return;
    }

    $status = run("sudo supervisorctl status {$workerName}:* 2>&1 || echo 'Not configured'");
    writeln($status);
});

desc('Verify deployment health via HTTP check');
task('deploy:verify', function () {
    $url = get('url');
    $timeout = get('verify_timeout', 15);
    $retries = get('verify_retries', 5);
    $retryDelay = get('verify_retry_delay', 2);

    if (empty($url)) {
        warning('No URL configured for verification');

        return;
    }

    info('Verifying deployment health...');
    run('sleep 2');

    $passed = false;
    $lastStatusCode = '000';

    for ($attempt = 1; $attempt <= $retries; $attempt++) {
        info("Health check attempt {$attempt}/{$retries}...");

        $result = runLocally(
            "curl -s -o /dev/null -w '%{http_code}' -L --max-time {$timeout} --insecure {$url} 2>/dev/null || echo '000'"
        );
        $lastStatusCode = trim($result);

        if (in_array($lastStatusCode, ['200', '301', '302', '303', '307', '308'])) {
            $passed = true;
            break;
        }

        warning("Attempt {$attempt}: HTTP {$lastStatusCode}");
        if ($attempt < $retries) {
            run("sleep {$retryDelay}");
        }
    }

    if (! $passed) {
        warning("Health check failed (HTTP {$lastStatusCode})");

        writeln('Recent Laravel logs:');
        run('tail -30 {{deploy_path}}/shared/storage/logs/laravel.log 2>/dev/null || echo "No logs"');

        set('health_check_failed', true);
        throw new \RuntimeException("Deployment verification failed: HTTP {$lastStatusCode}");
    }

    info("Health check passed (HTTP {$lastStatusCode})");
});

desc('Rollback to previous release');
task('rollback', function () {
    $releases = get('releases_list');

    if (count($releases) < 2) {
        warning('No previous release available');

        return;
    }

    $currentRelease = $releases[0];
    $previousRelease = $releases[1];

    info("Rolling back from {$currentRelease} to {$previousRelease}...");

    if (test('[ -f {{deploy_path}}/current/artisan ]')) {
        run('cd {{deploy_path}}/current && {{bin/php}} artisan down --retry=60 2>/dev/null || true');
    }

    run('cd {{deploy_path}} && {{bin/symlink}} releases/'.$previousRelease.' current');

    invoke('php-fpm:restart');
    invoke('queue:restart');

    run('cd {{deploy_path}}/current && {{bin/php}} artisan up');

    info("Rolled back to: {$previousRelease}");
});

desc('Automatic rollback on deploy failure');
task('deploy:rollback-on-failure', function () {
    $releases = get('releases_list');

    if (count($releases) < 2) {
        return;
    }

    $healthCheckFailed = has('health_check_failed') && get('health_check_failed');

    if ($healthCheckFailed) {
        warning('Health check failed, initiating rollback...');
        invoke('rollback');
    }
});

desc('Show last 50 lines of Laravel log');
task('artisan:log', function () {
    run('tail -50 {{deploy_path}}/current/storage/logs/laravel.log 2>/dev/null || echo "No log file"');
});

desc('Put application in maintenance mode');
task('artisan:down', function () {
    if (! test('[ -f {{deploy_path}}/current/artisan ]')) {
        return;
    }

    $secret = bin2hex(random_bytes(16));
    run("cd {{deploy_path}}/current && {{bin/php}} artisan down --secret={$secret} --retry=60");
    info("Maintenance mode enabled (secret: {$secret})");
});

desc('Bring application out of maintenance mode');
task('artisan:up', function () {
    run('cd {{release_path}} && {{bin/php}} artisan up');
    info('Application is live');
});

desc('Clear and rebuild caches');
task('artisan:cache:refresh', function () {
    within('{{release_path}}', function () {
        run('{{bin/php}} artisan cache:clear 2>/dev/null || true');
        run('{{bin/php}} artisan config:clear');
        run('{{bin/php}} artisan route:clear');
        run('{{bin/php}} artisan view:clear');
        run('{{bin/php}} artisan config:cache');
        run('{{bin/php}} artisan route:cache');
        run('{{bin/php}} artisan view:cache');
    });
    info('Caches refreshed');
});

desc('NPM install');
task('npm:install', function () {
    run('cd {{release_path}} && npm ci --cache={{deploy_path}}/shared/cache/npm --prefer-offline');
});

desc('NPM build');
task('npm:build', function () {
    run('cd {{release_path}} && npm run build');
});

desc('Create custom storage symlinks');
task('storage:link-custom', function () {
    $links = get('storage_links', []);

    foreach ($links as $link => $target) {
        $linkPath = "{{release_path}}/public/{$link}";
        $targetPath = "{{deploy_path}}/shared/{$target}";

        run("mkdir -p {$targetPath}");

        if (test("[ -L {$linkPath} ]")) {
            continue;
        }

        if (test("[ -e {$linkPath} ]")) {
            info("Removing existing {$link} directory to create symlink");
            run("rm -rf {$linkPath}");
        }

        run("ln -s {$targetPath} {$linkPath}");
        info("Created symlink: {$link} -> shared/{$target}");
    }
});

desc('Pull git-lfs objects from remote after code checkout');
task('lfs:pull', function () {
    $repo = get('repository');
    run("cd {{release_path}} && git remote set-url origin {$repo}");

    try {
        run('cd {{release_path}} && git lfs pull', timeout: 600);
        info('LFS objects pulled from remote');

        return;
    } catch (\Throwable $e) {
        warning('git lfs pull failed: '.trim($e->getMessage()));
    }

    $source = '{{deploy_path}}/current/public/videos';
    if (! test("[ -d {$source} ]")) {
        throw new \RuntimeException("git lfs pull failed and there is no live release to reuse video assets from ({$source})");
    }

    run("cp -a {$source}/. {{release_path}}/public/videos/");

    $pointers = run("grep -rIl 'git-lfs.github.com' {{release_path}}/public/videos 2>/dev/null || true");
    if (trim($pointers) !== '') {
        throw new \RuntimeException('LFS fallback left unresolved pointer files: '.trim($pointers));
    }

    info('git lfs pull failed — reused video assets from the live release');
});

after('deploy:update_code', 'lfs:pull');

after('deploy:vendors', 'storage:acl');

// Hung off deploy:vendors, not artisan:config:cache: the recipe runs config:cache itself, so a chain
// hooked to it (plus an extra config:cache after vendors) ran npm, the backup and migrate:safe twice.
after('deploy:vendors', 'npm:install');
after('npm:install', 'npm:build');

after('npm:build', 'db:ensure-sqlite');

after('db:ensure-sqlite', 'db:backup');
after('db:backup', 'migrate:safe');

before('deploy:symlink', 'artisan:down');

after('deploy:symlink', 'php-fpm:restart');
after('deploy:symlink', 'artisan:up');
after('deploy:symlink', 'queue:restart');
after('deploy:symlink', 'artisan:cache:refresh');
after('artisan:cache:refresh', 'statamic:stache:warm');
after('statamic:stache:warm', 'storage:acl');

after('artisan:storage:link', 'storage:link-custom');
// After the media symlink: storage:link-custom replaces public/media with a link to shared/data/media,
// deleting anything the import copied there first (the first deploy lost og-image.webp that way).
after('storage:link-custom', 'content:import');

task('artisan:migrate', function () {})->hidden();

after('deploy:symlink', 'deploy:verify');

// Deployer keeps one failure handler per task (fail() replaces it), so two fail('deploy', …) lines
// left only the unlock and the automatic rollback never ran. Hook the recipe's deploy:failed instead.
after('deploy:failed', 'deploy:rollback-on-failure');
after('deploy:failed', 'deploy:unlock');
