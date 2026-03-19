<?php

namespace App\Filament\Resources\TeamSeasons;

use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\TeamSeasons\Pages\EditTeamSeason;
use App\Filament\Resources\TeamSeasons\Pages\ViewTeamSeason;
use App\Filament\Resources\TeamSeasons\Schemas\TeamSeasonForm;
use App\Models\TeamSeason;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class TeamSeasonResource extends Resource
{
    protected static ?string $model = TeamSeason::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'sezónu';

    protected static ?string $pluralModelLabel = 'Sezóny';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static bool $isScopedToTenant = false;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return TeamSeasonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Detaily sezóny')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Názov'),
                                TextEntry::make('team.name')
                                    ->label('Tím')
                                    ->formatStateUsing(fn ($record): string => $record->team?->getTranslation('name', 'sk') ?? '-'),
                                TextEntry::make('starts_at')
                                    ->label('Začiatok')
                                    ->date(),
                                TextEntry::make('ends_at')
                                    ->label('Koniec')
                                    ->date(),
                                TextEntry::make('fee_display')
                                    ->label('Suma')
                                    ->state(fn (TeamSeason $record): string => number_format((float) $record->fee_amount, 2).' '.$record->fee_currency),
                                TextEntry::make('payment_deadline_days')
                                    ->label('Splatnosť (dní)'),
                                TextEntry::make('status_display')
                                    ->label('Stav')
                                    ->badge()
                                    ->state(fn (TeamSeason $record): string => $record->isActive() ? 'Aktívna' : ($record->isFuture() ? 'Budúca' : 'Ukončená'))
                                    ->color(fn (string $state): string => match ($state) {
                                        'Aktívna' => 'success',
                                        'Budúca' => 'info',
                                        default => 'gray',
                                    }),
                            ])
                            ->columns(3)
                            ->columnSpan(2),

                        Section::make('Štatistiky')
                            ->schema([
                                TextEntry::make('memberships_total')
                                    ->label('Celkovo členstiev')
                                    ->state(fn (TeamSeason $record): int => $record->memberships()->count()),
                                TextEntry::make('memberships_active')
                                    ->label('Aktívnych')
                                    ->state(fn (TeamSeason $record): int => $record->memberships()->where('status', 'active')->count()),
                                TextEntry::make('memberships_pending')
                                    ->label('Čakajúcich')
                                    ->state(fn (TeamSeason $record): int => $record->memberships()->where('status', 'pending')->count()),
                                TextEntry::make('memberships_cancelled')
                                    ->label('Zrušených')
                                    ->state(fn (TeamSeason $record): int => $record->memberships()->where('status', 'cancelled')->count()),
                                TextEntry::make('memberships_free')
                                    ->label('Zadarmo')
                                    ->state(fn (TeamSeason $record): int => $record->memberships()->where('is_free', true)->count()),
                                TextEntry::make('max_capacity')
                                    ->label('Kapacita')
                                    ->placeholder('Neobmedzená'),
                                IconEntry::make('has_capacity_display')
                                    ->label('Voľné miesta')
                                    ->boolean()
                                    ->state(fn (TeamSeason $record): bool => $record->hasCapacity()),
                                TextEntry::make('trainings_count')
                                    ->label('Tréningy')
                                    ->state(fn (TeamSeason $record): int => $record->trainings()->count()),
                                TextEntry::make('created_at')
                                    ->label('Vytvorená')
                                    ->dateTime(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembershipsRelationManager::class,
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return TeamResource::getUrl('index', $parameters, $isAbsolute, $panel, $tenant);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewTeamSeason::route('/{record}'),
            'edit' => EditTeamSeason::route('/{record}/edit'),
        ];
    }
}
