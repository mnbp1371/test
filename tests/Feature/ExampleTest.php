<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get(route('hi'));

        $response->assertStatus(200);

//        dd(
//            $response->json()
//        );
        $response->assertSimilarJson([
            'name' => 30
        ]);
    }
}
