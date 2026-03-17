<?php

namespace App\Filament\Widgets;

use App\Enums\RegistrationStatusEnum;
use App\Models\Training;
use App\Services\TrainingFilterService;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class AvailableTrainingsWidget extends Widget
{
    protected static string $view = 'filament.widgets.available-trainings-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        $team = Filament::getTenant();
        $user = auth()->user();
        $filterService = app(TrainingFilterService::class);

        $registeredTrainingIds = $user
            ? $user->trainingRegistrations()
                ->whereIn('status', [RegistrationStatusEnum::Approved, RegistrationStatusEnum::Pending])
                ->pluck('training_id')
                ->toArray()
            : [];

        $trainings = Training::query()
            ->where('team_id', $team?->id)
            ->where('is_active', true)
            ->whereNotIn('id', $registeredTrainingIds)
            ->with(['sportCategory', 'coaches', 'registrations'])
            ->get()
            ->filter(fn (Training $training) => $user ? $filterService->matchesUserProfile($training, $user) : true)
            ->values();

        return [
            'trainings' => $trainings,
        ];
    }
}
