@props([
    'title' => 'SIGAP-SAR',
    'hideChrome' => false,
    'fullBleed' => false,
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Sistem Informasi Tanggap Darurat & SAR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .leaflet-container { font-family: inherit; background: #e2e8f0; }
        .leaflet-control-zoom { overflow: hidden; border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 8px 24px rgba(15, 23, 42, .12) !important; }
        .leaflet-control-zoom a { color: #334155 !important; border-color: #e2e8f0 !important; }
        .leaflet-control-attribution { color: #64748b; background: rgba(255, 255, 255, .88) !important; }
        .leaflet-popup-content-wrapper { border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 14px 32px rgba(15, 23, 42, .16); }
        .leaflet-popup-content { margin: 10px 14px; color: #334155; line-height: 1.45; }
        .leaflet-popup-tip { box-shadow: none; }

        .timsar-map-marker { position: relative; width: 44px; height: 44px; }
        .timsar-map-marker__halo { position: absolute; inset: 5px; border-radius: 999px; background: var(--marker-color); opacity: .2; animation: timsar-marker-pulse 2s ease-out infinite; }
        .timsar-map-marker__body { position: absolute; left: 8px; top: 8px; display: grid; width: 28px; height: 28px; place-items: center; color: #fff; background: var(--marker-color); border: 3px solid #fff; box-shadow: 0 5px 14px rgba(15, 23, 42, .3); }
        .timsar-map-marker__symbol { display: block; font: 900 14px/1 ui-sans-serif, system-ui, sans-serif; }
        .timsar-map-marker--incident { --marker-color: #dc2626; }
        .timsar-map-marker--incident .timsar-map-marker__body { border-radius: 50% 50% 50% 12%; transform: rotate(-45deg); }
        .timsar-map-marker--incident .timsar-map-marker__symbol { transform: rotate(45deg); }
        .timsar-map-marker--member { --marker-color: #059669; }
        .timsar-map-marker--member .timsar-map-marker__body,
        .timsar-map-marker--user .timsar-map-marker__body { border-radius: 999px; }
        .timsar-map-marker--member .timsar-map-marker__symbol { transform: translateY(-1px); }
        .timsar-map-marker--user { --marker-color: #2563eb; }
        .timsar-map-marker--cell { --marker-color: #d97706; }
        .timsar-map-marker--cell .timsar-map-marker__body { border-radius: 999px; }
        .timsar-map-marker--offline { --marker-color: #64748b; }
        .timsar-map-marker--still .timsar-map-marker__halo { animation: none; opacity: .12; }

        .timsar-route-line { animation: timsar-route-flow 1.4s linear infinite; filter: drop-shadow(0 1px 2px rgba(220, 38, 38, .22)); }
        .timsar-trail-line { filter: drop-shadow(0 1px 2px rgba(37, 99, 235, .2)); }

        @keyframes timsar-marker-pulse {
            0% { transform: scale(.55); opacity: .4; }
            75%, 100% { transform: scale(1.35); opacity: 0; }
        }
        @keyframes timsar-route-flow { to { stroke-dashoffset: -24; } }
        @media (prefers-reduced-motion: reduce) {
            .timsar-map-marker__halo, .timsar-route-line { animation: none !important; }
        }
    </style>
    <script>
        window.TimsarMap = (() => {
            const markerAnimations = new WeakMap();

            function icon(type, options = {}) {
                const symbol = type === 'incident' ? '!' : (type === 'member' ? '&#9650;' : (type === 'cell' ? '&#8644;' : '&#9679;'));
                const stillClass = options.pulse === false ? ' timsar-map-marker--still' : '';
                const offlineClass = options.offline ? ' timsar-map-marker--offline' : '';

                return L.divIcon({
                    className: '',
                    html: `<div class="timsar-map-marker timsar-map-marker--${type}${stillClass}${offlineClass}" aria-hidden="true"><span class="timsar-map-marker__halo"></span><span class="timsar-map-marker__body"><span class="timsar-map-marker__symbol">${symbol}</span></span></div>`,
                    iconSize: [44, 44],
                    iconAnchor: [22, type === 'incident' ? 36 : 22],
                    popupAnchor: [0, type === 'incident' ? -34 : -22],
                });
            }

            function addTiles(map) {
                map.getContainer().classList.add('timsar-map');
                return L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    updateWhenIdle: true,
                    keepBuffer: 3,
                    attribution: '&copy; <a href="https://www.openstreetmap.org">OpenStreetMap</a> &copy; <a href="https://carto.com">CARTO</a>',
                }).addTo(map);
            }

            function routeOptions(overrides = {}) {
                return { color: '#dc2626', weight: 5, opacity: .92, dashArray: '12 8', lineCap: 'round', lineJoin: 'round', className: 'timsar-route-line', ...overrides };
            }

            function trailOptions(overrides = {}) {
                return { color: '#2563eb', weight: 5, opacity: .82, lineCap: 'round', lineJoin: 'round', className: 'timsar-trail-line', ...overrides };
            }

            function moveMarker(marker, point, duration = 700) {
                const target = L.latLng(point);
                const start = marker.getLatLng();
                const previousFrame = markerAnimations.get(marker);
                if (previousFrame) cancelAnimationFrame(previousFrame);

                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || start.distanceTo(target) > 5000) {
                    marker.setLatLng(target);
                    return;
                }

                const startedAt = performance.now();
                const animate = (now) => {
                    const progress = Math.min((now - startedAt) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    marker.setLatLng([
                        start.lat + ((target.lat - start.lat) * eased),
                        start.lng + ((target.lng - start.lng) * eased),
                    ]);
                    if (progress < 1) markerAnimations.set(marker, requestAnimationFrame(animate));
                    else markerAnimations.delete(marker);
                };
                markerAnimations.set(marker, requestAnimationFrame(animate));
            }

            return { icon, addTiles, routeOptions, trailOptions, moveMarker };
        })();

        window.TimsarNativeBridge = (() => {
            let latestCell = null;

            window.addEventListener('timsar:cell-info', (event) => {
                try {
                    latestCell = typeof event.detail === 'string' ? JSON.parse(event.detail) : event.detail;
                } catch (error) {
                    latestCell = null;
                }
            });

            return {
                cell: () => latestCell,
                available: () => latestCell !== null,
            };
        })();
    </script>
    @auth
        @if(auth()->user()->isMember())
            <script>
                window.TimsarNativeBackgroundActive = false;
                window.addEventListener('timsar:background-service', (event) => {
                    window.TimsarNativeBackgroundActive = event.detail?.active === true;
                });

                window.addEventListener('load', () => {
                    window.TimsarNative?.postMessage(JSON.stringify({
                        action: 'syncBackgroundService',
                        origin: window.location.origin,
                        csrf: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        activeUrl: @json(route('member.active-assignment')),
                        locationUrl: @json(route('member.location.update')),
                        heartbeatUrl: @json(route('member.heartbeat')),
                    }));
                });

                document.addEventListener('submit', (event) => {
                    if (!event.target?.action?.endsWith('/logout')) return;
                    window.TimsarNative?.postMessage(JSON.stringify({ action: 'stopBackgroundService' }));
                });
            </script>
        @endif
    @endauth
</head>
<body class="min-h-screen bg-[#f4f7fb] text-slate-800 antialiased flex flex-col font-sans selection:bg-orange-500 selection:text-white">
    @unless($hideChrome)
    @php
        $isMemberShell = auth()->check() && auth()->user()->isMember();
    @endphp

    @if($isMemberShell)
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 shadow-sm backdrop-blur-2xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6">
            <a href="{{ route('member.dashboard') }}" class="flex min-w-0 items-center gap-3 group">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-600 via-amber-600 to-red-600 text-sm font-black text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">SG</span>
                <span class="min-w-0">
                    <span class="flex items-center gap-2">
                        <span class="block truncate text-base font-black tracking-tight text-slate-900">SIGAP-SAR</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> LIVE</span>
                    </span>
                    <span class="block truncate text-xs font-semibold text-slate-500">Tactical Member Operation Unit</span>
                </span>
            </a>
            <nav class="flex shrink-0 items-center gap-2 text-xs font-bold">
                <a class="inline-flex h-10 items-center rounded-xl px-4 transition-all {{ request()->routeIs('member.dashboard') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/20 font-black' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}" href="{{ route('member.dashboard') }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="inline-flex h-10 items-center rounded-xl border border-slate-200 bg-slate-50 px-4 text-slate-700 shadow-sm hover:bg-red-600 hover:border-red-600 hover:text-white transition-all font-bold">Keluar</button>
                </form>
            </nav>
        </div>
    </header>
    @else
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-2xl shadow-sm">
        @php
            $homeRoute = auth()->check()
                ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('member.dashboard'))
                : route('public.report');
        @endphp
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 py-3.5">
            <a href="{{ $homeRoute }}" class="flex items-center gap-3 group">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-orange-600 via-amber-600 to-red-600 font-black text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform text-lg">SG</span>
                <span>
                    <span class="flex items-center gap-2.5">
                        <span class="block text-xl font-black tracking-tight text-slate-900">SIGAP-SAR</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-extrabold text-emerald-700 border border-emerald-200 shadow-sm"><span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> ONLINE</span>
                    </span>
                    <span class="block text-xs font-semibold text-slate-500 tracking-wide">Sistem Informasi Tanggap Darurat & Gerak Cepat SAR</span>
                </span>
            </a>
            <nav class="flex items-center gap-2.5 text-sm font-bold">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="rounded-xl px-4 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all {{ request()->routeIs('admin.*') ? 'bg-orange-50 text-orange-600 border border-orange-200/80 font-extrabold shadow-sm' : '' }}" href="{{ route('admin.dashboard') }}">Posko Komando</a>
                    @else
                        <a class="rounded-xl px-4 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all {{ request()->routeIs('member.*') ? 'bg-orange-50 text-orange-600 border border-orange-200/80 font-extrabold shadow-sm' : '' }}" href="{{ route('member.dashboard') }}">Panel Anggota</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 hover:bg-red-600 hover:border-red-600 hover:text-white transition-all shadow-sm">Keluar</button>
                    </form>
                @else
                    <a class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-600 via-amber-600 to-red-600 px-5 py-2.5 font-extrabold text-white shadow-md shadow-orange-500/20 hover:brightness-110 transition-all active:scale-95" href="{{ route('login') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Login Posko
                    </a>
                @endauth
            </nav>
        </div>
    </header>
    @endif
    @endunless

    <main class="flex-1 {{ $fullBleed ? 'w-full' : 'mx-auto max-w-7xl w-full px-4 sm:px-6 py-8' }}">
        @if(session('status'))
            <div id="appStatusNotice" class="{{ $fullBleed ? 'fixed left-4 right-4 top-4 z-[900] rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-extrabold text-emerald-800 shadow-xl backdrop-blur-xl transition-all duration-300 flex items-center gap-3' : 'mb-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800 shadow-md backdrop-blur-md flex items-center gap-3' }}">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500 text-white font-black">✓</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div id="appErrorNotice" class="{{ $fullBleed ? 'fixed left-4 right-4 top-4 z-[900] rounded-2xl border border-red-300 bg-red-50 px-5 py-4 text-sm font-extrabold text-red-800 shadow-xl backdrop-blur-xl transition-all duration-300 flex items-center gap-3' : 'mb-6 rounded-2xl border border-red-300 bg-red-50 px-5 py-4 text-sm font-bold text-red-800 shadow-md backdrop-blur-md flex items-center gap-3' }}">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-red-500 text-white font-black">!</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{ $slot }}
    </main>

    @unless($hideChrome)
    <footer class="mt-auto border-t border-slate-200/80 bg-white/80 backdrop-blur-lg py-6 text-slate-500 text-xs">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:px-6 sm:flex-row">
            <div class="flex items-center gap-3">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-gradient-to-br from-orange-600 to-red-600 font-black text-white text-[11px] shadow-sm">SG</span>
                <span class="font-extrabold text-slate-800 text-sm tracking-wide">SIGAP-SAR Command Center</span>
                <span class="text-slate-300">&bull;</span>
                <span class="rounded-md bg-orange-50 px-2 py-0.5 font-mono text-[11px] text-orange-600 border border-orange-200/80 font-bold">v3.0.0-Cloud</span>
            </div>
            <div class="flex items-center gap-4 text-slate-500 font-semibold">
                <span>&copy; {{ date('Y') }} SIGAP-SAR Operation Network. All rights reserved.</span>
            </div>
        </div>
    </footer>
    @endunless

    @stack('scripts')
    @if($fullBleed && (session('status') || $errors->any()))
        <script>
            window.setTimeout(() => {
                ['appStatusNotice', 'appErrorNotice'].forEach((id) => {
                    const notice = document.getElementById(id);
                    if (!notice) return;
                    notice.classList.add('opacity-0', '-translate-y-2');
                    window.setTimeout(() => notice.remove(), 350);
                });
            }, 3500);
        </script>
    @endif
</body>
</html>
