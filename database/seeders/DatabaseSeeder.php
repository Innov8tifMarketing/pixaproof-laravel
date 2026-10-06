<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds the site's Statamic content: pages, navigations, SEO & brand values and redirects.
     */
    public function run(): void
    {
        if (Artisan::call('pixaproof:import-content') !== 0) {
            throw new RuntimeException('pixaproof:import-content failed: '.Artisan::output());
        }
    }
}
