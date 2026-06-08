<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Enums\SettingTypeEnum;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->admin->teams()->attach($this->team);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_can_list_settings(): void
    {
        $settings = Setting::factory()->count(3)->create();

        Livewire::test(ListSettings::class)
            ->assertOk()
            ->assertCanSeeTableRecords($settings);
    }

    public function test_can_edit_setting_value(): void
    {
        $setting = Setting::factory()->create(['key' => 'original_key', 'value' => 'old_value']);

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'value' => 'new_value',
            ])
            ->call('save')
            ->assertNotified();

        $setting->refresh();
        $this->assertEquals('new_value', $setting->value);
        $this->assertEquals('original_key', $setting->key);
    }

    public function test_can_upload_image_setting(): void
    {
        Storage::fake('public');

        $setting = Setting::factory()->create([
            'key' => 'default_og_image',
            'type' => SettingTypeEnum::IMAGE,
            'value' => null,
        ]);

        $file = UploadedFile::fake()->image('og.png', 1200, 630);

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('value')
            ->fillForm(['value' => [$file]])
            ->call('save')
            ->assertNotified();

        $setting->refresh();
        $this->assertNotNull($setting->value);
        Storage::disk('public')->assertExists($setting->value);
    }

    public function test_cannot_create_settings(): void
    {
        $this->assertFalse(SettingResource::canCreate());
    }
}
