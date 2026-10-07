<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * @return list<array{string}>
     */
    public static function pageProvider(): array
    {
        return [
            'home' => ['/'],
            'contact' => ['/contact'],
            'privacy' => ['/privacy'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function expectedHeaders(): array
    {
        return [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];
    }

    #[DataProvider('pageProvider')]
    public function test_every_page_carries_security_headers(string $path): void
    {
        $response = $this->get($path);

        $response->assertOk();

        foreach ($this->expectedHeaders() as $name => $value) {
            $response->assertHeader($name, $value);
        }
    }

    public function test_enforced_policy_keeps_its_nonce_free_directives(): void
    {
        $policy = (string) $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("frame-ancestors 'self'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("base-uri 'self'", $policy);

        $this->assertStringNotContainsString('script-src', $policy);
    }

    public function test_report_only_policy_allows_the_one_external_origin(): void
    {
        $policy = (string) $this->get('/')->headers->get('Content-Security-Policy-Report-Only');

        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString('googletagmanager.com', $policy);
    }

    public function test_report_only_policy_uses_a_nonce_instead_of_unsafe_inline(): void
    {
        $policy = (string) $this->get('/')->headers->get('Content-Security-Policy-Report-Only');

        $this->assertMatchesRegularExpression("/script-src [^;]*'nonce-[A-Za-z0-9]+'/", $policy);
        $this->assertMatchesRegularExpression("/style-src [^;]*'nonce-[A-Za-z0-9]+'/", $policy);
        $this->assertStringNotContainsString("'unsafe-inline'", $policy);
    }

    public function test_unsafe_eval_remains_as_the_documented_alpine_exception(): void
    {
        $policy = (string) $this->get('/')->headers->get('Content-Security-Policy-Report-Only');

        $this->assertStringContainsString("'unsafe-eval'", $policy, 'Alpine evaluates x- expressions at runtime.');
        $this->assertStringNotContainsString("'strict-dynamic'", $policy, "'strict-dynamic' would void the GTM host allowlist.");
    }

    public function test_the_nonce_is_regenerated_per_request(): void
    {
        $extract = function (string $policy): string {
            preg_match("/'nonce-([A-Za-z0-9]+)'/", $policy, $matches);

            return $matches[1] ?? '';
        };

        $first = $extract((string) $this->get('/')->headers->get('Content-Security-Policy-Report-Only'));
        $second = $extract((string) $this->get('/')->headers->get('Content-Security-Policy-Report-Only'));

        $this->assertNotSame('', $first);
        $this->assertNotSame($first, $second, 'A reused nonce is no better than unsafe-inline.');
    }

    #[DataProvider('pageProvider')]
    public function test_no_inline_script_or_style_is_rendered_without_the_nonce(string $path): void
    {
        config(['marketing-toolkit.tracking.environments' => ['testing'], 'marketing-toolkit.tracking.gtm_id' => 'GTM-TEST123']);

        $response = $this->get($path);
        $html = $response->getContent();

        $policy = (string) $response->headers->get('Content-Security-Policy-Report-Only');
        preg_match("/'nonce-([A-Za-z0-9]+)'/", $policy, $matches);
        $nonce = $matches[1];

        preg_match_all('/<(script|style)(?![^>]*\ssrc=)([^>]*)>/i', $html, $tags, PREG_SET_ORDER);

        $executable = array_filter($tags, fn (array $tag) => ! str_contains($tag[2], 'type="application/ld+json"'));

        $this->assertNotEmpty($executable, "Expected the tracking tags' inline scripts on {$path}.");

        foreach ($executable as [$tag, $element, $attributes]) {

            $this->assertStringContainsString(
                'nonce="'.$nonce.'"',
                $attributes,
                "Inline <{$element}> on {$path} is missing the CSP nonce: {$tag}",
            );
        }
    }

    public function test_both_policies_advertise_the_report_endpoint(): void
    {
        $response = $this->get('/');

        foreach (['Content-Security-Policy', 'Content-Security-Policy-Report-Only'] as $header) {
            $policy = (string) $response->headers->get($header);
            $this->assertStringContainsString('report-uri', $policy, $header);
            $this->assertStringContainsString('report-to csp-endpoint', $policy, $header);
        }

        $this->assertStringContainsString(
            'csp-endpoint=',
            (string) $response->headers->get('Reporting-Endpoints'),
        );
    }

    public function test_the_control_panel_keeps_the_enforced_policy_without_the_report_only_one(): void
    {
        $response = $this->get('/cp/auth/login')->assertOk();

        $this->assertStringContainsString("frame-ancestors 'self'", (string) $response->headers->get('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('Content-Security-Policy-Report-Only'));
    }

    public function test_powered_by_banner_is_stripped(): void
    {
        $this->assertFalse($this->get('/')->headers->has('X-Powered-By'));
    }

    public function test_hsts_is_absent_over_plain_http(): void
    {
        $this->assertFalse(
            $this->get('/')->headers->has('Strict-Transport-Security'),
            'HSTS must only be emitted over secure production connections.',
        );
    }
}
