<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        Page::factory()->published()->create([
            'title' => ['sk' => 'Domov'],
            'slug' => '/',
            'is_system' => true,
            'system_key' => 'homepage',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
