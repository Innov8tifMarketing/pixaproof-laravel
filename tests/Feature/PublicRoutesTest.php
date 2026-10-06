<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\Entry;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    /**
     * @return list<array{string, string}>
     */
    public static function pageProvider(): array
    {
        return [
            'home' => ['/', 'Pixaproof'],
            'contact' => ['/contact', 'sales@innov8tif.com'],
            'privacy' => ['/privacy', 'Privacy'],
        ];
    }

    #[DataProvider('pageProvider')]
    public function test_page_renders(string $path, string $expected): void
    {
        $this->get($path)
            ->assertOk()
            ->assertSee($expected, false);
    }

    public function test_no_cookie_notice_is_rendered(): void
    {
        config(['seo.tracking.gtm' => 'GTM-TEST123', 'seo.tracking.environments' => ['testing']]);

        $this->get('/')
            ->assertOk()
            ->assertSee('GTM-TEST123', false)
            ->assertDontSee('pixaproof_cookie_notice', false)
            ->assertDontSee('Cookie notice', false);
    }

    /**
     * @return list<array{string, string}>
     */
    public static function redirectProvider(): array
    {
        return [
            ['/technology', '/#how-it-works'],
            ['/product', '/#how-it-works'],
            ['/about', '/#about'],
            ['/company/contact', '/contact'],
            ['/solutions/insurance-claims', '/#solutions'],
        ];
    }

    #[DataProvider('redirectProvider')]
    public function test_legacy_paths_redirect(string $from, string $to): void
    {
        $this->get($from)->assertRedirect($to);
    }

    public function test_legacy_paths_redirect_only_for_get_and_head(): void
    {
        $this->head('/technology')->assertRedirect('/#how-it-works');
        $this->post('/technology')->assertNotFound();
    }

    public function test_privacy_page_shows_when_the_policy_was_last_edited(): void
    {
        $privacy = Entry::whereCollection('pages')->first(fn ($entry) => $entry->slug() === 'privacy');

        $this->get('/privacy')
            ->assertOk()
            ->assertSee('Last updated: '.$privacy->lastModified()->format('F j, Y'))
            ->assertSee('<h2>Data Security</h2>', false);
    }

    public function test_vite_build_produces_the_app_entrypoints(): void
    {
        $manifestPath = public_path('build/manifest.json');

        if (! file_exists($manifestPath)) {
            $this->markTestSkipped('No Vite build present; run `npm run build` to exercise this.');
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        $this->assertIsArray($manifest, 'build/manifest.json should be valid JSON.');
        $this->assertArrayHasKey('resources/css/app.css', $manifest, 'The CSS entrypoint is missing from the build.');
        $this->assertArrayHasKey('resources/js/app.js', $manifest, 'The JS entrypoint is missing from the build.');

        foreach (['resources/css/app.css', 'resources/js/app.js'] as $entry) {
            $this->assertFileExists(
                public_path('build/'.$manifest[$entry]['file']),
                "Manifest references {$entry} but the emitted file is absent.",
            );
        }
    }

    public function test_health_endpoint_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
