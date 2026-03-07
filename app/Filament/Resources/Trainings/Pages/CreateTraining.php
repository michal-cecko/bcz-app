<?php

namespace App\Filament\Resources\Trainings\Pages;

use App\Filament\Resources\Trainings\TrainingResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateTraining extends CreateRecord
{
    protected static string $resource = TrainingResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
