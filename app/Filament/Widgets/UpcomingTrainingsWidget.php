<?php

namespace App\Filament\Widgets;

use App\Enums\RegistrationStatusEnum;
use App\Models\TrainingRegistration;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class UpcomingTrainingsWidget extends Widget
{
    protected static string $view = 'filament.widgets.upcoming-trainings-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $team = Filament::getTenant();
        $user = auth()->user();

        $registrations = TrainingRegistration::query()
            ->where('user_id', $user?->id)
            ->where('status', RegistrationStatusEnum::Approved)
            ->whereHas('training', fn ($q) => $q->where('team_id', $team?->id)->where('is_active', true))
            ->with(['training.sportCategory', 'training.coaches'])
            ->get();

        return [
            'registrations' => $registrations,
        ];
    }
}
