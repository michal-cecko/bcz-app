<?php

namespace App\Providers;

use App\Enums\MenuLocationEnum;
use App\Models\CompetitionRegistration;
use App\Models\MediaLibraryFolder;
use App\Models\MediaLibraryItem;
use App\Models\Membership;
use App\Models\Menu;
use App\Models\Team;
use App\Models\TeamSubscription;
use App\Models\TrainingRegistration;
use App\Observers\TeamObserver;
use App\Services\UuidMediaLibraryItemDriver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use RalphJSmit\Filament\MediaLibrary\Drivers\MediaLibraryItemDriver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MediaLibraryItemDriver::class, UuidMediaLibraryItemDriver::class);

        $this->app->afterResolving(MediaLibraryItemDriver::class, function (MediaLibraryItemDriver $driver): void {
            $driver->mediaLibraryItemModel(MediaLibraryItem::class);
            $driver->mediaLibraryFolderModel(MediaLibraryFolder::class);
        });
    }

    public function boot(): void
    {
        Relation::morphMap([
            'filament_media_library_item' => MediaLibraryItem::class,
            'membership' => Membership::class,
            'training_registration' => TrainingRegistration::class,
            'competition_registration' => CompetitionRegistration::class,
            'team_subscription' => TeamSubscription::class,
        ]);

        $this->app->bind(
            \RalphJSmit\Filament\MediaLibrary\Models\MediaLibraryItem::class,
            MediaLibraryItem::class,
        );

        $this->app->bind(
            \RalphJSmit\Filament\MediaLibrary\Models\MediaLibraryFolder::class,
            MediaLibraryFolder::class,
        );

        Team::observe(TeamObserver::class);

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
