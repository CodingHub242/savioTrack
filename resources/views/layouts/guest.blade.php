<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --gradient-start: rgba(15, 23, 42, 0.85);
            --gradient-end: rgba(5, 6, 9, 0.83);
            --gradient-angle: 135deg;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            filter: brightness(0.6);
        }

        .gradient-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(var(--gradient-angle), var(--gradient-start), var(--gradient-end));
            z-index: -1;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.93);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body class="bg-gray-900 font-sans antialiased">
    <video autoplay muted loop playsinline class="video-background">
        <source src="{{ asset('savio.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="gradient-overlay"></div>

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full sm:max-w-md">
            <div class="mb-6 flex justify-center">
                <a href="/" style="font-size:35px;font-weight:800;font-family:emoji;">
                    savioTrack
                </a>
            </div>

            <div class="mb-6 flex justify-center">
                <p>
                    Login to manage your savio savings
                </p>
            </div>

            <div class="auth-card shadow-md overflow-hidden sm:rounded-lg animate-fade-in-up">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
