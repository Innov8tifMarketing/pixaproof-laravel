<?php

namespace Tests\Feature;

use App\Mail\ContactConfirmationMail;
use App\Mail\ContactEnquiryMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use Statamic\Facades\Form;
use Tests\TestCase;

/**
 * The contact form section posts to Statamic's `contact` form; an enquiry is kept, mailed to
 * marketing with sales copied, and the visitor is thanked by email.
 */
class ContactFormTest extends TestCase
{
    private const array ENQUIRY = [
        'name' => 'Aisha Rahman',
        'email' => 'aisha@bank.example',
        'company' => 'Example Bank',
        'phone' => '+60 12 345 6789',
        'interest' => 'demo',
        'message' => 'We need to verify loan draw photos.',
    ];

    public function test_the_home_and_contact_pages_show_the_form(): void
    {
        foreach (['/', '/contact'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('action="'.route('statamic.forms.submit', 'contact').'"', false)
                ->assertSee('<option value="integration" >SDK integration</option>', false);
        }

        $this->get('/')->assertSee('id="contact"', false);
    }

    public function test_an_enquiry_is_kept_mailed_to_marketing_and_sales_and_the_visitor_is_thanked(): void
    {
        Mail::fake();

        $this->submit(self::ENQUIRY, referer: url('/'))
            ->assertOk()
            ->assertJson(['success' => true, 'submission_created' => true]);

        $this->assertSame('Example Bank', Form::find('contact')->submissions()->sole()->get('company'));

        Mail::assertQueued(ContactEnquiryMail::class, function (ContactEnquiryMail $mail): bool {
            return $mail->hasTo('marketing@innov8tif.com')
                && $mail->hasCc('sales@innov8tif.com')
                && $mail->hasReplyTo('aisha@bank.example')
                && $mail->hasSubject('New PixaProof enquiry: Aisha Rahman, Example Bank')
                && $mail->details['Interested in'] === 'A demo'
                && $mail->details['Sent from'] === url('/');
        });
        Mail::assertQueued(ContactConfirmationMail::class, fn (ContactConfirmationMail $mail): bool => $mail->hasTo('aisha@bank.example')
            && $mail->hasReplyTo('sales@innov8tif.com'));
        Mail::assertQueuedCount(2);
    }

    public function test_the_enquiry_email_lists_the_submitted_details(): void
    {
        $mail = new ContactEnquiryMail('Aisha Rahman', 'aisha@bank.example', 'Example Bank', [
            'Company' => 'Example Bank',
            'Message' => "Line one\nLine | two",
        ]);

        $mail->assertSeeInHtml('Example Bank')
            ->assertSeeInHtml('Line one Line / two');
    }

    public function test_an_incomplete_enquiry_is_refused_and_nothing_is_sent(): void
    {
        Mail::fake();

        $this->submit(['name' => 'Aisha Rahman', 'email' => 'not-an-email', 'interest' => 'lunch'])
            ->assertStatus(400)
            ->assertJsonPath('error.email', 'The Work email field must be a valid email address.')
            ->assertJsonPath('error.company', 'The Company field is required.')
            ->assertJsonPath('error.interest', 'The selected Interested in is invalid.')
            ->assertJsonPath('error.message', 'The Message field is required.');

        $this->assertSame(0, Form::find('contact')->submissions()->count());
        Mail::assertNothingQueued();
    }

    public function test_a_bot_filling_the_honeypot_is_told_it_worked_but_nothing_is_kept_or_sent(): void
    {
        Mail::fake();

        $this->submit([...self::ENQUIRY, 'website' => 'https://spam.example'])
            ->assertOk()
            ->assertJson(['success' => true, 'submission_created' => false]);

        $this->assertSame(0, Form::find('contact')->submissions()->count());
        Mail::assertNothingQueued();
    }

    /**
     * @param  array<string, string>  $data
     */
    private function submit(array $data, ?string $referer = null): TestResponse
    {
        return $this->withHeaders(array_filter(['X-Requested-With' => 'XMLHttpRequest', 'Referer' => $referer]))
            ->post(route('statamic.forms.submit', 'contact'), $data);
    }
}
