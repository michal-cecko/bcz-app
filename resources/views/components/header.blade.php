@php
    // Resolve the logged-in user's panel + profile-edit URL once. The admin
    // panel is tenant-scoped, so its UserResource edit route needs a {tenant};
    // teamless customers live on the tenant-free customer panel. Picking the
    // wrong panel (or passing a null tenant) throws UrlGenerationException and
    // 500s the whole frontend, so only use admin when a tenant actually exists.
    $homePanelId = auth()->check() ? auth()->user()->homePanelId() : 'admin';
    $profileUrl = null;

    if (auth()->check()) {
        $profileTenant = auth()->user()->teams->first();
        $useAdminPanel = $homePanelId === 'admin' && $profileTenant !== null;

        $profileUrl = \App\Filament\Resources\Users\UserResource::getUrl(
            'edit',
            ['record' => auth()->user()],
            panel: $useAdminPanel ? 'admin' : 'customer',
            tenant: $useAdminPanel ? $profileTenant : null,
        );
    }
@endphp

{{-- Header --}}
<header x-data="{ mobileOpen: false }" class="w-full bg-bcz-dark sticky top-0 z-50 border-b border-bcz-border/30">
    <div class="max-w-[1440px] mx-auto h-16 lg:h-20 flex items-center justify-between px-5 md:px-10 lg:px-20">
        <a href="{{ locale_url('/') }}" class="flex items-center">
            <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-8 lg:h-11">
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden xl:flex items-center gap-6">
            @foreach(collect($headerMenu->items ?? [])->sortBy('sort_order') as $item)
                <a href="{{ \App\Services\LinkResolver::resolve($item) ?? ($item['url'] ?? '#') }}" target="{{ $item['target'] ?? '_self' }}" class="text-bcz-muted text-xs font-medium tracking-widest uppercase hover:text-white transition-colors">
                    {{ $item['label_' . app()->getLocale()] ?? $item['label_sk'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <div class="hidden xl:block">
                <x-locale-switcher />
            </div>

            @auth
                @php
                    $avatarColors = ['#FF2D2D', '#2EC4B6', '#9B5DE5', '#FF6B35', '#00BBF9'];
                    $colorIndex = crc32(auth()->user()->email ?? '') % count($avatarColors);
                    $avatarBg = $avatarColors[$colorIndex];
                    $profileImage = auth()->user()->getProfileImageUrl();
                    $initials = auth()->user()->getInitials();
                @endphp

                {{-- User Menu (Desktop) --}}
                <div x-data="{ userMenuOpen: false }" class="relative hidden md:flex items-center gap-4">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 group">
                        @if($profileImage)
                            <img src="{{ $profileImage }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover">
                        @else
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[13px] font-bold" style="background-color: {{ $avatarBg }}">
                                {{ $initials }}
                            </div>
                        @endif
                        <svg class="w-3.5 h-3.5 text-bcz-muted transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <a href="{{ '/'.$homePanelId }}" class="bg-bcz-red text-white text-[11px] font-bold tracking-widest px-5 py-2.5 hover:bg-red-700 transition-colors">
                        {{ __('layout.user_zone') }}
                    </a>

                    {{-- Dropdown --}}
                    <div
                        x-show="userMenuOpen"
                        @click.outside="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        x-cloak
                        class="absolute left-0 top-full mt-4 w-max max-w-[220px] bg-[#111111] border border-bcz-border rounded-xl shadow-2xl overflow-hidden z-50"
                    >
                        <div class="px-5 py-3 border-b border-bcz-border">
                            <p class="text-white text-[13px] font-medium leading-tight truncate">{{ auth()->user()->name }}</p>
                            <p class="text-bcz-dim text-[11px] leading-tight truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ $profileUrl }}" class="flex items-center gap-2 px-5 py-3 text-bcz-muted text-[13px] font-medium hover:bg-white/10 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('layout.profile') }}
                        </a>
                        <div class="h-px bg-bcz-border"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <input type="hidden" name="redirect" value="{{ url()->current() }}">
                            <button type="submit" class="flex items-center gap-2 px-5 py-3 w-full text-bcz-muted text-[13px] font-medium hover:bg-white/10 hover:text-bcz-red transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('layout.sign_out') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/admin/login" class="hidden md:block text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">
                    {{ __('layout.sign_in') }}
                </a>
{{--                <a href="{{ locale_url('/pridaj-sa') }}" class="hidden md:block bg-bcz-red text-white text-[11px] font-bold tracking-widest px-5 py-2.5 hover:bg-red-700 transition-colors">--}}
{{--                    {{ __('layout.join_us') }}--}}
{{--                </a>--}}
            @endauth

            {{-- Hamburger Button --}}
            <button @click="mobileOpen = !mobileOpen" class="xl:hidden relative w-10 h-10">
                <span :style="mobileOpen ? 'top:19px;transform:rotate(45deg)' : 'top:13px;transform:rotate(0)'" class="absolute left-2 w-6 h-[2px] bg-white transition-all duration-300"></span>
                <span :style="mobileOpen ? 'top:19px;opacity:0' : 'top:19px;opacity:1'" class="absolute left-2 w-6 h-[2px] bg-white transition-all duration-300"></span>
                <span :style="mobileOpen ? 'top:19px;transform:rotate(-45deg)' : 'top:25px;transform:rotate(0)'" class="absolute left-2 w-6 h-[2px] bg-white transition-all duration-300"></span>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="xl:hidden bg-bcz-dark border-t border-bcz-border/30 px-5 pb-6"
    >
        <nav class="flex flex-col gap-4 pt-4">
            @foreach(collect($headerMenu->items ?? [])->sortBy('sort_order') as $item)
                <a href="{{ \App\Services\LinkResolver::resolve($item) ?? ($item['url'] ?? '#') }}" target="{{ $item['target'] ?? '_self' }}" class="text-bcz-muted text-sm font-medium tracking-widest uppercase hover:text-white transition-colors py-1">
                    {{ $item['label_' . app()->getLocale()] ?? $item['label_sk'] }}
                </a>
            @endforeach
            @auth
                <div class="flex items-center gap-3 pt-3 pb-3 border-t border-bcz-border/30 mt-2">
                    @if($profileImage ?? false)
                        <img src="{{ $profileImage }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-[13px] font-bold" style="background-color: {{ $avatarBg ?? '#FF2D2D' }}">
                            {{ $initials ?? '' }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-white text-sm font-medium leading-tight truncate">{{ auth()->user()->name }}</p>
                        <p class="text-bcz-dim text-[12px] leading-tight truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <a href="{{ '/'.$homePanelId }}" class="bg-bcz-red text-white text-sm font-bold tracking-widest px-7 py-3.5 hover:bg-red-700 transition-colors text-center w-full block">
                    {{ __('layout.user_zone') }}
                </a>
                <a href="{{ $profileUrl }}" class="flex items-center gap-2 text-bcz-muted text-sm font-medium tracking-widest uppercase hover:text-white transition-colors py-1 mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('layout.profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ url()->current() }}">
                    <button type="submit" class="flex items-center gap-2 text-bcz-red text-sm font-medium tracking-widest uppercase hover:text-red-400 transition-colors py-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        {{ __('layout.sign_out') }}
                    </button>
                </form>
            @else
                <a href="{{ locale_url('/pridaj-sa') }}" class="md:hidden bg-bcz-red text-white text-sm font-bold tracking-widest px-7 py-3.5 hover:bg-red-700 transition-colors text-center mt-2 w-full block">
                    {{ __('layout.join_us') }}
                </a>
            @endauth
            <div class="xl:hidden pt-2">
                <x-locale-switcher />
            </div>
        </nav>
    </div>
    {{-- CTA Modal hidden for MVP single-team flow — kept for future multi-team use --}}
</header>
