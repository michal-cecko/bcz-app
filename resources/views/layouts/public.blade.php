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
    <link rel="manifest" href="/favicon/site.webmanifest">
    <meta name="robots" content="noindex, nofollow">

    <link rel="alternate" hreflang="sk" href="{{ url(locale_switch_url('sk')) }}">
    <link rel="alternate" hreflang="cs" href="{{ url(locale_switch_url('cs')) }}">
    <link rel="alternate" hreflang="en" href="{{ url(locale_switch_url('en')) }}">
    <link rel="alternate" hreflang="x-default" href="{{ url(locale_switch_url('sk')) }}">
    <link rel="canonical" href="{{ url(request()->getPathInfo()) }}">

    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="67151dcd-4381-4ae0-8093-31caf51a32b1" data-blockingmode="auto" type="text/javascript"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-bcz-dark text-white font-sans antialiased">
    @livewire('banner-display', ['pageId' => $page->id ?? null])

    @include('components.header')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

    @livewireScripts
    @livewireScriptConfig
    @filamentScripts
</body>
</html>
