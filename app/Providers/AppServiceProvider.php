<?php

namespace App\Providers;

use App\Enums\MenuLocationEnum;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Menu;
use App\Models\TeamSubscription;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Observers\TrainingObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Training::observe(TrainingObserver::class);

        Relation::morphMap([
            'membership' => Membership::class,
            'training_registration' => TrainingRegistration::class,
            'competition_registration' => EventRegistration::class,
            'event_registration' => EventRegistration::class,
            'team_subscription' => TeamSubscription::class,
        ]);

        View::composer('components.header', function ($view) {
            $view->with('headerMenu', Cache::remember('menu_header', 3600, function () {
                return Menu::query()->where('location', MenuLocationEnum::Header)->first();
            }));
        });

        View::composer('components.footer', function ($view) {
            $view->with('footerDiscoverMenu', Cache::remember('menu_footer_discover', 3600, function () {
                return Menu::query()->where('location', MenuLocationEnum::FooterDiscover)->first();
            }));
            $view->with('footerProgramsMenu', Cache::remember('menu_footer_programs', 3600, function () {
                return Menu::query()->where('location', MenuLocationEnum::FooterPrograms)->first();
            }));
        });

        Menu::saved(function () {
            Cache::forget('menu_header');
            Cache::forget('menu_footer_discover');
            Cache::forget('menu_footer_programs');
        });
    }
}
