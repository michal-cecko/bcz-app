<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use RalphJSmit\Filament\MediaLibrary\Models\MediaLibraryFolder as BaseMediaLibraryFolder;

class MediaLibraryFolder extends BaseMediaLibraryFolder
{
    use HasUuidV7;
}
