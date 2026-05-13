<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Your Shipment - Eagle Cargo Freights</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body { font-family: 'Outfit', sans-serif; }

        .hero-bg {
            background-image: url('{{ asset('images/bg.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
        }

        /* Reduced overlay color */
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(6, 20, 50, 0.55);
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            border-radius: 1.25rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
        }

        .track-btn {
            background: #4b0a82;
            transition: background 0.2s;
        }
        .track-btn:hover {
            background: #3d0769;
        }

        .track-input:focus {
            outline: none;
            border-color: #4b0a82;
            box-shadow: 0 0 0 3px rgba(75, 10, 130, 0.15);
        }

        /* Mobile responsive adjustments */
        @media (max-width: 640px) {
            .hero-bg {
                min-height: 100vh;
                overflow-y: auto;
            }
            .glass-card {
                border-radius: 1rem;
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.75rem;
            }
            .feature-badges {
                flex-direction: column;
                align-items: center;
            }
            .feature-badges > div {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="hero-bg flex flex-col">
    <div class="hero-content flex flex-col min-h-screen">

        {{-- Top Nav Bar --}}
        <nav class="py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-white text-lg sm:text-xl font-bold tracking-wide drop-shadow">Eagle Cargo Freights</span>
            </div>
        </nav>

        {{-- Hero Tracking Section --}}
        <main class="flex-grow flex items-center justify-center px-3 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-2xl">

                {{-- Tagline --}}
                <div class="text-center mb-6 sm:mb-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 sm:w-20 sm:h-20 rounded-full bg-white/10 border border-white/20 mb-4 sm:mb-6">
                        <svg class="h-8 w-8 sm:h-10 sm:w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight drop-shadow-lg">
                        Track Your Shipment
                    </h1>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-base text-white/80 font-light max-w-xs sm:max-w-md mx-auto">
                        Enter your tracking number below to get real-time updates on your package.
                    </p>
                </div>

                {{-- Glass Card Search Box --}}
                <div class="glass-card p-5 sm:p-8 lg:p-10">
                    <form action="{{ route('tracking.result') }}" method="GET">
                        <label for="tracking_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tracking Number
                        </label>
                        <div class="flex rounded-xl overflow-hidden shadow-md border-2 border-gray-200 focus-within:border-gray-800 transition-all duration-200">
                            <div class="flex-grow">
                                <input
                                    id="tracking_number"
                                    name="tracking_number"
                                    type="text"
                                    required
                                    class="track-input block w-full px-4 py-3 sm:py-4 text-base sm:text-lg text-gray-900 bg-gray-50 border-0 placeholder-gray-400 focus:bg-white transition-all duration-200"
                                    placeholder="e.g. BRY-20250315-000001"
                                    value="{{ old('tracking_number') }}"
                                >
                            </div>
                            <button type="submit" class="track-btn inline-flex items-center gap-2 px-5 sm:px-7 py-3 sm:py-4 text-white font-semibold text-sm whitespace-nowrap">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Track
                            </button>
                        </div>

                        @if($errors->any())
                            <div class="mt-4 rounded-lg bg-red-50 p-3 sm:p-4 border border-red-200 flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
                            </div>
                        @endif
                    </form>

                    {{-- Help Text --}}
                    <p class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-gray-500">
                        Your tracking number can be found on your shipment receipt or confirmation email.
                    </p>
                </div>

                {{-- Feature Badges --}}
                <div class="mt-6 sm:mt-8 flex flex-wrap justify-center gap-3 sm:feature-badges">
                    <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-3 sm:px-4 py-2">
                        <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        <span class="text-white/90 text-xs sm:text-sm font-medium">Air Cargo</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-3 sm:px-4 py-2">
                        <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        <span class="text-white/90 text-xs sm:text-sm font-medium">Sea Cargo</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-3 sm:px-4 py-2">
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-white/90 text-xs sm:text-sm font-medium">Real-time Updates</span>
                    </div>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="py-3 sm:py-4 text-center px-4">
            <p class="text-white/50 text-xs sm:text-sm">&copy; {{ date('Y') }} Eagle Cargo Freights. All rights reserved.</p>
        </footer>

    </div>
</div>

</body>
</html>
