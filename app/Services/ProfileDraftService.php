<?php

namespace App\Services;

use App\Enums\DraftStatusEnum;
use App\Enums\ProfileTypeEnum;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Models\JudgeProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProfileDraftService
{
    /**
     * Save draft data on a profile model and mark it as pending.
     */
    public function saveDraft(AthleteProfile|CoachProfile|JudgeProfile $profile, array $data): void
    {
        $profile->update([
            'draft_data' => $data,
            'draft_status' => DraftStatusEnum::Pending,
            'draft_rejection_reason' => null,
            'draft_submitted_at' => now(),
        ]);
    }

    /**
     * Approve a pending draft: merge draft_data into main columns and clear draft state.
     */
    public function approveDraft(AthleteProfile|CoachProfile|JudgeProfile $profile, User $user): void
    {
        $draftData = $profile->draft_data;
        if (! $draftData) {
            return;
        }

        // Merge draft fields into the main columns
        $mainFields = $this->getMainFieldsForProfile($profile);
        $updateData = array_intersect_key($draftData, array_flip($mainFields));

        $profile->update([
            ...$updateData,
            'draft_data' => null,
            'draft_status' => null,
            'draft_rejection_reason' => null,
            'draft_submitted_at' => null,
        ]);

        // Set the appropriate approval timestamp on the user
        $approvalColumn = $this->getApprovalColumn($profile);
        $user->update([$approvalColumn => now()]);

        // Approve any pending gallery items for this profile type
        $profileType = $this->getProfileType($profile);
        $user->profileGalleryItems()
            ->where('profile_type', $profileType)
            ->where('is_approved', false)
            ->update(['is_approved' => true]);
    }

    /**
     * Reject a pending draft with a reason.
     */
    public function rejectDraft(AthleteProfile|CoachProfile|JudgeProfile $profile, string $reason): void
    {
        $profile->update([
            'draft_status' => DraftStatusEnum::Rejected,
            'draft_rejection_reason' => $reason,
        ]);
    }

    /**
     * Get draft data if it exists, otherwise return the live (main column) data.
     */
    public function getDraftOrLiveData(AthleteProfile|CoachProfile|JudgeProfile $profile): array
    {
        if ($profile->draft_data) {
            return $profile->draft_data;
        }

        $mainFields = $this->getMainFieldsForProfile($profile);

        return collect($mainFields)
            ->mapWithKeys(fn (string $field) => [$field => $profile->getAttribute($field)])
            ->toArray();
    }

    public function hasPendingDraft(AthleteProfile|CoachProfile|JudgeProfile $profile): bool
    {
        return $profile->draft_status === DraftStatusEnum::Pending;
    }

    /**
     * @return list<string>
     */
    private function getMainFieldsForProfile(Model $profile): array
    {
        return match (true) {
            $profile instanceof CoachProfile => ['biography', 'date_started_coaching'],
            $profile instanceof AthleteProfile => ['journey_text', 'date_started_working_out'],
            $profile instanceof JudgeProfile => ['biography', 'disciplines', 'date_started_judging'],
        };
    }

    private function getApprovalColumn(Model $profile): string
    {
        return match (true) {
            $profile instanceof CoachProfile => 'coach_profile_approved_at',
            $profile instanceof AthleteProfile => 'athlete_profile_approved_at',
            $profile instanceof JudgeProfile => 'judge_profile_approved_at',
        };
    }

    private function getProfileType(Model $profile): ProfileTypeEnum
    {
        return match (true) {
            $profile instanceof CoachProfile => ProfileTypeEnum::Coach,
            $profile instanceof AthleteProfile => ProfileTypeEnum::Athlete,
            $profile instanceof JudgeProfile => ProfileTypeEnum::Judge,
        };
    }
}
