<?php

namespace App\Filament\Resources\EventRegistrations;

use App\Filament\Clusters\Events\EventsCluster;
use App\Filament\RelationManagers\RegistrationPaymentsRelationManager;
use App\Filament\Resources\EventRegistrations\Pages\ListEventRegistrations;
use App\Filament\Resources\EventRegistrations\Pages\ViewEventRegistration;
use App\Filament\Resources\EventRegistrations\Tables\EventRegistrationsTable;
use App\Models\EventRegistration;
use App\Support\RegistrationFieldFormatter;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'registráciu';

    protected static ?string $pluralModelLabel = 'Registrácie na podujatia';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = EventsCluster::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        // Managed only through the Event relation manager; hide the standalone list.
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('event', fn (Builder $q) => $q->where('team_id', Filament::getTenant()?->id));
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
                                TextEntry::make('athleteCategory.name')
                                    ->label('Kategória')
                                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                                    ->placeholder('-'),
                                TextEntry::make('weight_in')
                                    ->label('Váha')
                                    ->suffix(' kg')
                                    ->placeholder('-'),
                                TextEntry::make('registered_at')
                                    ->label('Dátum registrácie')
                                    ->dateTime(),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Podujatie')
                                    ->schema([
                                        TextEntry::make('event.title')
                                            ->label('Názov'),
                                        TextEntry::make('event.event_type')
                                            ->label('Typ')
                                            ->badge(),
                                        TextEntry::make('event.date')
                                            ->label('Dátum')
                                            ->date('d.m.Y'),
                                    ]),

                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Údaje z formulára')
                    ->schema(function ($record): array {
                        $fieldValues = $record->fieldValues;

                        if ($fieldValues->isEmpty()) {
                            return [
                                TextEntry::make('no_field_values')
                                    ->label('')
                                    ->state('Žiadne údaje z formulára'),
                            ];
                        }

                        $locale = app()->getLocale();
                        $event = $record->event;
                        $schema = $event?->organization?->registration_form_schema ?? [];
                        $schemaByKey = collect($schema)->keyBy(fn ($f) => $f['name'] ?? $f['key'] ?? '');

                        $entries = [];
                        foreach ($fieldValues as $fv) {
                            $field = $schemaByKey->get($fv->field_key);
                            if (! $field) {
                                $field = ['name' => $fv->field_key, 'type' => $fv->field_type, 'label' => $fv->field_key];
                            }

                            $formatted = RegistrationFieldFormatter::format($field, $fv->value, $locale, $event);

                            if ($formatted['isImage'] && $formatted['fileUrl']) {
                                $entries[] = ImageEntry::make("field_value_{$fv->id}")
                                    ->label($formatted['label'])
                                    ->state($formatted['fileUrl'])
                                    ->size(160)
                                    ->extraAttributes(['class' => 'object-cover']);

                                continue;
                            }

                            if ($formatted['isFile']) {
                                $entries[] = TextEntry::make("field_value_{$fv->id}")
                                    ->label($formatted['label'])
                                    ->state($formatted['value'])
                                    ->url($formatted['fileUrl'], shouldOpenInNewTab: true)
                                    ->color('primary');

                                continue;
                            }

                            $entries[] = TextEntry::make("field_value_{$fv->id}")
                                ->label($formatted['label'])
                                ->state($formatted['value']);
                        }

                        return $entries;
                    })
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return EventRegistrationsTable::configure($table);
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
            'index' => ListEventRegistrations::route('/'),
            'view' => ViewEventRegistration::route('/{record}'),
        ];
    }
}
