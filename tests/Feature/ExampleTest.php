<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @return void
     */
    public function test_basic_test()
    {
        $this->get('/')->assertSuccessful();
    }
}
