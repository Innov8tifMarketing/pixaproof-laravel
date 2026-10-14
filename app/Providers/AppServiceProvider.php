<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Icon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The global set holding the site-wide copy (navbar button, footer, 404 page).
     * Not `site`: the cascade already has a `site` variable, which a global set would replace.
     */
    public const string SITE_CONTENT_GLOBAL = 'site_content';

    public function register(): void {}

    public function boot(): void
    {
        Icon::register('heroicons', resource_path('icons/heroicons'));

        View::composer(['components.navbar', 'components.footer', 'errors.404'], function ($view): void {
            $view->with('siteContent', GlobalSet::findByHandle(self::SITE_CONTENT_GLOBAL)?->inDefaultSite());
        });
    }
}
