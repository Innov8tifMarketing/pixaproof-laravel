<?php

namespace App\Support;

use Illuminate\Support\Str;
use Statamic\Contracts\Forms\Submission;

/**
 * A form submission's values for an email to the team: by field label, select options by their
 * label, blanks left out, and the page it was sent from last.
 */
final class SubmissionDetails
{
    /**
     * @param  list<string>  $except  field handles to leave out
     * @return array<string, string>
     */
    public static function of(Submission $submission, array $except = []): array
    {
        $fields = $submission->form()->blueprint()->fields()->all();
        $details = [];

        foreach ($submission->data()->except($except) as $handle => $value) {
            if (blank($value) || ! is_scalar($value)) {
                continue;
            }

            $field = $fields->get($handle);

            if ($field?->type() === 'select') {
                $value = $field->get('options')[$value] ?? $value;
            }

            $details[$field ? (string) $field->display() : Str::headline((string) $handle)] = (string) $value;
        }

        if ($page = request()->headers->get('referer')) {
            $details['Sent from'] = $page;
        }

        return $details;
    }
}
