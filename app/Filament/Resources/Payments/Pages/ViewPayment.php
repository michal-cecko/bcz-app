<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
