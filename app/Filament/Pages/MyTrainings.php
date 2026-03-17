<?php

namespace App\Filament\Pages;

use App\Enums\RegistrationStatusEnum;
use App\Models\TrainingRegistration;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyTrainings extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Moje tréningy';

    protected static ?string $title = 'Moje tréningy';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.my-trainings';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isMemberLevel() ?? false;
    }

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->query(
                TrainingRegistration::query()
                    ->where('user_id', auth()->id())
                    ->whereHas('training', fn (Builder $q) => $q->where('team_id', $team?->id))
                    ->with(['training.sportCategory', 'training.coaches'])
            )
            ->columns([
                TextColumn::make('training.title')
                    ->label('Tréning')
                    ->formatStateUsing(fn ($record): string => $record->training->getTranslation('title', app()->getLocale()) ?: $record->training->getTranslation('title', 'sk'))
                    ->description(fn ($record): ?string => $record->training->sportCategory?->getTranslation('name', app()->getLocale()))
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('training', fn ($q) => $q->where('title', 'ilike', "%{$search}%"))),
                TextColumn::make('training.schedule_days')
                    ->label('Rozvrh')
                    ->formatStateUsing(function ($record): string {
                        $days = $record->training->schedule_days ? implode(', ', $record->training->schedule_days) : '';
                        $time = $record->training->start_time ?? '';

                        return trim("{$days} {$time}");
                    }),
                TextColumn::make('training.coaches')
                    ->label('Tréner')
                    ->formatStateUsing(fn ($record): string => $record->training->coaches->pluck('name')->implode(', '))
                    ->placeholder('-'),
                TextColumn::make('training.place_name')
                    ->label('Miesto')
                    ->formatStateUsing(fn ($record): string => $record->training->getTranslation('place_name', app()->getLocale()) ?: $record->training->getTranslation('place_name', 'sk') ?: '-'),
                TextColumn::make('training.max_capacity')
                    ->label('Kapacita')
                    ->formatStateUsing(function ($record): string {
                        $training = $record->training;
                        if (! $training->max_capacity) {
                            return '-';
                        }
                        $approved = $training->registrations()->where('status', RegistrationStatusEnum::Approved)->count();

                        return "{$approved}/{$training->max_capacity}";
                    }),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Nie ste registrovaný na žiadne tréningy')
            ->emptyStateDescription('Pozrite si dostupné tréningy a zaregistrujte sa.')
            ->emptyStateActions([
                Action::make('available-trainings')
                    ->label('Dostupné tréningy')
                    ->url(AvailableTrainings::getUrl())
                    ->icon(Heroicon::OutlinedMagnifyingGlass),
            ])
            ->paginated(false);
    }
}
