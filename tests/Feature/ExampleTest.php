<?php

namespace Tests\Feature;

use Tests\Concerns\ImportsSiteContent;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use ImportsSiteContent;

    /**
     * @return void
     */
    public function test_basic_test()
    {
        $this->get('/')->assertSuccessful();
    }
}
