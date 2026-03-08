<?php

namespace App\Providers\Filament;

use App\Models\Team;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;

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
            ->profile()
            ->emailChangeVerification()
            ->passwordReset()
            ->tenant(Team::class, ownershipRelationship: 'teams')
            ->userMenuItems([
                Action::make('my-team')
                    ->label('Môj tím')
                    ->url(fn (): string => \App\Filament\Resources\Teams\TeamResource::getUrl('view', ['record' => \Filament\Facades\Filament::getTenant()]))
                    ->icon(Heroicon::OutlinedUserGroup),
                Action::make('settings')
                    ->label('Nastavenia')
                    ->url(fn (): string => \App\Filament\Resources\Settings\SettingResource::getUrl())
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
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                'Organizácia',
                'Obsah',
            ])
            ->font('DM Sans')
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            )
            ->plugin(FilamentApexChartsPlugin::make())
            ->plugin(
                FilamentMediaLibrary::make()
                    ->spatieTagsIntegration()
            );
    }
}
