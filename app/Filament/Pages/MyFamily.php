<?php

namespace App\Filament\Pages;

use App\Enums\RoleEnum;
use App\Filament\Schemas\MemberForm;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class MyFamily extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.my-family';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return (bool) config('family.enabled') && (auth()->user()?->hasRole(RoleEnum::CUSTOMER) ?? false)
            && (auth()->user()?->is_parent ?? false);
    }

    public static function getNavigationLabel(): string
    {
        return __('family.title');
    }

    public function getTitle(): string
    {
        return __('family.title');
    }

    public function table(Table $table): Table
    {
        return $table->query(auth()->user()->members()->getQuery())
            ->columns([
                TextColumn::make('name')->label(__('family.member'))->searchable()->sortable(),
                TextColumn::make('birth_date')->label(__('family.birth_date'))->date('d.m.Y'),
            ])
            ->headerActions([
                Action::make('addMember')->label(__('family.add'))->schema(MemberForm::fields())
                    ->action(function (array $data): void {
                        Gate::authorize('create', Member::class);
                        auth()->user()->members()->create(Arr::only($data, ['first_name', 'last_name', 'birth_date', 'gender']));
                    }),
            ])
            ->recordActions([
                Action::make('editMember')->label(__('family.edit'))->schema(MemberForm::fields())
                    ->fillForm(fn (Member $record): array => $record->only(['first_name', 'last_name', 'birth_date', 'gender']))
                    ->action(function (Member $record, array $data): void {
                        Gate::authorize('update', $record);
                        $record->update(Arr::only($data, ['first_name', 'last_name', 'birth_date', 'gender']));
                    }),
            ])->emptyStateHeading(__('family.empty'));
    }
}
