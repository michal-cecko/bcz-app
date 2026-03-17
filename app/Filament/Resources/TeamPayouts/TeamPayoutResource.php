<?php

namespace App\Filament\Resources\TeamPayouts;

use App\Filament\Clusters\Finances\FinancesCluster;
use App\Filament\Resources\TeamPayouts\Pages\ListTeamPayouts;
use App\Filament\Resources\TeamPayouts\Pages\ViewTeamPayout;
use App\Filament\Resources\TeamPayouts\Tables\TeamPayoutsTable;
use App\Models\TeamPayout;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeamPayoutResource extends Resource
{
    protected static ?string $model = TeamPayout::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static ?string $modelLabel = 'výplatu';

    protected static ?string $pluralModelLabel = 'Výplaty';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = FinancesCluster::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Detaily výplaty')
                            ->schema([
                                TextEntry::make('gross_amount')
                                    ->label('Hrubá suma')
                                    ->formatStateUsing(fn ($record): string => number_format((float) $record->gross_amount, 2).' '.$record->currency),
                                TextEntry::make('fee_amount')
                                    ->label('Poplatok')
                                    ->formatStateUsing(fn ($record): string => number_format((float) $record->fee_amount, 2).' '.$record->currency),
                                TextEntry::make('net_amount')
                                    ->label('Čistá suma')
                                    ->formatStateUsing(fn ($record): string => number_format((float) $record->net_amount, 2).' '.$record->currency),
                                TextEntry::make('status')
                                    ->label('Stav')
                                    ->badge(),
                                TextEntry::make('bank_account_iban')
                                    ->label('IBAN')
                                    ->placeholder('-'),
                                TextEntry::make('bank_account_name')
                                    ->label('Príjemca')
                                    ->placeholder('-'),
                                TextEntry::make('reference')
                                    ->label('Referencia')
                                    ->placeholder('-'),
                                TextEntry::make('notes')
                                    ->label('Poznámky')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Obdobie a dátumy')
                            ->schema([
                                TextEntry::make('period_from')
                                    ->label('Obdobie od')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('period_to')
                                    ->label('Obdobie do')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('paid_at')
                                    ->label('Vyplatené')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->label('Vytvorené')
                                    ->dateTime(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return TeamPayoutsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeamPayouts::route('/'),
            'view' => ViewTeamPayout::route('/{record}'),
        ];
    }
}
