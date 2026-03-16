<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(): View
    {
        $teamId = Setting::get('default_team_id');
        $coachCount = User::whereHas('teams', fn ($q) => $q
            ->where('teams.id', $teamId)
            ->where('team_user.role', RoleEnum::COACH->value)
        )->count();

        return view('pages.coaches.index', compact('coachCount'));
    }

    public function show(User $user): View
    {
        abort_unless(
            $user->teams()->wherePivot('role', RoleEnum::COACH->value)->exists(),
            404
        );

        $user->load([
            'coachProfile',
            'coachedTrainings' => fn ($query) => $query->where('is_active', true)->with(['sportCategory', 'team']),
            'certifications',
        ]);

        return view('pages.coaches.show', compact('user'));
    }

    public function indexAthletes(): View
    {
        $teamId = Setting::get('default_team_id');
        $athleteCount = User::where('has_public_profile', true)
            ->whereNotNull('public_profile_approved_at')
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
            ->whereHas('teams', fn ($q) => $q->where('teams.id', $teamId))
            ->count();

        return view('pages.judges.index', compact('judgeCount'));
    }

    public function showAthlete(User $user): View
    {
        abort_unless(
            $user->teams()->wherePivot('role', RoleEnum::ATHLETE->value)->exists(),
            404
        );

        $user->load([
            'athleteProfile',
            'certifications',
        ]);

        return view('pages.coaches.show', compact('user'));
    }

    public function showJudge(User $user): View
    {
        abort_unless($user->hasRole(RoleEnum::JUDGE), 404);

        $user->load([
            'certifications',
            'judgedCompetitions' => fn ($q) => $q->latest('date_start'),
        ]);

        return view('pages.judges.show', compact('user'));
    }
}
