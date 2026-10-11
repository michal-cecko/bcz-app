<?php

namespace App\Filament\Concerns;

use App\Models\Member;
use Livewire\Attributes\Url;

trait HasFamilyMemberSelection
{
    #[Url(as: 'member')]
    public ?string $memberId = null;

    public function mountHasFamilyMemberSelection(): void
    {
        if (config('family.enabled') && auth()->check() && ! $this->memberId) {
            $members = auth()->user()->members()->get();
            if ($members->count() === 1) {
                $this->memberId = $members->first()->id;
            }
        }
        $this->familyMember();
    }

    public function familyMember(): ?Member
    {
        return config('family.enabled') && $this->memberId
            ? auth()->user()->members()->whereKey($this->memberId)->firstOrFail() : null;
    }

    public function familyMemberOptions(): array
    {
        return auth()->user()->members()->orderBy('name')->pluck('name', 'id')->all();
    }

    public function updatedMemberId(): void
    {
        $this->familyMember();
        unset($this->currentMembership, $this->membership, $this->pendingPayment, $this->qrCode,
            $this->continuousMembershipEnabled, $this->continuousMembershipLocked, $this->pendingMembershipsForDisable);
        if (method_exists($this, 'resetTable')) {
            $this->resetTable();
        }
    }
}
