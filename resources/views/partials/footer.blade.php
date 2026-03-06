<footer id="footer" class="bg-bcz-darker pt-12 lg:pt-20 pb-10">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
    {{-- Partners --}}
    <div class="flex flex-wrap justify-center gap-6 lg:gap-12 pb-10">
        @for ($i = 0; $i < 4; $i++)
            <div class="w-24 h-10 bg-bcz-dark border border-[#1A1A1A] flex items-center justify-center">
                <span class="text-bcz-faint text-[10px] font-semibold tracking-widest">PARTNER</span>
            </div>
        @endfor
    </div>

    {{-- Footer Top --}}
    <div class="flex flex-col lg:flex-row justify-between gap-10 lg:gap-0 mt-10 lg:mt-16">
        <div class="w-full lg:w-[350px] flex flex-col gap-5">
            <div class="flex items-center">
                <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-10">
            </div>
            <p class="text-bcz-dim text-sm leading-relaxed">
                Beyond Comfort Zone. Profesionálna asociácia parkouru a kalisteniky venovaná súťažiam, tréningom a spektakulárnym vystúpeniam.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 lg:gap-20">
            <div class="flex flex-col gap-5">
                <span class="text-bcz-red text-[11px] font-bold tracking-widest">OBJAVTE</span>
                <div class="flex flex-col gap-3">
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">O nás</a>
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">Náš tím</a>
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">Kontakt</a>
                </div>
            </div>
            <div class="flex flex-col gap-5">
                <span class="text-bcz-red text-[11px] font-bold tracking-widest">PROGRAMY</span>
                <div class="flex flex-col gap-3">
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">Súťaže</a>
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">Tréningy</a>
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">Vystúpenia</a>
                    <a href="#" class="text-bcz-muted text-sm hover:text-white transition-colors">Školské workshopy</a>
                </div>
            </div>
            <div class="flex flex-col gap-5 col-span-2 md:col-span-1">
                <span class="text-bcz-red text-[11px] font-bold tracking-widest">KONTAKT</span>
                <div class="flex flex-col gap-3">
                    <span class="text-bcz-muted text-sm flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-bcz-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Dominik Klimek
                    </span>
                    <span class="text-bcz-muted text-sm flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-bcz-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Michal Cecko
                    </span>
                    <span class="text-bcz-muted text-sm flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-bcz-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        info@bczclub.com
                    </span>
                    <span class="text-bcz-muted text-sm flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-bcz-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        +421 XXX XXX XXX
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Divider --}}
    <div class="w-full h-px bg-bcz-border mt-10 lg:mt-16"></div>

    {{-- Footer Bottom --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 lg:mt-10">
        <span class="text-[#555555] text-[13px]">&copy; {{ date('Y') }} BCZ Club. Všetky práva vyhradené.</span>
        <div class="flex items-center gap-5">
            <span class="text-bcz-subtle text-[11px] font-medium">BCZ = Beyond Comfort Zone</span>
            <span class="w-1 h-1 rounded-full bg-bcz-faint"></span>
            <span class="text-bcz-subtle text-[11px] font-medium">BCZ we can</span>
        </div>
    </div>
    </div>
</footer>
