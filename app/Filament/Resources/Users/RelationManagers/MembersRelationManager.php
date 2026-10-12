<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Actions\DeleteFamilyMemberAction;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Schemas\MemberForm;
use App\Models\Member;
use App\Services\MemberProfileService;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return config('family.enabled') && $ownerRecord->is_parent && Gate::allows('update', $ownerRecord);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('family.members');
    }

    public function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function (Builder $query): void {
            if (auth()->id() !== $this->getOwnerRecord()->id && ! auth()->user()->isGlobalAdmin()) {
                $query->whereHas('teams', fn (Builder $teams) => $teams->whereKey(filament()->getTenant()?->id));
            }
        })->columns([
            TextColumn::make('name')->label(__('family.member')),
            TextColumn::make('birth_date')->label(__('family.birth_date'))->date('d.m.Y'),
        ])->headerActions([
            Action::make('addMember')->label(__('family.add'))->schema(MemberForm::fields())
                ->visible(fn (): bool => Gate::allows('create', [Member::class, $this->getOwnerRecord()]))
                ->action(function (array $data): void {
                    Gate::authorize('update', $this->getOwnerRecord());
                    Gate::authorize('create', [Member::class, $this->getOwnerRecord()]);
                    app(MemberProfileService::class)->createMember($this->getOwnerRecord(), $data);
                }),
        ])->recordActions([
            Action::make('editMember')->label(__('family.edit'))->schema([
                ...MemberForm::fields(),
                Toggle::make('has_free_membership')->label(__('family.free_membership'))
                    ->visible(fn (): bool => UserForm::canEditPrivilegedFields($this->getOwnerRecord()))
                    ->dehydrated(fn (): bool => UserForm::canEditPrivilegedFields($this->getOwnerRecord())),
            ])->fillForm(fn (Member $record): array => $record->only(['first_name', 'last_name', 'birth_date', 'gender', 'has_free_membership']))
                ->action(function (Member $record, array $data): void {
                    Gate::authorize('update', $this->getOwnerRecord());
                    app(MemberProfileService::class)->saveMember($record, $data, UserForm::canEditPrivilegedFields($this->getOwnerRecord()));
                }),
            DeleteFamilyMemberAction::make(),
        ]);
    }
}
