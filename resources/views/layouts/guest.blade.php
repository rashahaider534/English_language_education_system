<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fluent') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 bg-gradient-to-br from-fluent-dark via-fluent-primary to-fluent-sky">
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Fluent" class="w-20 h-20 rounded-full object-cover shadow-lg">
                    <span class="text-white font-bold text-2xl tracking-wide">Fluent</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white shadow-xl overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
