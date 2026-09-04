<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SavioTrack - Savings Tracker CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ============================================
         CUSTOMIZABLE STYLES - Edit these to change
         the look of the welcome page
         ============================================ -->
    <style>
        /* --- Gradient Overlay Settings --- */
        :root {
            --gradient-start: rgba(15, 23, 42, 0.85);
            --gradient-end: rgba(5, 6, 9, 0.83);
            --gradient-angle: 135deg;
        }

        /* --- Video Background --- */
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

        /* --- Gradient Overlay --- */
        .gradient-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                var(--gradient-angle),
                var(--gradient-start),
                var(--gradient-end)
            );
            z-index: -1;
        }

        /* --- Content Card --- */
        .content-card {
            background: rgba(255, 255, 255, 0.29);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* --- Feature Cards --- */
        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* --- Button Styles --- */
        .btn-primary {
            background: linear-gradient(135deg, #e56b46, #08060b);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(229, 107, 70, 0.4);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
        }

        /* --- Text Colors --- */
        .text-gradient {
            background: linear-gradient(135deg, #b6b6b6, #6c1d02);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gray-700{
            background: linear-gradient(135deg, #b6b6b6, #6c1d02) !important;
        }

        .text-indigo-900{
            color:#000 !important;
        }

        .text-purple-900{
            color:#6c1d02 !important;
        }

        /* --- Animations --- */
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

        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }
    </style>
    <!-- ============================================
         END CUSTOMIZABLE STYLES
         ============================================ -->
</head>
<body class="bg-gray-900">
    <!-- Video Background -->
    <!-- Replace the src below with your own video URL -->
    <video autoplay muted loop playsinline class="video-background" poster="{{ asset('savio.mp4') }}">
        <source src="{{asset('savio.mp4')}}" type="video/mp4">
        <!-- Fallback for browsers that don't support video -->
        Your browser does not support the video tag.
    </video>

    <!-- Gradient Overlay -->
    <div class="gradient-overlay"></div>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-6xl w-full mx-auto">
            <div class="content-card rounded-2xl shadow-2xl p-12 text-center animate-fade-in-up">
                <h1 class="text-6xl font-bold text-gradient mb-6 animate-fade-in-up animate-delay-1">
                    SavioTrack
                </h1>
                <p class="text-2xl text-gray-700 mb-12 animate-fade-in-up animate-delay-2">
                    Your Comprehensive Personal Savings Partner
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="feature-card rounded-xl p-8 text-center animate-fade-in-up animate-delay-2">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="text-xl font-semibold text-indigo-900 mb-3">Goals</h3>
                        <p class="text-gray-600">Set and track savings goals with progress visualization</p>
                    </div>
                    <div class="feature-card rounded-xl p-8 text-center animate-fade-in-up animate-delay-3">
                        <div class="text-4xl mb-4">💎</div>
                        <h3 class="text-xl font-semibold text-purple-900 mb-3">Wants & Needs</h3>
                        <p class="text-gray-600">Manage aspirations and essentials linked to each goal</p>
                    </div>
                    <div class="feature-card rounded-xl p-8 text-center animate-fade-in-up animate-delay-4">
                        <div class="text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-semibold text-green-900 mb-3">Smart Tracking</h3>
                        <p class="text-gray-600">Daily, weekly, monthly deposits with AI insights</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up animate-delay-4">
                    @if(Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary text-white px-8 py-4 rounded-xl text-lg font-semibold">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary text-white px-8 py-4 rounded-xl text-lg font-semibold">
                                Log in
                            </a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-secondary text-white px-8 py-4 rounded-xl text-lg font-semibold">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-12 text-white/60 text-sm animate-fade-in-up animate-delay-4">
                <p>SavioTrack - Nhyiraba Coding Hub &copy;2026</p>
            </div>
        </div>
    </div>
</body>
</html>
