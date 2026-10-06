<?php

namespace Tests\Feature\Guard;

use Dom\HTMLDocument;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\ImportsSiteContent;
use Tests\TestCase;

/**
 * Safety net for the Statamic conversion: records the public URL surface
 * (status, redirects, head metadata) and fails on any drift.
 *
 * Regenerate with `UPDATE_SNAPSHOTS=1 php artisan test --filter=UrlSnapshotTest`
 * only for intended changes listed in DEV_FILES/statamic-conversion/00-plan.md.
 */
class UrlSnapshotTest extends TestCase
{
    use ImportsSiteContent;

    private const string SNAPSHOT_PATH = 'tests/__snapshots__/urls.json';

    private const string APP_URL_PLACEHOLDER = '{app_url}';

    /**
     * @var list<string>
     */
    private const array PAGE_PATHS = [
        '/',
        '/contact',
        '/privacy',
        '/this-does-not-exist',
    ];

    /**
     * @var list<string>
     */
    private const array REDIRECT_PATHS = [
        '/technology',
        '/about',
        '/company/about',
        '/company/contact',
        '/solutions/loan-draw-inspections',
        '/solutions/insurance-claims',
        '/solutions/kyc-onboarding',
        '/solutions/asset-verification',
        '/solutions/banking',
        '/solutions/insurance',
        '/solutions/real-estate',
        '/solutions/government',
        '/solutions/ecommerce',
        '/solutions/healthcare',
        '/resources/injection-attacks',
        '/resources/fraud-statistics',
        '/resources/compliance',
        '/resources/case-studies',
        '/pricing',
        '/how-it-works',
        '/enterprise',
        '/product',
    ];

    /**
     * @var list<string>
     */
    private const array TEXT_PATHS = [
        '/sitemap.xml',
        '/robots.txt',
        '/llms.txt',
    ];

    /**
     * Files the web server answers before Laravel sees the request.
     *
     * @var list<string>
     */
    private const array STATIC_FILES = [
        'robots.txt',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config(['seo.robots.noindex_outside_production' => false]);
    }

    public function test_public_url_surface_matches_the_snapshot(): void
    {
        $actual = json_encode(
            $this->captureSnapshot(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        )."\n";

        $snapshotFile = base_path(self::SNAPSHOT_PATH);

        if (getenv('UPDATE_SNAPSHOTS') || ! file_exists($snapshotFile)) {
            if (! is_dir(dirname($snapshotFile))) {
                mkdir(dirname($snapshotFile), 0755, true);
            }

            file_put_contents($snapshotFile, $actual);

            $this->markTestIncomplete('Snapshot written to '.self::SNAPSHOT_PATH.'; review and commit it.');
        }

        $this->assertSame(
            file_get_contents($snapshotFile),
            $actual,
            'The public URL surface drifted. If the change is intended, regenerate with UPDATE_SNAPSHOTS=1.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function captureSnapshot(): array
    {
        $fixtures = [];

        foreach ([...self::PAGE_PATHS, ...self::REDIRECT_PATHS] as $path) {
            $fixtures['GET '.$path] = $this->describePage($this->get($this->absoluteUrl($path)));
        }

        foreach (self::TEXT_PATHS as $path) {
            $fixtures['GET '.$path] = $this->describeText($this->get($this->absoluteUrl($path)));
        }

        $fixtures['POST /csp-report'] = $this->describeStatus($this->call(
            'POST',
            $this->absoluteUrl('/csp-report'),
            server: ['CONTENT_TYPE' => 'application/csp-report'],
            content: (string) json_encode(['csp-report' => [
                'effective-directive' => 'script-src-elem',
                'blocked-uri' => 'https://evil.example/x.js',
                'document-uri' => $this->absoluteUrl('/'),
            ]]),
        ));

        $staticFiles = [];

        foreach (self::STATIC_FILES as $file) {
            $filePath = public_path($file);
            $staticFiles[$file] = file_exists($filePath) ? (string) file_get_contents($filePath) : null;
        }

        return [
            'fixtures' => $fixtures,
            'static_files' => $staticFiles,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function describePage(TestResponse $response): array
    {
        $description = $this->describeStatus($response);

        if (! $response->isRedirection() && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            $description['head'] = $this->describeHead((string) $response->getContent());
        }

        return $description;
    }

    /**
     * @return array<string, mixed>
     */
    private function describeText(TestResponse $response): array
    {
        $description = $this->describeStatus($response);

        if ($response->isOk()) {
            $description['content_type'] = $response->headers->get('Content-Type');
            $description['body'] = $this->sortListItems($this->normalise(
                (string) preg_replace('#<lastmod>[^<]*</lastmod>#', '<lastmod>{date}</lastmod>', (string) $response->getContent()),
            ));
        }

        return $description;
    }

    /**
     * @return array{status: int, location?: string}
     */
    private function describeStatus(TestResponse $response): array
    {
        $description = ['status' => $response->getStatusCode()];

        if ($response->headers->has('Location')) {
            $description['location'] = $this->relativeLocation((string) $response->headers->get('Location'));
        }

        return $description;
    }

    /**
     * @return array{title: string|null, description: string|null, canonical: string|null, robots: string|null, meta: array<string, list<string>>, json_ld: list<mixed>}
     */
    private function describeHead(string $html): array
    {
        $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);

        $title = $document->querySelector('head title');

        $socialMeta = [];

        foreach ($document->querySelectorAll('meta[property^="og:"], meta[name^="og:"], meta[name^="twitter:"], meta[property^="twitter:"]') as $element) {
            $key = $element->getAttribute('property') ?: $element->getAttribute('name');
            $socialMeta[$key][] = $this->collapseWhitespace($this->normalise((string) $element->getAttribute('content')));
        }

        ksort($socialMeta);

        $jsonLd = [];

        foreach ($document->querySelectorAll('script[type="application/ld+json"]') as $element) {
            $decoded = json_decode((string) $element->textContent, true);

            if (is_array($decoded)) {
                array_walk_recursive($decoded, function (mixed &$value): void {
                    $value = is_string($value) ? $this->normalise($value) : $value;
                });
            }

            $jsonLd[] = $decoded;
        }

        return [
            'title' => $title ? $this->collapseWhitespace((string) $title->textContent) : null,
            'description' => $this->attributeOf($document, 'meta[name="description"]', 'content'),
            'canonical' => $this->attributeOf($document, 'link[rel="canonical"]', 'href'),
            'robots' => $this->attributeOf($document, 'meta[name="robots"]', 'content'),
            'meta' => $socialMeta,
            'json_ld' => $jsonLd,
        ];
    }

    private function attributeOf(HTMLDocument $document, string $selector, string $attribute): ?string
    {
        $element = $document->querySelector($selector);

        if ($element === null || ! $element->hasAttribute($attribute)) {
            return null;
        }

        return $this->collapseWhitespace($this->normalise((string) $element->getAttribute($attribute)));
    }

    private function absoluteUrl(string $path): string
    {
        return rtrim((string) config('app.url'), '/').$path;
    }

    private function relativeLocation(string $location): string
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        if (str_starts_with($location, $appUrl)) {
            $location = substr($location, strlen($appUrl));
        }

        return $location === '' ? '/' : $location;
    }

    /**
     * Strips per-request and per-machine values: app URL, CSP nonces, cache-busting hashes and Glide signatures (from APP_KEY).
     */
    private function normalise(string $value): string
    {
        $value = str_replace(
            [rtrim((string) config('app.url'), '/'), str_replace('/', '\/', rtrim((string) config('app.url'), '/'))],
            self::APP_URL_PLACEHOLDER,
            $value,
        );

        $value = (string) preg_replace('/nonce="[^"]*"/', 'nonce="{nonce}"', $value);

        $value = (string) preg_replace('/([?&])s=[a-f0-9]{32}/', '$1s={signature}', $value);

        return (string) preg_replace('/([?&])v=[A-Za-z0-9]+/', '$1v={hash}', $value);
    }

    /**
     * Sorts each run of markdown list lines (llms.txt): the toolkit orders pages by
     * last-modified time without a tie-break, so pages saved in the same second swap places.
     */
    private function sortListItems(string $body): string
    {
        return (string) preg_replace_callback('/(?:^- .*\n)+/m', function (array $match): string {
            $lines = explode("\n", rtrim($match[0], "\n"));
            sort($lines);

            return implode("\n", $lines)."\n";
        }, $body);
    }

    private function collapseWhitespace(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }
}
