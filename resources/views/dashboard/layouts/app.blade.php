<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? 'Dashboard').' | '.config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="dashboard-body">
        <div class="dashboard-shell" data-dashboard-shell>
            @include('dashboard.partials.sidebar')

            <div class="dashboard-main">
                @include('dashboard.partials.topbar')

                <main class="dashboard-content">
                    @include('dashboard.partials.breadcrumbs')
                    @yield('content')
                </main>

                @include('dashboard.partials.footer')
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
