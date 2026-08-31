<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Models\PaymentMethod;
use App\Models\Setting;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentMethods';

    protected static ?string $title = 'Platobné metódy';

    protected static ?string $modelLabel = 'platobnú metódu';

    protected static ?string $pluralModelLabel = 'Platobné metódy';

    /**
     * See {@see \App\Filament\Resources\Teams\RelationManagers\SeasonsRelationManager::isReadOnly()}
     * — without this, `AttachAction`/`EditAction`/`DetachAction` are hidden on
     * `ViewTeam` even though they work on `EditTeam` for the same team and user.
     * Note that Filament's Attach/Detach actions only consult `isReadOnly()` and
     * never fall back to a policy check, so this must stay based on
     * `TeamPolicy::update` rather than a blanket `false`.
     */
    public function isReadOnly(): bool
    {
        return ! auth()->user()?->can('update', $this->getOwnerRecord());
    }

    public function table(Table $table): Table
    {
        $isDefaultTeam = $this->getOwnerRecord()->id === Setting::get('default_team_id');

        return $table
            ->reorderable('payment_method_team.sort_order')
            ->defaultSort('payment_method_team.sort_order')
            ->columns([
                TextColumn::make('method')
                    ->label('Metóda')
                    ->badge(),
                TextColumn::make('pivot_title')
                    ->label('Názov')
                    ->state(fn (PaymentMethod $record): string => self::effectiveTitle($record)),
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
                    ->schema(fn (AttachAction $action): array => array_merge(
                        [$action->getRecordSelect()],
                        self::pivotFormComponents(),
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(self::pivotFormComponents()),
                DetachAction::make(),
            ])
            ->description(
                $isDefaultTeam
                    ? null
                    : 'Platby cez GoPay smerujú na platformový účet. Tím dostane výplatu manuálne.'
            );
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
                                ->helperText('Zobrazí sa zákazníkovi po výbere tejto metódy, pokiaľ to neprepíše konkrétny event/tréning.')
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
                ->label('Povolená')
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
