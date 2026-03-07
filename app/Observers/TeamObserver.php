<?php

namespace App\Observers;

use App\Models\Team;
use App\Services\MediaLibraryFolderService;

class TeamObserver
{
    public function __construct(private MediaLibraryFolderService $folderService) {}

    public function created(Team $team): void
    {
        $this->folderService->ensureTeamFolder($team);
    }
}
