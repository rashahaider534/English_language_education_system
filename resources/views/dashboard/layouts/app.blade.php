<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? 'Dashboard').' | '.config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700|tajawal:400,500,600,700,800|poppins:500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="dashboard-body">
        <div class="dashboard-shell" data-dashboard-shell>
            @include('dashboard.partials.sidebar')
            <button type="button" class="dashboard-mobile-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>

            <div class="dashboard-main">
                <main class="dashboard-content">
                    @yield('content')
                </main>
            </div>
        </div>
          @hasanyrole(['admin', 'super-admin'])
            @vite('resources/js/firebase-notifications.js')
        @endhasanyrole
        @stack('scripts')
    </body>
</html>
