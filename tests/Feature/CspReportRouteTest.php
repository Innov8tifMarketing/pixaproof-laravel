<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Tests\TestCase;

class CspReportRouteTest extends TestCase
{
    /**
     * @return list<class-string>
     */
    private function gatheredMiddleware(string $routeName): array
    {
        $route = Route::getRoutes()->getByName($routeName);

        $this->assertNotNull($route, "Route [{$routeName}] is not registered.");

        return resolve(Router::class)->gatherRouteMiddleware($route);
    }

    public function test_session_and_forgery_middleware_are_stripped_from_the_collector(): void
    {
        $gathered = $this->gatheredMiddleware('csp.report');

        foreach ([PreventRequestForgery::class, StartSession::class, ShareErrorsFromSession::class] as $middleware) {
            $this->assertNotContains($middleware, $gathered);
        }
    }

    public function test_the_web_group_still_applies_the_excluded_middleware(): void
    {
        $gathered = $this->gatheredMiddleware('statamic.site');

        foreach ([PreventRequestForgery::class, StartSession::class, ShareErrorsFromSession::class] as $middleware) {
            $this->assertContains($middleware, $gathered);
        }
    }

    public function test_the_collector_accepts_a_report_without_a_session_or_token(): void
    {
        $this->call(
            'POST',
            route('csp.report'),
            server: ['CONTENT_TYPE' => 'application/csp-report'],
            content: json_encode(['csp-report' => [
                'effective-directive' => 'script-src',
                'blocked-uri' => 'https://evil.example.com/x.js',
                'document-uri' => 'https://pixaproof.com/',
                'disposition' => 'report',
            ]]),
        )->assertNoContent();
    }
}
