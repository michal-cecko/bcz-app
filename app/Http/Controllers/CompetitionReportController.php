<?php

namespace App\Http\Controllers;

use App\Models\CompetitionReport;

class CompetitionReportController extends Controller
{
    public function show(CompetitionReport $competitionReport): \Illuminate\View\View
    {
        abort_unless($competitionReport->is_published, 404);

        $competitionReport->load('competition', 'user');

        return view('competition-reports.show', compact('competitionReport'));
    }
}
