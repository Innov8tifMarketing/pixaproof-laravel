<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Tests\TestCase;

class BackupConfigTest extends TestCase
{
    public function test_backups_go_encrypted_to_the_b2_disk(): void
    {
        $this->assertSame(['b2'], config('backup.backup.destination.disks'));
        $this->assertSame('aes256', config('backup.backup.encryption'));
        $this->assertSame(GzipCompressor::class, config('backup.backup.database_dump_compressor'));
        $this->assertTrue(config('backup.backup.verify_backup'));

        $this->assertSame('s3', config('filesystems.disks.b2.driver'));
        $this->assertSame('when_required', config('filesystems.disks.b2.request_checksum_calculation'));
        $this->assertTrue(config('filesystems.disks.b2.throw'));
    }

    public function test_cleanup_keeps_thirty_daily_and_twelve_monthly_backups_without_a_size_cap(): void
    {
        $strategy = config('backup.cleanup.default_strategy');

        $this->assertSame(30, $strategy['keep_all_backups_for_days'] + $strategy['keep_daily_backups_for_days']);
        $this->assertSame(0, $strategy['keep_weekly_backups_for_weeks']);
        $this->assertSame(12, $strategy['keep_monthly_backups_for_months']);
        $this->assertSame(0, $strategy['keep_yearly_backups_for_years']);
        $this->assertNull($strategy['delete_oldest_backups_when_using_more_megabytes_than']);
    }

    public function test_only_failures_are_mailed(): void
    {
        $notifications = config('backup.notifications.notifications');

        $this->assertSame(['mail'], $notifications[BackupHasFailedNotification::class]);
        $this->assertSame([], $notifications[BackupWasSuccessfulNotification::class]);
        $this->assertSame(['b2'], config('backup.monitor_backups.0.disks'));
    }

    public function test_a_placeholder_from_address_falls_back_to_a_valid_one(): void
    {
        $original = $_SERVER['MAIL_FROM_ADDRESS'] ?? null;
        $_SERVER['MAIL_FROM_ADDRESS'] = 'hello@{{DOMAIN}}';

        try {
            $config = require config_path('backup.php');
        } finally {
            if ($original === null) {
                unset($_SERVER['MAIL_FROM_ADDRESS']);
            } else {
                $_SERVER['MAIL_FROM_ADDRESS'] = $original;
            }
        }

        $this->assertSame('noreply@pixaproof.com', $config['notifications']['mail']['from']['address']);
    }

    public function test_the_shared_dir_is_backed_up_without_caches_or_the_live_sqlite_file(): void
    {
        $sharedPath = config('backup.backup.source.files.relative_path');

        $this->assertSame([$sharedPath], config('backup.backup.source.files.include'));

        foreach (['cache', 'storage/framework', 'storage/logs', 'storage/statamic', 'data/sqlite', 'data/backups'] as $excluded) {
            $this->assertContains($sharedPath.'/'.$excluded, config('backup.backup.source.files.exclude'));
        }

        $this->assertNotContains($sharedPath.'/data/media', config('backup.backup.source.files.exclude'));
        $this->assertNotContains($sharedPath.'/storage/app', config('backup.backup.source.files.exclude'));
    }

    public function test_backup_commands_are_scheduled_daily_in_utc(): void
    {
        $expressions = [
            'backup:clean' => '50 19 * * *',
            'backup:run' => '55 19 * * *',
            'backup:monitor' => '55 20 * * *',
        ];

        $events = collect(app(Schedule::class)->events());

        foreach ($expressions as $command => $expression) {
            $event = $events->first(fn (Event $event): bool => str_ends_with((string) $event->command, "artisan' {$command}")
                || str_ends_with((string) $event->command, "artisan {$command}"));

            $this->assertNotNull($event, "{$command} is not scheduled");
            $this->assertSame($expression, $event->expression, "{$command} runs at the wrong time");
        }

        $this->assertSame('UTC', config('app.timezone'));
    }
}
