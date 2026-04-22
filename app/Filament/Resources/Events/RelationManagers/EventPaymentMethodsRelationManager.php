<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Filament\RelationManagers\PaymentMethodsRelationManager;
use Illuminate\Database\Eloquent\Model;

class EventPaymentMethodsRelationManager extends PaymentMethodsRelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type !== EventTypeEnum::Report;
    }
}
