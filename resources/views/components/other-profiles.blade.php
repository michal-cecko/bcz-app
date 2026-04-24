@props(['user' => null, 'judge' => null, 'role' => 'athlete', 'locale' => 'sk'])

@php
    use App\Enums\RoleEnum;

    if ($role === 'judge') {
        $excludeId = $judge?->id;
        $others = \App\Models\Judge::query()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->inRandomOrder()
            ->limit(3)
            ->get();
    } else {
        $teamIds = $user->teams()->pluck('teams.id');
        $approvalColumn = match($role) {
            'coach' => 'coach_profile_approved_at',
            'athlete' => 'athlete_profile_approved_at',
        };
        $pivotRole = match($role) {
            'coach' => RoleEnum::COACH->value,
            'athlete' => RoleEnum::ATHLETE->value,
        };

        $others = \App\Models\User::where('id', '!=', $user->id)
            ->whereNotNull($approvalColumn)
            ->whereHas('teams', fn ($q) => $q
                ->whereIn('teams.id', $teamIds)
                ->where('team_user.role', $pivotRole)
            )
            ->with(['athleteProfile', 'coachProfile'])
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    $sectionTitle = match($role) {
        'coach' => __('ĎALŠÍ TRÉNERI'),
        'athlete' => __('ĎALŠÍ ATLÉTI'),
        'judge' => __('ĎALŠÍ POROTCOVIA'),
    };

    $sectionDesc = match($role) {
        'coach' => __('Spoznaj ostatných trénerov'),
        'athlete' => __('Spoznaj ostatných členov tímu'),
        'judge' => __('Spoznaj ostatných porotcov'),
    };

    $routeName = match($role) {
        'coach' => 'coach.show',
        'athlete' => 'athlete.show',
        'judge' => 'judge.show',
    };
@endphp

@if($others->isNotEmpty())
    <section class="bg-[#0A0A0A] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('TÍM') }}</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ $sectionTitle }}</h2>
                <p class="text-[#888888] text-base">{{ $sectionDesc }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($others as $other)
                    <a href="{{ route($routeName, $other) }}" class="bg-[#111111] border border-bcz-border rounded-lg overflow-hidden group hover:border-bcz-red/50 transition-colors">
                        {{-- Image --}}
                        @php
                            $profileImage = $role === 'judge'
                                ? $other->getFirstMediaUrl('profile_image')
                                : $other->getProfileImageUrl();
                            $initials = $role === 'judge'
                                ? mb_strtoupper(mb_substr($other->name, 0, 2))
                                : $other->getInitials();
                        @endphp
                        <div class="h-[220px] bg-[#1A1A1A] overflow-hidden">
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $other->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-bcz-red font-display font-bold text-5xl">{{ $initials }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-6 flex flex-col gap-2">
                            <h3 class="text-white font-semibold text-xl group-hover:text-bcz-red transition-colors">{{ $other->name }}</h3>
                            @if($role !== 'judge')
                                <span class="text-[#666666] text-[11px] font-semibold tracking-[1px]">{{ $other->country_code ?? 'SK' }}</span>
                            @endif

                            <div class="flex items-center gap-2 text-bcz-red text-[11px] font-bold tracking-wider mt-2">
                                {{ __('ZOBRAZIT PROFIL') }}
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
