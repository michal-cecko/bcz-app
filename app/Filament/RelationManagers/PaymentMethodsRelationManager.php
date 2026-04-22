<?php

namespace App\Filament\RelationManagers;

use App\Models\PaymentMethod;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentMethods';

    protected static ?string $title = 'Platobné metódy';

    protected static ?string $modelLabel = 'platobnú metódu';

    protected static ?string $pluralModelLabel = 'Platobné metódy';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::pivotFormComponents());
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('payable_payment_method.sort_order')
            ->defaultSort('payable_payment_method.sort_order')
            ->columns([
                TextColumn::make('method')
                    ->label('Metóda')
                    ->badge(),
                TextColumn::make('pivot_title')
                    ->label('Názov')
                    ->state(fn (PaymentMethod $record): string => self::effectiveTitle($record)),
                IconColumn::make('pivot.is_enabled')
                    ->label('Aktívna')
                    ->boolean(),
                TextColumn::make('pivot.sort_order')
                    ->label('Poradie'),
            ])
            ->headerActions([
                Action::make('attach')
                    ->label('Priradiť metódu')
                    ->modalHeading('Priradiť platobnú metódu')
                    ->schema(array_merge(
                        [
                            Select::make('payment_method_id')
                                ->label('Platobná metóda')
                                ->options(fn () => $this->availablePaymentMethodOptions())
                                ->required()
                                ->searchable(),
                        ],
                        self::pivotFormComponents(),
                    ))
                    ->action(function (array $data): void {
                        $methodId = $data['payment_method_id'];
                        unset($data['payment_method_id']);

                        $this->getOwnerRecord()->paymentMethods()->attach($methodId, $data);

                        Notification::make()
                            ->success()
                            ->title('Platobná metóda bola priradená.')
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }

    /**
     * @return array<int|string, string>
     */
    protected function availablePaymentMethodOptions(): array
    {
        $attachedIds = $this->getOwnerRecord()->paymentMethods()->pluck('payment_methods.id')->all();

        return PaymentMethod::query()
            ->whereNotIn('id', $attachedIds)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (PaymentMethod $m) => [
                $m->id => $m->getTranslation('title', 'sk').' ('.($m->method?->value ?? '').')',
            ])
            ->all();
    }

    /**
     * @return array<int, Component|Field>
     */
    protected static function pivotFormComponents(): array
    {
        return [
            Tabs::make('Preklady')
                ->tabs([
                    Tabs\Tab::make('SK')
                        ->schema([
                            TextInput::make('title.sk')
                                ->label('Názov (SK)')
                                ->placeholder('Ponechaj prázdne pre použitie predvoleného názvu'),
                            RichEditor::make('description.sk')
                                ->label('Popis (SK)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                ->placeholder('Ponechaj prázdne pre použitie predvoleného popisu')
                                ->columnSpanFull(),
                            RichEditor::make('instructions.sk')
                                ->label('Pokyny pre platbu (SK)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                ->helperText('Zobrazí sa zákazníkovi po výbere tejto metódy.')
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('EN')
                        ->schema([
                            TextInput::make('title.en')
                                ->label('Názov (EN)'),
                            RichEditor::make('description.en')
                                ->label('Popis (EN)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                ->columnSpanFull(),
                            RichEditor::make('instructions.en')
                                ->label('Pokyny pre platbu (EN)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('CZ')
                        ->schema([
                            TextInput::make('title.cs')
                                ->label('Názov (CZ)'),
                            RichEditor::make('description.cs')
                                ->label('Popis (CZ)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                ->columnSpanFull(),
                            RichEditor::make('instructions.cs')
                                ->label('Pokyny pre platbu (CZ)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
            Toggle::make('is_enabled')
                ->label('Aktívna')
                ->default(true),
            TextInput::make('sort_order')
                ->label('Poradie')
                ->numeric()
                ->default(0),
        ];
    }

    protected static function effectiveTitle(PaymentMethod $record): string
    {
        $pivotTitle = $record->pivot?->getTranslation('title', app()->getLocale(), false);

        if (filled($pivotTitle)) {
            return $pivotTitle;
        }

        return $record->getTranslation('title', app()->getLocale());
    }
}
