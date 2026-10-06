<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Imports the pages, navigations, SEO & brand values and redirects before each
 * test, so pages render from Statamic entries as they do on the live site.
 */
trait ImportsSiteContent
{
    protected function setUpImportsSiteContent(): void
    {
        Storage::fake('media');

        $this->artisan('pixaproof:import-content')->assertSuccessful();
    }
}
