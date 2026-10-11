<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Models\Member;
use App\Services\SeasonService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class EnrolledMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'enrolledMembers';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return config('family.enabled') && Gate::allows('view', $ownerRecord);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('family.member');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('family.member'))->searchable(),
            TextColumn::make('user.name')->label(__('family.contact_name')),
            TextColumn::make('user.email')->label(__('family.contact_email')),
            TextColumn::make('joined_at')->date(),
        ])->recordActions([
            Action::make('enrolment')->label(__('family.edit'))->schema([
                Toggle::make('is_active')->required(),
                Toggle::make('continuous_membership')->label(__('family.renew'))->required(),
            ])->fillForm(fn (Member $record): array => [
                'is_active' => $record->pivot->is_active, 'continuous_membership' => $record->pivot->continuous_membership,
            ])->action(function (Member $record, array $data): void {
                Gate::authorize('update', $this->getOwnerRecord());
                $this->getOwnerRecord()->enrolledMembers()->updateExistingPivot($record->id,
                    Arr::only($data, ['is_active', 'continuous_membership']));
            }),
            Action::make('issueMembership')->label(__('member.membership.title'))->schema([
                Select::make('team_season_id')->label(__('member.membership.season'))->required()
                    ->options(fn () => $this->getOwnerRecord()->seasons()->where('ends_at', '>=', today())->pluck('name', 'id')),
            ])->action(function (Member $record, array $data): void {
                Gate::authorize('update', $this->getOwnerRecord());
                $season = $this->getOwnerRecord()->seasons()->findOrFail($data['team_season_id']);
                app(SeasonService::class)->findOrCreateMembership($season, $record);
            }),
        ]);
    }
}
