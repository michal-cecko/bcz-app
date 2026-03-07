<?php

namespace App\Filament\Resources\Inquiries\Tables;

use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByRaw("CASE status WHEN 'new' THEN 0 WHEN 'in_progress' THEN 1 WHEN 'resolved' THEN 2 END")->orderByDesc('created_at'))
            ->columns([
                SelectColumn::make('status')
                    ->label('Stav')
                    ->options(InquiryStatusEnum::class)
                    ->selectablePlaceholder(false)
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('reason')
                    ->label('Dôvod'),
                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->since()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Aktualizované')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('reason')
                    ->label('Dôvod')
                    ->options(InquiryReasonEnum::class),
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(InquiryStatusEnum::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markResolved')
                        ->label('Označiť ako vyriešené')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['status' => InquiryStatusEnum::RESOLVED])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
