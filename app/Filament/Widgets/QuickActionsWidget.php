<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;
}
