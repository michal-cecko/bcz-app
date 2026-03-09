<?php

namespace App\Services;

use App\Models\MediaLibraryFolder;
use App\Models\Team;
use App\Models\User;

class MediaLibraryFolderService
{
    /**
     * Ensure a root "Tímy" folder exists to group all team folders.
     */
    public function ensureTeamsRootFolder(): MediaLibraryFolder
    {
        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => 'Tímy',
                'parent_id' => null,
                'tenant_type' => null,
                'tenant_id' => null,
            ],
        );
    }

    /**
     * Ensure a folder exists for the given team, nested under "Tímy".
     */
    public function ensureTeamFolder(Team $team): MediaLibraryFolder
    {
        $teamsRoot = $this->ensureTeamsRootFolder();

        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => $team->getTranslation('name', 'sk'),
                'parent_id' => $teamsRoot->id,
                'tenant_type' => $team->getMorphClass(),
                'tenant_id' => $team->id,
            ],
        );
    }

    /**
     * Ensure an "Atléti" subfolder exists within the team folder.
     */
    public function ensureAthletesFolder(Team $team): MediaLibraryFolder
    {
        $teamFolder = $this->ensureTeamFolder($team);

        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => 'Atléti',
                'parent_id' => $teamFolder->id,
                'tenant_type' => $team->getMorphClass(),
                'tenant_id' => $team->id,
            ],
        );
    }

    /**
     * Ensure an athlete-specific folder inside "Atléti".
     */
    public function ensureAthleteFolder(User $user, Team $team): MediaLibraryFolder
    {
        $athletesFolder = $this->ensureAthletesFolder($team);

        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => $user->name,
                'parent_id' => $athletesFolder->id,
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

    /**
     * Ensure a "Webový obsah" folder exists (no tenant scoping).
     */
    public function ensureWebContentFolder(): MediaLibraryFolder
    {
        return MediaLibraryFolder::query()->firstOrCreate(
            [
                'name' => 'Webový obsah',
                'parent_id' => null,
                'tenant_type' => null,
                'tenant_id' => null,
            ],
        );
    }
}
