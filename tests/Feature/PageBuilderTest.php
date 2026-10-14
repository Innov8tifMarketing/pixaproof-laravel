<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Tests\TestCase;

/**
 * The pages render their sections (the `blocks` Replicator) as edited in the control panel,
 * and the navbar, footer and 404 page render the Site global.
 */
class PageBuilderTest extends TestCase
{
    public function test_edited_text_image_and_icon_show_on_the_page(): void
    {
        $this->editHomeBlock('hero', function (array $hero): array {
            $hero['heading'] = 'Catch Every Fake Photo In Your';
            $hero['panels'][0]['image'] = 'mockups/property-inspection.webp';

            return $hero;
        });
        $this->editHomeBlock('trust_strip', function (array $strip): array {
            $strip['items'][0]['icon'] = 'bolt';

            return $strip;
        });

        $this->get('/')
            ->assertOk()
            ->assertSee('Catch Every Fake Photo In Your')
            ->assertDontSee('Stop Fake Images From Entering Your')
            ->assertSee('src="'.url('/media/mockups/property-inspection.webp').'"', false)
            ->assertDontSee('hero-live-capture.webp')
            ->assertSee(Blade::render('<x-heroicon-o-bolt class="text-primary-600 h-5 w-5 shrink-0" />'), false);
    }

    public function test_sections_render_in_the_order_they_are_saved(): void
    {
        $home = $this->home();
        $home->set('blocks', array_reverse($home->get('blocks')))->save();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'Stop Fake Images From Entering Your'), strpos($html, 'Frequently Asked Questions'));
    }

    public function test_a_switched_off_section_is_hidden(): void
    {
        $this->editHomeBlock('faq', fn (array $faq): array => [...$faq, 'enabled' => false]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Frequently Asked Questions')
            ->assertDontSee('id="faq"', false)
            ->assertSee('id="about"', false);
    }

    /**
     * The scroll story's script drives one section only.
     */
    public function test_a_second_challenge_section_renders_nothing(): void
    {
        $home = $this->home();
        $blocks = $home->get('blocks');
        $challenge = collect($blocks)->firstWhere('type', 'challenge');
        $home->set('blocks', [...$blocks, ['id' => 'second-challenge', 'heading' => 'A second problem', 'anchor' => null] + $challenge])->save();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'data-scrub-section'));
        $this->assertStringNotContainsString('A second problem', $html);
    }

    public function test_an_industry_without_a_citation_prints_no_citation_line(): void
    {
        $this->editHomeBlock('industries', function (array $industries): array {
            $industries['industries'] = [$industries['industries'][1], ['citation_source' => null] + $industries['industries'][1]];
            $industries['industries'][1]['tab_key'] = 'insurance-again';

            return $industries;
        });

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<p class="mb-1 text-xs text-neutral-500">'));
        $this->assertStringContainsString('— Signicat, <em>The Battle Against AI-Driven Identity Fraud</em>, 2024', $html);
    }

    public function test_the_site_global_drives_the_navbar_footer_and_404_page(): void
    {
        GlobalSet::findByHandle('site_content')->in('default')->merge([
            'cta_label' => 'Get a demo',
            'contact_email' => 'hello@pixaproof.test',
            'copyright' => 'PixaProof Ltd.',
            'not_found_heading' => 'Nothing here',
        ])->save();

        $html = $this->get('/contact')
            ->assertOk()
            ->assertSee('href="mailto:hello@pixaproof.test"', false)
            ->assertSee('&copy; '.date('Y').' PixaProof Ltd.', false)
            ->getContent();

        $this->assertSame(3, substr_count($html, 'Get a demo'), 'The navbar button (desktop and phone) and the footer link');

        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee('Nothing here')
            ->assertSee('Get a demo');
    }

    private function home(): StatamicEntry
    {
        return Entry::query()->where('collection', 'pages')->where('slug', 'home')->first();
    }

    /**
     * @param  callable(array<string, mixed>): array<string, mixed>  $edit
     */
    private function editHomeBlock(string $type, callable $edit): void
    {
        $home = $this->home();

        $home->set('blocks', array_map(
            fn (array $block): array => $block['type'] === $type ? $edit($block) : $block,
            $home->get('blocks'),
        ))->save();
    }
}
