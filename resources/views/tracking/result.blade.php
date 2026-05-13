<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracking: {{ $shipment->tracking_number }} | Eagle Cargo Freights Ltd</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f0f4f8; }

        .hero-nav {
            background-image: url('{{ asset('images/bg.png') }}');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .hero-nav::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(6, 20, 50, 0.82);
            z-index: 0;
        }
        .hero-nav-content { position: relative; z-index: 1; }

        .card { background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px rgba(0,0,0,0.07); border: 1px solid #e8edf3; }

        .timeline-line {
            position: absolute;
            top: 2.5rem;
            left: 1.25rem;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #d1d5db, transparent);
        }

        .info-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: #8fa3b8; margin-bottom: 0.25rem; }
        .info-value { font-size: 1rem; font-weight: 600; color: #1e293b; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Navbar --}}
    <div class="hero-nav sticky top-0 z-50">
        <div class="hero-nav-content">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="{{ route('tracking.index') }}" class="text-white text-lg font-bold tracking-wide">
                        Eagle Cargo Freights Ltd
                    </a>
                    <a href="{{ route('tracking.index') }}" class="flex items-center gap-2 text-white/80 hover:text-white text-sm font-medium transition-colors duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Track Another Package
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-grow max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">

        @php
            $status = $shipment->current_status;
            $statusLower = strtolower($status);
            if (str_contains($statusLower, 'delivered')) {
                $sBg = 'bg-green-500'; $sBadgeBg = 'bg-green-100'; $sBadgeText = 'text-green-800'; $sBadgeBorder = 'border-green-200'; $sDot = 'bg-green-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
            } elseif (str_contains($statusLower, 'out for delivery')) {
                $sBg = 'bg-blue-500'; $sBadgeBg = 'bg-blue-100'; $sBadgeText = 'text-blue-800'; $sBadgeBorder = 'border-blue-200'; $sDot = 'bg-blue-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>';
            } elseif (str_contains($statusLower, 'transit') || str_contains($statusLower, 'shipped')) {
                $sBg = 'bg-indigo-500'; $sBadgeBg = 'bg-indigo-100'; $sBadgeText = 'text-indigo-800'; $sBadgeBorder = 'border-indigo-200'; $sDot = 'bg-indigo-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>';
            } elseif (str_contains($statusLower, 'arrived') || str_contains($statusLower, 'facility')) {
                $sBg = 'bg-purple-500'; $sBadgeBg = 'bg-purple-100'; $sBadgeText = 'text-purple-800'; $sBadgeBorder = 'border-purple-200'; $sDot = 'bg-purple-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>';
            } elseif (str_contains($statusLower, 'picked up')) {
                $sBg = 'bg-cyan-500'; $sBadgeBg = 'bg-cyan-100'; $sBadgeText = 'text-cyan-800'; $sBadgeBorder = 'border-cyan-200'; $sDot = 'bg-cyan-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>';
            } elseif (str_contains($statusLower, 'hold') || str_contains($statusLower, 'delayed')) {
                $sBg = 'bg-amber-500'; $sBadgeBg = 'bg-amber-100'; $sBadgeText = 'text-amber-800'; $sBadgeBorder = 'border-amber-200'; $sDot = 'bg-amber-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>';
            } elseif (str_contains($statusLower, 'cancelled') || str_contains($statusLower, 'failed')) {
                $sBg = 'bg-red-500'; $sBadgeBg = 'bg-red-100'; $sBadgeText = 'text-red-800'; $sBadgeBorder = 'border-red-200'; $sDot = 'bg-red-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            } else {
                $sBg = 'bg-gray-400'; $sBadgeBg = 'bg-gray-100'; $sBadgeText = 'text-gray-700'; $sBadgeBorder = 'border-gray-200'; $sDot = 'bg-gray-400';
                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>';
            }
        @endphp

        {{-- Hero Status Banner --}}
        <div class="card mb-6 overflow-hidden">
            {{-- Coloured top stripe --}}
            <div class="{{ $sBg }} h-2 w-full"></div>
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-full {{ $sBg }} flex items-center justify-center shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>
                    </div>
                    <div>
                        <p class="info-label">Tracking Number</p>
                        <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $shipment->tracking_number }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-start sm:items-end gap-2">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold {{ $sBadgeBg }} {{ $sBadgeText }} border {{ $sBadgeBorder }}">
                        <span class="w-2 h-2 rounded-full {{ $sDot }} animate-pulse"></span>
                        {{ $status }}
                    </span>
                    <span class="text-xs text-gray-400">
                        <span class="font-medium">{{ $shipment->shipment_type === 'air' ? '✈ Air Cargo' : '🚢 Sea Cargo' }}</span>
                        &nbsp;·&nbsp; Updated {{ $shipment->updated_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Route Card --}}
            <div class="card p-6 lg:col-span-2">
                <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">↔</span>
                    Route & Delivery
                </h3>

                {{-- Route Visual --}}
                <div class="flex items-center gap-3 mb-6 bg-gray-50 rounded-xl p-4">
                    <div class="text-center flex-1">
                        <p class="info-label">Origin</p>
                        <p class="info-value text-lg">{{ $shipment->origin }}</p>
                    </div>
                    <div class="flex-shrink-0 flex flex-col items-center">
                        <div class="flex items-center gap-1">
                            <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                            <div class="w-16 h-0.5 bg-gradient-to-r from-indigo-400 to-indigo-600 relative">
                                <div class="absolute -top-2 left-1/2 -translate-x-1/2">
                                    @if($shipment->shipment_type === 'air')
                                        <span class="text-lg">✈</span>
                                    @else
                                        <span class="text-lg">🚢</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                        </div>
                    </div>
                    <div class="text-center flex-1">
                        <p class="info-label">Destination</p>
                        <p class="info-value text-lg">{{ $shipment->destination }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="info-label">Est. Delivery</p>
                        <p class="info-value">
                            @if($shipment->delivery_time_min && $shipment->delivery_time_max)
                                {{ $shipment->delivery_time_min }}–{{ $shipment->delivery_time_max }}
                                {{ $shipment->delivery_time_unit === 'months' ? 'months' : 'days' }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="info-label">Weight</p>
                        <p class="info-value">{{ $shipment->weight ? $shipment->weight . ' kg' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="info-label">Packages</p>
                        <p class="info-value">{{ $shipment->num_packages ?? 1 }} pkg</p>
                    </div>
                    @if($shipment->cbm)
                    <div>
                        <p class="info-label">CBM</p>
                        <p class="info-value">{{ $shipment->cbm }} m³</p>
                    </div>
                    @endif
                    @if($shipment->package_type)
                    <div>
                        <p class="info-label">Package Type</p>
                        <p class="info-value capitalize">{{ $shipment->package_type }}</p>
                    </div>
                    @endif
                    @if($shipment->fragile)
                    <div>
                        <p class="info-label">Handling</p>
                        <p class="info-value text-red-600">⚠ Fragile</p>
                    </div>
                    @endif
                </div>

                @if($shipment->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="info-label">Description</p>
                    <p class="text-gray-700 text-sm mt-1">{{ $shipment->description }}</p>
                </div>
                @endif
            </div>

            {{-- Client / Recipient Info --}}
            <div class="card p-6">
                <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs">👤</span>
                    Client Details
                </h3>

                @if($shipment->client)
                    <div class="flex items-center gap-3 mb-4 p-3 bg-blue-50 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($shipment->client->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $shipment->client->name }}</p>
                            @if($shipment->client->email)
                                <p class="text-xs text-gray-500">{{ $shipment->client->email }}</p>
                            @endif
                        </div>
                    </div>
                    @if($shipment->client->phone)
                    <div class="mb-3">
                        <p class="info-label">Phone</p>
                        <p class="info-value text-sm">{{ $shipment->client->phone }}</p>
                    </div>
                    @endif
                    @if($shipment->client->address)
                    <div class="mb-3">
                        <p class="info-label">Address</p>
                        <p class="info-value text-sm">{{ $shipment->client->address }}</p>
                    </div>
                    @endif
                @else
                    <div class="text-center py-6 text-gray-400">
                        <div class="text-3xl mb-2">👤</div>
                        <p class="text-sm">No client info available</p>
                    </div>
                @endif

                @if($shipment->receiver_name)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <h4 class="font-semibold text-gray-700 text-sm mb-3 flex items-center gap-1">
                        <span>📦</span> Receiver
                    </h4>
                    <p class="info-label">Name</p>
                    <p class="info-value text-sm mb-2">{{ $shipment->receiver_name }}</p>
                    @if($shipment->receiver_phone)
                    <p class="info-label">Phone</p>
                    <p class="info-value text-sm mb-2">{{ $shipment->receiver_phone }}</p>
                    @endif
                    @if($shipment->receiver_address)
                    <p class="info-label">Address</p>
                    <p class="info-value text-sm">{{ $shipment->receiver_address }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Tracking Timeline --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">🕐</span>
                    Tracking History
                </h3>
                <span class="text-xs text-gray-400 font-medium">{{ $shipment->statusUpdates->count() }} event(s)</span>
            </div>
            <div class="px-6 py-8 sm:px-8">
                @if($shipment->statusUpdates->count() > 0)
                    <ul class="space-y-0">
                        @foreach($shipment->statusUpdates as $update)
                            @php
                                $upLower = strtolower($update->status);
                                if (str_contains($upLower, 'delivered')) { $ic = 'bg-green-500'; $upPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'; }
                                elseif (str_contains($upLower, 'transit') || str_contains($upLower, 'shipped') || str_contains($upLower, 'out for delivery')) { $ic = 'bg-blue-500'; $upPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'; }
                                elseif (str_contains($upLower, 'arrived') || str_contains($upLower, 'facility')) { $ic = 'bg-purple-500'; $upPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>'; }
                                elseif (str_contains($upLower, 'hold') || str_contains($upLower, 'delayed')) { $ic = 'bg-amber-500'; $upPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'; }
                                elseif (str_contains($upLower, 'cancelled') || str_contains($upLower, 'failed')) { $ic = 'bg-red-500'; $upPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'; }
                                else { $ic = 'bg-gray-400'; $upPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'; }
                            @endphp
                            <li class="relative flex gap-5 {{ !$loop->last ? 'pb-8' : '' }}">
                                {{-- Connecting line --}}
                                @if(!$loop->last)
                                    <span class="absolute left-5 top-10 bottom-0 w-0.5 bg-gradient-to-b from-gray-300 to-transparent"></span>
                                @endif
                                {{-- Icon --}}
                                <div class="flex-shrink-0 z-10">
                                    <span class="w-10 h-10 rounded-full {{ $ic }} flex items-center justify-center shadow ring-4 ring-white">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $upPath !!}</svg>
                                    </span>
                                </div>
                                {{-- Content --}}
                                <div class="flex-1 min-w-0 pt-1">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1">
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">{{ $update->status }}</p>
                                            @if($update->location)
                                                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                                    <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    {{ $update->location }}
                                                </p>
                                            @endif
                                            @if($update->remarks)
                                                <p class="mt-2 text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100 inline-block">{{ $update->remarks }}</p>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400 whitespace-nowrap sm:text-right font-medium">
                                            {{ $update->created_at->format('d M Y') }}<br>
                                            <span class="text-gray-300">{{ $update->created_at->format('H:i') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">No tracking events yet</p>
                        <p class="text-gray-400 text-sm mt-1">Your shipment was registered on {{ $shipment->created_at->format('d M Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Track Another CTA --}}
        <div class="mt-6 text-center">
            <a href="{{ route('tracking.index') }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white px-6 py-3 rounded-full text-sm font-semibold transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Track Another Package
            </a>
        </div>

    </main>

    <footer class="py-6 mt-4 border-t border-gray-200 bg-white">
        <p class="text-center text-sm text-gray-400">&copy; {{ date('Y') }} Eagle Cargo Freights Ltd. All rights reserved.</p>
    </footer>

</body>
</html>
