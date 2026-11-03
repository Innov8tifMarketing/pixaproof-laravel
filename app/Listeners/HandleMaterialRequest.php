<?php

namespace App\Listeners;

use App\Mail\MaterialDownloadMail;
use App\Mail\MaterialRequestMail;
use App\Support\DownloadableMaterials;
use App\Support\SubmissionDetails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Statamic\Events\FormSubmitted;
use Statamic\Events\SubmissionCreated;

/**
 * A `material_download` form request: refuses one for a material that is not on offer and keeps
 * the material's title with it (the CP lists requests by title), then lets the visitor's session
 * download the file, mails them a link that works for a week, and tells marketing.
 * Auto-discovered (typed handle methods).
 */
class HandleMaterialRequest
{
    private const string FORM = 'material_download';

    public function handleFormSubmitted(FormSubmitted $event): void
    {
        if ($event->submission->form()->handle() !== self::FORM) {
            return;
        }

        $material = DownloadableMaterials::find((string) $event->submission->get('material'));

        if (! $material) {
            throw ValidationException::withMessages(['material' => 'This download is no longer available. Please reload the page.']);
        }

        $event->submission->set('material_title', (string) $material->get('title'));
    }

    public function handleSubmissionCreated(SubmissionCreated $event): void
    {
        $submission = $event->submission;

        if ($submission->form()->handle() !== self::FORM) {
            return;
        }

        $material = DownloadableMaterials::find((string) $submission->get('material'));

        if (! $material) {
            return;
        }

        DownloadableMaterials::grant($material);

        $name = (string) $submission->get('name');
        $email = (string) $submission->get('email');
        $title = (string) $material->get('title');

        Mail::to(config('services.lead_mail.marketing_to'))->queue(new MaterialRequestMail(
            $name,
            $email,
            $title,
            ['Material' => $title, ...SubmissionDetails::of($submission, except: ['material', 'material_title'])],
        ));

        Mail::to($email)->queue(new MaterialDownloadMail(
            $name,
            $title,
            URL::temporarySignedRoute('materials.download', now()->addDays(7), ['material' => $material->slug()]),
        ));
    }
}
