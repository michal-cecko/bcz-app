<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Enums\InquiryStatusEnum;
use App\Filament\Resources\Inquiries\InquiryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Všetky')
                ->badge(fn () => $this->getModel()::query()->count()),
            'new' => Tab::make('Nové')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InquiryStatusEnum::NEW))
                ->badge(fn () => $this->getModel()::query()->where('status', InquiryStatusEnum::NEW)->count())
                ->badgeColor('danger'),
            'in_progress' => Tab::make('Prebieha')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InquiryStatusEnum::IN_PROGRESS))
                ->badge(fn () => $this->getModel()::query()->where('status', InquiryStatusEnum::IN_PROGRESS)->count())
                ->badgeColor('warning'),
            'resolved' => Tab::make('Vyriešené')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InquiryStatusEnum::RESOLVED))
                ->badge(fn () => $this->getModel()::query()->where('status', InquiryStatusEnum::RESOLVED)->count())
                ->badgeColor('success'),
        ];
    }
}
