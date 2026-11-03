<?php

namespace App\Listeners;

use App\Mail\ContactConfirmationMail;
use App\Mail\ContactEnquiryMail;
use App\Support\SubmissionDetails;
use Illuminate\Support\Facades\Mail;
use Statamic\Events\SubmissionCreated;

/**
 * Mails a `contact` form enquiry to marketing, with sales copied, and thanks the visitor. Queued
 * one mail each, so a failed thank-you retries alone. Auto-discovered (typed handle()).
 */
class SendContactEnquiry
{
    public function handle(SubmissionCreated $event): void
    {
        $submission = $event->submission;

        if ($submission->form()->handle() !== 'contact') {
            return;
        }

        $name = (string) $submission->get('name');
        $email = (string) $submission->get('email');

        Mail::to(config('services.lead_mail.marketing_to'))
            ->cc(array_filter([config('services.lead_mail.sales_cc')]))
            ->queue(new ContactEnquiryMail($name, $email, (string) $submission->get('company'), SubmissionDetails::of($submission)));

        Mail::to($email)->queue(new ContactConfirmationMail($name));
    }
}
