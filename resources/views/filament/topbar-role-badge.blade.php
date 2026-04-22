@php
    use App\Enums\RoleEnum;

    $globalRoles = auth()->user()?->getRoleNames()
        ?->reject(fn ($r) => $r === 'panel_user')
        ?? collect();

    $teamRoles = collect();
    $tenant = filament()->getTenant();
    if ($tenant && auth()->user()) {
        $teamRoles = auth()->user()->teams()
            ->where('teams.id', $tenant->id)
            ->get()
            ->pluck('pivot.role')
            ->unique();
    }

    $allRoles = $globalRoles->merge($teamRoles)->unique();
@endphp

@if ($allRoles->isNotEmpty() && ! app()->environment('production'))
    <div class="flex items-center gap-2">
        @foreach ($allRoles as $role)
            <x-filament::badge size="sm" color="gray">
                {{ ($role instanceof RoleEnum ? $role : RoleEnum::tryFrom($role))?->getLabel() ?? $role }}
            </x-filament::badge>
        @endforeach
    </div>
@endif
