<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Training;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(): View
    {
        $defaultTeamId = Setting::get('default_team_id');

        $trainings = Training::query()
            ->where('is_active', true)
            ->with(['sportCategory', 'coaches', 'team'])
            ->orderByRaw('team_id = ? DESC', [$defaultTeamId])
            ->orderBy('sort_order')
            ->get();

        return view('pages.trainings.index', [
            'trainings' => $trainings,
            'team' => null,
        ]);
    }

    public function show(Training $training): View
    {
        $training->load(['sportCategory', 'coaches.coachProfile', 'team']);

        return view('pages.trainings.show', compact('training'));
    }
}
