<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BCZ Club')</title>
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-bcz-dark text-white font-sans antialiased flex flex-col min-h-screen">
    <header class="w-full bg-bcz-dark sticky top-0 z-50 border-b border-bcz-border/30">
        <div class="max-w-[1440px] mx-auto h-16 lg:h-20 flex items-center justify-between px-5 md:px-10 lg:px-20">
            <a href="/" class="flex items-center">
                <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-8 lg:h-11">
            </a>
            <a href="/" class="text-bcz-muted text-sm font-medium hover:text-white transition-colors">
                Hlavna stranka
            </a>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer')
</body>
</html>
