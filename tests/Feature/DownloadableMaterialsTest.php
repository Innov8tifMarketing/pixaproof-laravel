<?php

namespace Tests\Feature;

use App\Mail\MaterialDownloadMail;
use App\Mail\MaterialRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry;
use Statamic\Facades\Form;
use Tests\TestCase;

/**
 * Downloadable materials are offered once published with a file, behind the `material_download`
 * form: the visitor's session may then download it, they are emailed a signed link, and marketing
 * is told.
 */
class DownloadableMaterialsTest extends TestCase
{
    private const array VISITOR = [
        'name' => 'Lee Wei',
        'email' => 'lee@insurer.example',
        'company' => 'Example Insurance',
    ];

    public function test_drafts_and_materials_without_a_file_are_not_offered(): void
    {
        Storage::fake('materials');
        $this->material('pixaproof-brochure')->published(true)->save();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Take PixaProof Back to Your Team')
            ->assertDontSee('Download the use case')
            ->assertDontSee('id="material-request-title"', false);
    }

    public function test_a_published_material_shows_in_the_section_and_on_its_industry_tab(): void
    {
        Storage::fake('materials');
        $this->offer('pixaproof-brochure');
        $this->offer('property-inspection-use-case');

        $html = $this->get('/')
            ->assertOk()
            ->assertSee('Take PixaProof Back to Your Team')
            ->assertSee('PixaProof Brochure')
            ->assertSee('Property Inspection Use Case')
            ->assertDontSee('Insurance Claims Use Case')
            ->assertSee('href="https://youtu.be/CA2nlNLwCjM"', false)
            ->assertDontSee('/media/pixaproof-brochure.pdf')
            ->getContent();

        $this->assertSame(2, substr_count($html, 'Download the use case'), 'The property tab, on phones and on desktop');
        $this->assertSame(1, substr_count($html, 'id="material-request-title"'), 'One request dialog for the page');
    }

    public function test_a_request_unlocks_the_file_emails_a_link_and_tells_marketing(): void
    {
        Mail::fake();
        Storage::fake('materials');
        $material = $this->offer('insurance-claims-use-case');

        $this->get(route('materials.download', 'insurance-claims-use-case'))->assertRedirect('/#resources');

        $this->request($material->id())->assertOk()->assertJson(['success' => true, 'submission_created' => true]);

        $this->assertSame('Example Insurance', Form::find('material_download')->submissions()->sole()->get('company'));

        $this->get(route('materials.download', 'insurance-claims-use-case'))
            ->assertOk()
            ->assertDownload('insurance-claims-use-case.pdf');

        Mail::assertQueued(MaterialRequestMail::class, fn (MaterialRequestMail $mail): bool => $mail->hasTo('marketing@innov8tif.com')
            && $mail->cc === []
            && $mail->hasReplyTo('lee@insurer.example')
            && $mail->details['Material'] === 'Insurance Claims Use Case'
            && ! array_key_exists('Material (entry ID)', $mail->details));
        Mail::assertQueued(MaterialDownloadMail::class, fn (MaterialDownloadMail $mail): bool => $mail->hasTo('lee@insurer.example')
            && $mail->materialTitle === 'Insurance Claims Use Case');
        Mail::assertQueuedCount(2);
    }

    public function test_the_emailed_link_works_without_the_session_for_seven_days(): void
    {
        Mail::fake();
        $this->freezeTime();
        Storage::fake('materials');
        $material = $this->offer('pixaproof-brochure');

        $this->request($material->id());
        $link = Mail::queued(MaterialDownloadMail::class)->sole()->downloadUrl;
        $this->flushSession();

        $this->get($link)->assertOk()->assertDownload('pixaproof-brochure.pdf');
        $this->get(str_replace('signature=', 'signature=0', $link))->assertRedirect('/#resources');

        $this->travel(7)->days();
        $this->travel(1)->minute();

        $this->get($link)->assertRedirect('/#resources');
    }

    public function test_a_request_for_a_material_not_on_offer_is_refused_and_nothing_is_sent(): void
    {
        Mail::fake();
        Storage::fake('materials');
        $draft = $this->material('pixaproof-brochure');

        foreach ([$draft->id(), 'not-an-id'] as $id) {
            $this->request($id)
                ->assertStatus(400)
                ->assertJsonPath('error.material', 'This download is no longer available. Please reload the page.');
        }

        $this->assertSame(0, Form::find('material_download')->submissions()->count());
        Mail::assertNothingQueued();
    }

    public function test_an_unknown_or_unpublished_material_is_not_found(): void
    {
        Storage::fake('materials');

        $this->get(route('materials.download', 'no-such-material'))->assertNotFound();
        $this->get(route('materials.download', 'pixaproof-brochure'))->assertNotFound();
    }

    /**
     * Publishes a material with a PDF in the materials container (its disk faked by the test).
     */
    private function offer(string $slug): StatamicEntry
    {
        Storage::disk('materials')->put("{$slug}.pdf", '%PDF-1.4 test');
        AssetContainer::find('materials')->makeAsset("{$slug}.pdf")->save();

        $material = $this->material($slug);
        $material->set('file', "{$slug}.pdf")->published(true)->save();

        return $material;
    }

    private function material(string $slug): StatamicEntry
    {
        return Entry::query()->where('collection', 'downloadable_materials')->where('slug', $slug)->first();
    }

    private function request(string $materialId): TestResponse
    {
        return $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post(route('statamic.forms.submit', 'material_download'), ['material' => $materialId, ...self::VISITOR]);
    }
}
