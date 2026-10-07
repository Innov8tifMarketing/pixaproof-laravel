<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * The project's settings for jothamlec/laravel-offsite-backup; the package tests its own behaviour.
 */
class OffsiteBackupConfigTest extends TestCase
{
    public function test_backups_go_aes256_encrypted_to_the_b2_disk(): void
    {
        $this->assertSame(['b2'], config('backup.backup.destination.disks'));
        $this->assertSame('aes256', config('backup.backup.encryption'));
        $this->assertSame('jotham@cothink.ing', config('backup.notifications.mail.to'));

        $this->assertSame('s3', config('filesystems.disks.b2.driver'));
        $this->assertSame('when_required', config('filesystems.disks.b2.request_checksum_calculation'));
        $this->assertSame('when_required', config('filesystems.disks.b2.response_checksum_validation'));
        $this->assertTrue(config('filesystems.disks.b2.throw'));
    }

    public function test_the_sqlite_connection_is_dumped_explicitly(): void
    {
        $this->assertSame(['sqlite'], config('offsite-backup.connections'));
        $this->assertSame(['sqlite'], config('backup.backup.source.databases'));
    }

    public function test_the_shared_dir_is_backed_up_without_caches_or_the_live_sqlite_file(): void
    {
        $sharedPath = config('backup.backup.source.files.relative_path');
        $exclude = config('backup.backup.source.files.exclude');

        $this->assertSame([$sharedPath], config('backup.backup.source.files.include'));

        foreach (['cache', 'storage/framework', 'storage/logs', 'storage/statamic', 'data/sqlite', 'data/backups'] as $excluded) {
            $this->assertContains($sharedPath.'/'.$excluded, $exclude);
        }

        $this->assertNotContains($sharedPath.'/data/media', $exclude);
        $this->assertNotContains($sharedPath.'/storage/app', $exclude);
    }

    public function test_each_backup_command_is_scheduled_once_at_the_same_utc_times(): void
    {
        $events = collect(app(Schedule::class)->events());

        $this->assertSame(['50 19 * * *'], $this->expressionsFor($events, 'backup:clean'));
        $this->assertSame(['55 19 * * *'], $this->expressionsFor($events, 'backup:run'));
        $this->assertSame(['50 20 * * *'], $this->expressionsFor($events, 'backup:monitor'));
        $this->assertSame('UTC', $this->eventFor($events, 'backup:run')->timezone);
    }

    /**
     * @param  Collection<int, Event>  $events
     * @return list<string>
     */
    private function expressionsFor(Collection $events, string $command): array
    {
        return $events
            ->filter(fn (Event $event): bool => $this->runs($event, $command))
            ->map(fn (Event $event): string => $event->expression)
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Event>  $events
     */
    private function eventFor(Collection $events, string $command): Event
    {
        return $events->first(fn (Event $event): bool => $this->runs($event, $command));
    }

    private function runs(Event $event, string $command): bool
    {
        return (bool) preg_match("/artisan'? ".preg_quote($command, '/').'(\s|$)/', (string) $event->command);
    }
}
