<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Off-site backups to Backblaze B2 (config/backup.php, AGENTS.md "Backups"). Times are
 * UTC and staggered across CoThinking projects.
 */
$backupHeartbeatUrl = config('backup.backup.heartbeat_url');

Schedule::command('backup:clean')->dailyAt('19:50')->withoutOverlapping();

Schedule::command('backup:run')->dailyAt('19:55')->withoutOverlapping()
    ->pingOnSuccessIf(filled($backupHeartbeatUrl), $backupHeartbeatUrl.'?status=up&msg=OK')
    ->pingOnFailureIf(filled($backupHeartbeatUrl), $backupHeartbeatUrl.'?status=down&msg=backup%3Arun+failed');

Schedule::command('backup:monitor')->dailyAt('20:55');
