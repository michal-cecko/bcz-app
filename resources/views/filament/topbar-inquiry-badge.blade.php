@php
    $user = auth()->user();
    if ($user?->isMemberLevel()) {
        return;
    }

    $count = \App\Models\Inquiry::query()
        ->where('team_id', \Filament\Facades\Filament::getTenant()?->id)
        ->where('status', \App\Enums\InquiryStatusEnum::NEW)
        ->count();

    $url = \App\Filament\Resources\Inquiries\InquiryResource::getUrl();
@endphp

<a
    href="{{ $url }}"
    class="relative flex items-center justify-center outline-none"
    title="Dopyty"
>
    <x-filament::icon
        icon="heroicon-o-envelope"
        class="h-6 w-6"
        style="color: #71717b;"
    />

    @if ($count > 0)
        <div class="absolute -right-2.5 -top-2.5">
            <x-filament::badge color="warning" size="xs" class="dark:!bg-amber-500/80 dark:!text-white">
                {{ $count }}
            </x-filament::badge>
        </div>
    @endif
</a>
