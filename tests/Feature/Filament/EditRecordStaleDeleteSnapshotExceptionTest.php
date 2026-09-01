<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Cities\Pages\EditCity;
use App\Models\City;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\EndpointResolver;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Sentry issue BCZ-APP-Q: `Error: Typed property
 * Filament\Resources\Pages\EditRecord::$record must not be accessed before
 * initialization`.
 *
 * When a Filament `EditRecord` page deletes its record via `DeleteAction`,
 * `InteractsWithRecord::afterActionCalled()` legitimately sets
 * `$this->record = null` so Livewire doesn't try to dehydrate a model that
 * no longer exists. The resulting (checksum-valid) snapshot therefore
 * carries a literal `record: null`.
 *
 * If that exact snapshot is replayed with another Livewire "update" request
 * -- a duplicate submit, a queued network retry, or a bfcache-resurrected
 * tab -- `HandleComponents::hydrateProperties()` deliberately skips setting
 * typed properties back to `null` (see the comment in that method), leaving
 * `$record` *uninitialized* instead. The next read of `$this->record`
 * (`InteractsWithRecord::getBaseRecord()`, via `authorizeAccess()`) then
 * throws a raw, uncatchable-by-`instanceof` `Error` instead of a normal
 * Filament/Livewire exception.
 *
 * This is Livewire correctly bouncing a stale/replayed request, not an app
 * bug -- no app code reads or writes `$record` directly. See the
 * `dontReportWhen()` entry in bootstrap/app.php.
 */
class EditRecordStaleDeleteSnapshotExceptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $team = Team::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($team);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($team);
        Filament::bootCurrentPanel();
    }

    public function test_replayed_update_after_delete_is_not_reported(): void
    {
        config()->set('app.debug', false);

        $city = City::factory()->create();

        // Legitimately delete the record, exactly like the "Delete" header
        // action does. The response snapshot now carries `record: null`.
        $postDeleteSnapshot = json_encode(
            Livewire::test(EditCity::class, ['record' => $city->getRouteKey()])
                ->callAction(DeleteAction::class)
                ->snapshot
        );

        Exceptions::fake();

        // Replay that exact snapshot as a follow-up update, simulating a
        // duplicate submit / retried request / resurrected tab. No client
        // tampering is involved -- the checksum is valid because the
        // snapshot is the real one Livewire just issued.
        $response = $this->withHeaders(['X-Livewire' => 'true'])
            ->postJson(EndpointResolver::updatePath(), ['components' => [
                [
                    'snapshot' => $postDeleteSnapshot,
                    'updates' => [],
                    'calls' => [],
                ],
            ]]);

        $response->assertStatus(500);

        Exceptions::assertNotReported(
            fn (\Error $e) => str_contains($e->getMessage(), '::$record must not be accessed before initialization')
        );
    }
}
