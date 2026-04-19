<?php

namespace Database\Factories;

use App\Models\Battle;
use App\Models\BattleCompetitor;
use App\Models\CompetitionRound;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Battle> */
class BattleFactory extends Factory
{
    protected $model = Battle::class;

    public function definition(): array
    {
        return [
            'competition_round_id' => CompetitionRound::factory(),
            'bracket_position' => fake()->numberBetween(1, 16),
            'winner_side' => null,
        ];
    }

    /**
     * Attach competitors to both sides of the battle after creation.
     *
     * @param  User|array<int, User>  $sideA
     * @param  User|array<int, User>  $sideB
     */
    public function pair(User|array $sideA, User|array $sideB, ?User $winner = null): self
    {
        $sideAUsers = is_array($sideA) ? $sideA : [$sideA];
        $sideBUsers = is_array($sideB) ? $sideB : [$sideB];

        $winnerSide = null;
        if ($winner !== null) {
            if (collect($sideAUsers)->contains(fn (User $u) => $u->id === $winner->id)) {
                $winnerSide = 'a';
            } elseif (collect($sideBUsers)->contains(fn (User $u) => $u->id === $winner->id)) {
                $winnerSide = 'b';
            }
        }

        return $this
            ->state(fn () => $winnerSide ? ['winner_side' => $winnerSide] : [])
            ->afterCreating(function (Battle $battle) use ($sideAUsers, $sideBUsers): void {
                foreach (['a' => $sideAUsers, 'b' => $sideBUsers] as $side => $users) {
                    foreach ($users as $position => $user) {
                        BattleCompetitor::create([
                            'battle_id' => $battle->id,
                            'side' => $side,
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'position' => $position,
                        ]);
                    }
                }
            });
    }
}
