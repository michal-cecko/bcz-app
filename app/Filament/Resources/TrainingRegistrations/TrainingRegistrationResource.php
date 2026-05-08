<?php

namespace App\Filament\Resources\TrainingRegistrations;

use App\Enums\RegistrationStatusEnum;
use App\Filament\Clusters\Trainings\TrainingsCluster;
use App\Filament\RelationManagers\RegistrationPaymentsRelationManager;
use App\Filament\Resources\TrainingRegistrations\Pages\ListTrainingRegistrations;
use App\Filament\Resources\TrainingRegistrations\Pages\ViewTrainingRegistration;
use App\Filament\Resources\TrainingRegistrations\Tables\TrainingRegistrationsTable;
use App\Models\TrainingRegistration;
use App\Support\RegistrationFieldOptions;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class TrainingRegistrationResource extends Resource
{
    protected static ?string $model = TrainingRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'registráciu';

    protected static ?string $pluralModelLabel = 'Registrácie na tréningy';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = TrainingsCluster::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('training', fn (Builder $q) => $q->where('team_id', Filament::getTenant()?->id));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Registrácia')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Používateľ')
                                    ->placeholder('Hosť'),
                                TextEntry::make('user.email')
                                    ->label('E-mail')
                                    ->placeholder('-'),
                                TextEntry::make('user.phone')
                                    ->label('Telefón')
                                    ->placeholder('-'),
                                TextEntry::make('status')
                                    ->label('Stav')
                                    ->badge(),
                                TextEntry::make('registered_at')
                                    ->label('Dátum registrácie')
                                    ->dateTime(),
                                TextEntry::make('payment_due_at')
                                    ->label('Splatnosť')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('cancellation_reason')
                                    ->label('Dôvod zrušenia')
                                    ->placeholder('-')
                                    ->columnSpanFull()
                                    ->visible(fn ($record): bool => $record->status === RegistrationStatusEnum::Cancelled || $record->status === RegistrationStatusEnum::Rejected),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Tréning')
                                    ->schema([
                                        TextEntry::make('training.title')
                                            ->label('Názov'),
                                        TextEntry::make('training.pricing_type')
                                            ->label('Typ ceny')
                                            ->badge(),
                                        TextEntry::make('training.price_amount')
                                            ->label('Cena')
                                            ->state(fn ($record): string => $record->training?->price_amount
                                                ? number_format((float) $record->training->price_amount, 2).' EUR'
                                                : '-'),
                                    ]),

                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Údaje z formulára')
                    ->schema(function ($record): array {
                        $formData = $record->form_data ?? [];
                        $schema = $record->training?->registration_form_schema ?? [];

                        if (empty($formData)) {
                            return [
                                TextEntry::make('no_form_data')
                                    ->label('')
                                    ->state('Žiadne údaje z formulára'),
                            ];
                        }

                        $locale = app()->getLocale();
                        $entries = [];
                        foreach ($schema as $field) {
                            $key = $field['name'] ?? $field['key'] ?? '';
                            $value = $formData[$key] ?? null;
                            if ($value === null || $value === '') {
                                continue;
                            }

                            $label = is_array($field['label'] ?? null)
                                ? ($field['label'][$locale] ?? $field['label']['sk'] ?? reset($field['label']))
                                : ($field['label'] ?? $key);

                            $type = $field['type'] ?? null;
                            if (in_array($type, ['select', 'multi_select', 'category'], true)) {
                                $displayValue = RegistrationFieldOptions::labelFor($field, $value, $locale);
                                $entries[] = TextEntry::make("form_data.{$key}")
                                    ->label($label)
                                    ->state($displayValue);
                            } elseif ($type === 'file_input' && is_string($value)) {
                                $entries[] = TextEntry::make("form_data.{$key}")
                                    ->label($label)
                                    ->state(basename($value))
                                    ->url(Storage::disk('public')->url($value), shouldOpenInNewTab: true)
                                    ->color('primary');
                            } else {
                                $entries[] = TextEntry::make("form_data.{$key}")
                                    ->label($label)
                                    ->state($value);
                            }
                        }

                        return $entries;
                    })
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return TrainingRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RegistrationPaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingRegistrations::route('/'),
            'view' => ViewTrainingRegistration::route('/{record}'),
        ];
    }
}
