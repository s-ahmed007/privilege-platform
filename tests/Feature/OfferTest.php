<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OfferTest extends TestCase
{
    /** @test */
    public function getAllOffers()
    {
        $this->withoutExceptionHandling();
        $this->get('/')
            ->assertSuccessful();
    }
}
