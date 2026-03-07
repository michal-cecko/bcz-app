<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use RalphJSmit\Filament\Explore\Drivers\Modifications;
use RalphJSmit\Filament\Explore\Enums\FileType;
use RalphJSmit\Filament\MediaLibrary\Drivers\MediaLibraryItemDriver;

class UuidMediaLibraryItemDriver extends MediaLibraryItemDriver
{
    /**
     * Override to use a nil UUID instead of '0' for impossible ID matching.
     *
     * The parent implementation uses '0' which is not a valid UUID
     * and causes errors with PostgreSQL UUID columns.
     *
     * @param  Builder  $target
     */
    public function applyModification(mixed $target, Modifications\Modification $modification): Builder
    {
        if ($modification instanceof Modifications\Scopes\FileTypeModification) {
            $fileTypes = $modification->getFileTypes();

            $fileTypesContainsTargetModel = $fileTypes->contains(function (FileType $fileType) use ($target) {
                $fileTypeModel = match ($fileType) {
                    FileType::File => $this->getMediaLibraryItemModel(),
                    FileType::Folder => $this->getMediaLibraryFolderModel(),
                };

                return $target->getModel()::class === $fileTypeModel;
            });

            if (! $fileTypesContainsTargetModel) {
                return $target->where($target->getModel()->qualifyColumn('id'), '00000000-0000-0000-0000-000000000000');
            }

            return $target;
        }

        return parent::applyModification($target, $modification);
    }
}
