<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection as LaravelCollection;
use Illuminate\Support\Str;
use Statamic\Assets\Asset;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

/**
 * The `downloadable_materials` collection: PDFs kept on the private `materials` disk and handed
 * out by the download route to someone who filled in the request form. A material is offered only
 * while it is published and has a file.
 */
final class DownloadableMaterials
{
    public const string COLLECTION = 'downloadable_materials';

    public const string CONTAINER = 'materials';

    /**
     * The private disk the container keeps the files on (config/filesystems.php).
     */
    public const string DISK = 'materials';

    /**
     * The session key listing the materials this visitor has asked for (entry IDs).
     */
    public const string SESSION_KEY = 'material_access';

    /**
     * The materials on offer, in the collection's order.
     *
     * @return LaravelCollection<int, Entry>
     */
    public static function all(): LaravelCollection
    {
        return EntryFacade::whereCollection(self::COLLECTION)
            ->map(self::onOffer(...))
            ->filter()
            ->sortBy(fn (Entry $entry) => $entry->order())
            ->values();
    }

    /**
     * The materials picked in a field (entry IDs), in the field's order; all of them when none are picked.
     *
     * @return LaravelCollection<int, Entry>
     */
    public static function picked(mixed $ids): LaravelCollection
    {
        $ids = array_filter(Arr::wrap($ids));

        if ($ids === []) {
            return self::all();
        }

        return collect($ids)->map(fn ($id) => self::find((string) $id))->filter()->values();
    }

    /**
     * A material on offer by its entry ID. Anything that is not an entry ID finds nothing.
     */
    public static function find(?string $id): ?Entry
    {
        if (! Str::isUuid((string) $id)) {
            return null;
        }

        return self::onOffer(EntryFacade::find($id));
    }

    public static function findBySlug(string $slug): ?Entry
    {
        return self::onOffer(EntryFacade::whereCollection(self::COLLECTION)->first(fn ($entry) => $entry->slug() === $slug));
    }

    public static function file(Entry $entry): ?Asset
    {
        $file = $entry->augmentedValue('file')->value();

        return $file instanceof Asset && $file->exists() ? $file : null;
    }

    /**
     * Lets this visitor's session download the material.
     */
    public static function grant(Entry $entry): void
    {
        $granted = session()->get(self::SESSION_KEY, []);

        session()->put(self::SESSION_KEY, array_values(array_unique([...$granted, (string) $entry->id()])));
    }

    public static function isGranted(Entry $entry): bool
    {
        return in_array((string) $entry->id(), session()->get(self::SESSION_KEY, []), true);
    }

    private static function onOffer(mixed $entry): ?Entry
    {
        if (! $entry instanceof Entry || $entry->collectionHandle() !== self::COLLECTION || $entry->status() !== 'published') {
            return null;
        }

        return self::file($entry) ? $entry : null;
    }
}
