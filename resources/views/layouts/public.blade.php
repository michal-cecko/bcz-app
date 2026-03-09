<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'BCZ Club'))</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}">
</head>
<body class="bg-bcz-dark text-white font-sans antialiased">
@php
    $rebrandingShowUntil = \App\Models\Setting::get('rebranding_modal_show_until');
@endphp
@if($rebrandingShowUntil && now()->lte(\Carbon\Carbon::parse($rebrandingShowUntil)))
    @livewire('rebranding-modal')
@endif

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @livewireScripts
    @livewireScriptConfig
    @filamentScripts
</body>
</html>
