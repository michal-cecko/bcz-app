<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Chyba servera | BCZ Club</title>
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
        <div class="absolute left-1/2 -translate-x-1/2 top-[80px] w-[600px] h-[600px] rounded-full opacity-50 pointer-events-none"
             style="background: radial-gradient(circle, rgba(255,45,45,0.031) 0%, rgba(10,10,10,0) 100%);">
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center min-h-[760px] gap-10 px-5">
            {{-- Warning icon circle --}}
            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-[#FF2D2D15] border border-[#FF2D2D33]">
                <svg class="w-9 h-9 text-[#FF2D2D]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>

            {{-- Big 500 --}}
            <span class="font-display text-[80px] sm:text-[100px] md:text-[120px] font-bold tracking-[6px] leading-none select-none"
                  style="background: linear-gradient(180deg, #FFFFFF 0%, rgba(255,255,255,0.133) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                500
            </span>

            {{-- Text group --}}
            <div class="flex flex-col items-center gap-4 -mt-4">
                <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold tracking-[1px]">Niečo sa pokazilo</h1>
                <p class="text-[#888888] text-base leading-relaxed text-center max-w-[480px]">
                    Server narazil na neočakávanú chybu. Náš tím na tom pracuje. Skús to prosím o chvíľu znova.
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-wrap items-center justify-center gap-4">
                <button onclick="location.reload()" class="inline-flex items-center gap-2.5 bg-[#FF2D2D] text-white text-sm font-bold px-8 py-4 rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                    Skúsiť znova
                </button>
                <a href="/" class="inline-flex items-center gap-2.5 text-[#AAAAAA] text-sm font-medium px-8 py-4 rounded-lg border border-[#333333] hover:border-[#555555] hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                    Hlavná stránka
                </a>
            </div>

            {{-- Status bar --}}
            <div class="flex flex-wrap items-center justify-center gap-3 bg-[#111111] border border-[#1A1A1A] rounded-lg px-6 py-4">
                <span class="w-2 h-2 rounded-full bg-[#FF2D2D] animate-pulse"></span>
                <span class="text-[#666666] text-[13px] font-medium">Stav systému: Údržba prebieha</span>
                <span class="text-[#333333] text-[13px]">·</span>
                <span class="text-[#555555] text-[13px]">Odhadovaný čas: ~5 minút</span>
            </div>
        </div>
    </main>

</body>
</html>
