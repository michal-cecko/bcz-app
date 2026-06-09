<?php

namespace App\Providers;

use App\Enums\MenuLocationEnum;
use App\Http\Responses\FilamentLoginResponse;
use App\Http\Responses\LogoutResponse;
use App\Jobs\OptimizeImageJob;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Menu;
use App\Models\Team;
use App\Models\TeamSubscription;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Observers\TrainingObserver;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            LogoutResponse::class,
        );

        $this->app->bind(
            LoginResponse::class,
            FilamentLoginResponse::class,
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

        $this->registerImageOptimizationHooks();
    }

    /**
     * Queue image optimization for every uploaded image — both Spatie media
     * (via MediaHasBeenAddedEvent) and raw Filament FileUpload paths (via a
     * wrapper around the default saveUploadedFileUsing callback).
     */
    protected function registerImageOptimizationHooks(): void
    {
        Event::listen(function (MediaHasBeenAddedEvent $event): void {
            $media = $event->media;

            if (! str_starts_with((string) $media->mime_type, 'image/')) {
                return;
            }

            if ($media->hasCustomProperty('optimized_at')) {
                return;
            }

            dispatch(OptimizeImageJob::forMedia($media->id));
        });

        FileUpload::configureUsing(function (FileUpload $component): void {
            // Spatie subclass has its own pipeline — covered by the event above.
            if ($component instanceof SpatieMediaLibraryFileUpload) {
                return;
            }

            $component->saveUploadedFileUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                try {
                    if (! $file->exists()) {
                        return null;
                    }
                } catch (UnableToCheckFileExistence) {
                    return null;
                }

                $disk = $component->getDiskName();
                $path = $file->storeAs(
                    $component->getDirectory(),
                    $component->getUploadedFileNameForStorage($file),
                    $disk,
                );

                if ($path === false || $path === null || $path === '') {
                    return null;
                }

                if ($component->getVisibility() === 'public') {
                    rescue(fn () => $component->getDisk()->setVisibility($path, 'public'), report: false);
                }

                if (str_starts_with((string) $file->getMimeType(), 'image/')) {
                    dispatch(OptimizeImageJob::forPath($disk, $path));
                }

                return $path;
            });
        });
    }

    /**
     * Bridge team-scoped roles (stored in team_user pivot) to Spatie permissions.
     * This allows $user->can('Training:ViewAny') to work for TEAM_ADMIN/COACH/ATHLETE.
     */
    protected function registerTeamScopedGate(): void
    {
        Gate::before(function ($user, string $ability) {
            if (! $user instanceof User) {
                return null;
            }

            $tenant = filament()->getTenant();
            if (! $tenant instanceof Team) {
                return null;
            }

            return $user->grantsTeamPermission($ability, $tenant) ?: null;
        });
    }
}
