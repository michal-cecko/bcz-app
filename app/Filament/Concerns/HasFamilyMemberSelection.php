<?php

namespace App\Filament\Concerns;

use App\Models\Member;
use App\Services\MemberProfileService;
use Livewire\Attributes\Url;

trait HasFamilyMemberSelection
{
    #[Url(as: 'member')]
    public ?string $memberId = null;

    public function mountHasFamilyMemberSelection(): void
    {
        if (config('family.enabled') && auth()->check() && ! $this->memberId) {
            $this->memberId = app(MemberProfileService::class)->resolveMember(auth()->user())?->id;
        }
        $this->familyMember();
    }

    public function familyMember(): ?Member
    {
        return config('family.enabled') && $this->memberId
            ? auth()->user()->members()->whereKey($this->memberId)->firstOrFail() : null;
    }

    public function showFamilyMemberSelector(): bool
    {
        return config('family.enabled') && (auth()->user()?->is_parent ?? false)
            && auth()->user()->members()->limit(2)->get(['id'])->count() > 1;
    }

    public function familyMemberOptions(): array
    {
        return auth()->user()->members()->orderBy('name')->pluck('name', 'id')->all();
    }

    public function updatedMemberId(): void
    {
        $this->mountHasFamilyMemberSelection();
        unset($this->currentMembership, $this->membership, $this->pendingPayment, $this->qrCode,
            $this->continuousMembershipEnabled, $this->continuousMembershipLocked, $this->pendingMembershipsForDisable);
        if (method_exists($this, 'resetTable')) {
            $this->resetTable();
        }
    }
}
