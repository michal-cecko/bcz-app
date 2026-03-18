<?php

namespace App\Filament\Widgets;

use App\Enums\RegistrationStatusEnum;
use App\Filament\Pages\MyTrainings;
use App\Models\TrainingRegistration;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTrainingsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Najbližšie tréningy';

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->headerActions([
                Action::make('viewAll')
                    ->label('Všetky moje tréningy')
                    ->url(MyTrainings::getUrl())
                    ->link()
                    ->button()
                    ->color('gray')
                    ->size('sm'),
            ])
            ->query(
                TrainingRegistration::query()
                    ->where('user_id', auth()->id())
                    ->whereIn('status', [RegistrationStatusEnum::Approved, RegistrationStatusEnum::Pending])
                    ->whereHas('training', fn (Builder $q) => $q->where('team_id', $team?->id)->where('is_active', true))
                    ->with(['training.sportCategory', 'training.coaches'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('training.title')
                    ->label('Tréning')
                    ->formatStateUsing(fn ($record): string => $record->training->getTranslation('title', app()->getLocale()) ?: $record->training->getTranslation('title', 'sk'))
                    ->description(fn ($record): ?string => $record->training->sportCategory?->getTranslation('name', app()->getLocale())),
                TextColumn::make('training.schedule_days')
                    ->label('Rozvrh')
                    ->formatStateUsing(function ($record): string {
                        $days = $record->training->schedule_days ? implode(', ', $record->training->schedule_days) : '';
                        $time = $record->training->start_time ?? '';

                        return trim("{$days} {$time}");
                    }),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Žiadne tréningy')
            ->emptyStateDescription('Nie ste registrovaný na žiadne tréningy.')
            ->emptyStateActions([
                Action::make('findTraining')
                    ->label('Nájsť tréning')
                    ->url(MyTrainings::getUrl())
                    ->icon('heroicon-o-magnifying-glass'),
            ]);
    }
}
