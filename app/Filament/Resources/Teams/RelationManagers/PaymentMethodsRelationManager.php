<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Models\Setting;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentMethods';

    protected static ?string $title = 'Platobné metódy';

    protected static ?string $modelLabel = 'platobnú metódu';

    protected static ?string $pluralModelLabel = 'Platobné metódy';

    public function table(Table $table): Table
    {
        $isDefaultTeam = $this->getOwnerRecord()->id === Setting::get('default_team_id');

        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('method')
                    ->label('Metóda')
                    ->badge(),
                TextColumn::make('title')
                    ->label('Názov'),
                IconColumn::make('pivot.is_enabled')
                    ->label('Povolená')
                    ->boolean(),
                TextColumn::make('pivot.sort_order')
                    ->label('Poradie')
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->where('is_active', true))
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Toggle::make('is_enabled')
                            ->label('Povolená')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->default(0),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->description(
                $isDefaultTeam
                    ? null
                    : 'Platby cez GoPay smerujú na platformový účet. Tím dostane výplatu manuálne.'
            );
    }
}
