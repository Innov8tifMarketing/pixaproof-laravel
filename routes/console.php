<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Off-site backups (backup:clean, backup:run, backup:monitor and a scheduler heartbeat) are
 * scheduled by jothamlec/laravel-offsite-backup; see config/offsite-backup.php and AGENTS.md
 * (Backups). Don't schedule them here too: they would run twice.
 */
