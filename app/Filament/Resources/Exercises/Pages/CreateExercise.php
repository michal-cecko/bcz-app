<?php

namespace App\Filament\Resources\Exercises\Pages;

use App\Filament\Resources\Exercises\ExerciseResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateExercise extends CreateRecord
{
    protected static string $resource = ExerciseResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
