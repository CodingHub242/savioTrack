<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-900 font-sans antialiased">
        <!-- Video Background -->
        <!-- Replace the src below with your own video URL -->
        <video autoplay muted loop playsinline class="video-background" poster="{{ asset('images/video-poster.jpg') }}">
            <source src="{{ asset('savio.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Gradient Overlay -->
        <div class="gradient-overlay"></div>

        <!-- Main Content -->
        <div class="min-h-screen flex items-center justify-center p-6">
            <div class="w-full sm:max-w-md">
                <div class="mb-6 flex justify-center">
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-white" />
                    </a>
                </div>

                <div class="auth-card shadow-md overflow-hidden sm:rounded-lg animate-fade-in-up">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
