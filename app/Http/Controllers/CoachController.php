<?php

namespace App\Http\Controllers;

use App\Enums\ProfileTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(): View
    {
        $teamId = Setting::get('default_team_id');
        $coachCount = User::whereNotNull('coach_profile_approved_at')
            ->whereHas('teams', fn ($q) => $q
                ->where('teams.id', $teamId)
                ->where('team_user.role', RoleEnum::COACH->value)
            )->count();

        return view('pages.coaches.index', compact('coachCount'));
    }

    public function show(User $user): View
    {
        abort_unless(
            $user->teams()->wherePivot('role', RoleEnum::COACH->value)->exists()
            && $user->coach_profile_approved_at,
            404
        );

        $user->load([
            'coachProfile',
            'coachedTrainings' => fn ($query) => $query->where('is_active', true)->with(['sportCategory', 'team']),
            'certifications',
            'profileGalleryItems' => fn ($q) => $q->where('profile_type', ProfileTypeEnum::Coach)->where('is_approved', true)->orderBy('sort_order'),
        ]);

        // Check if user also has an approved athlete profile for cross-link
        $hasAthleteProfile = $user->athlete_profile_approved_at !== null;

        return view('pages.coaches.show', compact('user', 'hasAthleteProfile'));
    }

    public function indexAthletes(): View
    {
        $teamId = Setting::get('default_team_id');
        $athleteCount = User::whereNotNull('athlete_profile_approved_at')
            ->whereHas('teams', fn ($q) => $q
                ->where('teams.id', $teamId)
                ->where('team_user.role', RoleEnum::ATHLETE->value)
            )->count();

        return view('pages.athletes.index', compact('athleteCount'));
    }

    public function indexJudges(): View
    {
        $teamId = Setting::get('default_team_id');
        $judgeCount = User::role(RoleEnum::JUDGE)
            ->whereNotNull('judge_profile_approved_at')
            ->whereHas('teams', fn ($q) => $q->where('teams.id', $teamId))
            ->count();

        return view('pages.judges.index', compact('judgeCount'));
    }

    public function showAthlete(User $user): View
    {
        abort_unless(
            $user->teams()->wherePivot('role', RoleEnum::ATHLETE->value)->exists()
            && $user->athlete_profile_approved_at,
            404
        );

        $user->load([
            'athleteProfile',
            'athleteExercises' => fn ($q) => $q->orderBy('sort_order')->with(['exercise', 'media']),
            'athleteGoals' => fn ($q) => $q->orderBy('sort_order')->with('media'),
            'certifications',
            'competitionResults.roundPart.competitionRound.competitionDetail.event',
            'profileGalleryItems' => fn ($q) => $q->where('profile_type', ProfileTypeEnum::Athlete)->where('is_approved', true)->orderBy('sort_order'),
        ]);

        $hasCoachProfile = $user->coach_profile_approved_at !== null;

        return view('pages.athletes.show', compact('user', 'hasCoachProfile'));
    }

    public function showJudge(User $user): View
    {
        abort_unless(
            $user->hasRole(RoleEnum::JUDGE)
            && $user->judge_profile_approved_at,
            404
        );

        $user->load([
            'judgeProfile',
            'certifications',
            'judgedCompetitionDetails' => fn ($q) => $q->with('event'),
            'profileGalleryItems' => fn ($q) => $q->where('profile_type', ProfileTypeEnum::Judge)->where('is_approved', true)->orderBy('sort_order'),
        ]);

        return view('pages.judges.show', compact('user'));
    }
}
