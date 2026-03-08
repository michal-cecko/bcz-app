@php
    use App\Enums\RoleEnum;

    $roles = auth()->user()?->getRoleNames() ?? collect();
@endphp

@if ($roles->isNotEmpty())
    <div class="flex items-center gap-2">
        @foreach ($roles->reject(fn ($r) => $r === 'panel_user') as $role)
            <x-filament::badge size="sm" color="gray">
                {{ RoleEnum::tryFrom($role)?->getLabel() ?? $role }}
            </x-filament::badge>
        @endforeach
    </div>
@endif
