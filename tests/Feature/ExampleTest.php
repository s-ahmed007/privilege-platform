<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function an_example()
    {
        $this->get('/adminDashboard')
            ->assertSuccessful();
    }
}
