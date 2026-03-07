<?php

namespace App\Services;

use App\Models\MediaLibraryFolder;
use App\Models\Team;
use App\Models\User;

class MediaLibraryFolderService
{
    /**
     * Ensure a root folder exists for the given team.
     */
    public function ensureTeamFolder(Team $team): MediaLibraryFolder
    {
        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => $team->getTranslation('name', 'sk'),
                'parent_id' => null,
                'tenant_type' => $team->getMorphClass(),
                'tenant_id' => $team->id,
            ],
        );
    }

    /**
     * Ensure a user folder exists within the team folder.
     */
    public function ensureUserFolder(User $user, Team $team): MediaLibraryFolder
    {
        $teamFolder = $this->ensureTeamFolder($team);

        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => $user->name,
                'parent_id' => $teamFolder->id,
                'tenant_type' => $team->getMorphClass(),
                'tenant_id' => $team->id,
            ],
        );
    }

    /**
     * Create folder structure for a user across all their teams.
     */
    public function ensureUserFolders(User $user): void
    {
        $user->teams->each(fn (Team $team) => $this->ensureUserFolder($user, $team));
    }
}
