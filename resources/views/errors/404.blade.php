<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Stránka nenájdená | BCZ Club</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#0A0A0A] text-white font-sans antialiased min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="w-full bg-[#0A0A0A] border-b border-[#1A1A1A]/30">
        <div class="max-w-[1440px] mx-auto h-20 flex items-center justify-between px-5 md:px-10 lg:px-20">
            <a href="/" class="flex items-center gap-3">
                <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-8 lg:h-11">
            </a>

            <nav class="hidden xl:flex items-center gap-10">
                <a href="/o-nas" class="text-[#888888] text-xs font-medium tracking-[1px] hover:text-white transition-colors">O NÁS</a>
                <a href="/sutaze" class="text-[#888888] text-xs font-medium tracking-[1px] hover:text-white transition-colors">SÚŤAŽE</a>
                <a href="/treningy" class="text-[#888888] text-xs font-medium tracking-[1px] hover:text-white transition-colors">TRÉNINGY</a>
                <a href="/vystupenia" class="text-[#888888] text-xs font-medium tracking-[1px] hover:text-white transition-colors">VYSTÚPENIA</a>
                <a href="/kontakt" class="text-[#888888] text-xs font-medium tracking-[1px] hover:text-white transition-colors">KONTAKT</a>
            </nav>

            <a href="/admin" class="hidden md:block bg-[#FF2D2D] text-white text-xs font-bold tracking-[2px] px-7 py-3.5 hover:bg-red-700 transition-colors">
                PRIDAJ SA
            </a>
        </div>
    </header>

    {{-- Body --}}
    <main class="flex-1 relative overflow-hidden">
        {{-- Radial glow --}}
        <div class="absolute left-1/2 -translate-x-1/2 -top-[100px] w-[800px] h-[800px] rounded-full opacity-60 pointer-events-none"
             style="background: radial-gradient(circle, rgba(255,45,45,0.063) 0%, rgba(10,10,10,0) 100%);">
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center min-h-[760px] gap-10 px-5">
            {{-- Big 404 --}}
            <span class="font-display font-bold leading-none select-none"
                  style="font-size: clamp(140px, 15vw, 220px); letter-spacing: 8px; background: linear-gradient(180deg, #FF2D2D 0%, rgba(255,45,45,0.2) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                404
            </span>

            {{-- Text group --}}
            <div class="flex flex-col items-center gap-4 -mt-4">
                <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold tracking-[1px]">Stratil si sa?</h1>
                <p class="text-[#888888] text-base leading-relaxed text-center max-w-[520px]">
                    Táto stránka neexistuje alebo bola presunutá. Ale neboj sa, aj street workout je o tom nájsť správnu cestu.
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="/" class="inline-flex items-center gap-2.5 bg-[#FF2D2D] text-white text-sm font-bold px-8 py-4 rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                    Späť na hlavnú
                </a>
                <button onclick="history.back()" class="inline-flex items-center gap-2.5 text-[#AAAAAA] text-sm font-medium px-8 py-4 rounded-lg border border-[#333333] hover:border-[#555555] hover:text-white transition-colors bg-transparent">
                    <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Vrátiť sa späť
                </button>
            </div>

            {{-- Quick links --}}
            <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-8">
                <span class="text-[#555555] text-[13px] font-medium">Obľúbené stránky:</span>
                <a href="/treningy" class="text-[#666666] text-[13px] font-medium hover:text-white transition-colors">Tréningy</a>
                <a href="/sutaze" class="text-[#666666] text-[13px] font-medium hover:text-white transition-colors">Súťaže</a>
                <a href="/o-nas" class="text-[#666666] text-[13px] font-medium hover:text-white transition-colors">O nás</a>
                <a href="/kontakt" class="text-[#666666] text-[13px] font-medium hover:text-white transition-colors">Kontakt</a>
            </div>
        </div>
    </main>

</body>
</html>
