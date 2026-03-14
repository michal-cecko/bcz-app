<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Resources\Teams\Pages\CreateTeam;
use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Filament\Resources\Teams\RelationManagers\InvitationsRelationManager;
use App\Filament\Resources\Teams\RelationManagers\JoinRequestsRelationManager;
use App\Filament\Resources\Teams\RelationManagers\MembersRelationManager;
use App\Filament\Resources\Teams\Schemas\TeamForm;
use App\Filament\Resources\Teams\Tables\TeamsTable;
use App\Models\Team;
use App\Services\SubscriptionLimitService;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Filament\MediaLibrary\Filament\Infolists\Components\MediaEntry;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'tím';

    protected static ?string $pluralModelLabel = 'Tímy';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\UnitEnum|null $navigationGroup = 'Organizácia';

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = false;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getTranslation('name', 'sk');
    }

    public static function form(Schema $schema): Schema
    {
        return TeamForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Základné údaje')
                            ->schema([
                                MediaEntry::make('logo')
                                    ->label('Logo')
                                    ->circular(),
                                TextEntry::make('name')
                                    ->label('Názov'),
                                TextEntry::make('slug')
                                    ->label('Slug'),
                                IconEntry::make('is_active')
                                    ->label('Aktívny')
                                    ->boolean(),
                                TextEntry::make('story')
                                    ->label('Príbeh')
                                    ->columnSpanFull(),
                                TextEntry::make('achievements')
                                    ->label('Úspechy')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Štatistiky')
                                    ->schema([
                                        TextEntry::make('members_count')
                                            ->label('Členovia')
                                            ->state(fn ($record) => $record->members()->count()),
                                        TextEntry::make('created_at')
                                            ->label('Vytvorený')
                                            ->dateTime(),
                                    ]),

                                Section::make('Predplatné')
                                    ->schema([
                                        TextEntry::make('current_plan')
                                            ->label('Aktuálny plán')
                                            ->state(fn (Team $record): string => $record->currentSubscription?->plan?->getTranslation('name', 'sk') ?? 'Žiadny'),
                                        TextEntry::make('subscription_status')
                                            ->label('Stav')
                                            ->badge()
                                            ->state(fn (Team $record) => $record->currentSubscription?->status),
                                        TextEntry::make('subscription_period')
                                            ->label('Obdobie')
                                            ->state(fn (Team $record) => $record->currentSubscription?->billing_period?->getLabel())
                                            ->placeholder('-'),
                                        TextEntry::make('subscription_ends')
                                            ->label('Platné do')
                                            ->state(fn (Team $record): ?string => $record->currentSubscription?->ends_at?->format('d.m.Y'))
                                            ->placeholder('Neobmedzene'),
                                        TextEntry::make('usage_stats')
                                            ->label('Využitie')
                                            ->state(function (Team $record): string {
                                                $service = app(SubscriptionLimitService::class);
                                                $members = $service->getUsage($record, 'max_members');
                                                $trainings = $service->getUsage($record, 'max_trainings');
                                                $competitions = $service->getUsage($record, 'max_competitions_yearly');
                                                $events = $service->getUsage($record, 'max_events_yearly');

                                                $memberLimit = $service->getRemainingQuota($record, 'max_members');
                                                $trainingLimit = $service->getRemainingQuota($record, 'max_trainings');

                                                return "Členovia: {$members}"
                                                    .($memberLimit !== null ? " (zostáva: {$memberLimit})" : '')
                                                    ." | Tréningy: {$trainings}"
                                                    .($trainingLimit !== null ? " (zostáva: {$trainingLimit})" : '')
                                                    ." | Súťaže: {$competitions}"
                                                    ." | Podujatia: {$events}";
                                            }),
                                    ]),

                                Section::make('Sociálne siete')
                                    ->schema([
                                        KeyValueEntry::make('socials')
                                            ->label('')
                                            ->keyLabel('Platforma')
                                            ->valueLabel('URL'),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return TeamsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MembersRelationManager::class,
            InvitationsRelationManager::class,
            JoinRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'view' => ViewTeam::route('/{record}'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
