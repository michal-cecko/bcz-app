<?php

namespace Tests\Feature\Filament;

use App\Filament\Auth\Login;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Exceptions;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\EndpointResolver;
use Tests\TestCase;

/**
 * Sentry issue BCZ-APP-G: bot/scanner traffic POSTs a forged Livewire snapshot
 * update at the admin login page, targeting `discoveredSchemaNames` — an
 * internal property Filament locks against client updates. Livewire is
 * correctly rejecting the tampered request (CannotUpdateLockedPropertyException),
 * not an app bug, so it should stop reaching Sentry. See the dontReport() entry
 * in bootstrap/app.php and https://github.com/filamentphp/filament/discussions/18949.
 */
class LoginLockedPropertyExceptionTest extends TestCase
{
    public function test_forged_update_to_locked_schema_property_is_rejected_but_not_reported(): void
    {
        config()->set('app.debug', false);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $snapshot = json_encode(Livewire::test(Login::class)->snapshot);

        Exceptions::fake();

        $response = $this->withHeaders(['X-Livewire' => 'true'])
            ->postJson(EndpointResolver::updatePath(), ['components' => [
                [
                    'snapshot' => $snapshot,
                    'updates' => ['discoveredSchemaNames' => ['forged']],
                    'calls' => [],
                ],
            ]]);

        $response->assertStatus(419);

        Exceptions::assertNotReported(CannotUpdateLockedPropertyException::class);
    }
}
