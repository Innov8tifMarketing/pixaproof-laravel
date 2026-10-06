<?php

/**
 * Content-Security-Policy violation reporting.
 *
 * The site ships its full target policy as Report-Only (see
 * App\Http\Middleware\SecurityHeaders). Browsers POST violations to
 * `route('csp.report')`, which App\Http\Controllers\CspReportController writes
 * to the `csp` log channel, one JSON object per line. Aggregate with jq:
 *   cat storage/logs/csp-*.log | jq -r '.context | "\(.directive)\t\(.blocked_origin)"' \\
 *     | sort | uniq -c | sort -rn
 * Promote a directive from report-only to enforced only once that data is clean for it.
 *
 * Every knob here is env-driven so report volume can be dialled down on a live
 * site without a deploy — set the value in shared/.env and run `config:cache`.
 */

return [
    // Master kill switch. When false the endpoint returns 204 and logs nothing.
    'reporting_enabled' => env('CSP_REPORTING_ENABLED', true),

    // Identical violations (same policy + directive + blocked origin + path) are
    // logged at most once per this many minutes.
    'dedupe_minutes' => (int) env('CSP_REPORT_DEDUPE_MINUTES', 10),

    // Request bodies larger than this are rejected unparsed. Chrome batches
    // reports, so this needs headroom over a single report.
    'max_body_bytes' => (int) env('CSP_REPORT_MAX_BYTES', 16384),

    /*
     * Browser extensions inject scripts and styles into every page and generate
     * violations that say nothing about the site. Without this filter they are
     * the overwhelming majority of reports and bury the real signal.
     */
    'ignored_schemes' => [
        'chrome-extension',
        'moz-extension',
        'safari-extension',
        'safari-web-extension',
        'webkit-masked-url',
        'chrome',
        'resource',
        'about',
    ],
];
