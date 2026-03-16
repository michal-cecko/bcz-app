@if(\App\Services\ImpersonationService::isImpersonating())
    <div class="fi-topbar-impersonation-banner flex items-center justify-center gap-2 bg-warning-500 px-4 py-2 text-sm font-medium text-white">
        <x-heroicon-s-eye class="h-4 w-4" />
        <span>Prihlaseny ako <strong>{{ auth()->user()->name }}</strong></span>
        <form action="{{ route('admin.impersonate.stop') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="ml-2 inline-flex items-center gap-1 rounded-md bg-white/20 px-2 py-0.5 text-xs font-semibold text-white hover:bg-white/30 transition">
                <x-heroicon-s-arrow-uturn-left class="h-3 w-3" />
                Ukoncit
            </button>
        </form>
    </div>
@endif
