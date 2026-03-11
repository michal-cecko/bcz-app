<?php

namespace App\Services;

use App\Models\MediaLibraryItem;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class SubscriptionLimitService
{
    public function canAddMember(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_members');

        return $limit === null || $this->getUsage($team, 'max_members') < $limit;
    }

    public function canCreateTraining(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_trainings');

        return $limit === null || $this->getUsage($team, 'max_trainings') < $limit;
    }

    public function canCreateCompetition(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_competitions_yearly');

        return $limit === null || $this->getUsage($team, 'max_competitions_yearly') < $limit;
    }

    public function canCreateEvent(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_events_yearly');

        return $limit === null || $this->getUsage($team, 'max_events_yearly') < $limit;
    }

    public function canUploadStorage(Team $team, int $fileSizeBytes = 0): bool
    {
        $limitMb = $this->getLimit($team, 'storage_limit_mb');

        if ($limitMb === null) {
            return true;
        }

        $usedBytes = $this->getStorageUsageBytes($team);

        return ($usedBytes + $fileSizeBytes) <= ($limitMb * 1024 * 1024);
    }

    /**
     * Get storage usage in bytes for a team.
     */
    public function getStorageUsageBytes(Team $team): int
    {
        return (int) DB::table('media')
            ->join('filament_media_library', 'media.model_id', '=', 'filament_media_library.id')
            ->where('filament_media_library.tenant_type', $team->getMorphClass())
            ->where('filament_media_library.tenant_id', $team->id)
            ->where('media.model_type', (new MediaLibraryItem)->getMorphClass())
            ->sum('media.size');
    }

    /**
     * Get formatted storage usage string (e.g. "45.2 MB / 100 MB").
     */
    public function getStorageUsageFormatted(Team $team): ?string
    {
        $limitMb = $this->getLimit($team, 'storage_limit_mb');

        if ($limitMb === null) {
            return null;
        }

        $usedBytes = $this->getStorageUsageBytes($team);
        $usedMb = round($usedBytes / (1024 * 1024), 1);

        return "{$usedMb} MB / {$limitMb} MB";
    }

    public function getRemainingQuota(Team $team, string $limitKey): ?int
    {
        $limit = $this->getLimit($team, $limitKey);

        if ($limit === null) {
            return null;
        }

        if ($limitKey === 'storage_limit_mb') {
            $usedBytes = $this->getStorageUsageBytes($team);
            $usedMb = (int) ceil($usedBytes / (1024 * 1024));

            return max(0, $limit - $usedMb);
        }

        return max(0, $limit - $this->getUsage($team, $limitKey));
    }

    public function getUsage(Team $team, string $limitKey): int
    {
        return match ($limitKey) {
            'max_members' => $team->members()->count(),
            'max_trainings' => $team->trainings()->where('is_active', true)->count(),
            'max_competitions_yearly' => $team->organizedCompetitions()
                ->whereYear('created_at', now()->year)
                ->count(),
            'max_events_yearly' => $team->events()
                ->whereYear('created_at', now()->year)
                ->count(),
            'storage_limit_mb' => (int) ceil($this->getStorageUsageBytes($team) / (1024 * 1024)),
            default => 0,
        };
    }

    private function getLimit(Team $team, string $limitKey): ?int
    {
        $subscription = $team->currentSubscription;

        if (! $subscription) {
            return null;
        }

        return $subscription->plan?->getLimit($limitKey);
    }
}
