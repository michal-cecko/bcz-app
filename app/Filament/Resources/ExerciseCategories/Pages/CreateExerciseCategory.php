<?php

namespace App\Filament\Resources\ExerciseCategories\Pages;

use App\Filament\Resources\ExerciseCategories\ExerciseCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateExerciseCategory extends CreateRecord
{
    protected static string $resource = ExerciseCategoryResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
