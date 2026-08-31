<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Enums\PayoutStatusEnum;
use App\Filament\Resources\TeamPayouts\Tables\TeamPayoutsTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PayoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'payouts';

    protected static ?string $title = 'Výplaty';

    protected static ?string $modelLabel = 'výplatu';

    protected static ?string $pluralModelLabel = 'Výplaty';

    /**
     * See {@see \App\Filament\Resources\Teams\RelationManagers\SeasonsRelationManager::isReadOnly()}
     * — without this, `CreateAction`/`EditAction`/`DeleteAction` are hidden on
     * `ViewTeam` even though they work on `EditTeam` for the same team and user.
     */
    public function isReadOnly(): bool
    {
        return ! auth()->user()?->can('update', $this->getOwnerRecord());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(self::formFields());
    }

    /** @return list<Component> */
    public static function formFields(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('gross_amount')
                        ->label('Hrubá suma')
                        ->numeric()
                        ->required()
                        ->minValue(0),
                    TextInput::make('fee_amount')
                        ->label('Poplatok')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0),
                    TextInput::make('net_amount')
                        ->label('Čistá suma')
                        ->numeric()
                        ->required()
                        ->minValue(0),
                    Select::make('currency')
                        ->label('Mena')
                        ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                        ->default('EUR')
                        ->required(),
                    Select::make('status')
                        ->label('Stav')
                        ->options(PayoutStatusEnum::translations())
                        ->default(PayoutStatusEnum::PENDING->value)
                        ->required(),
                    DateTimePicker::make('paid_at')
                        ->label('Vyplatené'),
                    DatePicker::make('period_from')
                        ->label('Obdobie od'),
                    DatePicker::make('period_to')
                        ->label('Obdobie do'),
                    TextInput::make('bank_account_iban')
                        ->label('IBAN'),
                    TextInput::make('bank_account_name')
                        ->label('Príjemca'),
                    TextInput::make('reference')
                        ->label('Referencia'),
                ]),
            Textarea::make('notes')
                ->label('Poznámky')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    public function table(Table $table): Table
    {
        return TeamPayoutsTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->label('Nová výplata')
                    ->schema(self::formFields()),
            ])
            ->recordActions([
                ...TeamPayoutsTable::recordActions(),
                EditAction::make()
                    ->schema(self::formFields()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
