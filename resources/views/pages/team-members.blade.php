@extends('layouts.public')

@section('title', $team->getTranslation('name', app()->getLocale()) . ' — Členovia | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('team.show', $team) }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">ČLENOVIA</span>
            </div>

            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">
                {{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }} — ČLENOVIA
            </h1>

            <p class="text-[#888888] text-[18px] text-center max-w-[600px]">
                {{ $team->members->count() }} {{ trans_choice('člen|členovia|členov', $team->members->count()) }} tímu {{ $team->getTranslation('name', app()->getLocale()) }}
            </p>
        </div>
    </section>

    {{-- Members Grid --}}
    <section class="bg-[#111111] py-16">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            @if($team->members->isEmpty())
                <div class="text-center py-20">
                    <p class="text-[#666666] text-lg">Tím zatiaľ nemá žiadnych členov.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($team->members as $member)
                        <div class="rounded-2xl bg-bcz-dark overflow-hidden group">
                            <div class="h-[280px] bg-[#1A1A1A] overflow-hidden">
                                @if($member->avatar_url)
                                    <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-[#1A1A1A]">
                                        <span class="font-display text-4xl font-bold text-bcz-faint">{{ mb_substr($member->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="px-5 py-4 flex flex-col gap-2">
                                <h3 class="text-white text-[17px] font-bold">{{ $member->name }}</h3>
                                <span class="text-bcz-red text-[11px] font-semibold tracking-wide uppercase">
                                    {{ $member->pivot->is_active ? 'Aktívny člen' : 'Člen' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
