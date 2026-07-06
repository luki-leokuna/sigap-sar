@php
    $reporterPhone = 'tel:' . preg_replace('/[^\d+]/', '', $assignment->report->reporter_phone);
    $assignmentClosed = in_array($assignment->status, ['completed', 'cancelled'], true);
    $navigationMode = !$assignmentClosed;
@endphp

<x-layouts.app title="Navigasi Tugas - Tema Maxim Driver" :hideChrome="true" :fullBleed="true">
    @push('scripts')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            body, .timsar-maxim-driver {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
                overflow: hidden;
            }
            #bottomSheet {
                transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.6);
            }
            #bottomSheet.peek-mode {
                transform: translateY(calc(100% - 76px));
            }
            #bottomSheet.expanded-mode {
                transform: translateY(0);
            }
            #assignmentMap {
                filter: contrast(1.05) saturate(1.1);
            }
            ::-webkit-scrollbar { width: 5px; }
            ::-webkit-scrollbar-track { background: #1e222b; }
            ::-webkit-scrollbar-thumb { background: #333846; border-radius: 4px; }
            #assignmentMap .leaflet-bottom { bottom: 84px; }
            #assignmentMap .leaflet-control-attribution { max-width: 42vw; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
            #assignmentMap .leaflet-control-zoom { margin-top: 68px; }
        </style>
        @if($navigationMode)
            <script src="https://unpkg.com/leaflet-rotate@0.2.7/dist/leaflet-rotate-src.js"></script>
        @endif
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const sheet = document.getElementById('bottomSheet');
                const handle = document.getElementById('sheetHandle');
                const toggleBtn = document.getElementById('toggleSheetBtn');
                const toggleIcon = document.getElementById('toggleSheetIcon');
                const peekHeader = document.getElementById('peekHeader');

                function toggleSheet() {
                    if (!sheet) return;
                    const isExpanded = sheet.classList.contains('expanded-mode');
                    if (isExpanded) {
                        sheet.classList.remove('expanded-mode');
                        sheet.classList.add('peek-mode');
                        if (toggleIcon) toggleIcon.textContent = '▲ Buka';
                    } else {
                        sheet.classList.remove('peek-mode');
                        sheet.classList.add('expanded-mode');
                        if (toggleIcon) toggleIcon.textContent = '▼ Ringkas';
                    }
                }

                if (handle) handle.addEventListener('click', toggleSheet);
                if (toggleBtn) toggleBtn.addEventListener('click', toggleSheet);
                if (peekHeader) peekHeader.addEventListener('click', (e) => {
                    if (!e.target.closest('button')) toggleSheet();
                });
            });
        </script>
    @endpush

    <div class="timsar-maxim-driver relative h-screen w-screen overflow-hidden bg-[#181a20]">
        
        {{-- LAYER 1: Full-Screen Map --}}
        <div id="assignmentMap" class="absolute inset-0 h-full w-full z-0"></div>

        {{-- LAYER 2: Floating Top Pill Header --}}
        <header class="absolute top-4 left-4 right-4 z-20 max-w-lg mx-auto pointer-events-none">
            <div class="pointer-events-auto rounded-full bg-[#181a20]/90 backdrop-blur-md border border-[#333846] p-2 sm:p-2.5 px-4 shadow-2xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('member.dashboard') }}" class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#242832] hover:bg-orange-500/20 text-slate-300 hover:text-orange-400 border border-[#333846] transition-all" title="Kembali ke Dashboard">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-black text-red-400 uppercase tracking-wider">{{ $assignment->report->tracking_code }}</span>
                            <span id="assignmentStatusText" class="text-xs font-bold text-amber-400 uppercase">{{ \App\Http\Controllers\PublicTrackingController::assignmentLabel($assignment->status) }}</span>
                        </div>
                        <h1 class="text-xs sm:text-sm font-black text-white truncate">{{ $assignment->report->incident_type }}</h1>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <span id="gpsStatus" class="rounded-full bg-emerald-500/20 border border-emerald-500/40 px-2.5 py-1 text-[10px] font-black text-emerald-400 truncate max-w-[120px]">Mengaktifkan...</span>
                </div>
            </div>
        </header>

        {{-- LAYER 2.2: Tactical Turn-by-Turn Navigation HUD (Google Maps / Waze Style) --}}
        <div id="navigationHud" class="absolute bottom-[84px] left-4 right-4 z-20 max-w-md mx-auto hidden pointer-events-auto transition-all duration-300">
            <div class="rounded-2xl bg-[#1e222b]/95 backdrop-blur-md border border-orange-500/50 p-3 shadow-2xl flex items-center justify-between gap-3 text-white">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="navManeuverIcon" class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 text-white font-black text-xl shadow-lg shadow-orange-500/30">
                        ⬆️
                    </div>
                    <div class="min-w-0">
                        <div id="navDistanceText" class="text-base font-black text-orange-400 leading-tight">Lurus terus</div>
                        <div id="navInstructionText" class="text-xs font-medium text-slate-200 truncate">Menuju lokasi kejadian</div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button id="voiceToggleBtn" type="button" class="grid h-9 w-9 place-items-center rounded-lg bg-[#242832] hover:bg-orange-500/20 text-orange-400 border border-[#333846] transition-all text-sm" title="Aktifkan/Matikan Suara Navigator">
                        🔊
                    </button>
                </div>
            </div>
        </div>

        {{-- LAYER 2.5: Tactical Map Buttons (Right Floating) --}}
        <div class="absolute right-4 top-20 z-20 flex flex-col gap-2 pointer-events-auto">
            <button id="focusMeButton" type="button" class="grid h-10 w-10 place-items-center rounded-xl bg-[#181a20]/90 backdrop-blur-md border border-[#333846] text-white hover:bg-orange-500/20 hover:text-orange-400 shadow-lg transition-all" title="Fokus ke posisi saya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </button>
            <button id="fitRouteButton" type="button" class="grid h-10 w-10 place-items-center rounded-xl bg-[#181a20]/90 backdrop-blur-md border border-[#333846] text-white hover:bg-orange-500/20 hover:text-orange-400 shadow-lg transition-all" title="Lihat rute penuh">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5 4V4l5 4 6-4 5 4v16l-5-4-6 4z"></path></svg>
            </button>
        </div>

        {{-- Route Deviation Alert --}}
        <div id="routeDeviationNotice" class="absolute bottom-[160px] left-4 right-4 z-20 hidden max-w-md mx-auto rounded-xl bg-amber-500/90 backdrop-blur-md border border-amber-400 p-2.5 text-xs font-black text-slate-950 shadow-xl text-center animate-bounce">
            ⚠️ Keluar jalur dari rute tugas! Memperbarui navigasi...
        </div>

        {{-- LAYER 3: Interactive Bottom Sheet --}}
        <div id="bottomSheet" class="expanded-mode absolute bottom-0 left-0 right-0 z-20 max-w-lg mx-auto bg-[#1e222b]/95 backdrop-blur-xl border-t border-x border-[#333846] rounded-t-3xl p-5 sm:p-6 max-h-[82vh] overflow-y-auto flex flex-col gap-4">
            
            {{-- Handle Bar --}}
            <div id="sheetHandle" class="w-14 h-1.5 bg-[#4b5265] hover:bg-orange-500 rounded-full mx-auto -mt-2 mb-1 cursor-pointer transition-colors" title="Klik untuk buka / tutup panel"></div>

            {{-- PEEK HEADER --}}
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#333846] cursor-pointer" id="peekHeader">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="rounded-xl bg-amber-500/20 border border-amber-500/40 px-3 py-1 text-xs font-black text-amber-300 uppercase shrink-0">ETA</span>
                    <span id="durationText" class="text-sm font-black text-white">{{ $assignment->duration_seconds ? round($assignment->duration_seconds / 60) . ' menit' : '-' }}</span>
                    <span class="text-slate-500 font-bold">•</span>
                    <span id="distanceText" class="text-xs font-bold text-slate-300 truncate">{{ $assignment->distance_meters ? number_format($assignment->distance_meters / 1000, 2) . ' km' : '-' }}</span>
                </div>
                <button type="button" id="toggleSheetBtn" class="text-xs font-bold text-orange-400 hover:text-orange-300 px-2.5 py-1 rounded-lg bg-[#242832] border border-[#333846] shrink-0">
                    <span id="toggleSheetIcon">▼ Ringkas</span>
                </button>
            </div>

            {{-- EXPANDABLE CONTENT --}}
            <div id="sheetContent" class="space-y-4">
                
                {{-- TACTICAL ACTION BUTTONS (MANDATORY FOR MISSION) --}}
                @unless($assignmentClosed)
                <div class="p-3 rounded-2xl bg-[#242832] border border-[#333846] shadow-inner space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-1">
                        <span>Tindakan Lapangan:</span>
                        <span id="mapRouteMeta" class="text-orange-400">{{ $assignment->distance_meters ? number_format($assignment->distance_meters / 1000, 2) . ' km' : '-' }} - {{ $assignment->duration_seconds ? round($assignment->duration_seconds / 60) . ' menit' : '-' }}</span>
                    </div>
                    
                    @if($assignment->status === 'assigned')
                        <form method="POST" action="{{ route('member.assignments.accept', $assignment) }}" data-stop-assignment-alarm>
                            @csrf
                            <button class="w-full rounded-xl bg-gradient-to-r from-orange-600 via-amber-600 to-red-600 px-4 py-4 font-black text-white shadow-lg shadow-orange-500/30 hover:brightness-110 active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-base">
                                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>TERIMA TUGAS SEKARANG</span>
                            </button>
                        </form>
                    @elseif($assignment->status === 'accepted')
                        <form method="POST" action="{{ route('member.assignments.start', $assignment) }}">
                            @csrf
                            <button class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-4 font-black text-white shadow-lg shadow-blue-500/30 hover:brightness-110 active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-base">
                                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>MULAI JALAN (OTW KE LOKASI)</span>
                            </button>
                        </form>
                    @elseif($assignment->status === 'on_the_way')
                        <form method="POST" action="{{ route('member.assignments.arrive', $assignment) }}">
                            @csrf
                            <button class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-4 font-black text-white shadow-lg shadow-amber-500/30 hover:brightness-110 active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>SAYA SUDAH TIBA DI LOKASI</span>
                            </button>
                        </form>
                    @elseif($assignment->status === 'arrived')
                        <form method="POST" action="{{ route('member.assignments.handling', $assignment) }}">
                            @csrf
                            <button class="w-full rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 px-4 py-4 font-black text-white shadow-lg shadow-purple-500/30 hover:brightness-110 active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-base">
                                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>MULAI TANGANI KORBAN</span>
                            </button>
                        </form>
                    @elseif($assignment->status === 'handling')
                        <form method="POST" action="{{ route('member.assignments.complete', $assignment) }}">
                            @csrf
                            <button class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-4 font-black text-white shadow-lg shadow-emerald-500/30 hover:brightness-110 active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>SELESAI & TUTUP TUGAS</span>
                            </button>
                        </form>
                    @endif

                    <div class="flex items-center justify-between pt-1">
                        <button id="wakeLockButton" type="button" class="w-full rounded-xl border border-[#333846] bg-[#181a20] px-3 py-2 text-center text-xs font-black text-slate-300 hover:text-white transition-colors">💡 Layar Tetap Aktif</button>
                    </div>
                </div>
                @endunless

                {{-- INCIDENT & REPORTER DETAILS CARD --}}
                <div class="rounded-2xl bg-[#242832] border border-[#333846] p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-[#333846] pb-2.5">
                        <span class="text-xs font-black text-orange-400 uppercase tracking-wider">Detail Laporan Masyarakat</span>
                        <span class="rounded bg-red-500/20 px-2 py-0.5 text-[10px] font-bold text-red-400">{{ $assignment->report->tracking_code }}</span>
                    </div>

                    <p class="text-sm font-semibold text-slate-300 leading-relaxed">{{ $assignment->report->description }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        <a href="{{ $reporterPhone }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#181a20] border border-[#333846] hover:border-orange-500/50 transition-all group">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Hubungi Pelapor</p>
                                <p class="text-xs font-black text-white truncate">{{ $assignment->report->reporter_name }}</p>
                                <p class="text-[11px] font-semibold text-emerald-400 truncate">{{ $assignment->report->reporter_phone }}</p>
                            </div>
                        </a>

                        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#181a20] border border-[#333846]">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/20 text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Koordinat Laporan</p>
                                <p class="text-xs font-black text-white truncate">{{ number_format($assignment->report->latitude, 5) }}, {{ number_format($assignment->report->longitude, 5) }}</p>
                                <p class="text-[10px] font-semibold text-slate-500 truncate">Titik darurat warga</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TELEMETRY & DEVICE STATUS GRID --}}
                <div class="rounded-2xl bg-[#242832] border border-[#333846] p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-[#333846] pb-2">
                        <span class="text-xs font-black text-slate-300 uppercase tracking-wider">Status Telemetri & Perangkat</span>
                        <span id="deviceStatus" class="text-[10px] font-bold text-emerald-400 truncate max-w-[180px]">GPS aktif</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center">
                        <div class="p-2 rounded-xl bg-[#181a20] border border-[#333846]">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Akurasi</p>
                            <p id="accuracyValue" class="text-xs font-black text-amber-400 mt-0.5">-</p>
                        </div>
                        <div class="p-2 rounded-xl bg-[#181a20] border border-[#333846]">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Terkirim</p>
                            <p id="lastSentValue" class="text-xs font-black text-emerald-400 mt-0.5">-</p>
                        </div>
                        <div class="p-2 rounded-xl bg-[#181a20] border border-[#333846]">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Jaringan</p>
                            <p id="networkStatus" class="text-xs font-black text-blue-400 mt-0.5 uppercase">-</p>
                        </div>
                        <div class="p-2 rounded-xl bg-[#181a20] border border-[#333846]">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Ditempuh</p>
                            <p id="trailDistanceValue" class="text-xs font-black text-purple-400 mt-0.5">-</p>
                        </div>
                        <div class="p-2 rounded-xl bg-[#181a20] border border-[#333846] col-span-2 sm:col-span-1">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Mode</p>
                            <p id="cellStatusValue" class="text-xs font-black text-slate-300 mt-0.5 truncate">Web</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        @if($navigationMode)
            <script src="https://unpkg.com/leaflet-rotate@0.2.7/dist/leaflet-rotate-src.js"></script>
            <style>
                #assignmentMap .leaflet-bottom {
                    bottom: 74px;
                }
                #assignmentMap .leaflet-control-attribution {
                    max-width: 42vw;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }
                #assignmentMap .leaflet-control-zoom {
                    margin-top: 68px;
                }
            </style>
        @endif
        <script>
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const navigationMode = @json($navigationMode);
            const currentAssignmentId = @json($assignment->id);
            const reportPoint = [{{ $assignment->report->latitude }}, {{ $assignment->report->longitude }}];
            const map = L.map('assignmentMap', {
                attributionControl: false,
                zoomControl: false,
                zoomSnap: navigationMode ? 0.25 : 1,
                rotate: navigationMode,
                rotateControl: false,
                touchRotate: navigationMode,
                shiftKeyRotate: navigationMode,
            }).setView(reportPoint, navigationMode ? 17 : 14);
            L.control.zoom({ position: navigationMode ? 'topleft' : 'bottomright' }).addTo(map);
            L.control.attribution({ position: navigationMode ? 'bottomleft' : 'bottomright', prefix: 'Leaflet' }).addTo(map);
            TimsarMap.addTiles(map);

            const reportMarker = L.marker(reportPoint, { icon: TimsarMap.icon('incident') }).addTo(map).bindPopup('<strong>Lokasi kejadian</strong>');
            let routeLine = null;
            let routeSignature = '';
            let trailLines = [];
            let trailSignature = '';
            let currentRouteLatLngs = [];
            let memberMarker = null;
            let memberAccuracyCircle = null;
            let latestPosition = null;
            let bestWarmupPosition = null;
            let gpsWarmupStartedAt = null;
            let gpsWarmupSamples = 0;
            let gpsReady = false;
            let watchId = null;
            let wakeLock = null;
            let wakeLockWanted = false;
            let autoFollow = true;
            let autoFollowResumeTimeout = null;
            let autoFollowCountdownInterval = null;
            let autoFollowResumeAt = null;
            let navigationHeading = null;
            let displayedMapBearing = null;
            let bearingAnimationFrame = null;
            let headingAnchorPosition = null;
            let compassHeading = null;
            let compassUpdatedAt = 0;
            let lastCompassMapUpdateAt = 0;
            let lastFollowPosition = null;
            let lastFollowCenter = null;
            let locationSendInFlight = false;
            let lastLocationAttemptAt = 0;

            let currentRouteSteps = @json($assignment->route_steps_json ?? []);
            let voiceEnabled = true;
            let lastSpokenStepIndex = -1;
            const initialRoute = @json($assignment->route_geometry_json);
            const targetAccuracyMeters = 50;
            const maxAcceptedAccuracyMeters = 1500;
            const warmupMinSamples = 3;
            const warmupMaxMilliseconds = 12000;
            const autoFollowResumeMilliseconds = 10000;
            const stationarySpeedMps = 0.9;
            const displayMoveThresholdMeters = 12;
            const maxStationaryJitterMeters = 45;
            const jumpWithoutMotionMeters = 90;
            const navigationMoveThresholdMeters = 6;
            const navigationBearingDeadbandDegrees = 2.5;
            const navigationLookAheadMinSpeedMps = 1.5;
            const gpsStatus = document.getElementById('gpsStatus');
            const accuracyValue = document.getElementById('accuracyValue');
            const lastSentValue = document.getElementById('lastSentValue');
            const networkStatus = document.getElementById('networkStatus');
            const trailDistanceValue = document.getElementById('trailDistanceValue');
            const cellStatusValue = document.getElementById('cellStatusValue');
            const deviceStatus = document.getElementById('deviceStatus');
            const wakeLockButton = document.getElementById('wakeLockButton');
            const focusMeButton = document.getElementById('focusMeButton');
            const fitRouteButton = document.getElementById('fitRouteButton');
            const distanceText = document.getElementById('distanceText');
            const durationText = document.getElementById('durationText');
            const assignmentStatusText = document.getElementById('assignmentStatusText');
            const mapRouteMeta = document.getElementById('mapRouteMeta');
            const routeDeviationNotice = document.getElementById('routeDeviationNotice');
            const navigationHud = document.getElementById('navigationHud');
            const navManeuverIcon = document.getElementById('navManeuverIcon');
            const navDistanceText = document.getElementById('navDistanceText');
            const navInstructionText = document.getElementById('navInstructionText');
            const voiceToggleBtn = document.getElementById('voiceToggleBtn');

            function networkType() {
                if (!navigator.onLine) return 'offline';
                if (window.TimsarNativeBridge?.networkType) {
                    const nativeType = window.TimsarNativeBridge.networkType();
                    if (nativeType) return nativeType;
                }
                const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
                if (conn?.type && conn.type !== 'unknown' && conn.type !== 'other' && conn.type !== 'none') {
                    if (conn.type === 'cellular') return conn.effectiveType || 'cellular';
                    return conn.type;
                }
                return conn?.effectiveType || conn?.type || 'unknown';
            }

            function updateNetworkUi() {
                networkStatus.textContent = networkType();
            }

            window.addEventListener('timsar:cell-info', (event) => {
                const cell = window.TimsarNativeBridge?.cell();
                if (!cell || !cell.cell_id) return;
                cellStatusValue.textContent = `${cell.radio_type || 'CELL'} ${cell.cell_id}`;
                cellStatusValue.title = `${cell.operator_label || cell.operator_name || 'Operator'} - Cell ${cell.cell_id}`;
            });

            function formatDistance(meters) {
                if (!meters) return '-';
                return meters >= 1000 ? `${(meters / 1000).toFixed(2)} km` : `${Math.round(meters)} m`;
            }

            function formatDuration(seconds) {
                if (!seconds) return '-';
                return `${Math.max(1, Math.round(seconds / 60))} menit`;
            }

            function updateGpsUi(message, pos = latestPosition) {
                gpsStatus.textContent = message;
                accuracyValue.textContent = pos ? `${Math.round(pos.coords.accuracy)} m` : '-';
                updateNetworkUi();
            }

            function geometryToLatLngs(geometry) {
                if (!geometry || !geometry.coordinates) return [];
                return geometry.coordinates.map((point) => [point[1], point[0]]);
            }

            function clearTrailLines() {
                trailLines.forEach((line) => line.remove());
                trailLines = [];
            }

            function setTrailData(trail) {
                const signature = JSON.stringify(trail?.segments ?? []);
                if (signature === trailSignature) return;

                trailSignature = signature;
                clearTrailLines();

                (trail?.segments ?? []).forEach((segment) => {
                    const latLngs = (segment.points ?? []).map((point) => [point.latitude, point.longitude]);
                    if (latLngs.length < 2) return;

                    trailLines.push(L.polyline(latLngs, TimsarMap.trailOptions()).addTo(map));
                });

                const pointCount = trail?.summary?.point_count ?? 0;
                trailDistanceValue.textContent = pointCount > 0
                    ? (trail.summary.distance_meters > 0 ? formatDistance(trail.summary.distance_meters) : '0 m')
                    : '-';
            }

            function setRouteGeometry(geometry, shouldFit = false) {
                const latLngs = geometryToLatLngs(geometry);
                const signature = JSON.stringify(geometry?.coordinates ?? []);
                if (!latLngs.length || signature === routeSignature) return;

                routeSignature = signature;
                currentRouteLatLngs = latLngs;
                if (!routeLine) {
                    routeLine = L.polyline(latLngs, TimsarMap.routeOptions({ weight: 6 })).addTo(map);
                } else {
                    routeLine.setLatLngs(latLngs);
                }

                if (shouldFit) {
                    fitRoute();
                }

                updateRouteDeviationUi(latestPosition);
            }

            function fitRoute() {
                setAutoFollow(false, true);
                if (routeLine) {
                    map.fitBounds(routeLine.getBounds(), { padding: [36, 36] });
                    return;
                }
                map.setView(reportPoint, 15);
            }

            function focusMe() {
                if (!latestPosition) return;
                setAutoFollow(true);
                followNavigation(latestPosition, true);
            }

            function clearAutoFollowResume() {
                window.clearTimeout(autoFollowResumeTimeout);
                window.clearInterval(autoFollowCountdownInterval);
                autoFollowResumeTimeout = null;
                autoFollowCountdownInterval = null;
                autoFollowResumeAt = null;
            }

            function updateAutoFollowButton() {
                if (!navigationMode) return;

                const seconds = autoFollowResumeAt
                    ? Math.max(1, Math.ceil((autoFollowResumeAt - Date.now()) / 1000))
                    : null;
                focusMeButton.textContent = autoFollow
                    ? 'Mengikuti'
                    : (seconds ? `Ikuti ${seconds} dtk` : 'Ikuti');
                focusMeButton.className = autoFollow
                    ? 'min-w-24 rounded-xl bg-emerald-600 px-3 py-2 text-sm font-black text-white shadow-lg'
                    : 'min-w-24 rounded-xl bg-white/95 px-3 py-2 text-sm font-black text-slate-900 shadow-lg';
            }

            function scheduleAutoFollowResume() {
                clearAutoFollowResume();
                autoFollowResumeAt = Date.now() + autoFollowResumeMilliseconds;
                updateAutoFollowButton();
                autoFollowCountdownInterval = window.setInterval(updateAutoFollowButton, 1000);
                autoFollowResumeTimeout = window.setTimeout(() => {
                    setAutoFollow(true);
                    if (latestPosition) followNavigation(latestPosition, true);
                }, autoFollowResumeMilliseconds);
            }

            function setAutoFollow(enabled, resumeAfterDelay = false) {
                clearAutoFollowResume();
                autoFollow = enabled;
                updateAutoFollowButton();
                if (!enabled && resumeAfterDelay) scheduleAutoFollowResume();
            }

            function pauseAutoFollow() {
                if (navigationMode) setAutoFollow(false, true);
            }

            function normalizeHeading(value) {
                return ((value % 360) + 360) % 360;
            }

            function headingDelta(from, to) {
                return ((to - from + 540) % 360) - 180;
            }

            function bearingBetween(from, to) {
                const lat1 = from.coords.latitude * Math.PI / 180;
                const lat2 = to.coords.latitude * Math.PI / 180;
                const dLon = (to.coords.longitude - from.coords.longitude) * Math.PI / 180;
                const y = Math.sin(dLon) * Math.cos(lat2);
                const x = Math.cos(lat1) * Math.sin(lat2) -
                    Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLon);
                return normalizeHeading(Math.atan2(y, x) * 180 / Math.PI);
            }

            function smoothHeading(current, next, weight = 0.28) {
                if (current === null) return next;
                const delta = headingDelta(current, next);
                return normalizeHeading(current + (delta * weight));
            }

            function animateMapBearing() {
                bearingAnimationFrame = null;
                if (!navigationMode || navigationHeading === null || typeof map.setBearing !== 'function') return;

                displayedMapBearing ??= normalizeHeading(
                    typeof map.getBearing === 'function' ? map.getBearing() || 0 : 0,
                );

                const delta = headingDelta(displayedMapBearing, navigationHeading);
                if (Math.abs(delta) <= 0.25) {
                    displayedMapBearing = navigationHeading;
                    map.setBearing(displayedMapBearing);
                    return;
                }

                displayedMapBearing = normalizeHeading(displayedMapBearing + (delta * 0.12));
                map.setBearing(displayedMapBearing);
                bearingAnimationFrame = window.requestAnimationFrame(animateMapBearing);
            }

            function requestBearingAnimation() {
                if (!navigationMode || typeof map.setBearing !== 'function') return;
                if (bearingAnimationFrame) return;
                bearingAnimationFrame = window.requestAnimationFrame(animateMapBearing);
            }

            function setNavigationHeadingTarget(nextHeading, weight = 0.18) {
                const normalized = normalizeHeading(nextHeading);
                if (
                    navigationHeading !== null &&
                    Math.abs(headingDelta(navigationHeading, normalized)) < navigationBearingDeadbandDegrees
                ) {
                    return;
                }

                navigationHeading = smoothHeading(navigationHeading, normalized, weight);
                requestBearingAnimation();
            }

            function applyCompassHeading(value) {
                const parsed = Number(value);
                if (!Number.isFinite(parsed)) return;

                compassHeading = smoothHeading(compassHeading, normalizeHeading(parsed), 0.08);
                compassUpdatedAt = Date.now();

                const speedMps = Number.isFinite(latestPosition?.coords.speed)
                    ? latestPosition.coords.speed
                    : 0;
                if (!navigationMode || !autoFollow || speedMps >= navigationLookAheadMinSpeedMps) return;
                const deltaDegrees = navigationHeading === null
                    ? 360
                    : Math.abs(headingDelta(navigationHeading, compassHeading));
                if (deltaDegrees < 4) return;
                if (Date.now() - lastCompassMapUpdateAt < 120) return;

                lastCompassMapUpdateAt = Date.now();
                setNavigationHeadingTarget(compassHeading, 0.1);
            }

            window.addEventListener('timsar:compass-heading', (event) => {
                applyCompassHeading(event.detail?.heading);
            });

            window.addEventListener('deviceorientationabsolute', (event) => {
                if (!Number.isFinite(event.alpha)) return;
                const screenAngle = window.screen.orientation?.angle ?? window.orientation ?? 0;
                applyCompassHeading(360 - event.alpha + screenAngle);
            });

            function updateNavigationHeading(pos) {
                if (!navigationMode) return;

                const speedMps = Number.isFinite(pos.coords.speed) ? pos.coords.speed : 0;
                const gpsHeading = Number.isFinite(pos.coords.heading) && pos.coords.heading >= 0
                    ? normalizeHeading(pos.coords.heading)
                    : null;
                const recentCompassHeading = Date.now() - compassUpdatedAt < 2000
                    ? compassHeading
                    : null;
                let nextHeading = speedMps >= 1.5 ? gpsHeading : recentCompassHeading;

                if (nextHeading === null && headingAnchorPosition) {
                    const movedMeters = distanceMeters(headingAnchorPosition, pos);
                    const movementThreshold = Math.max(8, Math.min(pos.coords.accuracy * 0.45, 25));
                    if (movedMeters >= movementThreshold) {
                        nextHeading = bearingBetween(headingAnchorPosition, pos);
                    }
                }

                if (nextHeading !== null) {
                    setNavigationHeadingTarget(nextHeading, speedMps >= navigationLookAheadMinSpeedMps ? 0.2 : 0.1);
                    headingAnchorPosition = pos;
                } else if (!headingAnchorPosition) {
                    headingAnchorPosition = pos;
                }
            }

            function navigationZoom(pos) {
                const speedKph = (Number.isFinite(pos.coords.speed) ? pos.coords.speed : 0) * 3.6;
                if (speedKph >= 55) return 16.25;
                if (speedKph >= 25) return 16.75;
                if (speedKph >= 5) return 17.25;
                return 17.75;
            }

            function destinationLatLng(point, bearingDegrees, meters) {
                const earthRadius = 6371000;
                const bearing = bearingDegrees * Math.PI / 180;
                const distance = meters / earthRadius;
                const lat1 = point[0] * Math.PI / 180;
                const lon1 = point[1] * Math.PI / 180;
                const lat2 = Math.asin(
                    (Math.sin(lat1) * Math.cos(distance)) +
                    (Math.cos(lat1) * Math.sin(distance) * Math.cos(bearing)),
                );
                const lon2 = lon1 + Math.atan2(
                    Math.sin(bearing) * Math.sin(distance) * Math.cos(lat1),
                    Math.cos(distance) - (Math.sin(lat1) * Math.sin(lat2)),
                );

                return [
                    lat2 * 180 / Math.PI,
                    (((lon2 * 180 / Math.PI) + 540) % 360) - 180,
                ];
            }

            function navigationLookAheadMeters(pos) {
                const speedMps = Number.isFinite(pos.coords.speed) ? pos.coords.speed : 0;
                if (speedMps < navigationLookAheadMinSpeedMps || navigationHeading === null) return 0;

                const speedKph = speedMps * 3.6;
                if (speedKph >= 55) return 120;
                if (speedKph >= 25) return 95;
                if (speedKph >= 10) return 70;
                return 45;
            }

            function navigationCenterPoint(point, pos) {
                const lookAheadMeters = navigationLookAheadMeters(pos);
                if (!lookAheadMeters) return point;
                return destinationLatLng(point, navigationHeading, lookAheadMeters);
            }

            function followNavigation(pos, immediate = false) {
                const point = [pos.coords.latitude, pos.coords.longitude];
                const zoom = navigationMode ? navigationZoom(pos) : Math.max(map.getZoom(), 16);
                const centerPoint = navigationMode ? navigationCenterPoint(point, pos) : point;
                if (!immediate && lastFollowPosition) {
                    const movedMeters = distanceMeters(lastFollowPosition, pos);
                    const speedMps = reportedSpeedMps(pos);
                    const threshold = navigationMode && speedMps >= navigationLookAheadMinSpeedMps
                        ? navigationMoveThresholdMeters
                        : displayMoveThresholdMeters;
                    const centerMovedMeters = lastFollowCenter
                        ? L.latLng(lastFollowCenter).distanceTo(L.latLng(centerPoint))
                        : movedMeters;
                    if (movedMeters < threshold && centerMovedMeters < threshold) return;
                }

                requestBearingAnimation();

                if (immediate) {
                    map.setView(centerPoint, zoom, { animate: false });
                } else if (Math.abs(map.getZoom() - zoom) > 0.35) {
                    map.setView(centerPoint, zoom, { animate: true, duration: 0.55, easeLinearity: 0.2 });
                } else {
                    map.panTo(centerPoint, { animate: true, duration: 0.55, easeLinearity: 0.2 });
                }
                lastFollowPosition = pos;
                lastFollowCenter = centerPoint;

            }

            function nearestPointOnSegment(point, start, end) {
                const earthRadius = 6371000;
                const latitudeReference = point.coords.latitude * Math.PI / 180;
                const toLocalPoint = (latLng) => ({
                    x: (latLng[1] - point.coords.longitude) * Math.PI / 180 * earthRadius * Math.cos(latitudeReference),
                    y: (latLng[0] - point.coords.latitude) * Math.PI / 180 * earthRadius,
                });
                const a = toLocalPoint(start);
                const b = toLocalPoint(end);
                const dx = b.x - a.x;
                const dy = b.y - a.y;
                const lengthSquared = (dx * dx) + (dy * dy);
                const projection = lengthSquared > 0
                    ? Math.max(0, Math.min(1, -((a.x * dx) + (a.y * dy)) / lengthSquared))
                    : 0;
                const nearestX = a.x + (projection * dx);
                const nearestY = a.y + (projection * dy);
                return {
                    distance: Math.sqrt((nearestX * nearestX) + (nearestY * nearestY)),
                    point: [
                        start[0] + ((end[0] - start[0]) * projection),
                        start[1] + ((end[1] - start[1]) * projection),
                    ],
                };
            }

            function nearestRouteMatch(pos) {
                if (currentRouteLatLngs.length < 2) return null;

                let nearest = null;
                for (let index = 1; index < currentRouteLatLngs.length; index += 1) {
                    const match = nearestPointOnSegment(
                        pos,
                        currentRouteLatLngs[index - 1],
                        currentRouteLatLngs[index],
                    );
                    if (!nearest || match.distance < nearest.distance) {
                        nearest = { ...match, nextIndex: index };
                    }
                }
                return nearest;
            }

            function updateRouteDeviationUi(pos) {
                if (!navigationMode || !pos || currentRouteLatLngs.length < 2) return false;

                const thresholdMeters = Math.max(70, Math.min(150, pos.coords.accuracy * 1.5));
                const nearest = nearestRouteMatch(pos);
                const deviated = nearest && nearest.distance > thresholdMeters;
                routeDeviationNotice.classList.toggle('hidden', !deviated);

                if (!deviated && nearest && routeLine) {
                    routeLine.setLatLngs([
                        nearest.point,
                        ...currentRouteLatLngs.slice(nearest.nextIndex),
                    ]);
                }
                return deviated;
            }

            function updateMemberMarker(pos) {
                const point = [pos.coords.latitude, pos.coords.longitude];
                if (!memberMarker) {
                    memberMarker = L.marker(point, { icon: TimsarMap.icon('member') }).addTo(map).bindPopup('<strong>Posisi saya</strong><br><span class="text-xs text-slate-500">Bergerak menuju lokasi</span>');
                } else {
                    const previousPoint = memberMarker.getLatLng();
                    if (previousPoint.distanceTo(L.latLng(point)) >= 3) {
                        TimsarMap.moveMarker(memberMarker, point);
                    }
                }

                if (!memberAccuracyCircle) {
                    memberAccuracyCircle = L.circle(point, {
                        radius: pos.coords.accuracy,
                        color: '#16a34a',
                        fillColor: '#22c55e',
                        fillOpacity: 0.08,
                        weight: 1,
                    }).addTo(map);
                } else {
                    memberAccuracyCircle.setLatLng(point);
                    memberAccuracyCircle.setRadius(pos.coords.accuracy);
                }

                if (autoFollow) {
                    followNavigation(pos);
                }
            }

            function maneuverIcon(type, modifier) {
                if (type === 'depart') return '🚀';
                if (type === 'arrive') return '🎯';
                if (modifier && modifier.includes('left')) return '↖️';
                if (modifier && modifier.includes('right')) return '↗️';
                if (modifier === 'uturn') return '↩️';
                return '⬆️';
            }

            function speakInstruction(text) {
                if (!voiceEnabled || !('speechSynthesis' in window)) return;
                if (window.speechSynthesis.speaking) return;
                try {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 1.0;
                    window.speechSynthesis.speak(utterance);
                } catch (e) {}
            }

            function updateTurnByTurnNavigation(pos) {
                if (!navigationHud || !navigationMode) {
                    if (navigationHud) navigationHud.classList.add('hidden');
                    return;
                }

                navigationHud.classList.remove('hidden');

                const activePos = pos || bestWarmupPosition || latestPosition;
                let stepsToUse = currentRouteSteps;
                if (!stepsToUse || !stepsToUse.length) {
                    const targetLat = {{ $assignment->report->latitude }};
                    const targetLng = {{ $assignment->report->longitude }};
                    const distToTarget = activePos ? L.latLng(activePos.coords.latitude, activePos.coords.longitude).distanceTo(L.latLng(targetLat, targetLng)) : {{ $assignment->distance_meters ?? 0 }};
                    stepsToUse = [{
                        type: 'arrive',
                        modifier: null,
                        name: '{{ addslashes($assignment->report->incident_type) }}',
                        distance: distToTarget,
                        location: [targetLng, targetLat]
                    }];
                }

                let nextStepIndex = -1;
                let minStepDist = Infinity;
                const currentLat = activePos ? activePos.coords.latitude : null;
                const currentLng = activePos ? activePos.coords.longitude : null;

                if (currentLat !== null && currentLng !== null) {
                    for (let i = 0; i < stepsToUse.length; i++) {
                        const step = stepsToUse[i];
                        if (step.location && step.location.length === 2) {
                            const dist = L.latLng(currentLat, currentLng).distanceTo(L.latLng(step.location[1], step.location[0]));
                            if (dist < minStepDist && (dist > 15 || i === stepsToUse.length - 1)) {
                                minStepDist = dist;
                                nextStepIndex = i;
                            }
                        }
                    }
                }

                if (nextStepIndex === -1 && stepsToUse.length > 0) {
                    nextStepIndex = 0;
                }

                if (nextStepIndex >= 0) {
                    const step = stepsToUse[nextStepIndex];
                    if (navManeuverIcon) navManeuverIcon.textContent = maneuverIcon(step.type, step.modifier);
                    if (navDistanceText) navDistanceText.textContent = minStepDist !== Infinity ? `Dalam ${formatDistance(minStepDist)}` : formatDistance(step.distance);
                    
                    let instructionText = step.name && step.name !== 'Jalan Raya' ? `Ke ${step.name}` : `Lurus terus mengikuti rute`;
                    if (step.modifier && step.modifier.includes('left')) instructionText = `Belok kiri ke ${step.name}`;
                    if (step.modifier && step.modifier.includes('right')) instructionText = `Belok kanan ke ${step.name}`;
                    if (step.type === 'arrive') instructionText = `Menuju lokasi kejadian`;
                    
                    if (navInstructionText) navInstructionText.textContent = instructionText;

                    if (activePos && nextStepIndex !== lastSpokenStepIndex && minStepDist < 150) {
                        lastSpokenStepIndex = nextStepIndex;
                        speakInstruction(`${minStepDist !== Infinity ? 'Dalam ' + Math.round(minStepDist) + ' meter, ' : ''}${instructionText}`);
                    }
                }
            }

            function reportedSpeedMps(pos) {
                return Number.isFinite(pos?.coords?.speed) ? pos.coords.speed : null;
            }

            function isMeaningfullyBetterDisplayAccuracy(pos, previous) {
                const accuracy = Number(pos?.coords?.accuracy);
                const previousAccuracy = Number(previous?.coords?.accuracy);
                if (!Number.isFinite(accuracy) || !Number.isFinite(previousAccuracy)) return false;

                return previousAccuracy - accuracy >= Math.max(8, previousAccuracy * 0.25);
            }

            function shouldHoldDisplayPosition(pos) {
                if (!latestPosition) return null;

                const movedMeters = distanceMeters(latestPosition, pos);
                const speedMps = reportedSpeedMps(pos);
                const accuracy = Number.isFinite(pos.coords.accuracy) ? pos.coords.accuracy : maxAcceptedAccuracyMeters;
                const previousAccuracy = Number.isFinite(latestPosition.coords.accuracy) ? latestPosition.coords.accuracy : accuracy;
                const accuracyReference = Math.max(accuracy, previousAccuracy);
                const stationaryRadius = Math.max(
                    displayMoveThresholdMeters,
                    Math.min(maxStationaryJitterMeters, accuracyReference * 0.45),
                );
                const stationarySpeed = speedMps === null || speedMps <= stationarySpeedMps;
                const betterAccuracy = isMeaningfullyBetterDisplayAccuracy(pos, latestPosition);

                if (stationarySpeed && movedMeters < stationaryRadius && !betterAccuracy) {
                    return `GPS stabil, titik ditahan ${Math.round(previousAccuracy)} m`;
                }

                if (
                    stationarySpeed &&
                    movedMeters > Math.max(jumpWithoutMotionMeters, accuracyReference * 1.2) &&
                    accuracy >= previousAccuracy * 0.8 &&
                    !betterAccuracy
                ) {
                    return `Lompatan GPS diabaikan ${Math.round(movedMeters)} m`;
                }

                return null;
            }

            function positionFromServer(data) {
                if (!data || data.latitude === null || data.longitude === null) return null;

                return {
                    coords: {
                        latitude: Number(data.latitude),
                        longitude: Number(data.longitude),
                        accuracy: Number(data.accuracy ?? latestPosition?.coords.accuracy ?? 0),
                        speed: latestPosition?.coords.speed ?? null,
                    },
                    timestamp: Date.now(),
                };
            }

            function acceptGpsPosition(pos, message) {
                latestPosition = pos;
                gpsReady = true;
                updateNavigationHeading(pos);
                updateMemberMarker(pos);
                updateTurnByTurnNavigation(pos);
                if (updateRouteDeviationUi(pos)) {
                    sendLocation();
                }
                updateGpsUi(message, pos);
            }

            function handleGpsPosition(pos) {
                const now = Date.now();
                gpsWarmupStartedAt ??= now;
                gpsWarmupSamples += 1;

                if (!bestWarmupPosition || pos.coords.accuracy < bestWarmupPosition.coords.accuracy) {
                    bestWarmupPosition = pos;
                }

                if (!gpsReady) {
                    const elapsed = now - gpsWarmupStartedAt;
                    const enoughSamples = gpsWarmupSamples >= warmupMinSamples;
                    const goodEarlyLock = bestWarmupPosition.coords.accuracy <= targetAccuracyMeters && gpsWarmupSamples >= 2;
                    const timeoutReached = elapsed >= warmupMaxMilliseconds;
                    updateGpsUi(`Mengunci GPS (${Math.min(gpsWarmupSamples, warmupMinSamples)}/${warmupMinSamples})`, bestWarmupPosition);

                    if (!enoughSamples && !goodEarlyLock && !timeoutReached) {
                        return;
                    }

                    acceptGpsPosition(bestWarmupPosition, `GPS aktif ${Math.round(bestWarmupPosition.coords.accuracy)} m`);
                    return;
                }

                if (
                    latestPosition &&
                    pos.coords.accuracy > maxAcceptedAccuracyMeters &&
                    pos.coords.accuracy > latestPosition.coords.accuracy
                ) {
                    updateGpsUi(`GPS melemah ${Math.round(pos.coords.accuracy)} m`, latestPosition);
                    return;
                }

                if (
                    latestPosition &&
                    pos.coords.accuracy > latestPosition.coords.accuracy * 1.8 &&
                    distanceMeters(latestPosition, pos) < pos.coords.accuracy
                ) {
                    updateGpsUi(`Titik kasar diabaikan ${Math.round(pos.coords.accuracy)} m`, latestPosition);
                    return;
                }

                const holdMessage = shouldHoldDisplayPosition(pos);
                if (holdMessage) {
                    updateGpsUi(holdMessage, latestPosition);
                    return;
                }

                acceptGpsPosition(pos, `GPS aktif ${Math.round(pos.coords.accuracy)} m`);
            }

            function startLocationWatch() {
                updateNetworkUi();
                if (!navigator.geolocation) {
                    updateGpsUi('Browser tidak mendukung GPS.');
                    return;
                }

                if (watchId !== null) return;

                watchId = navigator.geolocation.watchPosition(
                    handleGpsPosition,
                    (error) => updateGpsUi(geolocationErrorMessage(error)),
                    { enableHighAccuracy: true, timeout: 30000, maximumAge: 0 },
                );
            }

            async function sendLocation() {
                updateNetworkUi();

                if (!latestPosition || !gpsReady) {
                    updateGpsUi('Menunggu GPS terbaik...', bestWarmupPosition);
                    return;
                }
                if (window.TimsarNativeBackgroundActive) {
                    lastSentValue.textContent = 'Latar aktif';
                    return;
                }
                if (locationSendInFlight) return;
                if (Date.now() - lastLocationAttemptAt < 2500) return;

                const pos = latestPosition;
                locationSendInFlight = true;
                lastLocationAttemptAt = Date.now();
                try {
                    const payload = {
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                        accuracy: pos.coords.accuracy,
                        speed: pos.coords.speed ? pos.coords.speed * 3.6 : null,
                        network_type: networkType(),
                        recorded_at: new Date().toISOString(),
                        cell: window.TimsarNativeBridge?.cell() ?? null,
                    };

                    const res = await fetch('{{ route('member.location.update') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: JSON.stringify(payload),
                    });

                    if (res.ok) {
                        const result = await res.json();
                        lastSentValue.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        if (result.data && result.data.accepted_for_routing === false) {
                            const stablePosition = positionFromServer(result.data);
                            if (stablePosition) {
                                updateMemberMarker(stablePosition);
                            }
                            updateGpsUi(`Terkirim, posisi live ditahan ${Math.round(pos.coords.accuracy)} m`, stablePosition ?? pos);
                            return;
                        }
                        updateGpsUi(`Terkirim ${Math.round(pos.coords.accuracy)} m`, pos);
                        updateRouteDeviationUi(pos);
                        await refreshAssignment();
                    }
                } catch (error) {
                    updateGpsUi('Lokasi belum terkirim.', pos);
                } finally {
                    locationSendInFlight = false;
                }
            }

            async function sendHeartbeat() {
                try {
                    await fetch('{{ route('member.heartbeat') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ network_type: networkType() }),
                    });
                } catch (error) {
                    //
                }
            }

            async function refreshAssignment() {
                try {
                    const res = await fetch('{{ route('member.active-assignment') }}', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;

                    const data = await res.json();
                    if (!data.assignment) {
                        window.location.href = '{{ route('member.dashboard') }}';
                        return;
                    }
                    if (data.assignment.id !== currentAssignmentId) {
                        window.location.href = `{{ url('/member/assignments') }}/${data.assignment.id}`;
                        return;
                    }

                    assignmentStatusText.textContent = data.assignment.status_label;
                    distanceText.textContent = formatDistance(data.assignment.distance_meters);
                    durationText.textContent = formatDuration(data.assignment.duration_seconds);
                    mapRouteMeta.textContent = `${formatDistance(data.assignment.distance_meters)} - ${formatDuration(data.assignment.duration_seconds)}`;
                    setRouteGeometry(data.assignment.route_geometry);
                    if (data.assignment.route_steps) {
                        currentRouteSteps = data.assignment.route_steps;
                    }
                    updateTurnByTurnNavigation(latestPosition);
                } catch (error) {
                    //
                }
            }

            async function refreshTrail() {
                try {
                    const res = await fetch('{{ route('member.assignments.trail', $assignment) }}', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;

                    setTrailData(await res.json());
                } catch (error) {
                    //
                }
            }

            function distanceMeters(a, b) {
                const earthRadius = 6371000;
                const lat1 = a.coords.latitude * Math.PI / 180;
                const lat2 = b.coords.latitude * Math.PI / 180;
                const dLat = (b.coords.latitude - a.coords.latitude) * Math.PI / 180;
                const dLon = (b.coords.longitude - a.coords.longitude) * Math.PI / 180;
                const h = Math.sin(dLat / 2) ** 2 +
                    Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) ** 2;
                return earthRadius * 2 * Math.atan2(Math.sqrt(h), Math.sqrt(1 - h));
            }

            function geolocationErrorMessage(error) {
                if (error.code === error.PERMISSION_DENIED) {
                    return 'Izin lokasi ditolak.';
                }
                if (error.code === error.POSITION_UNAVAILABLE) {
                    return 'GPS HP belum tersedia.';
                }
                if (error.code === error.TIMEOUT) {
                    return 'GPS terlalu lama merespons.';
                }

                return 'Gagal mengambil GPS.';
            }

            function updateWakeLockUi() {
                if (!('wakeLock' in navigator)) {
                    if (wakeLockButton && !navigationMode) {
                        wakeLockButton.disabled = true;
                        wakeLockButton.textContent = 'Tidak didukung';
                    }
                    deviceStatus.textContent = 'Browser ini belum mendukung layar tetap aktif.';
                    return;
                }

                if (wakeLockButton && !navigationMode) {
                    wakeLockButton.disabled = false;
                }

                if (wakeLock) {
                    if (wakeLockButton && !navigationMode) {
                        wakeLockButton.textContent = 'Layar aktif';
                        wakeLockButton.className = 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-center font-black text-emerald-700';
                    }
                    deviceStatus.textContent = 'Layar dijaga tetap aktif selama halaman tugas terbuka.';
                } else {
                    if (wakeLockButton && !navigationMode) {
                        wakeLockButton.textContent = 'Layar aktif';
                        wakeLockButton.className = 'rounded-xl border border-slate-300 bg-white px-4 py-4 text-center font-black text-slate-800';
                    }
                    deviceStatus.textContent = 'GPS tetap dikirim selama halaman ini terbuka.';
                }
            }

            async function requestWakeLock() {
                if (!('wakeLock' in navigator)) {
                    updateWakeLockUi();
                    return;
                }

                try {
                    wakeLock = await navigator.wakeLock.request('screen');
                    wakeLockWanted = true;
                    wakeLock.addEventListener('release', () => {
                        wakeLock = null;
                        updateWakeLockUi();
                    });
                } catch (error) {
                    deviceStatus.textContent = 'Gagal menjaga layar aktif. Matikan hemat baterai jika perlu.';
                }

                updateWakeLockUi();
            }

            async function releaseWakeLock() {
                wakeLockWanted = false;
                if (wakeLock) {
                    await wakeLock.release();
                    wakeLock = null;
                }
                updateWakeLockUi();
            }

            wakeLockButton?.addEventListener('click', () => {
                if (navigationMode) return;
                if (wakeLock) {
                    releaseWakeLock();
                    return;
                }
                requestWakeLock();
            });

            document.querySelectorAll('[data-stop-assignment-alarm]').forEach((form) => {
                form.addEventListener('submit', () => {
                    window.TimsarNative?.postMessage(JSON.stringify({
                        action: 'stopAssignmentAlarm',
                        assignmentId: {{ $assignment->id }},
                    }));
                });
            });

            focusMeButton.addEventListener('click', focusMe);
            fitRouteButton.addEventListener('click', fitRoute);
            map.getContainer().addEventListener('pointerdown', pauseAutoFollow, { passive: true });
            map.getContainer().addEventListener('touchstart', pauseAutoFollow, { passive: true });
            map.getContainer().addEventListener('wheel', pauseAutoFollow, { passive: true });

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible' && wakeLockWanted && !wakeLock) {
                    requestWakeLock();
                }
            });

            setRouteGeometry(initialRoute, !navigationMode);
            updateTurnByTurnNavigation(null);
            mapRouteMeta.textContent = `${distanceText.textContent} - ${durationText.textContent}`;
            setAutoFollow(true);
            startLocationWatch();
            sendHeartbeat();
            sendLocation();
            refreshAssignment();
            refreshTrail();
            updateWakeLockUi();
            if (navigationMode) {
                requestWakeLock();
            }
            setInterval(sendHeartbeat, 10000);
            setInterval(sendLocation, 5000);
            setInterval(refreshAssignment, 5000);
            setInterval(refreshTrail, 5000);
        </script>
    @endpush
</x-layouts.app>
