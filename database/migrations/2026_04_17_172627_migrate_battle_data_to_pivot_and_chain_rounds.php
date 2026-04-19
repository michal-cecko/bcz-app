<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Uid\Uuid;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $this->backfillBattleCompetitors();
            $this->backfillWinnerSide();
            $this->chainRounds();
            $this->backfillCompetitorCount();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            DB::table('battle_competitors')->delete();
            DB::table('competition_rounds')->update([
                'next_round_id' => null,
                'competitor_count' => null,
            ]);
            DB::table('battles')->update(['winner_side' => null]);
        });
    }

    private function backfillBattleCompetitors(): void
    {
        $battles = DB::table('battles')
            ->select(['id', 'competitor_a_id', 'competitor_b_id', 'created_at', 'updated_at'])
            ->get();

        foreach ($battles as $battle) {
            foreach (['a', 'b'] as $side) {
                $column = "competitor_{$side}_id";
                $raw = $battle->{$column};

                if ($raw === null) {
                    continue;
                }

                $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
                if (! is_array($decoded)) {
                    continue;
                }

                $userId = $decoded[0] ?? $decoded['id'] ?? null;
                $userName = $decoded[1] ?? $decoded['name'] ?? null;
                if ($userId === null) {
                    continue;
                }

                $userExists = DB::table('users')->where('id', $userId)->exists();
                if (! $userExists) {
                    continue;
                }

                DB::table('battle_competitors')->insertOrIgnore([
                    'id' => (string) Uuid::v7(),
                    'battle_id' => $battle->id,
                    'side' => $side,
                    'user_id' => $userId,
                    'user_name' => $userName ?? 'TBD',
                    'position' => 0,
                    'created_at' => $battle->created_at,
                    'updated_at' => $battle->updated_at,
                ]);
            }
        }
    }

    private function backfillWinnerSide(): void
    {
        $battles = DB::table('battles')
            ->whereNotNull('winner_id')
            ->select(['id', 'competitor_a_id', 'competitor_b_id', 'winner_id'])
            ->get();

        foreach ($battles as $battle) {
            $winner = is_string($battle->winner_id) ? json_decode($battle->winner_id, true) : $battle->winner_id;
            $a = is_string($battle->competitor_a_id) ? json_decode($battle->competitor_a_id, true) : $battle->competitor_a_id;
            $b = is_string($battle->competitor_b_id) ? json_decode($battle->competitor_b_id, true) : $battle->competitor_b_id;

            $winnerUser = $winner[0] ?? $winner['id'] ?? null;
            $aUser = $a[0] ?? $a['id'] ?? null;
            $bUser = $b[0] ?? $b['id'] ?? null;

            $side = null;
            if ($winnerUser !== null && $winnerUser === $aUser) {
                $side = 'a';
            } elseif ($winnerUser !== null && $winnerUser === $bUser) {
                $side = 'b';
            }

            if ($side !== null) {
                DB::table('battles')->where('id', $battle->id)->update(['winner_side' => $side]);
            }
        }
    }

    private function chainRounds(): void
    {
        $groups = DB::table('competition_rounds')
            ->select(['id', 'competition_detail_id', 'athlete_category_id', 'sort_order'])
            ->orderBy('competition_detail_id')
            ->orderBy('athlete_category_id')
            ->orderBy('sort_order')
            ->orderBy('round_number')
            ->get()
            ->groupBy(fn ($r) => $r->competition_detail_id.'|'.($r->athlete_category_id ?? 'null'));

        foreach ($groups as $chain) {
            $rounds = $chain->values();
            for ($i = 0; $i < $rounds->count() - 1; $i++) {
                DB::table('competition_rounds')
                    ->where('id', $rounds[$i]->id)
                    ->update(['next_round_id' => $rounds[$i + 1]->id]);
            }
        }
    }

    private function backfillCompetitorCount(): void
    {
        $rounds = DB::table('competition_rounds')
            ->select(['id', 'advancement_type', 'athlete_category_id', 'competition_detail_id'])
            ->get();

        foreach ($rounds as $round) {
            if ($round->advancement_type === 'battle_winner') {
                $battleCount = DB::table('battles')
                    ->where('competition_round_id', $round->id)
                    ->count();
                $count = $battleCount > 0 ? $battleCount * 2 : null;
            } else {
                $eventId = DB::table('competition_details')
                    ->where('id', $round->competition_detail_id)
                    ->value('event_id');

                if ($eventId === null || $round->athlete_category_id === null) {
                    $count = null;
                } else {
                    $count = DB::table('event_registrations')
                        ->where('event_id', $eventId)
                        ->where('athlete_category_id', $round->athlete_category_id)
                        ->where('status', 'approved')
                        ->count();
                    $count = $count > 0 ? $count : null;
                }
            }

            if ($count !== null) {
                DB::table('competition_rounds')
                    ->where('id', $round->id)
                    ->update(['competitor_count' => $count]);
            }
        }
    }
};
