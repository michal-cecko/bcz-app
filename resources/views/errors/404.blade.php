@extends('layouts.public')

@section('title', '404 — Stránka nenájdená | BCZ Club')

@section('content')
<div class="flex-1 relative overflow-hidden">
    <div class="absolute left-1/2 -translate-x-1/2 -top-[100px] w-[800px] h-[800px] rounded-full opacity-60 pointer-events-none"
         style="background: radial-gradient(circle, rgba(255,45,45,0.063) 0%, rgba(10,10,10,0) 100%);">
    </div>

    <div class="relative z-10 flex flex-col items-center justify-center min-h-[600px] gap-10 px-5 py-20">
        <span class="font-display font-bold leading-none select-none"
              style="font-size: clamp(140px, 15vw, 220px); letter-spacing: 8px; background: linear-gradient(180deg, #FF2D2D 0%, rgba(255,45,45,0.2) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            404
        </span>

        <div class="flex flex-col items-center gap-4 -mt-4">
            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold tracking-[1px]">Stratil si sa?</h1>
            <p class="text-bcz-muted text-base leading-relaxed text-center max-w-[520px]">
                Táto stránka neexistuje alebo bola presunutá. Ale neboj sa, aj street workout je o tom nájsť správnu cestu.
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="/" class="inline-flex items-center gap-2.5 bg-bcz-red text-white text-sm font-bold px-8 py-4 rounded-lg hover:bg-red-700 transition-colors">
                <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Späť na hlavnú
            </a>
            <button onclick="history.back()" class="inline-flex items-center gap-2.5 text-bcz-muted text-sm font-medium px-8 py-4 rounded-lg border border-bcz-border hover:border-bcz-muted hover:text-white transition-colors bg-transparent">
                <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Vrátiť sa späť
            </button>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-8">
            <span class="text-bcz-dim text-[13px] font-medium">Obľúbené stránky:</span>
            <a href="/treningy" class="text-bcz-muted text-[13px] font-medium hover:text-white transition-colors">Tréningy</a>
            <a href="/sutaze" class="text-bcz-muted text-[13px] font-medium hover:text-white transition-colors">Súťaže</a>
            <a href="/o-nas" class="text-bcz-muted text-[13px] font-medium hover:text-white transition-colors">O nás</a>
            <a href="/kontakt" class="text-bcz-muted text-[13px] font-medium hover:text-white transition-colors">Kontakt</a>
        </div>
    </div>
</div>
@endsection
