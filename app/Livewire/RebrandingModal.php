<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Components\View;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View as ViewContract;
use Livewire\Attributes\On;
use Livewire\Component;

class RebrandingModal extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public bool $shouldShow = false;

    public function rebrandingAction(): Action
    {
        return Action::make('rebranding')
            ->modalHeading(' ')
            ->modalWidth('lg')
            ->modalCloseButton(true)
            ->closeModalByClickingAway(false)
            ->schema([
                View::make('livewire.rebranding-modal-content'),
            ])
            ->modalSubmitActionLabel('Rozumiem')
            ->modalCancelAction(false)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->color('danger')
            ->action(fn () => null);
    }

    public function mount(): void
    {
        $this->shouldShow = app()->isLocal() || ! session()->has('rebranding_dismissed');
    }

    #[On('show-rebranding-modal')]
    public function showModal(): void
    {
        if ($this->shouldShow) {
            session()->put('rebranding_dismissed', true);
            $this->mountAction('rebranding');
        }
    }

    public function render(): ViewContract
    {
        return view('livewire.rebranding-modal');
    }
}
