<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Platba | BCZ Club</title>
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#0A0A0A] text-white font-sans antialiased min-h-screen">
    {{-- Top bar --}}
    <header class="w-full px-5 md:px-10 py-4 flex items-center justify-between max-w-[1200px] mx-auto">
        <a href="/" class="flex items-center gap-2">
            <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-8">
        </a>
        <div class="flex items-center gap-2 bg-[#0D2A1A] border border-[#1A4D2E] rounded-full px-4 py-2">
            <svg class="w-3.5 h-3.5 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span class="text-[#22C55E] text-xs font-semibold">Zabezpečená platba</span>
        </div>
    </header>

    {{-- Main content --}}
    <main class="px-5 md:px-10 pb-16 pt-6">
        @livewire('payment-page', ['payment' => $payment])
    </main>

    @livewireScripts
    @livewireScriptConfig
</body>
</html>
