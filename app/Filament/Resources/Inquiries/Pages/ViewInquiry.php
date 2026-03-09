<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Enums\InquiryStatusEnum;
use App\Filament\Resources\Inquiries\InquiryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewInquiry extends ViewRecord
{
    protected static string $resource = InquiryResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status === InquiryStatusEnum::NEW) {
            $this->record->update(['status' => InquiryStatusEnum::IN_PROGRESS]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label('Zmeniť stav')
                ->icon(Heroicon::OutlinedArrowPath)
                ->schema([
                    Select::make('status')
                        ->label('Stav')
                        ->options(InquiryStatusEnum::class)
                        ->required(),
                ])
                ->fillForm(fn () => [
                    'status' => $this->record->status,
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => $data['status']]);
                    $this->refreshFormData(['status']);
                }),
            Action::make('editNote')
                ->label('Poznámka')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->schema([
                    Textarea::make('internal_note')
                        ->label('Interná poznámka')
                        ->rows(4),
                ])
                ->fillForm(fn () => [
                    'internal_note' => $this->record->internal_note,
                ])
                ->action(function (array $data) {
                    $this->record->update(['internal_note' => $data['internal_note']]);
                }),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }
}
