<x-layouts.app title="Dashboard Anggota TIMSAR - Tema Maxim Driver" :hideChrome="true" :fullBleed="true">
    @push('scripts')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            body, .timsar-maxim-driver {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
                overflow: hidden;
            }
            /* Bottom sheet styling & transitions */
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
            /* Map dark contrast */
            #memberMap {
                filter: contrast(1.05) saturate(1.1);
            }
            /* Custom scrollbar in dark mode */
            ::-webkit-scrollbar { width: 5px; }
            ::-webkit-scrollbar-track { background: #1e222b; }
            ::-webkit-scrollbar-thumb { background: #333846; border-radius: 4px; }
        </style>
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
        
        {{-- LAYER 1: Full-Screen Map (Mendominasi 100% Layar) --}}
        <div id="memberMap" class="absolute inset-0 h-full w-full z-0"></div>

        {{-- LAYER 2: Floating Top Pill Header (Ringkas & Tetap Di Atas) --}}
        <header class="absolute top-4 left-4 right-4 z-20 max-w-lg mx-auto pointer-events-none">
            <div class="pointer-events-auto rounded-full bg-[#181a20]/90 backdrop-blur-md border border-[#333846] p-2 sm:p-2.5 px-4 shadow-2xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-orange-600 via-amber-600 to-red-600 text-white font-black text-sm shadow-md shadow-orange-500/20 border border-orange-400/30">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </span>
                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-sm font-black text-white truncate">{{ auth()->user()->name }}</h1>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Anggota Lapangan • GPS 5s</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    @if($activeAssignment)
                        <span id="dutyStateBadge" class="rounded-full bg-red-500/20 border border-red-500/40 px-3 py-1 text-[10px] font-black text-red-400 animate-pulse uppercase">Sedang Bertugas</span>
                    @else
                        <span id="dutyStateBadge" class="rounded-full bg-emerald-500/20 border border-emerald-500/40 px-3 py-1 text-[10px] font-black text-emerald-400 uppercase">Standby Posko</span>
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="grid h-8 w-8 place-items-center rounded-full bg-[#242832] hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-[#333846] transition-all" title="Keluar / Offline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- LAYER 3: Interactive Bottom Sheet (Swipe Up Drawer ala Maxim Driver) --}}
        <div id="bottomSheet" class="expanded-mode absolute bottom-0 left-0 right-0 z-20 max-w-lg mx-auto bg-[#1e222b]/95 backdrop-blur-xl border-t border-x border-[#333846] rounded-t-3xl p-5 sm:p-6 max-h-[82vh] overflow-y-auto flex flex-col gap-4">
            
            {{-- Handle Bar for Swipe / Toggle --}}
            <div id="sheetHandle" class="w-14 h-1.5 bg-[#4b5265] hover:bg-orange-500 rounded-full mx-auto -mt-2 mb-1 cursor-pointer transition-colors" title="Klik untuk buka / tutup panel"></div>

            {{-- PEEK HEADER (Always Visible in Peek Mode) --}}
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#333846] cursor-pointer" id="peekHeader">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span id="gpsQualityBadge" class="rounded-xl bg-amber-500/20 border border-amber-500/40 px-3 py-1 text-xs font-black text-amber-300 uppercase">Menunggu GPS</span>
                    <span id="routeMeta" class="text-xs font-semibold text-slate-400 truncate">Menunggu data tugas...</span>
                </div>
                <button type="button" id="toggleSheetBtn" class="text-xs font-bold text-orange-400 hover:text-orange-300 px-2.5 py-1 rounded-lg bg-[#242832] border border-[#333846]">
                    <span id="toggleSheetIcon">▼ Ringkas</span>
                </button>
            </div>

            {{-- EXPANDABLE CONTENT (GPS Details, Active Assignment, History & Tactical Controls) --}}
            <div id="sheetContent" class="space-y-4">
                
                {{-- 1. ACTIVE ASSIGNMENT ALERT (OR STANDBY STATUS) --}}
                <div id="assignmentPanel" class="rounded-2xl border {{ $activeAssignment ? 'border-red-500/50 bg-red-500/15' : 'border-emerald-500/30 bg-emerald-500/10' }} p-4 shadow-lg transition-all">
                    @if($activeAssignment)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-600 px-2.5 py-0.5 text-[10px] font-black uppercase text-white animate-pulse">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-ping"></span> TUGAS DARURAT AKTIF
                                </span>
                                <span class="text-[10px] font-mono font-bold text-red-300">{{ $activeAssignment->report->tracking_code }}</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-white">{{ $activeAssignment->report->incident_type }}</h2>
                                <p class="text-xs font-semibold text-slate-300 mt-0.5">Segera buka mode navigasi tugas untuk pengarahan rute lapangan.</p>
                            </div>
                            <a href="{{ route('member.assignments.show', $activeAssignment) }}" class="block w-full rounded-xl bg-gradient-to-r from-orange-600 via-amber-600 to-red-600 py-3.5 text-center text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-orange-500/30 hover:brightness-110 transition-all">
                                🚀 BUKA MODE TUGAS & NAVIGASI &rarr;
                            </a>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-emerald-500/20 text-emerald-400 font-black text-lg border border-emerald-500/30">
                                📡
                            </span>
                            <div>
                                <h2 class="text-sm font-black text-emerald-300">Standby & Siaga Posko</h2>
                                <p class="text-xs font-medium text-slate-400">Belum ada penugasan darurat aktif. Tetap biarkan aplikasi terbuka.</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 2. TACTICAL GPS & NETWORK STATS --}}
                <div class="rounded-2xl bg-[#242832] border border-[#333846] p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Status Sensor GPS</span>
                        <span id="gpsStatus" class="text-xs font-black text-orange-400">Mengaktifkan GPS...</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-1 border-t border-[#333846]">
                        <div class="bg-[#181a20] rounded-xl p-2.5 text-center border border-[#333846]/60">
                            <p class="text-[9px] font-black uppercase text-slate-500">Akurasi</p>
                            <p id="accuracyValue" class="mt-0.5 text-xs font-black text-white">-</p>
                        </div>
                        <div class="bg-[#181a20] rounded-xl p-2.5 text-center border border-[#333846]/60">
                            <p class="text-[9px] font-black uppercase text-slate-500">Terkirim</p>
                            <p id="lastSentValue" class="mt-0.5 text-xs font-black text-white">-</p>
                        </div>
                        <div class="bg-[#181a20] rounded-xl p-2.5 text-center border border-[#333846]/60">
                            <p class="text-[9px] font-black uppercase text-slate-500">Jaringan</p>
                            <p id="networkStatus" class="mt-0.5 text-xs font-black text-white">-</p>
                        </div>
                    </div>
                </div>

                {{-- 3. TACTICAL CONTROLS (WAKELOCK & NOTIF) --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <button id="wakeLockButton" type="button" class="w-full rounded-xl bg-[#242832] hover:bg-[#2c303d] border border-[#333846] px-3 py-2.5 text-xs font-bold text-slate-200 transition-all flex items-center justify-center gap-1.5">
                        <span>💡</span> <span>Layar Aktif</span>
                    </button>
                    <button id="notificationButton" type="button" class="w-full rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 px-3 py-2.5 text-xs font-bold text-red-400 transition-all flex items-center justify-center gap-1.5">
                        <span>🔔</span> <span>Notif Alarm</span>
                    </button>
                </div>
                <p id="deviceStatus" class="text-[11px] text-center font-medium text-slate-500">GPS otomatis dikirim ke posko tiap 5 detik.</p>

                {{-- 4. RECENT ASSIGNMENTS HISTORY --}}
                <div class="pt-2 border-t border-[#333846] space-y-2.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Riwayat Tugas Saya</h3>
                        <span class="rounded-full bg-[#242832] px-2 py-0.5 text-[10px] font-bold text-slate-400">{{ $recentAssignments->count() }}</span>
                    </div>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        @forelse($recentAssignments as $assignmentItem)
                            <div class="rounded-xl bg-[#242832] border border-[#333846] p-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-white truncate">{{ $assignmentItem->report->incident_type }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $assignmentItem->report->tracking_code }}</p>
                                </div>
                                <span class="shrink-0 rounded-lg px-2 py-1 text-[9px] font-black uppercase {{ in_array($assignmentItem->status, ['completed', 'cancelled'], true) ? 'bg-[#181a20] text-slate-500 border border-[#333846]' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                    {{ \App\Http\Controllers\PublicTrackingController::assignmentLabel($assignmentItem->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="rounded-xl bg-[#242832] p-3 text-xs text-center text-slate-500">Belum ada riwayat tugas.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const map = L.map('memberMap').setView([-8.586, 116.1], 13);
            TimsarMap.addTiles(map);

            let memberMarker = null;
            let memberAccuracyCircle = null;
            let reportMarker = null;
            let routeLine = null;
            let routeSignature = '';
            let latestPosition = null;
            let bestWarmupPosition = null;
            let gpsWarmupStartedAt = null;
            let gpsWarmupSamples = 0;
            let gpsReady = false;
            let watchId = null;
            let lastAssignmentId = null;
            let assignmentLoaded = false;
            let assignmentAudioContext = null;
            let wakeLock = null;
            let wakeLockWanted = false;

            const targetAccuracyMeters = 50;
            const maxAcceptedAccuracyMeters = 1500;
            const warmupMinSamples = 3;
            const warmupMaxMilliseconds = 12000;
            const gpsStatus = document.getElementById('gpsStatus');
            const gpsQualityBadge = document.getElementById('gpsQualityBadge');
            const accuracyValue = document.getElementById('accuracyValue');
            const lastSentValue = document.getElementById('lastSentValue');
            const networkStatus = document.getElementById('networkStatus');
            const deviceStatus = document.getElementById('deviceStatus');
            const notificationButton = document.getElementById('notificationButton');
            const wakeLockButton = document.getElementById('wakeLockButton');

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

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function updateNetworkUi() {
                networkStatus.textContent = networkType();
            }

            function gpsQuality(pos) {
                if (!pos) return ['Menunggu', 'bg-slate-200 text-slate-700'];
                if (pos.coords.accuracy <= targetAccuracyMeters) return ['Akurat', 'bg-emerald-100 text-emerald-700'];
                if (pos.coords.accuracy <= maxAcceptedAccuracyMeters) return ['Cukup', 'bg-amber-100 text-amber-700'];
                return ['Rendah', 'bg-red-100 text-red-700'];
            }

            function updateGpsUi(message, pos = latestPosition) {
                gpsStatus.textContent = message;
                const [label, className] = gpsQuality(pos);
                gpsQualityBadge.textContent = label;
                gpsQualityBadge.className = `rounded-full px-3 py-1 text-xs font-black ${className}`;
                accuracyValue.textContent = pos ? `${Math.round(pos.coords.accuracy)} m` : '-';
                updateNetworkUi();
            }

            function updateMemberMarker(pos) {
                const point = [pos.coords.latitude, pos.coords.longitude];
                if (!memberMarker) {
                    memberMarker = L.marker(point, { icon: TimsarMap.icon('member') }).addTo(map).bindPopup('<strong>Posisi saya</strong><br><span class="text-xs text-slate-500">GPS aktif</span>');
                } else {
                    TimsarMap.moveMarker(memberMarker, point);
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
                updateMemberMarker(pos);
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
                    updateGpsUi(`Mengunci GPS awal (${Math.min(gpsWarmupSamples, warmupMinSamples)}/${warmupMinSamples})`, bestWarmupPosition);

                    if (!enoughSamples && !goodEarlyLock && !timeoutReached) {
                        return;
                    }

                    acceptGpsPosition(bestWarmupPosition, `GPS aktif - akurasi ${Math.round(bestWarmupPosition.coords.accuracy)} m`);
                    return;
                }

                if (
                    latestPosition &&
                    pos.coords.accuracy > maxAcceptedAccuracyMeters &&
                    pos.coords.accuracy > latestPosition.coords.accuracy
                ) {
                    updateGpsUi(`GPS melemah (${Math.round(pos.coords.accuracy)} m), memakai titik terbaik sebelumnya.`, latestPosition);
                    return;
                }

                if (
                    latestPosition &&
                    pos.coords.accuracy > latestPosition.coords.accuracy * 1.8 &&
                    distanceMeters(latestPosition, pos) < pos.coords.accuracy
                ) {
                    updateGpsUi(`Titik kasar diabaikan (${Math.round(pos.coords.accuracy)} m).`, latestPosition);
                    return;
                }

                acceptGpsPosition(pos, `GPS aktif - akurasi ${Math.round(pos.coords.accuracy)} m`);
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
                    updateGpsUi('Menunggu GPS mengunci titik terbaik...', bestWarmupPosition);
                    return;
                }

                const pos = latestPosition;
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
                            updateGpsUi(`Terkirim, posisi live ditahan agar peta stabil (${Math.round(pos.coords.accuracy)} m)`, stablePosition ?? pos);
                            return;
                        }
                        updateGpsUi(`Terkirim - akurasi ${Math.round(pos.coords.accuracy)} m`, pos);
                    }
                } catch (error) {
                    updateGpsUi('Lokasi belum terkirim. Periksa koneksi internet.', pos);
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
                    return 'Izin lokasi ditolak. Izinkan lokasi presisi untuk situs TIMSAR.';
                }
                if (error.code === error.POSITION_UNAVAILABLE) {
                    return 'Lokasi belum tersedia. Pastikan GPS HP aktif dan mode presisi menyala.';
                }
                if (error.code === error.TIMEOUT) {
                    return 'GPS terlalu lama merespons. Coba di area lebih terbuka.';
                }

                return 'Gagal mengambil GPS. Pastikan GPS dan izin lokasi browser aktif.';
            }

            function geometryToLatLngs(geometry) {
                if (!geometry || !geometry.coordinates) return [];
                return geometry.coordinates.map((point) => [point[1], point[0]]);
            }

            function updateNotificationUi() {
                if (!('Notification' in window)) {
                    notificationButton.disabled = true;
                    notificationButton.textContent = 'Notifikasi tidak didukung';
                    return;
                }

                if (Notification.permission === 'granted') {
                    notificationButton.disabled = false;
                    notificationButton.textContent = 'Notifikasi tugas aktif';
                    notificationButton.className = 'w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700';
                    return;
                }

                if (Notification.permission === 'denied') {
                    notificationButton.disabled = true;
                    notificationButton.textContent = 'Notifikasi diblokir';
                    return;
                }

                notificationButton.disabled = false;
                notificationButton.textContent = 'Aktifkan notifikasi tugas';
            }

            notificationButton.addEventListener('click', async () => {
                unlockAssignmentAudio();
                if ('Notification' in window) {
                    await Notification.requestPermission();
                }
                updateNotificationUi();
            });

            function unlockAssignmentAudio() {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;

                assignmentAudioContext ??= new AudioContext();
                if (assignmentAudioContext.state === 'suspended') {
                    assignmentAudioContext.resume();
                }
            }

            function playAssignmentTone() {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;

                const context = assignmentAudioContext || new AudioContext();
                if (context.state === 'suspended') {
                    context.resume();
                }

                const oscillator = context.createOscillator();
                const gain = context.createGain();
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, context.currentTime);
                oscillator.frequency.setValueAtTime(660, context.currentTime + 0.18);
                gain.gain.setValueAtTime(0.001, context.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.25, context.currentTime + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.45);
                oscillator.connect(gain);
                gain.connect(context.destination);
                oscillator.start();
                oscillator.stop(context.currentTime + 0.5);
            }

            function notifyNewAssignment(assignment) {
                document.title = 'Tugas baru - TIMSAR';
                if ('vibrate' in navigator) {
                    navigator.vibrate([250, 120, 250]);
                }

                playAssignmentTone();

                if ('Notification' in window && Notification.permission === 'granted') {
                    const notification = new Notification('Tugas TIMSAR baru', {
                        body: `${assignment.report.incident_type}. Tekan untuk membuka tugas.`,
                        tag: `assignment-${assignment.id}`,
                        requireInteraction: true,
                    });

                    notification.onclick = () => {
                        window.focus();
                        window.location.href = `/member/assignments/${assignment.id}`;
                    };
                }
            }

            function updateWakeLockUi() {
                if (!('wakeLock' in navigator)) {
                    wakeLockButton.disabled = true;
                    wakeLockButton.textContent = 'Layar aktif tidak didukung';
                    deviceStatus.textContent = 'Browser ini belum mendukung fitur layar tetap aktif.';
                    return;
                }

                wakeLockButton.disabled = false;
                if (wakeLock) {
                    wakeLockButton.textContent = 'Layar tetap aktif';
                    wakeLockButton.className = 'w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700';
                    deviceStatus.textContent = 'Layar dijaga tetap aktif selama halaman ini terbuka.';
                } else {
                    wakeLockButton.textContent = 'Jaga layar tetap aktif';
                    wakeLockButton.className = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-black text-slate-800';
                    deviceStatus.textContent = 'GPS dikirim tiap 5 detik. Saat bertugas, aktifkan layar tetap aktif.';
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
                    deviceStatus.textContent = 'Layar tetap aktif gagal. Pastikan browser memakai HTTPS dan baterai tidak hemat daya.';
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

            wakeLockButton.addEventListener('click', () => {
                if (wakeLock) {
                    releaseWakeLock();
                    return;
                }
                requestWakeLock();
            });

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible' && wakeLockWanted && !wakeLock) {
                    requestWakeLock();
                }
            });

            async function refreshAssignment() {
                const res = await fetch('{{ route('member.active-assignment') }}', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;

                const data = await res.json();
                const assignment = data.assignment;

                if (!assignment) {
                    lastAssignmentId = null;
                    assignmentLoaded = true;
                    const dutyBadge = document.getElementById('dutyStateBadge');
                    if (dutyBadge) {
                        dutyBadge.textContent = 'Standby posko';
                        dutyBadge.className = 'rounded-full bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-700';
                    }
                    const assignmentPanel = document.getElementById('assignmentPanel');
                    assignmentPanel.className = 'rounded-2xl border border-emerald-200 bg-emerald-50/40 p-5 shadow-sm';
                    assignmentPanel.innerHTML = `
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                            <div class="max-w-3xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">Standby posko</span>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-700">Belum ada tugas aktif</span>
                                </div>
                                <h2 class="mt-2 text-2xl font-black">Tidak sedang bertugas</h2>
                                <p class="mt-1 text-sm font-semibold text-slate-600">Tetap biarkan halaman ini terbuka agar GPS dan status online terkirim ke posko.</p>
                            </div>
                        </div>
                    `;
                    if (routeLine) {
                        routeLine.remove();
                        routeLine = null;
                    }
                    routeSignature = '';
                    if (reportMarker) {
                        reportMarker.remove();
                        reportMarker = null;
                    }
                    document.getElementById('routeMeta').textContent = 'Belum ada tugas aktif.';
                    return;
                }

                if (assignmentLoaded && lastAssignmentId !== assignment.id) {
                    notifyNewAssignment(assignment);
                    routeSignature = '';
                }
                lastAssignmentId = assignment.id;
                assignmentLoaded = true;

                const dutyBadge = document.getElementById('dutyStateBadge');
                if (dutyBadge) {
                    dutyBadge.textContent = 'Sedang bertugas';
                    dutyBadge.className = 'rounded-full bg-red-100 px-4 py-2 text-sm font-black text-red-700';
                }
                const assignmentPanel = document.getElementById('assignmentPanel');
                assignmentPanel.className = 'rounded-2xl border border-red-200 bg-red-50/40 p-5 shadow-sm';
                assignmentPanel.innerHTML = `
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">Sedang bertugas</span>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-wide text-red-700">${escapeHtml(assignment.status_label)}</span>
                            </div>
                            <h2 class="mt-1 text-2xl font-black">${escapeHtml(assignment.report.incident_type)}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-600">${escapeHtml(assignment.report.tracking_code)} - buka mode tugas untuk navigasi dan update status lapangan.</p>
                        </div>
                        <a href="/member/assignments/${assignment.id}" class="rounded-xl bg-red-600 px-5 py-3 text-center font-black text-white">Buka mode tugas</a>
                    </div>
                `;

                const reportPoint = [assignment.report.latitude, assignment.report.longitude];
                if (!reportMarker) {
                    reportMarker = L.marker(reportPoint, { icon: TimsarMap.icon('incident') }).addTo(map).bindPopup('<strong>Lokasi kejadian</strong>');
                } else {
                    reportMarker.setLatLng(reportPoint);
                }

                const latLngs = geometryToLatLngs(assignment.route_geometry);
                if (latLngs.length) {
                    const nextSignature = JSON.stringify(assignment.route_geometry?.coordinates ?? []);
                    if (nextSignature !== routeSignature) {
                        routeSignature = nextSignature;
                        if (!routeLine) {
                            routeLine = L.polyline(latLngs, TimsarMap.routeOptions()).addTo(map);
                            map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });
                        } else {
                            routeLine.setLatLngs(latLngs);
                        }
                    }
                }
                const distance = assignment.distance_meters ? (assignment.distance_meters / 1000).toFixed(2) + ' km' : '-';
                const duration = assignment.duration_seconds ? Math.round(assignment.duration_seconds / 60) + ' menit' : '-';
                document.getElementById('routeMeta').textContent = `Jarak ${distance}, estimasi ${duration}.`;
            }

            startLocationWatch();
            sendHeartbeat();
            sendLocation();
            refreshAssignment();
            updateNotificationUi();
            updateWakeLockUi();
            setInterval(sendHeartbeat, 10000);
            setInterval(sendLocation, 5000);
            setInterval(refreshAssignment, 3000);
        </script>
    @endpush
</x-layouts.app>
