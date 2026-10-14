<?php

namespace App\Console\Commands;

use App\Providers\AppServiceProvider;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use JothamLec\MarketingToolkit\Redirects\Csv;
use JothamLec\MarketingToolkit\Redirects\Redirect;
use LogicException;
use Statamic\Assets\AssetContainer as StatamicAssetContainer;
use Statamic\Entries\Collection as StatamicCollection;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Nav;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet as StatamicGlobalSet;
use Statamic\Structures\CollectionStructure;
use Statamic\Structures\Nav as StatamicNav;

#[Signature('pixaproof:import-content {--once : If the pages collection exists, only fill in what is missing (empty page sections, the Site global, media), so edits made in the control panel are kept}')]
#[Description('Create the Statamic content: pages and their sections, navigation, media, the Site, Brand and Marketing settings values and redirects')]
class ImportStatamicContent extends Command
{
    private const string CONTAINER = 'assets';

    private const string REDIRECTS_CSV = 'database/seo/redirects.csv';

    /**
     * The template every page renders with: its sections, from the page builder.
     */
    private const string TEMPLATE = 'page';

    /**
     * Values for the Site global set, used while the set is empty.
     */
    private const string SITE_CONTENT = 'database/content/site.php';

    /**
     * Files copied into the asset container, keyed by their asset path; sources are relative to the project root.
     *
     * @var array<string, string>
     */
    private const array ASSETS = [
        'og-image.webp' => 'public/images/og-image.webp',
        'pixaproof-icon.png' => 'database/seo/pixaproof-icon.png',
        'mockups/hero-live-capture.webp' => 'public/images/mockups/hero-live-capture.webp',
        'mockups/hero-tampered-gallery.webp' => 'public/images/mockups/hero-tampered-gallery.webp',
        'mockups/hero-ai-generated-id.webp' => 'public/images/mockups/hero-ai-generated-id.webp',
        'mockups/prevention-camera-live.webp' => 'public/images/mockups/prevention-camera-live.webp',
        'mockups/loan-draw-construction.webp' => 'public/images/mockups/loan-draw-construction.webp',
        'mockups/insurance-car-damage.webp' => 'public/images/mockups/insurance-car-damage.webp',
        'mockups/asset-warehouse.webp' => 'public/images/mockups/asset-warehouse.webp',
        'mockups/property-inspection.webp' => 'public/images/mockups/property-inspection.webp',
        'videos/genuine-device.mp4' => 'public/videos/genuine-device.mp4',
        'videos/genuine-device.webm' => 'public/videos/genuine-device.webm',
        'videos/genuine-device-poster.webp' => 'public/videos/genuine-device-poster.webp',
        'videos/virtual-camera.mp4' => 'public/videos/virtual-camera.mp4',
        'videos/virtual-camera.webm' => 'public/videos/virtual-camera.webm',
        'videos/virtual-camera-poster.webp' => 'public/videos/virtual-camera-poster.webp',
        'videos/emulator.mp4' => 'public/videos/emulator.mp4',
        'videos/emulator.webm' => 'public/videos/emulator.webm',
        'videos/emulator-poster.webp' => 'public/videos/emulator-poster.webp',
        'videos/device-farm.mp4' => 'public/videos/device-farm.mp4',
        'videos/device-farm.webm' => 'public/videos/device-farm.webm',
        'videos/device-farm-poster.webp' => 'public/videos/device-farm-poster.webp',
    ];

    /**
     * Pages in tree order; the first is the root (home) page. `blocks` names the file holding the
     * page's sections (the page builder's `blocks` field).
     *
     * @var list<array{slug: string, title: string, blocks: string, seo: array<string, string>}>
     */
    private const array PAGES = [
        [
            'slug' => 'home',
            'title' => 'Home',
            'blocks' => 'database/content/home.php',
            'seo' => [
                'title' => 'Verify Every Image. Eliminate Fraud.',
                'image' => 'og-image.webp',
                'description' => 'PixaProof AI-powered verification detects image tampering in seconds—protecting your business from fraudulent claims, fake documents, and compliance failures.',
            ],
        ],
        [
            'slug' => 'contact',
            'title' => 'Contact',
            'blocks' => 'database/content/contact.php',
            'seo' => [
                'description' => 'Get in touch with the PixaProof team. Email our sales team to book a demo, discuss your use case, or explore integration options.',
            ],
        ],
        [
            'slug' => 'privacy',
            'title' => 'Privacy Policy',
            'blocks' => 'database/content/privacy.php',
            'seo' => [
                'description' => 'PixaProof privacy policy — how we collect, use, and protect your data when using our image verification platform.',
            ],
        ],
    ];

    /**
     * Navigation items by handle; `entry` items link to a page by slug.
     *
     * @var array<string, array{title: string, items: list<array{title: string, url?: string, entry?: string}>}>
     */
    private const array NAVIGATIONS = [
        'main' => [
            'title' => 'Main',
            'items' => [
                ['title' => 'Home', 'entry' => 'home'],
                ['title' => 'Solutions', 'url' => '/#solutions'],
                ['title' => 'Technology', 'url' => '/#how-it-works'],
                ['title' => 'About', 'url' => '/#about'],
                ['title' => 'FAQ', 'url' => '/#faq'],
            ],
        ],
        'footer' => [
            'title' => 'Footer',
            'items' => [
                ['title' => 'Solutions', 'url' => '/#solutions'],
                ['title' => 'Technology', 'url' => '/#how-it-works'],
                ['title' => 'About', 'url' => '/#about'],
            ],
        ],
    ];

    /**
     * Values for the toolkit's "Brand" global set.
     *
     * @var array<string, mixed>
     */
    private const array BRAND = [
        'title_site_name' => false,
        'default_description' => 'PixaProof verifies images at the point of capture, stopping fraudulent photos, AI-generated documents and tampered evidence before they enter your workflow.',
        'default_image' => 'og-image.webp',
        'favicon' => 'pixaproof-icon.png',
        'og_accent' => '#0284c7',
        'og_text' => '#0f172a',
        'og_background' => '#ffffff',
    ];

    /**
     * Values for the toolkit's "Marketing settings" global set (tracking, consent, leads, crawlers).
     *
     * @var array<string, mixed>
     */
    private const array MARKETING_SETTINGS = [
        'ga4_id' => 'G-VKS70BYBWN',
    ];

    public function handle(): int
    {
        if ($this->option('once') && Collection::findByHandle('pages')) {
            $this->components->info('Content already imported; filling in only what is missing.');

            $this->importAssets();
            $this->usePageTemplate($this->pagesCollection());
            $this->fillExistingPages();
            $this->importSiteContent();

            return $this->verifyCounts() ? self::SUCCESS : self::FAILURE;
        }

        $this->importAssets();
        $entryIds = $this->importPages();
        $this->importNavigations($entryIds);

        if ($this->call('statamic:mt:install', ['--container' => self::CONTAINER]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->importGlobal('marketing-toolkit.global', self::BRAND);
        $this->importGlobal('marketing-toolkit.settings_global', self::MARKETING_SETTINGS);
        $this->importSiteContent();

        if (! $this->importRedirects()) {
            return self::FAILURE;
        }

        return $this->verifyCounts() ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Copies each file in and adds it to the container, leaving files and assets that are already there
     * (and anything an editor set on them, such as alt text) as they are.
     */
    private function importAssets(): void
    {
        $container = AssetContainer::findByHandle(self::CONTAINER) ?? AssetContainer::make(self::CONTAINER);

        if (! $container instanceof StatamicAssetContainer) {
            throw new LogicException('Asset containers are expected to extend '.StatamicAssetContainer::class.'.');
        }

        $container->disk('media')->title('Assets');

        /*
         * Through the repository: the Eloquent driver's AssetContainer::save()
         * skips the Blink cache reset, so the null cached by findByHandle() above
         * would outlive the save.
         */
        AssetContainer::save($container);

        $disk = Storage::disk($container->diskHandle());

        foreach (self::ASSETS as $assetPath => $sourcePath) {
            if (! $disk->exists($assetPath)) {
                $source = fopen(base_path($sourcePath), 'r');

                if ($source === false) {
                    throw new LogicException("Cannot read {$sourcePath}.");
                }

                $disk->writeStream($assetPath, $source);
                fclose($source);
            }

            if (! $container->queryAssets()->where('path', $assetPath)->first()) {
                $container->makeAsset($assetPath)->save();
            }
        }

        $this->components->info('Assets: '.count(self::ASSETS).' files.');
    }

    private function pagesCollection(): StatamicCollection
    {
        return Collection::findByHandle('pages') ?? Collection::make('pages');
    }

    private function usePageTemplate(StatamicCollection $collection): void
    {
        $collection->template(self::TEMPLATE)->save();
    }

    /**
     * @return array<string, string> entry IDs by slug
     */
    private function importPages(): array
    {
        $collection = $this->pagesCollection()
            ->title('Pages')
            ->routes('{parent_uri}/{slug}')
            ->structureContents(['root' => true]);

        $this->usePageTemplate($collection);

        $entryIds = [];

        foreach (self::PAGES as $page) {
            $entry = $this->findPage($page['slug']) ?? $this->makePage($page['slug']);

            $entry->published(true)->merge([
                'title' => $page['title'],
                'seo' => $page['seo'],
            ]);

            $this->fillBlocks($entry, $page['blocks']);

            $entry->save();

            $entryIds[$page['slug']] = (string) $entry->id();
        }

        $structure = $collection->structure();

        if (! $structure instanceof CollectionStructure) {
            throw new LogicException('The pages collection has no structure.');
        }

        $structure->in(Site::default()->handle())
            ->tree(array_map(fn (string $id) => ['entry' => $id], array_values($entryIds)))
            ->save();

        $this->components->info('Pages: '.implode(', ', array_keys($entryIds)).'.');

        return $entryIds;
    }

    /**
     * Gives the existing pages their sections where they have none, without touching anything else and
     * without changing when they were last edited (the privacy page shows that date).
     */
    private function fillExistingPages(): void
    {
        $filled = [];

        foreach (self::PAGES as $page) {
            $entry = $this->findPage($page['slug']);

            if (! $entry || ! $this->fillBlocks($entry, $page['blocks'])) {
                continue;
            }

            $lastModified = $entry->lastModified();

            $entry->save();

            config('statamic.eloquent-driver.entries.model')::query()
                ->whereKey($entry->id())
                ->update(['updated_at' => $lastModified]);

            $filled[] = $page['slug'];
        }

        $this->components->info('Pages given their sections: '.($filled === [] ? 'none' : implode(', ', $filled)).'.');
    }

    /**
     * Gives the page its sections when it has none, carrying over the markdown `content` of a page
     * from before the page builder into its legal text section, and drops the per-page template
     * (the collection's applies). Sections the page already has are never replaced.
     *
     * @return bool whether the entry changed
     */
    private function fillBlocks(StatamicEntry $entry, string $blocksFile): bool
    {
        $changed = $entry->has('template') || $entry->has('content');

        if (blank($entry->get('blocks'))) {
            /** @var list<array<string, mixed>> $blocks */
            $blocks = require base_path($blocksFile);
            $legacyContent = $entry->get('content');

            $blocks = array_map(function (array $block) use ($legacyContent): array {
                if ($block['type'] === 'legal_text' && filled($legacyContent)) {
                    $block['content'] = $legacyContent;
                }

                return $this->withRowIds($block);
            }, $blocks);

            $entry->set('blocks', $blocks);
            $changed = true;
        }

        // The Eloquent driver also keeps the template on the entry itself and writes it back on save.
        $entry->template(null)->remove('template')->remove('content');

        return $changed;
    }

    /**
     * Gives each grid row in a section a stable ID (the control panel adds random ones to rows without).
     *
     * @param  array<string, mixed>  $block
     * @return array<string, mixed>
     */
    private function withRowIds(array $block): array
    {
        foreach ($block as $field => $value) {
            if (! is_array($value) || ! array_is_list($value) || ! is_array($value[0] ?? null)) {
                continue;
            }

            foreach ($value as $index => $row) {
                $block[$field][$index] = ['id' => "{$block['id']}-{$field}-".($index + 1), ...$row];
            }
        }

        return $block;
    }

    private function findPage(string $slug): ?StatamicEntry
    {
        $entry = Entry::whereCollection('pages')->first(fn (StatamicEntry $entry) => $entry->slug() === $slug);

        if ($entry !== null && ! $entry instanceof StatamicEntry) {
            throw new LogicException('Entries are expected to extend '.StatamicEntry::class.'.');
        }

        return $entry;
    }

    private function makePage(string $slug): StatamicEntry
    {
        $entry = Entry::make();

        if (! $entry instanceof StatamicEntry) {
            throw new LogicException('Entries are expected to extend '.StatamicEntry::class.'.');
        }

        return $entry->collection('pages')->slug($slug);
    }

    /**
     * @param  array<string, string>  $entryIds
     */
    private function importNavigations(array $entryIds): void
    {
        foreach (self::NAVIGATIONS as $handle => $definition) {
            $navigation = Nav::findByHandle($handle) ?? Nav::make($handle);

            if (! $navigation instanceof StatamicNav) {
                throw new LogicException('Navigations are expected to extend '.StatamicNav::class.'.');
            }

            $navigation
                ->title($definition['title'])
                ->maxDepth(1)
                ->expectsRoot(false)
                ->collections(['pages'])
                ->save();

            $tree = array_map(fn (array $item) => isset($item['entry'])
                ? ['entry' => $entryIds[$item['entry']], 'title' => $item['title']]
                : ['title' => $item['title'], 'url' => $item['url']],
                $definition['items'],
            );

            /*
             * The Eloquent driver stores an empty tree for a tree it has no row for yet,
             * so a new tree is saved once empty (as the control panel does) before its items.
             */
            $navigationTree = $navigation->in(Site::default()->handle());

            if ($navigationTree === null) {
                $navigationTree = $navigation->makeTree(Site::default()->handle());
                $navigationTree->save();
            }

            $navigationTree->tree($tree)->save();
        }

        $this->components->info('Navigations: '.implode(', ', array_keys(self::NAVIGATIONS)).'.');
    }

    /**
     * @param  string  $configKey  the config key naming the global set's handle
     * @param  array<string, mixed>  $values
     */
    private function importGlobal(string $configKey, array $values): void
    {
        $globalSet = GlobalSet::findByHandle((string) config($configKey));

        if (! $globalSet instanceof StatamicGlobalSet) {
            throw new LogicException("The global set [{$configKey}] is missing or does not extend ".StatamicGlobalSet::class.'.');
        }

        $variables = $globalSet->in(Site::default()->handle());

        $variables->merge($values)->save();

        $this->components->info($globalSet->title().': '.implode(', ', array_keys($values)).'.');
    }

    /**
     * Creates the Site global set (navbar button, footer, 404 page) and fills it while it is empty.
     */
    private function importSiteContent(): void
    {
        if (! GlobalSet::findByHandle(AppServiceProvider::SITE_CONTENT_GLOBAL)) {
            GlobalSet::make(AppServiceProvider::SITE_CONTENT_GLOBAL)->title('Site')->save();
        }

        $variables = GlobalSet::findByHandle(AppServiceProvider::SITE_CONTENT_GLOBAL)->in(Site::default()->handle());

        if ($variables->data()->filter(fn ($value) => filled($value))->isNotEmpty()) {
            $this->components->info('Site: already filled in; kept.');

            return;
        }

        $variables->data(require base_path(self::SITE_CONTENT))->save();

        $this->components->info('Site: filled in.');
    }

    private function importRedirects(): bool
    {
        $result = app(Csv::class)->import((string) file_get_contents(base_path(self::REDIRECTS_CSV)));

        foreach ($result['errors'] as $error) {
            $this->components->error($error);
        }

        $this->components->info("Redirects: {$result['created']} created, {$result['updated']} updated.");

        return $result['errors'] === [];
    }

    private function verifyCounts(): bool
    {
        $redirectSources = array_map(
            fn (string $line) => str_getcsv($line, escape: '')[0],
            array_slice(file(base_path(self::REDIRECTS_CSV), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [], 1),
        );

        $pages = Entry::whereCollection('pages');
        $siteContent = GlobalSet::findByHandle(AppServiceProvider::SITE_CONTENT_GLOBAL)?->inDefaultSite();

        $counts = [
            'pages' => [count(self::PAGES), $pages->count()],
            'pages with sections' => [count(self::PAGES), $pages->filter(fn (StatamicEntry $entry) => filled($entry->get('blocks')))->count()],
            'filled-in Site global sets' => [1, $siteContent && $siteContent->data()->filter(fn ($value) => filled($value))->isNotEmpty() ? 1 : 0],
            'navigations' => [count(self::NAVIGATIONS), Nav::all()->filter(fn ($nav) => array_key_exists($nav->handle(), self::NAVIGATIONS))->count()],
            'redirects' => [count($redirectSources), Redirect::query()->whereIn('source', $redirectSources)->count()],
        ];

        $mismatches = array_filter($counts, fn (array $pair) => $pair[0] !== $pair[1]);

        foreach ($mismatches as $name => [$expected, $actual]) {
            $this->components->error("Expected {$expected} {$name}, found {$actual}.");
        }

        return $mismatches === [];
    }
}
