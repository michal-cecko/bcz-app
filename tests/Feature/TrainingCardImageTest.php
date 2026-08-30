<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrainingCardImageTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected SportCategory $sportCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->team = Team::factory()->create(['slug' => 'bcz-club']);
        $this->sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
    }

    private function training(array $attributes = []): Training
    {
        return Training::factory()->create(array_merge([
            'team_id' => $this->team->id,
            'sport_category_id' => $this->sportCategory->id,
            'title' => ['sk' => 'Street Workout'],
            'slug' => 'street-workout',
            'is_active' => true,
        ], $attributes));
    }

    private function attachCardImage(Training $training, string $fileName = 'trening.jpg'): void
    {
        $training->addMedia(UploadedFile::fake()->image($fileName, 800, 600))
            ->preservingOriginal()
            ->toMediaCollection('card_image', 'public');
    }

    private function attachCategoryHero(string $fileName = 'kategoria.jpg'): void
    {
        $this->sportCategory->addMedia(UploadedFile::fake()->image($fileName, 800, 600))
            ->preservingOriginal()
            ->toMediaCollection('hero_image', 'public');
    }

    public function test_listing_card_uses_the_trainings_own_image(): void
    {
        $training = $this->training();
        $this->attachCardImage($training);
        $this->attachCategoryHero();

        $response = $this->get('/timy/'.$this->team->slug.'/treningy');

        $response->assertStatus(200);
        $response->assertSee('trening.jpg', false);
        $response->assertDontSee('kategoria.jpg', false);
    }

    public function test_listing_card_falls_back_to_the_sport_category_image(): void
    {
        $this->training();
        $this->attachCategoryHero();

        $response = $this->get('/timy/'.$this->team->slug.'/treningy');

        $response->assertStatus(200);
        $response->assertSee('kategoria.jpg', false);
    }

    public function test_listing_card_shows_the_placeholder_when_no_image_exists(): void
    {
        $this->training();

        $response = $this->get('/timy/'.$this->team->slug.'/treningy');

        $response->assertStatus(200);
        $response->assertDontSee('<img src="'.config('app.url'), false);
        $response->assertSee('svg class="w-12 h-12 text-[#333333]"', false);
    }

    public function test_detail_hero_uses_the_trainings_own_image(): void
    {
        $training = $this->training();
        $this->attachCardImage($training);
        $this->attachCategoryHero();

        $response = $this->get('/timy/'.$this->team->slug.'/treningy/'.$training->slug);

        $response->assertStatus(200);
        $response->assertSee('trening.jpg', false);
    }

    public function test_detail_hero_falls_back_to_the_sport_category_image(): void
    {
        $training = $this->training();
        $this->attachCategoryHero();

        $response = $this->get('/timy/'.$this->team->slug.'/treningy/'.$training->slug);

        $response->assertStatus(200);
        $response->assertSee('kategoria.jpg', false);
    }

    public function test_admin_can_upload_a_card_image_for_a_training(): void
    {
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($this->team);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();

        $training = $this->training();

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->assertFormFieldExists('card_image')
            ->fillForm(['card_image' => [UploadedFile::fake()->image('nahrane.jpg', 800, 600)]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotEmpty($training->refresh()->getFirstMediaUrl('card_image'));
    }
}
