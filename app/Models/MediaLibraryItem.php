<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use RalphJSmit\Filament\MediaLibrary\Models\MediaLibraryItem as BaseMediaLibraryItem;

class MediaLibraryItem extends BaseMediaLibraryItem
{
    use HasUuidV7;
}
