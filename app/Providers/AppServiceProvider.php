<?php

namespace App\Providers;

use App\Enums\MenuLocationEnum;
use App\Http\Responses\LogoutResponse;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Menu;
use App\Models\TeamSubscription;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Observers\TrainingObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            LogoutResponse::class,
        );
    }

    public function boot(): void
    {
        $this->registerTeamScopedGate();

        Training::observe(TrainingObserver::class);

        Relation::morphMap([
            'membership' => Membership::class,
            'training_registration' => TrainingRegistration::class,
            'competition_registration' => EventRegistration::class,
            'event_registration' => EventRegistration::class,
            'team_subscription' => TeamSubscription::class,
        ]);

        View::composer('components.header', function ($view) {
            $view->with('headerMenu', Menu::query()->where('location', MenuLocationEnum::Header)->first());
        });

        View::composer('components.footer', function ($view) {
            $view->with('footerDiscoverMenu', Menu::query()->where('location', MenuLocationEnum::FooterDiscover)->first());
            $view->with('footerProgramsMenu', Menu::query()->where('location', MenuLocationEnum::FooterPrograms)->first());
            $view->with('footerLegalMenu', Menu::query()->where('location', MenuLocationEnum::FooterLegal)->first());
        });
    }

    /**
     * Bridge team-scoped roles (stored in team_user pivot) to Spatie permissions.
     * This allows $user->can('Training:ViewAny') to work for TEAM_ADMIN/COACH/ATHLETE.
     */
    protected function registerTeamScopedGate(): void
    {
        Gate::before(function ($user, string $ability) {
            $tenant = filament()->getTenant();
            if (! $tenant) {
                return null;
            }

            $teamRoles = $user->teams()
                ->where('teams.id', $tenant->id)
                ->pluck('team_user.role')
                ->toArray();

            if (empty($teamRoles)) {
                return null;
            }

            $hasPermission = Role::query()
                ->whereIn('name', $teamRoles)
                ->whereHas('permissions', fn ($q) => $q->where('name', $ability))
                ->exists();

            return $hasPermission ?: null;
        });
    }
}
