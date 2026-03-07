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
            ->where('team_id', $defaultTeamId)
            ->where('is_active', true)
            ->with(['sportCategory', 'coaches'])
            ->orderBy('sort_order')
            ->get();

        return view('pages.trainings.index', compact('trainings'));
    }

    public function show(Training $training): View
    {
        $training->load(['sportCategory', 'coaches.coachProfile']);

        return view('pages.trainings.show', compact('training'));
    }
}
