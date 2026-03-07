<?php

namespace App\Filament\Resources\ExerciseCategories\Pages;

use App\Filament\Resources\ExerciseCategories\ExerciseCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExerciseCategories extends ListRecords
{
    protected static string $resource = ExerciseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
