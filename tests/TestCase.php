<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Migrate and seed the site's content once per run; each test then runs in a transaction.
     *
     * @var bool
     */
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config(['logging.channels.csp' => config('logging.channels.null')]);
    }

    /**
     * Keeps the asset container's files (copied in by the seeder) out of public/media.
     */
    protected function beforeRefreshingDatabase(): void
    {
        Storage::fake('media');
    }
}
