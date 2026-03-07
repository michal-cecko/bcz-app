<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Enums\InquiryStatusEnum;
use App\Filament\Resources\Inquiries\InquiryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ViewInquiry extends ViewRecord
{
    protected static string $resource = InquiryResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

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
