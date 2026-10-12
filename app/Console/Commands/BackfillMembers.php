<?php

namespace App\Console\Commands;

use App\Enums\GenderEnum;
use App\Models\Member;
use App\Models\Membership;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RuntimeException;

class BackfillMembers extends Command
{
    protected $signature = 'members:backfill {--apply : Apply the reviewed mapping} {--mapping= : Path to a private JSON mapping}';

    protected $description = 'Audit and map existing training participants without issuing fees or messages';

    public function handle(): int
    {
        $tables = ['memberships', 'training_registrations', 'training_waitlist', 'team_join_requests'];
        $mapping = ['members' => [], 'records' => [], 'enrolments' => []];
        if ($path = $this->option('mapping')) {
            if (! is_file($path)) {
                $this->error('Mapping file does not exist.');

                return self::FAILURE;
            }
            try {
                $mapping = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                $this->error('Mapping must be valid JSON.');

                return self::FAILURE;
            }
        }
        $validator = Validator::make(is_array($mapping) ? $mapping : [], [
            'members' => 'present|array', 'members.*.id' => 'required|uuid|distinct',
            'members.*.user_id' => 'required|uuid|exists:users,id', 'members.*.name' => 'required|string|max:255',
            'members.*.first_name' => 'nullable|string|max:255', 'members.*.last_name' => 'nullable|string|max:255',
            'members.*.birth_date' => 'nullable|date|before_or_equal:today',
            'members.*.gender' => ['nullable', Rule::enum(GenderEnum::class)],
            'members.*.has_free_membership' => 'sometimes|boolean',
            'records' => 'present|array', 'enrolments' => 'sometimes|array',
            'enrolments.*.member_id' => 'required|uuid', 'enrolments.*.team_id' => 'required|uuid|exists:teams,id',
            'enrolments.*.is_active' => 'required|boolean', 'enrolments.*.continuous_membership' => 'required|boolean',
            'enrolments.*.joined_at' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            $this->error('Invalid mapping: '.implode(' ', $validator->errors()->all()));

            return self::FAILURE;
        }
        $mapping = $validator->validated();
        $owners = Member::pluck('user_id', 'id')->all();
        foreach ($mapping['members'] as $data) {
            if (isset($owners[$data['id']]) && $owners[$data['id']] !== $data['user_id']) {
                $this->error('A mapping cannot transfer member ownership.');

                return self::FAILURE;
            }
            $owners[$data['id']] = $data['user_id'];
        }
        foreach ($mapping['records'] as $table => $records) {
            if (! in_array($table, $tables, true) || ! is_array($records)) {
                $this->error('Unknown record table or invalid mapping.');

                return self::FAILURE;
            }
            foreach ($records as $id => $memberId) {
                $record = DB::table($table)->where('id', $id)->first();
                if (! is_string($memberId) || ! isset($owners[$memberId]) || ! $record
                    || $record->user_id !== $owners[$memberId]
                    || ($record->member_id !== null && $record->member_id !== $memberId)) {
                    $this->error('Record mapping must preserve the account and existing member assignment.');

                    return self::FAILURE;
                }
            }
        }
        foreach ($mapping['enrolments'] ?? [] as $enrolment) {
            if (! isset($owners[$enrolment['member_id']])) {
                $this->error('Unknown enrolment member.');

                return self::FAILURE;
            }
        }

        $users = User::with(['teams', 'trainingRegistrations.training'])->get();
        $ambiguous = [];
        foreach ($users as $user) {
            $names = $user->trainingRegistrations->map(fn ($registration) => mb_strtolower(trim($registration->athleteName() ?? '')))
                ->filter()->unique();
            $birthDates = $user->trainingRegistrations->map(fn ($registration) => RegistrationService::extractBirthDateFromFormData(
                $registration->form_data ?? [], $registration->training?->registration_form_schema ?? []))->filter()->unique();
            $differentBirthDate = $birthDates->contains(fn ($date) => substr((string) $date, 0, 10) !== $user->birth_date?->format('Y-m-d'));
            $conflicting = $user->teams->groupBy('id')->contains(fn ($roles) => $roles->map(fn ($team) => [
                $team->pivot->is_active, (string) $team->pivot->joined_at, $team->pivot->continuous_membership,
            ])->unique()->count() > 1);
            if ($conflicting || $differentBirthDate || $names->contains(fn ($name) => $name !== mb_strtolower(trim($user->name)))) {
                $ambiguous[] = $user->id;
                $this->warn('Needs explicit review: account '.$user->id);
            }
        }
        foreach ($tables as $table) {
            $this->line($table.': '.DB::table($table)->whereNull('member_id')->whereNotNull('user_id')->count().' unmapped account records');
        }
        if (! $this->option('apply')) {
            $this->info('Dry run. No records changed. Keep the reviewed mapping outside git.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($mapping, $users, $ambiguous, $tables): void {
                foreach ($mapping['members'] as $data) {
                    if (! Member::whereKey($data['id'])->exists()) {
                        $member = new Member($data);
                        $member->forceFill(['id' => $data['id']])->save();
                    }
                }
                foreach ($users as $user) {
                    if (in_array($user->id, $ambiguous, true)) {
                        continue;
                    }
                    $member = $user->legacyMember();
                    foreach ($user->teams->unique('id') as $team) {
                        if ($user->participatesInMembershipBilling($team)) {
                            $member->teams()->syncWithoutDetaching([$team->id => $member->teams()->whereKey($team->id)->exists() ? [] : [
                                'is_active' => $team->pivot->is_active, 'joined_at' => $team->pivot->joined_at,
                                'continuous_membership' => $team->pivot->continuous_membership,
                            ]]);
                        }
                    }
                    foreach ($user->memberships()->get()->unique('team_id') as $membership) {
                        if (! $member->teams()->whereKey($membership->team_id)->exists()) {
                            $member->teams()->syncWithoutDetaching([$membership->team_id => [
                                'is_active' => true, 'joined_at' => $membership->starts_at,
                                'continuous_membership' => false,
                            ]]);
                        }
                    }
                    foreach ($tables as $table) {
                        DB::table($table)->where('user_id', $user->id)->whereNull('member_id')
                            ->whereNotIn('id', array_keys($mapping['records'][$table] ?? []))->update(['member_id' => $member->id]);
                    }
                }
                foreach ($mapping['records'] as $table => $records) {
                    foreach ($records as $id => $memberId) {
                        DB::table($table)->where('id', $id)->update(['member_id' => $memberId]);
                    }
                }
                foreach ($mapping['enrolments'] ?? [] as $data) {
                    $member = Member::findOrFail($data['member_id']);
                    $teamId = $data['team_id'];
                    unset($data['member_id'], $data['team_id']);
                    $member->teams()->syncWithoutDetaching([$teamId => $data]);
                }
                DB::table('users')->where('is_parent', false)->whereIn('id', DB::table('members')->select('user_id')
                    ->groupBy('user_id')->havingRaw('COUNT(*) > 1'))->update(['is_parent' => true]);
                if (Membership::whereNotNull('member_id')->whereDoesntHave('member.teams',
                    fn ($query) => $query->whereColumn('teams.id', 'memberships.team_id'))->exists()) {
                    throw new RuntimeException('Membership enrolments are missing. Add them to the reviewed mapping. All changes rolled back.');
                }
                foreach ($tables as $table) {
                    if (DB::table($table)->whereNull('member_id')->whereNotNull('user_id')->exists()) {
                        throw new RuntimeException('Incomplete mapping. All changes rolled back.');
                    }
                }
                if (DB::table('training_registrations')->select('member_id')->whereNotNull('member_id')->where('status', '!=', 'cancelled')
                    ->groupBy('training_id', 'member_id')->havingRaw('COUNT(*) > 1')->exists()
                    || DB::table('memberships')->select('member_id')->whereNotNull('member_id')->whereIn('status', ['active', 'pending'])
                        ->groupBy('team_id', 'team_season_id', 'member_id')->havingRaw('COUNT(*) > 1')->exists()) {
                    throw new RuntimeException('Duplicate current records require review. All changes rolled back.');
                }
            });
        } catch (QueryException $exception) {
            $this->error('Mapping conflicts with existing records. All changes rolled back.');

            return self::FAILURE;
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
        $this->info('Mapping complete. No fees, payments or messages were created.');

        return self::SUCCESS;
    }
}
