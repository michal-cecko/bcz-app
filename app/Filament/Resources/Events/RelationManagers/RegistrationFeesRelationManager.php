<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Models\AthleteCategory;
use App\Models\RegistrationFee;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RegistrationFeesRelationManager extends RelationManager
{
    protected static string $relationship = 'competitionDetail';

    protected static ?string $title = 'Poplatky za kategórie';

    protected static ?string $modelLabel = 'poplatok';

    protected static ?string $pluralModelLabel = 'Poplatky';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type === EventTypeEnum::Competition;
    }

    public function form(Schema $schema): Schema
    {
        $detail = $this->getOwnerRecord()->competitionDetail;
        $linkedCategoryIds = $detail
            ? $detail->athleteCategories()->pluck('athlete_categories.id')->all()
            : [];

        return $schema->components([
            Select::make('athlete_category_id')
                ->label('Kategória')
                ->options(fn (): array => AthleteCategory::query()
                    ->when($linkedCategoryIds, fn ($q) => $q->whereIn('id', $linkedCategoryIds))
                    ->orderBy('sort_order')
                    ->get()
                    ->mapWithKeys(fn (AthleteCategory $cat) => [$cat->id => $cat->getTranslation('name', 'sk')])
                    ->all())
                ->helperText($linkedCategoryIds
                    ? 'Iba kategórie priradené k súťaži.'
                    : 'Najprv priraďte kategórie atlétov v záložke "Súťaž".')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('amount')
                ->label('Suma')
                ->numeric()
                ->required()
                ->step(0.01)
                ->helperText('Zadajte 0 pre kategóriu zdarma — registrácia preskočí platobný krok.'),
            Select::make('currency')
                ->label('Mena')
                ->options([
                    'EUR' => 'EUR',
                    'CZK' => 'CZK',
                    'USD' => 'USD',
                ])
                ->default('EUR')
                ->required(),
            Tabs::make('Popis (preklady)')
                ->tabs([
                    Tabs\Tab::make('SK')
                        ->schema([
                            Textarea::make('description.sk')
                                ->label('Popis (SK)')
                                ->rows(3)
                                ->helperText('Voliteľný popis. Pri sume 0 sa zobrazí používateľovi po registrácii namiesto platobných pokynov (napr. "Podporujeme šport u mladých — registrácia je zdarma").'),
                        ]),
                    Tabs\Tab::make('EN')
                        ->schema([
                            Textarea::make('description.en')
                                ->label('Popis (EN)')
                                ->rows(3),
                        ]),
                    Tabs\Tab::make('CZ')
                        ->schema([
                            Textarea::make('description.cs')
                                ->label('Popis (CZ)')
                                ->rows(3),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        $detail = $this->getOwnerRecord()->competitionDetail;

        if (! $detail) {
            return $table
                ->columns([])
                ->emptyStateHeading('Súťaž nie je nakonfigurovaná')
                ->emptyStateDescription('Najprv vyplňte záložku "Súťaž" a pridajte kategórie atlétov.');
        }

        return $table
            ->relationship(fn () => $detail->registrationFees())
            ->columns([
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn (RegistrationFee $record): string => $record->athleteCategory?->getTranslation('name', 'sk') ?? '-')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Suma')
                    ->state(fn (RegistrationFee $record): string => number_format((float) $record->amount, 2, ',', ' ').' '.$record->currency)
                    ->badge()
                    ->color(fn (RegistrationFee $record): string => (float) $record->amount === 0.0 ? 'success' : 'gray'),
                TextColumn::make('description')
                    ->label('Popis (SK)')
                    ->state(fn (RegistrationFee $record): ?string => $record->getTranslation('description', 'sk', false))
                    ->limit(50)
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('Žiadne kategorické poplatky')
            ->emptyStateDescription('Bez prepisov sa použije základná cena súťaže pre všetky kategórie.')
            ->headerActions([
                CreateAction::make()
                    ->label('Pridať poplatok')
                    ->modalHeading('Pridať poplatok pre kategóriu'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
