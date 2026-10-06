<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use JothamLec\MarketingToolkit\Redirects\Csv;
use JothamLec\MarketingToolkit\Redirects\Redirect;
use LogicException;
use Statamic\Assets\AssetContainer as StatamicAssetContainer;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Nav;
use Statamic\Facades\Site;
use Statamic\Structures\CollectionStructure;
use Statamic\Structures\Nav as StatamicNav;

#[Signature('pixaproof:import-content')]
#[Description('Create the Statamic content the Laravel site used to hard-code: pages, navigation, assets, SEO & brand values and redirects')]
class ImportStatamicContent extends Command
{
    private const string CONTAINER = 'assets';

    private const string REDIRECTS_CSV = 'database/seo/redirects.csv';

    /**
     * Public files copied into the asset container, keyed by their asset path.
     *
     * @var array<string, string>
     */
    private const array ASSETS = [
        'og-image.webp' => 'images/og-image.webp',
        'pixaproof-icon.png' => 'web-app-manifest-512x512.png',
    ];

    /**
     * Pages in tree order; the first is the root (home) page.
     *
     * @var list<array{slug: string, title: string, template: string, content?: string, seo: array<string, string>}>
     */
    private const array PAGES = [
        [
            'slug' => 'home',
            'title' => 'Home',
            'template' => 'home',
            'seo' => [
                'title' => 'Verify Every Image. Eliminate Fraud.',
                'description' => 'PixaProof AI-powered verification detects image tampering in seconds—protecting your business from fraudulent claims, fake documents, and compliance failures.',
            ],
        ],
        [
            'slug' => 'contact',
            'title' => 'Contact',
            'template' => 'contact',
            'seo' => [
                'description' => 'Get in touch with the PixaProof team. Email our sales team to book a demo, discuss your use case, or explore integration options.',
            ],
        ],
        [
            'slug' => 'privacy',
            'title' => 'Privacy Policy',
            'template' => 'default',
            'content' => <<<'MARKDOWN'
                ## Introduction

                PixaProof ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your information when you use our mobile application.

                ## Information We Collect

                When you use PixaProof, we may collect:

                - Images you submit for verification (processed in real-time, not stored)
                - Device information for app functionality
                - Usage analytics to improve our service

                ## How We Use Your Information

                We use the information we collect to:

                - Provide photo authenticity verification services
                - Improve and optimize our application
                - Respond to your inquiries and support requests

                ## Data Security

                We implement appropriate security measures to protect your information. Images submitted for verification are processed in real-time and are not stored on our servers after analysis is complete.

                ## Contact Us

                If you have questions about this Privacy Policy, please [contact us](/contact).
                MARKDOWN,
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
     * Values for the toolkit's "SEO & brand" global set.
     *
     * @var array<string, mixed>
     */
    private const array SEO_BRAND = [
        'title_site_name' => false,
        'default_description' => 'PixaProof verifies image authenticity at the point of capture — stopping fraudulent photos, AI-generated documents, and tampered evidence before they enter your workflow.',
        'default_image' => 'og-image.webp',
        'favicon' => 'pixaproof-icon.png',
        'ga4_id' => 'G-VKS70BYBWN',
    ];

    public function handle(): int
    {
        $this->importAssets();
        $entryIds = $this->importPages();
        $this->importNavigations($entryIds);

        if ($this->call('statamic:seo:install', ['--container' => self::CONTAINER]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->importSeoBrand();

        if (! $this->importRedirects()) {
            return self::FAILURE;
        }

        return $this->verifyCounts() ? self::SUCCESS : self::FAILURE;
    }

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

        foreach (self::ASSETS as $assetPath => $publicPath) {
            if (! $disk->exists($assetPath)) {
                $disk->put($assetPath, (string) file_get_contents(public_path($publicPath)));
            }

            $container->makeAsset($assetPath)->save();
        }

        $this->components->info('Assets: '.implode(', ', array_keys(self::ASSETS)).'.');
    }

    /**
     * @return array<string, string> entry IDs by slug
     */
    private function importPages(): array
    {
        $collection = Collection::findByHandle('pages') ?? Collection::make('pages');

        $collection
            ->title('Pages')
            ->routes('{parent_uri}/{slug}')
            ->structureContents(['root' => true])
            ->save();

        $entryIds = [];

        foreach (self::PAGES as $page) {
            $entry = $this->findOrMakePage($page['slug']);

            $entry->published(true)->merge([
                'title' => $page['title'],
                'template' => $page['template'],
                'content' => $page['content'] ?? null,
                'seo' => $page['seo'],
            ]);

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

    private function findOrMakePage(string $slug): StatamicEntry
    {
        $entry = Entry::whereCollection('pages')->first(fn (StatamicEntry $entry) => $entry->slug() === $slug)
            ?? Entry::make();

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

    private function importSeoBrand(): void
    {
        $globalSet = GlobalSet::findByHandle((string) config('seo.global'));
        $variables = $globalSet->in(Site::default()->handle());

        $variables->merge(self::SEO_BRAND)->save();

        $this->components->info('SEO & brand: '.implode(', ', array_keys(self::SEO_BRAND)).'.');
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

        $counts = [
            'pages' => [count(self::PAGES), Entry::whereCollection('pages')->count()],
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
