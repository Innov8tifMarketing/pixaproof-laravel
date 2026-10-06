<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use JothamLec\MarketingToolkit\Redirects\Redirect;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Nav;
use Tests\TestCase;

class ImportStatamicContentTest extends TestCase
{
    /**
     * Starts each test without the seeded content (inside the test's transaction), so the command runs as on a new install.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $contentRepositories = ['entries', 'collections', 'collection_trees', 'navigations', 'global_sets', 'global_set_variables', 'asset_containers', 'assets'];

        foreach ($contentRepositories as $repository) {
            config("statamic.eloquent-driver.{$repository}.model")::query()->delete();
        }

        Redirect::query()->delete();
    }

    public function test_it_creates_the_pages_with_their_routes_templates_and_seo(): void
    {
        $this->artisan('pixaproof:import-content')->assertSuccessful();

        $pages = Entry::query()->where('collection', 'pages')->get()->keyBy->slug();

        $this->assertSame(['home', 'contact', 'privacy'], $pages->keys()->all());
        $this->assertSame('/', $pages['home']->uri());
        $this->assertSame('/contact', $pages['contact']->uri());
        $this->assertSame('/privacy', $pages['privacy']->uri());

        $this->assertSame('home', $pages['home']->get('template'));
        $this->assertSame('contact', $pages['contact']->get('template'));
        $this->assertSame('default', $pages['privacy']->get('template'));

        $this->assertSame('Verify Every Image. Eliminate Fraud.', $pages['home']->get('seo')['title']);
        $this->assertSame('og-image.webp', $pages['home']->get('seo')['image']);
        $this->assertArrayNotHasKey('title', $pages['contact']->get('seo'));
        $this->assertStringContainsString('## Data Security', $pages['privacy']->get('content'));
        $this->assertStringContainsString('[contact us](/contact)', $pages['privacy']->get('content'));
    }

    public function test_it_creates_the_navigations_with_the_retargeted_technology_link(): void
    {
        $this->artisan('pixaproof:import-content')->assertSuccessful();

        $homeId = Entry::query()->where('collection', 'pages')->where('slug', 'home')->first()->id();

        $this->assertSame([
            ['entry' => $homeId, 'title' => 'Home'],
            ['title' => 'Solutions', 'url' => '/#solutions'],
            ['title' => 'Technology', 'url' => '/#how-it-works'],
            ['title' => 'About', 'url' => '/#about'],
            ['title' => 'FAQ', 'url' => '/#faq'],
        ], Nav::findByHandle('main')->in('default')->tree());

        $this->assertSame([
            ['title' => 'Solutions', 'url' => '/#solutions'],
            ['title' => 'Technology', 'url' => '/#how-it-works'],
            ['title' => 'About', 'url' => '/#about'],
        ], Nav::findByHandle('footer')->in('default')->tree());
    }

    public function test_it_fills_seo_and_brand_and_copies_the_images_into_the_asset_container(): void
    {
        $this->artisan('pixaproof:import-content')->assertSuccessful();

        $variables = GlobalSet::findByHandle('seo')->in('default');

        $this->assertFalse($variables->get('title_site_name'));
        $this->assertSame('og-image.webp', $variables->get('default_image'));
        $this->assertSame('pixaproof-icon.png', $variables->get('favicon'));
        $this->assertSame('G-VKS70BYBWN', $variables->get('ga4_id'));
        $this->assertSame(['#0284c7', '#0f172a', '#ffffff'], [$variables->get('og_accent'), $variables->get('og_text'), $variables->get('og_background')]);
        $this->assertSame(['/cp/'], $variables->get('robots_disallow'));

        Storage::disk('media')->assertExists(['og-image.webp', 'pixaproof-icon.png']);
    }

    public function test_it_imports_the_legacy_redirects(): void
    {
        $this->artisan('pixaproof:import-content')->assertSuccessful();

        $this->assertSame(22, Redirect::query()->count());
        $this->assertSame(22, Redirect::query()->where('status', 301)->where('active', true)->count());
        $this->assertSame('/#how-it-works', Redirect::forSource('/technology')->target);
        $this->assertSame('/#how-it-works', Redirect::forSource('/how-it-works')->target);
        $this->assertSame('/#how-it-works', Redirect::forSource('/product')->target);
        $this->assertSame('/contact', Redirect::forSource('/pricing')->target);
        $this->assertSame('/#solutions', Redirect::forSource('/solutions/insurance-claims')->target);
    }

    public function test_running_it_again_updates_in_place(): void
    {
        $this->artisan('pixaproof:import-content')->assertSuccessful();

        $firstIds = Entry::query()->where('collection', 'pages')->get()->map->id()->sort()->values()->all();

        $this->artisan('pixaproof:import-content')
            ->expectsOutputToContain('0 created, 22 updated')
            ->assertSuccessful();

        $this->assertSame($firstIds, Entry::query()->where('collection', 'pages')->get()->map->id()->sort()->values()->all());
        $this->assertSame(22, Redirect::query()->count());
        $this->assertCount(2, Nav::all());
    }

    public function test_once_imports_into_an_empty_site(): void
    {
        $this->artisan('pixaproof:import-content', ['--once' => true])->assertSuccessful();

        $this->assertSame(3, Entry::whereCollection('pages')->count());
    }

    public function test_once_keeps_content_edited_after_the_first_import(): void
    {
        $this->artisan('pixaproof:import-content')->assertSuccessful();

        $contact = Entry::query()->where('collection', 'pages')->where('slug', 'contact')->first();
        $contact->set('title', 'Talk to us')->save();

        $this->artisan('pixaproof:import-content', ['--once' => true])
            ->expectsOutputToContain('nothing to do')
            ->assertSuccessful();

        $this->assertSame('Talk to us', Entry::find($contact->id())->get('title'));
    }

    public function test_it_fails_when_a_redirect_row_is_rejected(): void
    {
        Redirect::query()->create(['source' => '/', 'target' => '/technology', 'status' => 301, 'active' => true]);

        $this->artisan('pixaproof:import-content')->assertFailed();
    }
}
