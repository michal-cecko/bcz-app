<?php

namespace App\Providers\Filament;

use App\Enums\RoleEnum;
use App\Filament\Pages\Auth\UserSetupWizard;
use App\Filament\Resources\Settings\SettingResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->emailChangeVerification()
            ->passwordReset()
            ->authenticatedRoutes(function () {
                Route::get('/setup-wizard', UserSetupWizard::class)
                    ->name('auth.setup-wizard');
            })
            ->tenant(Team::class, ownershipRelationship: 'teams')
            ->userMenuItems([
                'profile' => fn (Action $action) => $action
                    ->label('Môj profil')
                    ->url(fn (): string => Filament::getTenant()
                        ? UserResource::getUrl('edit', ['record' => auth()->user()])
                        : filament()->getUrl())
                    ->visible(fn (): bool => Filament::getTenant() !== null)
                    ->icon(Heroicon::OutlinedUserCircle),
                Action::make('my-team')
                    ->label('Môj tím')
                    ->url(function (): ?string {
                        $tenant = Filament::getTenant();

                        return $tenant ? TeamResource::getUrl('view', ['record' => $tenant]) : null;
                    })
                    ->visible(fn (): bool => Filament::getTenant() !== null && ! auth()->user()?->isMemberLevel())
                    ->icon(Heroicon::OutlinedUserGroup),
                Action::make('complete-profile')
                    ->label('Dokončiť profil')
                    ->url(fn (): string => '/'.filament()->getCurrentPanel()->getPath().'/setup-wizard')
                    ->visible(function (): bool {
                        $user = auth()->user();

                        return $user !== null
                            && ($user->hasRole([RoleEnum::CUSTOMER->value])
                                || $user->hasRole([RoleEnum::ATHLETE->value]));
                    })
                    ->icon(Heroicon::OutlinedPencilSquare),
                Action::make('settings')
                    ->label('Nastavenia')
                    ->url(function (): ?string {
                        return Filament::getTenant() ? SettingResource::getUrl() : null;
                    })
                    ->visible(fn (): bool => Filament::getTenant() !== null && ! auth()->user()?->isMemberLevel())
                    ->icon(Heroicon::OutlinedCog6Tooth),
            ])
            ->databaseNotifications()
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): View => view('filament.topbar-inquiry-badge'),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): View => view('filament.topbar-role-badge'),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): View => view('filament.topbar-homepage-link'),
            )
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                'Organizácia',
                'Obsah',
                'Ostatné',
            ])
            ->brandLogo(asset('logo/logo-horizontal-short.svg'))
            ->darkModeBrandLogo(asset('logo/logo-horizontal-short-white.svg'))
            ->brandLogoHeight('2rem')
            ->homeUrl('/')
            ->font('DM Sans')
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(
                FilamentShieldPlugin::make()
                    ->scopeToTenant(false)
                    ->registerNavigation(false)
            )
            ->plugin(FilamentApexChartsPlugin::make());
    }
}
