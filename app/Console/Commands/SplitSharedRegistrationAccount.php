<?php

namespace App\Console\Commands;

use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RoleEnum;
use App\Models\CompetitionRound;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('registrations:split-account {user : The account (User ID) holding many athletes} {--event= : Limit to a single event ID} {--dry-run : Print the plan without writing}')]
#[Description('Split a shared account\'s extra event registrations into their own distinct users, so competition scoring/results (keyed on user_id) no longer collapse different athletes registered under one email.')]
class SplitSharedRegistrationAccount extends Command
{
    public function handle(): int
    {
        $user = User::find($this->argument('user'));

        if (! $user) {
            $this->error("User {$this->argument('user')} not found.");

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        $registrations = EventRegistration::query()
            ->where('user_id', $user->id)
            ->when($this->option('event'), fn ($q, $eventId) => $q->where('event_id', $eventId))
            ->with(['fieldValues', 'athleteCategory'])
            ->orderBy('registered_at')
            ->get();

        if ($registrations->count() <= 1) {
            $this->info("Nothing to split: {$registrations->count()} registration(s) for {$user->name}.");

            return self::SUCCESS;
        }

        // Keep the account holder's own registration (the one whose form name matches the
        // account), otherwise the earliest — everything else becomes its own athlete/user.
        $keep = $registrations->first(fn (EventRegistration $reg): bool => $reg->athleteName() === $user->name)
            ?? $registrations->first();

        $toSplit = $registrations->reject(fn (EventRegistration $reg): bool => $reg->is($keep))->values();

        $this->line(($dryRun ? '[DRY RUN] ' : '')."Account {$user->name} <{$user->email}> — {$registrations->count()} registrations, splitting {$toSplit->count()}.");
        $this->line("Keeping on this account: {$keep->athleteName()} ({$keep->athleteCategory?->getTranslation('name', 'sk')})");
        $this->newLine();

        $mapping = [];

        DB::transaction(function () use ($toSplit, $keep, $user, $dryRun, &$mapping): void {
            foreach ($toSplit as $reg) {
                $first = $reg->athleteFirstName() ?? Str::before((string) $reg->athleteName(), ' ');
                $last = $reg->athleteLastName() ?? Str::of((string) $reg->athleteName())->after(' ')->toString();
                $email = $this->generateEmail($user->email);

                $newUserId = '(dry-run)';

                if (! $dryRun) {
                    $newUser = User::create([
                        'first_name' => $first,
                        'last_name' => $last,
                        'email' => $email,
                        'password' => Str::random(32),
                        'email_verified_at' => now(),
                        'locale' => $reg->locale ?? $user->locale,
                        'phone' => $this->fieldValue($reg, RegistrationFieldTypeEnum::PHONE),
                        'birth_date' => $this->fieldValue($reg, RegistrationFieldTypeEnum::BIRTH_DATE),
                    ]);
                    $newUser->assignRole(RoleEnum::CUSTOMER);
                    $reg->update(['user_id' => $newUser->id]);
                    $newUserId = $newUser->id;
                }

                $mapping[] = [
                    'registration' => $reg->id,
                    'athlete' => trim("{$first} {$last}"),
                    'category' => $reg->athleteCategory?->getTranslation('name', 'sk') ?? '-',
                    'old_user' => $user->id,
                    'new_user' => $newUserId,
                    'new_email' => $email,
                ];
            }

            if (! $dryRun) {
                $this->fixCompetitorOrder($user->id, $toSplit, $keep);
            }
        });

        $this->table(
            ['Registration', 'Athlete', 'Category', 'New user', 'New email'],
            array_map(fn (array $m): array => [$m['registration'], $m['athlete'], $m['category'], $m['new_user'], $m['new_email']], $mapping),
        );

        $this->newLine();
        $this->line('Reversal mapping (JSON) — keep this to undo:');
        $this->line((string) json_encode($mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }

    /**
     * Build a unique address derived from the account's email using gmail-style
     * plus-addressing, so the new accounts stay unique yet still reach the same inbox.
     */
    private function generateEmail(string $base): string
    {
        [$local, $domain] = array_pad(explode('@', $base, 2), 2, 'gmail.com');
        $local = Str::before($local, '+');

        do {
            $candidate = $local.'+'.Str::lower(Str::random(6)).'@'.$domain;
        } while (User::where('email', $candidate)->exists());

        return $candidate;
    }

    private function fieldValue(EventRegistration $reg, RegistrationFieldTypeEnum $type): ?string
    {
        $value = $reg->fieldValues->first(fn ($fieldValue): bool => $fieldValue->field_type === $type)?->value;

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /**
     * Replace the old shared user_id inside each affected round's competitor_order with
     * the split athletes' new user_ids (preserving the ordering slot). The kept
     * registration's id survives only when it belongs to that round's category.
     *
     * @param  Collection<int, EventRegistration>  $toSplit
     */
    private function fixCompetitorOrder(string $oldUserId, Collection $toSplit, EventRegistration $keep): void
    {
        $rounds = CompetitionRound::query()
            ->whereHas('competitionDetail', fn ($q) => $q->whereIn('event_id', $toSplit->pluck('event_id')->unique()->all()))
            ->with('competitionDetail')
            ->get();

        foreach ($rounds as $round) {
            $order = $round->competitor_order ?? [];
            $position = array_search($oldUserId, $order, true);

            if ($position === false) {
                continue;
            }

            $eventId = $round->competitionDetail->event_id;
            $categoryId = $round->athlete_category_id;

            $newIds = $toSplit
                ->filter(fn (EventRegistration $reg): bool => $reg->event_id === $eventId && $reg->athlete_category_id === $categoryId)
                ->pluck('user_id')
                ->all();

            $keepHere = $keep->event_id === $eventId && $keep->athlete_category_id === $categoryId;
            $replacement = array_values(array_filter(array_merge($keepHere ? [$oldUserId] : [], $newIds)));

            array_splice($order, $position, 1, $replacement);
            $round->update(['competitor_order' => array_values($order)]);
        }
    }
}
