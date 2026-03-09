<?php

namespace App\Mason\Bricks;

use App\Models\Setting;
use App\Models\User;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class TrainersBrick extends Brick
{
    public static function getId(): string
    {
        return 'trainers';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.trainers');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedUsers;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $teamId = Setting::get('default_team_id');

        $trainers = collect();

        if ($teamId) {
            $coachIds = DB::table('coach_training')
                ->join('trainings', 'trainings.id', '=', 'coach_training.training_id')
                ->where('trainings.team_id', $teamId)
                ->pluck('coach_training.user_id')
                ->unique();

            $trainers = User::query()
                ->whereIn('id', $coachIds)
                ->get();
        }

        return view('mason.bricks.trainers.index', [
            'trainers' => $trainers,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([]);
    }
}
