<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Statamic\Statamic;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    private const ENFORCED_CSP = [
        "frame-ancestors 'self'",
        "object-src 'none'",
        "base-uri 'self'",
    ];

    private const REPORT_GROUP = 'csp-endpoint';

    /**
     * @return list<string>
     */
    private function reportOnlyCsp(string $nonce): array
    {
        return [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com",
            "style-src 'self' 'nonce-{$nonce}'",
            "img-src 'self' data: https://www.googletagmanager.com https://www.google-analytics.com",
            "font-src 'self' data:",
            "frame-src 'self' https://www.googletagmanager.com",
            "connect-src 'self' https://www.googletagmanager.com https://www.google-analytics.com https://*.google-analytics.com https://*.analytics.google.com",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
        ];
    }

    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $reporting = [
            'report-uri '.route('csp.report'),
            'report-to '.self::REPORT_GROUP,
        ];

        $headers = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Content-Security-Policy' => implode('; ', [...self::ENFORCED_CSP, ...$reporting]),
            'Reporting-Endpoints' => self::REPORT_GROUP.'="'.route('csp.report').'"',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];

        /*
         * The control panel's Vue bundle doesn't follow the site's policy; reporting it would
         * only bury the site's own violations.
         */
        if (! Statamic::isCpRoute()) {
            $headers['Content-Security-Policy-Report-Only'] = implode('; ', [...$this->reportOnlyCsp($nonce), ...$reporting]);
        }

        if ($request->secure() && app()->isProduction()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
