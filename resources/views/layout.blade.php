<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    @section('seo')
        <s:seo:head />
    @show

    <!-- Fonts: Inter (via @fontsource) -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body>
    <s:seo:body />

    <div class="flex min-h-screen flex-col bg-white">
        <x-navbar />

        <main class="flex-1 pt-16">
            @yield('content')
        </main>

        <x-footer />
    </div>
</body>
</html>
