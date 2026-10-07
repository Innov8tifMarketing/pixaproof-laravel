<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_industry_solutions_render_without_kyc(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Loan Draw Inspections')
            ->assertSee('Insurance Claims')
            ->assertSee('Field Operations &amp; Assets', false)
            ->assertSee('Property Inspections')
            ->assertSee('Protect Your Rental Investment and Prevent Deposit Fraud')
            ->assertSee('images/mockups/property-inspection.webp', false)
            ->assertSee('warehouse inventory checks')
            ->assertDontSee('property listings')
            ->assertSee("activeTab: 'loan-draw'", false)
            ->assertDontSee('KYC Onboarding')
            ->assertDontSee('kyc-id-document')
            ->assertDontSee('loan draw inspections, KYC')
            ->assertDontSee("activeTab = 'kyc'", false)
            ->assertDontSee("activeTab === 'kyc'", false);
    }

    /**
     * NexG Berhad renamed itself back to Datasonic Group Berhad (2026-09).
     */
    public function test_the_parent_group_is_named_datasonic(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Part of Datasonic Group Berhad, a public-listed group')
            ->assertDontSee('NexG');
    }

    public function test_each_industry_renders_in_tabs_accordion_and_panels(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach (['loan-draw', 'insurance', 'asset', 'property'] as $id) {
            $this->assertSame(1, substr_count($html, "activeTab = '{$id}'"), "Desktop tab button for {$id}");
            $this->assertSame(1, substr_count($html, "activeTab = activeTab === '{$id}' ? '' : '{$id}'"), "Mobile accordion trigger for {$id}");
            $this->assertSame(2, substr_count($html, "x-show=\"activeTab === '{$id}'\""), "Mobile and desktop panels for {$id}");
        }
    }

    /**
     * @return list<array{string}>
     */
    public static function anchorProvider(): array
    {
        return [
            ['challenge'],
            ['solution'],
            ['how-it-works'],
            ['demos'],
            ['solutions'],
            ['about'],
            ['faq'],
        ];
    }

    #[DataProvider('anchorProvider')]
    public function test_section_anchor_is_present(string $id): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="'.$id.'"', false);
    }

    public function test_technology_anchor_is_intentionally_absent(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="technology"', false);
    }

    public function test_stats_strip_renders_labels_and_counter_hooks(): void
    {
        $html = $this->get('/')
            ->assertOk()
            ->assertSee('Verifications Processed')
            ->assertSee('Verification Speed')
            ->assertSee('Integrity Checks')
            ->assertSee('Granted Patents')
            ->assertSee("Motion.animateCounter(\$el, 10, 'M+')", false)
            ->assertSee("Motion.animateCounter(\$el, 35, '+')", false)
            ->assertSee("Motion.animateCounter(\$el, 3, '')", false)
            ->getContent();

        $this->assertSame(3, substr_count($html, 'Motion.animateCounter('));
    }

    public function test_video_lightbox_is_teleported_once(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'x-teleport="body"'));
        $this->assertStringContainsString('@keydown.escape.window="close()"', $html);
    }

    public function test_three_layers_keep_stagger_hook(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('[data-layer-card]', html_entity_decode($html));
        $this->assertSame(4, substr_count($html, 'data-layer-card="data-layer-card"'));
    }
}
