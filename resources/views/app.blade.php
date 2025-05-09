<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    {{--
    <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.22.0.js"></script> --}}
    {{--
    <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.23.1.js"></script> --}}
    @inertiaHead

    @livewireStyles
</head>
{{--

<body class="font-sans antialiased"> --}}

<body class="font-sans antialiased bg-gray-100 mt-24 sm:mt-0 dark:bg-slate-800 lg:overflow-hidden lg:h-full">
    @inertia

    @livewireScriptConfig
</body>

</html>
