<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EmailPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_logged_in_user_cannot_store_their_own_html_as_an_email_preview(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson('/admin/email-preview', [
                'content' => [['type' => 'email-rich-text', 'data' => ['content' => '<script>alert(1)</script>']]],
            ]);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse(Route::has('admin.email-preview.store'));
    }
}
