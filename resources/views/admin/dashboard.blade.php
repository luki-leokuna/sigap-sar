<x-layouts.app title="PUSKO Command Center - TIMSAR" :hideChrome="true" :fullBleed="true">
    @push('scripts')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            body, .timsar-maxim-admin {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
                overflow: hidden;
            }
            /* Map contrast in dark mode */
            #adminMap {
                filter: contrast(1.05) saturate(1.1);
            }
            /* Custom dark scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: #181a20; }
            ::-webkit-scrollbar-thumb { background: #333846; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #4b5265; }
        </style>
    @endpush

    <div class="timsar-maxim-admin h-screen w-screen overflow-hidden bg-[#181a20] flex flex-col">
        
        {{-- ── TOP TACTICAL COMMAND BAR (HEADER) ── --}}
        <header class="bg-[#1e222b] border-b border-[#333846] px-4 sm:px-6 py-3 shrink-0 flex items-center justify-between gap-4 shadow-xl z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-orange-600 via-amber-600 to-red-600 text-white font-black text-lg shadow-lg shadow-orange-500/20 border border-orange-400/30">
                    SG
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/20 border border-red-500/30 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-red-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-ping"></span> PUSKO RADAR v3.0
                        </span>
                        <h1 class="text-base sm:text-lg font-black text-white truncate">Pusat Kendali & Operasi SAR</h1>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 hidden sm:block truncate mt-0.5">Sistem monitoring laporan darurat & tracking koordinat armada realtime</p>
                </div>
            </div>

            {{-- HUD Stats Pills --}}
            <div class="hidden xl:flex items-center gap-2.5">
                <div class="bg-[#242832] border border-[#333846] px-3.5 py-1.5 rounded-xl flex items-center gap-2.5">
                    <span class="text-xs font-bold text-slate-400">🚨 Laporan Baru:</span>
                    <span class="font-mono font-black text-red-400 text-sm">{{ $stats['new'] }}</span>
                </div>
                <div class="bg-[#242832] border border-[#333846] px-3.5 py-1.5 rounded-xl flex items-center gap-2.5">
                    <span class="text-xs font-bold text-slate-400">⚡ Operasi Aktif:</span>
                    <span class="font-mono font-black text-amber-400 text-sm">{{ $stats['active'] }}</span>
                </div>
                <div class="bg-[#242832] border border-[#333846] px-3.5 py-1.5 rounded-xl flex items-center gap-2.5">
                    <span class="text-xs font-bold text-slate-400">📡 Armada Siaga:</span>
                    <span class="font-mono font-black text-emerald-400 text-sm">{{ $stats['members_online'] }}</span>
                </div>
                <a href="{{ route('admin.reports.index', ['status' => 'completed']) }}" class="bg-[#242832] hover:bg-[#2c303d] border border-[#333846] px-3.5 py-1.5 rounded-xl flex items-center gap-2.5 transition-all">
                    <span class="text-xs font-bold text-slate-400">🛡️ Selesai:</span>
                    <span class="font-mono font-black text-blue-400 text-sm">{{ $stats['completed_today'] }}</span>
                </a>
            </div>

            {{-- Actions & Alarm Controls --}}
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.reports.index') }}" class="rounded-xl bg-[#242832] hover:bg-[#2c303d] border border-[#333846] px-3.5 py-2 text-xs font-bold text-slate-200 transition-all">
                    📋 Riwayat
                </a>
                <button id="adminNotificationButton" type="button" class="rounded-xl bg-[#242832] hover:bg-[#2c303d] border border-[#333846] px-3.5 py-2 text-xs font-bold text-slate-200 transition-all flex items-center gap-1.5">
                    <span>🔔</span> <span>Alarm</span>
                </button>
                <button id="stopAdminAlarmButton" type="button" class="hidden rounded-xl bg-red-600 hover:bg-red-700 border border-red-500 px-3.5 py-2 text-xs font-black text-white shadow-lg shadow-red-600/30 transition-all animate-bounce">
                    🚨 Hentikan Alarm
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-xl bg-[#242832] hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-[#333846] transition-all" title="Keluar dari Komando">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </header>

        {{-- ── MAIN SPLIT-SCREEN TACTICAL COMMAND STATION ── --}}
        <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 overflow-hidden relative">
            
            {{-- LEFT PANE (65% / 8 Cols): Peta Komando Raksasa --}}
            <div class="lg:col-span-8 h-full relative overflow-hidden flex flex-col border-b lg:border-b-0 lg:border-r border-[#333846] bg-[#181a20]">
                <div class="bg-[#1e222b] px-4 py-2 border-b border-[#333846] flex items-center justify-between text-xs shrink-0 z-10">
                    <div class="flex items-center gap-2">
                        <span class="grid h-6 w-6 place-items-center rounded-lg bg-orange-500/20 text-orange-400 font-black text-xs border border-orange-500/30">🛰️</span>
                        <span class="font-black text-slate-200 uppercase tracking-wider">Peta Operasional Taktis</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="mapMeta" class="text-[11px] font-semibold text-slate-400">Memuat radar satelit...</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 px-2.5 py-0.5 text-[10px] font-black text-emerald-400 uppercase">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span> RADAR LIVE
                        </span>
                    </div>
                </div>
                <div id="adminMap" class="flex-1 w-full z-0"></div>
            </div>

            {{-- RIGHT PANE (35% / 4 Cols): Sidebar Komando Live --}}
            <aside class="lg:col-span-4 h-full bg-[#1e222b] flex flex-col justify-between overflow-y-auto">
                
                {{-- SECTION 1: Laporan Darurat Aktif (Live Feed) --}}
                <div class="p-4 sm:p-5 flex-1 flex flex-col border-b border-[#333846]">
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#333846] shrink-0">
                        <div>
                            <h2 class="text-xs font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>🚨</span> <span>Live Feed Darurat</span>
                            </h2>
                            <p class="text-[11px] font-medium text-slate-400 mt-0.5">Insiden aktif membutuhkan respons</p>
                        </div>
                        <span id="reportsCount" class="rounded-xl bg-red-500/20 border border-red-500/40 px-3 py-1 text-xs font-black text-red-400 font-mono shadow-sm">{{ $reports->count() }}</span>
                    </div>
                    
                    <div id="activeReportsList" class="mt-3 space-y-2.5 flex-1 overflow-y-auto pr-1">
                        @forelse($reports as $report)
                            @php
                                $priorityClass = match ($report->priority) {
                                    'critical' => 'bg-red-500/20 text-red-300 border-red-500/40 animate-pulse',
                                    'high' => 'bg-orange-500/20 text-orange-300 border-orange-500/40',
                                    'medium' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                                    default => 'bg-[#181a20] text-slate-300 border-[#333846]',
                                };
                                $priorityBorder = match ($report->priority) {
                                    'critical' => 'border-l-red-500',
                                    'high' => 'border-l-orange-500',
                                    'medium' => 'border-l-amber-500',
                                    default => 'border-l-slate-500',
                                };
                            @endphp
                            <a href="{{ route('admin.reports.show', $report) }}" class="block rounded-2xl bg-[#242832] border border-[#333846] border-l-4 {{ $priorityBorder }} p-3.5 hover:border-orange-500 hover:bg-[#2c303d] transition-all shadow-md group">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="font-bold text-white text-sm leading-tight group-hover:text-orange-400 transition-colors truncate">{{ $report->incident_type }}</p>
                                        <p class="text-[11px] text-slate-400 mt-1 font-mono">{{ $report->tracking_code }} • {{ $report->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-md px-2 py-0.5 text-[9px] font-black uppercase tracking-wider border {{ $priorityClass }}">{{ $report->priority }}</span>
                                </div>
                                <div class="mt-2.5 flex items-center justify-between text-[11px] border-t border-[#333846] pt-2 text-slate-400 font-semibold">
                                    <div>Status: <span class="font-bold text-slate-200">{{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}</span></div>
                                    <div>Petugas: <span class="font-bold text-orange-400">{{ $report->assignedMember?->name ?? 'Belum' }}</span></div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-[#333846] bg-[#242832] p-6 text-center shadow-sm my-auto">
                                <span class="block text-2xl mb-1">✅</span>
                                <p class="text-xs font-bold text-slate-400">Semua insiden terkendali. Belum ada laporan aktif baru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- SECTION 2: Radar Unit Rescue & Armada (Rescuers) --}}
                <div class="p-4 sm:p-5 flex-1 flex flex-col max-h-[45vh]">
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#333846] shrink-0">
                        <div>
                            <h2 class="text-xs font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>🟢</span> <span>Radar Armada TIMSAR</span>
                            </h2>
                            <p class="text-[11px] font-medium text-slate-400 mt-0.5">Monitoring konektivitas petugas lapangan</p>
                        </div>
                        <span class="rounded-xl bg-[#242832] border border-[#333846] px-2.5 py-1 text-xs font-mono font-bold text-slate-300">{{ $members->count() }} Unit</span>
                    </div>
                    
                    <div class="mt-3 space-y-2 overflow-y-auto pr-1 flex-1">
                        @foreach($members as $member)
                            @php
                                $isOnline = $member->memberLocation?->last_seen_at?->gt(now()->subSeconds(90));
                            @endphp
                            <div class="rounded-2xl bg-[#242832] border border-[#333846] p-3 hover:border-slate-500 transition-all shadow-sm">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-white text-xs sm:text-sm leading-tight flex items-center gap-2 truncate">
                                        <span>🧑‍🚒</span>
                                        <span class="truncate">{{ $member->name }}</span>
                                    </p>
                                    <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[9px] font-black uppercase tracking-wider border {{ $isOnline ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-[#181a20] text-slate-400 border-[#333846]' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500' }}"></span>
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400 font-mono">
                                    <span>{{ $member->phone }}</span>
                                    <span class="bg-[#181a20] border border-[#333846] px-2 py-0.5 rounded text-[9px] uppercase text-slate-300">{{ $member->memberLocation?->network_type ?? 'offline' }}</span>
                                </div>
                                <p class="mt-1.5 text-[10px] font-semibold text-slate-400 border-t border-[#333846] pt-1.5 flex items-center justify-between">
                                    <span>Posisi Terakhir:</span>
                                    <span class="text-slate-300 font-mono">{{ $member->memberLocation?->last_seen_at?->diffForHumans() ?? '-' }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

            </aside>
        </div>
    </div>

    @push('scripts')
        <style>
            /* Custom Leaflet styling to look modern */
            .leaflet-popup-content-wrapper {
                border-radius: 0.5rem !important;
                box-shadow: 0 4px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05) !important;
                border: 1px solid #e2e8f0 !important;
                padding: 0.25rem !important;
            }
            .leaflet-popup-content {
                font-family: inherit !important;
                font-size: 0.825rem !important;
                color: #334155 !important;
                line-height: 1.45 !important;
                margin: 0.5rem 0.75rem !important;
            }
            .leaflet-popup-tip {
                background: white !important;
                box-shadow: none !important;
            }
        </style>
        <script>
            const map = L.map('adminMap').setView([-8.586, 116.1], 12);
            TimsarMap.addTiles(map);
            let markers = [];
            let latestReportId = {{ $latestReportId }};
            const alertAudio = new Audio(@json(asset('audio/alarm-darurat.mp3')));
            alertAudio.loop = true;
            alertAudio.preload = 'auto';
            let activeAdminAlertReportId = null;
            let currentAdminAlertReportIds = [];
            let alertVibrationInterval = null;
            let alertAudioUnlocked = false;
            const notificationButton = document.getElementById('adminNotificationButton');
            const stopAlarmButton = document.getElementById('stopAdminAlarmButton');
            const activeReportsList = document.getElementById('activeReportsList');
            const reportsCount = document.getElementById('reportsCount');
            const silencedStorageKey = 'timsar_admin_silenced_report_ids';
            const silencedReportIds = new Set(storedSilencedReportIds());

            function storedSilencedReportIds() {
                try {
                    return JSON.parse(localStorage.getItem(silencedStorageKey) || '[]').map(Number).filter(Boolean);
                } catch (error) {
                    localStorage.removeItem(silencedStorageKey);
                    return [];
                }
            }

            function clearMarkers() {
                markers.forEach((marker) => marker.remove());
                markers = [];
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function priorityClass(priority) {
                if (priority === 'critical') return 'bg-red-100 text-red-700';
                if (priority === 'high') return 'bg-orange-100 text-orange-700';
                if (priority === 'medium') return 'bg-amber-100 text-amber-700';
                return 'bg-slate-100 text-slate-700';
            }

            function formatReportTime(value) {
                if (!value) return '-';
                return new Date(value).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            }

            function renderReports(reports) {
                reportsCount.textContent = reports.length;

                if (!reports.length) {
                    activeReportsList.innerHTML = '<div class="rounded-2xl border border-[#333846] bg-[#242832] p-6 text-center shadow-sm my-auto"><span class="block text-2xl mb-1">✅</span><p class="text-xs font-bold text-slate-400">Semua insiden terkendali. Belum ada laporan aktif baru.</p></div>';
                    return;
                }

                activeReportsList.innerHTML = reports.slice(0, 20).map((report) => {
                    const priorityBorder = {
                        critical: 'border-l-red-500',
                        high: 'border-l-orange-500',
                        medium: 'border-l-amber-500',
                        low: 'border-l-slate-500',
                    }[report.priority] || 'border-l-slate-500';

                    const priorityLabelClass = {
                        critical: 'bg-red-500/20 text-red-300 border-red-500/40 animate-pulse',
                        high: 'bg-orange-500/20 text-orange-300 border-orange-500/40',
                        medium: 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                    }[report.priority] || 'bg-[#181a20] text-slate-300 border-[#333846]';

                    return `
                        <a href="${report.url}" data-report-id="${report.id}" data-report-alarm-stop class="block rounded-2xl bg-[#242832] border border-[#333846] border-l-4 ${priorityBorder} p-3.5 hover:border-orange-500 hover:bg-[#2c303d] transition-all shadow-md group">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-bold text-white text-sm leading-tight group-hover:text-orange-400 transition-colors truncate">${escapeHtml(report.incident_type)}</p>
                                    <p class="text-[11px] text-slate-400 mt-1 font-mono">${escapeHtml(report.tracking_code)} • ${formatReportTime(report.created_at)}</p>
                                </div>
                                <span class="shrink-0 rounded-md px-2 py-0.5 text-[9px] font-black uppercase tracking-wider border ${priorityLabelClass}">${escapeHtml(report.priority).toUpperCase()}</span>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between text-[11px] border-t border-[#333846] pt-2 text-slate-400 font-semibold">
                                <div>Status: <span class="font-bold text-slate-200">${escapeHtml(report.status_label)}</span></div>
                                <div>Petugas: <span class="font-bold text-orange-400">${escapeHtml(report.assigned_member || 'Belum')}</span></div>
                            </div>
                        </a>
                    `;
                }).join('');
            }

            function updateNotificationUi() {
                if (!('Notification' in window)) {
                    notificationButton.disabled = true;
                    notificationButton.textContent = 'Notif Tidak Didukung';
                    return;
                }

                if (Notification.permission === 'granted') {
                    notificationButton.disabled = false;
                    notificationButton.textContent = 'Notifikasi Aktif';
                    notificationButton.className = 'rounded-xl border border-emerald-500/30 bg-emerald-500/15 px-3.5 py-2 text-xs font-bold text-emerald-400 hover:bg-emerald-500/25 transition-all flex items-center gap-1.5';
                    return;
                }

                if (Notification.permission === 'denied') {
                    notificationButton.disabled = true;
                    notificationButton.textContent = 'Notif Diblokir';
                    notificationButton.className = 'rounded-xl border border-red-500/30 bg-red-500/15 px-3.5 py-2 text-xs font-bold text-red-400 cursor-not-allowed flex items-center gap-1.5';
                    return;
                }

                notificationButton.disabled = false;
                notificationButton.textContent = 'Aktifkan Alarm';
                notificationButton.className = 'rounded-xl bg-[#242832] hover:bg-[#2c303d] border border-[#333846] px-3.5 py-2 text-xs font-bold text-slate-200 transition-all flex items-center gap-1.5';
            }

            function unlockAlertAudio() {
                if (alertAudioUnlocked) return;
                alertAudio.muted = true;
                alertAudio.play()
                    .then(() => {
                        alertAudio.pause();
                        alertAudio.currentTime = 0;
                        alertAudio.muted = false;
                        alertAudioUnlocked = true;
                    })
                    .catch(() => {
                        alertAudio.muted = false;
                    });
            }

            function saveSilencedReports() {
                localStorage.setItem(
                    silencedStorageKey,
                    JSON.stringify([...silencedReportIds].slice(-100)),
                );
            }

            function silenceReportIds(reportIds) {
                reportIds.map(Number).filter(Boolean).forEach((id) => silencedReportIds.add(id));
                saveSilencedReports();
            }

            function stopAlertAlarm(silenceCurrent = true) {
                if (silenceCurrent) {
                    silenceReportIds(currentAdminAlertReportIds.length ? currentAdminAlertReportIds : [activeAdminAlertReportId]);
                }
                alertAudio.pause();
                alertAudio.currentTime = 0;
                activeAdminAlertReportId = null;
                currentAdminAlertReportIds = [];
                if (alertVibrationInterval) {
                    window.clearInterval(alertVibrationInterval);
                    alertVibrationInterval = null;
                }
                if ('vibrate' in navigator) {
                    navigator.vibrate(0);
                }
                document.title = 'Dashboard Admin TIMSAR';
                stopAlarmButton.classList.add('hidden');
            }

            function startAlertAlarm(report, relatedReportIds = [report.id]) {
                if (activeAdminAlertReportId === report.id) {
                    currentAdminAlertReportIds = relatedReportIds;
                    return false;
                }
                stopAlertAlarm(false);
                activeAdminAlertReportId = report.id;
                currentAdminAlertReportIds = relatedReportIds;
                alertAudio.muted = false;
                alertAudio.currentTime = 0;
                alertAudio.play().catch(() => {
                    notificationButton.textContent = 'Klik aktifkan suara alarm';
                });
                if ('vibrate' in navigator) {
                    navigator.vibrate([700, 200, 700, 200, 1000]);
                    alertVibrationInterval = window.setInterval(() => {
                        navigator.vibrate([700, 200, 700, 200, 1000]);
                    }, 3200);
                }
                stopAlarmButton.classList.remove('hidden');
                return true;
            }

            notificationButton.addEventListener('click', async () => {
                unlockAlertAudio();
                if ('Notification' in window) {
                    await Notification.requestPermission();
                }
                updateNotificationUi();
            });

            stopAlarmButton.addEventListener('click', stopAlertAlarm);

            document.addEventListener('pointerdown', unlockAlertAudio, { once: true, passive: true });

            function notifyNewReport(report, relatedReportIds = [report.id]) {
                const started = startAlertAlarm(report, relatedReportIds);
                if (!started) return;

                document.title = 'Laporan baru - TIMSAR';

                if ('Notification' in window && Notification.permission === 'granted') {
                    const notification = new Notification('Laporan darurat baru', {
                        body: `${report.incident_type} - ${report.tracking_code}`,
                        tag: `report-${report.id}`,
                        requireInteraction: true,
                    });

                    notification.onclick = () => {
                        stopAlertAlarm();
                        window.focus();
                        window.location.href = report.url;
                    };
                }
            }

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[data-report-alarm-stop], a[href*="/admin/reports/"]');
                if (!link) return;
                const reportId = Number(link.dataset.reportId || link.href.match(/\/admin\/reports\/(\d+)/)?.[1] || 0);
                if (reportId) silenceReportIds([reportId]);
                stopAlertAlarm(false);
            });

            async function refreshMap() {
                const res = await fetch('{{ route('admin.map-data') }}');
                if (!res.ok) return;

                const data = await res.json();
                clearMarkers();
                renderReports(data.reports);
                if (
                    activeAdminAlertReportId &&
                    !data.reports.some((report) => report.id === activeAdminAlertReportId)
                ) {
                    stopAlertAlarm(false);
                }

                const alertableReports = data.reports
                    .filter((report) => report.status === 'new' && !silencedReportIds.has(Number(report.id)))
                    .sort((a, b) => b.id - a.id)[0];

                if (alertableReports) {
                    const relatedReportIds = data.reports
                        .filter((report) => report.status === 'new' && !silencedReportIds.has(Number(report.id)))
                        .map((report) => Number(report.id));
                    notifyNewReport(alertableReports, relatedReportIds);
                } else if (activeAdminAlertReportId) {
                    stopAlertAlarm(false);
                }

                latestReportId = Math.max(latestReportId, data.latest_report_id || 0);

                data.reports.forEach((report) => {
                    const marker = L.marker([report.latitude, report.longitude], {
                        icon: TimsarMap.icon('incident', { pulse: report.priority === 'critical' }),
                    }).addTo(map)
                        .bindPopup(`
                            <div class="space-y-1">
                                <div class="font-bold text-slate-900 text-sm">${escapeHtml(report.incident_type)}</div>
                                <div class="text-xs uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-red-50 text-red-700 inline-block font-mono">${escapeHtml(report.status_label)}</div>
                                <div class="text-xs text-slate-500 mt-1">Petugas: <span class="font-semibold text-slate-700">${escapeHtml(report.assigned_member || 'Belum ditugaskan')}</span></div>
                                <div class="pt-1.5 border-t border-slate-100 mt-1.5">
                                    <a href="${report.url}" data-report-id="${report.id}" data-report-alarm-stop class="text-xs font-bold text-red-600 hover:text-red-700 inline-flex items-center gap-0.5">Lihat Detail Laporan &rarr;</a>
                                </div>
                            </div>
                        `);
                    markers.push(marker);
                });

                data.members.forEach((member) => {
                    const marker = L.marker([member.latitude, member.longitude], {
                        icon: TimsarMap.icon('member', { pulse: member.is_online, offline: !member.is_online }),
                    }).addTo(map).bindPopup(`
                        <div class="space-y-1">
                            <div class="font-bold text-slate-900 text-sm">${member.name}</div>
                            <div class="text-xs uppercase font-bold tracking-wider px-1.5 py-0.5 rounded inline-block ${member.is_online ? 'bg-emerald-50 text-emerald-700 font-mono' : 'bg-slate-50 text-slate-500 font-mono'}">
                                ${member.is_online ? 'Online' : 'Offline'}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">Jaringan: <span class="font-semibold text-slate-700">${member.network_type}</span></div>
                        </div>
                    `);
                    markers.push(marker);
                });

                document.getElementById('mapMeta').textContent = `${data.reports.length} aktif, ${data.members.length} petugas. Update ${new Date().toLocaleTimeString('id-ID')}`;
            }

            updateNotificationUi();
            refreshMap();
            setInterval(refreshMap, 3000);
        </script>
    @endpush
</x-layouts.app>
