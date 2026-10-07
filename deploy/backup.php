<?php

namespace Deployer;

/*
 * Off-site backups to Backblaze B2 with spatie/laravel-backup (config/backup.php).
 * The same recipe is used by every CoThinking Laravel project; see AGENTS.md (Backups).
 * The scheduler already runs from /etc/cron.d/pixaproof-laravel, so this copy has no
 * scheduler:install task.
 *
 *   dep backup:env prod          write the B2 settings into shared/.env (needs `op`)
 *   dep backup:run prod          take a backup now
 *   dep backup:list prod         list the backups in B2
 *   dep backup:verify prod       download the newest backup and test-restore it locally
 */

set('backup_op_item', 'op://Personal/dig73jz7gqe6svm4llq7naj4fy');
set('backup_name', fn (): string => get('application'));

// Field in the 1Password item holding this project's Uptime Kuma push URL (optional).
set('backup_heartbeat_field', null);

desc('Writes the off-site backup settings into shared/.env from 1Password');
task('backup:env', function (): void {
    $item = get('backup_op_item');
    $read = fn (string $field): string => trim(runLocally('op read '.escapeshellarg("{$item}/{$field}")));
    $heartbeatField = get('backup_heartbeat_field');
    $endpoint = $read('endpoint');

    $values = [
        'BACKUP_NAME' => get('backup_name'),
        'BACKUP_SHARED_PATH' => run('cd {{deploy_path}}/shared && pwd -P'),
        'BACKUP_ARCHIVE_PASSWORD' => $read('archivePassword'),
        'BACKUP_HEARTBEAT_URL' => $heartbeatField
            ? trim(runLocally('op read '.escapeshellarg("{$item}/{$heartbeatField}").' 2>/dev/null || true'))
            : '',
        'B2_KEY_ID' => $read('keyID'),
        'B2_APPLICATION_KEY' => $read('applicationKey'),
        'B2_REGION' => preg_replace('/^s3\.([a-z0-9-]+)\.backblazeb2\.com$/', '$1', $endpoint),
        'B2_BUCKET' => $read('bucketName'),
        'B2_ENDPOINT' => "https://{$endpoint}",
    ];

    $block = "# >>> off-site backup (managed by `dep backup:env`)\n";
    foreach ($values as $key => $value) {
        if (str_contains($value, "'")) {
            throw new \RuntimeException("{$key} contains a single quote.");
        }
        $block .= "{$key}='{$value}'\n";
    }
    $block .= "# <<< off-site backup\n";

    $localBlock = tempnam(sys_get_temp_dir(), 'backup-env');
    chmod($localBlock, 0600);
    file_put_contents($localBlock, $block);

    try {
        upload($localBlock, '{{deploy_path}}/shared/.env.backup-block');
    } finally {
        unlink($localBlock);
    }

    // Rewrite .env in place (cat >) so its owner, mode and ACLs are kept.
    run('cd {{deploy_path}}/shared && umask 077 && '
        .'{ sed -e "/^# >>> off-site backup/,/^# <<< off-site backup/d" -e \'$a\\\' .env; cat .env.backup-block; } > .env.backup-new && '
        .'cat .env.backup-new > .env && rm -f .env.backup-new .env.backup-block');

    if (test('[ -L {{deploy_path}}/current ]')) {
        run('cd {{current_path}} && {{bin/php}} artisan config:cache');
    }

    info('Backup settings written to shared/.env');
});

desc('Takes an off-site backup now');
task('backup:run', function (): void {
    writeln(run('cd {{current_path}} && {{bin/php}} artisan backup:run --no-interaction', timeout: 3600));
});

desc('Lists the off-site backups');
task('backup:list', function (): void {
    writeln(run('cd {{current_path}} && {{bin/php}} artisan backup:list'));
});

desc('Downloads the newest off-site backup and test-restores it locally');
task('backup:verify', function (): void {
    writeln(runLocally('deploy/backup-verify '.escapeshellarg(get('backup_name')), timeout: 3600));
})->once();
