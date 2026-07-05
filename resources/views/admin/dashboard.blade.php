<x-layouts.app title="Dashboard Admin TIMSAR">

    <section class="space-y-6 mx-auto max-w-7xl px-2 sm:px-4 py-4">
        
        {{-- ── TACTICAL COMMAND HEADER ── --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center pb-5 border-b border-slate-200/80">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-black uppercase tracking-wider text-red-700 border border-red-200 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                    <span>PUSAT KENDALI OPERASI (PUSKO) v3.0</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 mt-2">Dashboard Komando Realtime</h1>
                <p class="text-xs sm:text-sm text-slate-600 font-semibold mt-1">Sistem monitoring laporan darurat dan tracking koordinat pergerakan tim rescue secara langsung.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-slate-300 bg-white hover:border-orange-500 hover:text-orange-600 px-4 py-2.5 text-xs font-black text-slate-700 shadow-sm transition-all">
                    📋 Riwayat Laporan
                </a>
                <button id="adminNotificationButton" type="button" class="rounded-xl border border-slate-300 bg-white hover:border-orange-500 hover:text-orange-600 px-4 py-2.5 text-xs font-black text-slate-700 shadow-sm transition-all flex items-center gap-2">
                    <span>🔔 Aktifkan Suara Alarm</span>
                </button>
                <button id="stopAdminAlarmButton" type="button" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-black text-red-700 shadow-md shadow-red-500/10 transition-all hover:bg-red-600 hover:text-white animate-bounce">
                    🚨 Hentikan Alarm
                </button>
            </div>
        </div>

        {{-- ── DIGITAL STATS BAR (Tactical HUD Cards) ── --}}
        <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-red-500/50 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-red-50 blur-2xl group-hover:bg-red-100 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Laporan Baru</span>
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-red-50 text-red-600 font-black text-xs border border-red-200 shadow-sm">🚨</span>
                </div>
                <p class="text-3xl sm:text-5xl font-black text-slate-900 mt-2 font-mono tracking-tight">{{ $stats['new'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md">Butuh Tindakan Segera</span>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-amber-500/50 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-amber-50 blur-2xl group-hover:bg-amber-100 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Sedang Ditangani</span>
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-amber-50 text-amber-600 font-black text-xs border border-amber-200 shadow-sm">⚡</span>
                </div>
                <p class="text-3xl sm:text-5xl font-black text-slate-900 mt-2 font-mono tracking-tight">{{ $stats['active'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">Operasi Berjalan</span>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/50 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-50 blur-2xl group-hover:bg-emerald-100 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Anggota Online</span>
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-50 text-emerald-600 font-black text-xs border border-emerald-200 shadow-sm">📡</span>
                </div>
                <p class="text-3xl sm:text-5xl font-black text-slate-900 mt-2 font-mono tracking-tight">{{ $stats['members_online'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md flex items-center gap-1.5 w-max"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Unit Siaga</span>
            </div>

            <a href="{{ route('admin.reports.index', ['status' => 'completed']) }}" class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-blue-500/50 transition-all block">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-blue-50 blur-2xl group-hover:bg-blue-100 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Selesai Hari Ini</span>
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-blue-50 text-blue-600 font-black text-xs border border-blue-200 shadow-sm">🛡️</span>
                </div>
                <p class="text-3xl sm:text-5xl font-black text-slate-900 mt-2 font-mono tracking-tight">{{ $stats['completed_today'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">Misi Berhasil &rarr;</span>
            </a>
        </div>

        {{-- ── MAIN PANEL: MAP & TACTICAL LISTS ── --}}
        <div class="grid gap-6 lg:grid-cols-[1fr_400px]">
            
            {{-- Peta Operasional Taktis --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xl flex flex-col">
                <div class="border-b border-slate-200/80 bg-slate-50 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-orange-100 text-orange-600 font-black text-sm border border-orange-200 shadow-sm">🛰️</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Peta Taktis Operasional</h2>
                            <p id="mapMeta" class="text-xs font-semibold text-slate-500 mt-0.5">Memuat data koordinat satelit...</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">RADAR LIVE</span>
                    </div>
                </div>
                <div id="adminMap" class="h-[550px] lg:h-[700px] z-10 w-full"></div>
            </div>

            {{-- Sidebar Komando --}}
            <aside class="space-y-6 flex flex-col justify-between">
                
                {{-- Laporan Aktif --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl flex-1 flex flex-col">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200/80 pb-4">
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Daftar Laporan Aktif</h2>
                            <p class="text-xs font-semibold text-slate-500">Insiden membutuhkan penanganan</p>
                        </div>
                        <span id="reportsCount" class="rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-black text-red-700 font-mono shadow-sm">{{ $reports->count() }}</span>
                    </div>
                    <div id="activeReportsList" class="mt-4 space-y-3 max-h-[260px] lg:max-h-[300px] overflow-y-auto pr-1">
                        @forelse($reports as $report)
                            @php
                                $priorityClass = match ($report->priority) {
                                    'critical' => 'bg-red-50 text-red-700 border-red-200 animate-pulse',
                                    'high' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                $priorityBorder = match ($report->priority) {
                                    'critical' => 'border-l-red-500',
                                    'high' => 'border-l-orange-500',
                                    'medium' => 'border-l-amber-500',
                                    default => 'border-l-slate-400',
                                };
                            @endphp
                            <a href="{{ route('admin.reports.show', $report) }}" class="block rounded-2xl border border-slate-200/80 border-l-4 {{ $priorityBorder }} p-4 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition-all shadow-sm group">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-black text-slate-900 text-sm sm:text-base leading-tight group-hover:text-orange-600 transition-colors">{{ $report->incident_type }}</p>
                                        <p class="text-[11px] text-slate-500 mt-1 font-mono">{{ $report->tracking_code }} • {{ $report->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider border {{ $priorityClass }}">{{ $report->priority }}</span>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-xs border-t border-slate-200/80 pt-2.5 text-slate-600">
                                    <div>Status: <span class="font-black text-slate-800">{{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}</span></div>
                                    <div>Petugas: <span class="font-black text-orange-600">{{ $report->assignedMember?->name ?? 'Belum' }}</span></div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-6 text-center shadow-sm">
                                <span class="block text-2xl mb-1">✅</span>
                                <p class="text-xs font-bold text-slate-500">Semua insiden telah terkendali. Belum ada laporan aktif baru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Anggota Lapangan --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl flex-1 flex flex-col">
                    <div class="border-b border-slate-200/80 pb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Unit Rescue TIMSAR</h2>
                            <p class="text-xs font-semibold text-slate-500">Monitoring konektivitas petugas lapangan</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-500">{{ $members->count() }} Personel</span>
                    </div>
                    <div class="mt-4 space-y-3 max-h-[280px] lg:max-h-[340px] overflow-y-auto pr-1">
                        @foreach($members as $member)
                            @php
                                $isOnline = $member->memberLocation?->last_seen_at?->gt(now()->subSeconds(90));
                            @endphp
                            <div class="rounded-2xl border border-slate-200/80 p-4 bg-slate-50 hover:bg-slate-100 transition-all shadow-sm">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-black text-slate-900 text-sm sm:text-base leading-tight flex items-center gap-2">
                                        <span>🧑‍🚒</span>
                                        <span>{{ $member->name }}</span>
                                    </p>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider border {{ $isOnline ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-200 text-slate-600 border-slate-300' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500' }}"></span>
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-slate-500 font-mono">
                                    <span>{{ $member->phone }}</span>
                                    <span class="bg-slate-200 px-2 py-0.5 rounded text-[10px] uppercase text-slate-700">{{ $member->memberLocation?->network_type ?? 'offline' }}</span>
                                </div>
                                <p class="mt-2 text-[11px] font-semibold text-slate-500 border-t border-slate-200/80 pt-2 flex items-center justify-between">
                                    <span>Posisi Terakhir:</span>
                                    <span class="text-slate-600 font-mono">{{ $member->memberLocation?->last_seen_at?->diffForHumans() ?? '-' }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

            </aside>
        </div>
    </section>

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
                    activeReportsList.innerHTML = '<p class="rounded bg-slate-50 p-4 text-xs text-slate-500 text-center">Belum ada laporan aktif.</p>';
                    return;
                }

                activeReportsList.innerHTML = reports.slice(0, 20).map((report) => {
                    const priorityBorder = {
                        critical: 'border-l-red-500',
                        high: 'border-l-orange-500',
                        medium: 'border-l-amber-500',
                        low: 'border-l-slate-400',
                    }[report.priority] || 'border-l-slate-300';

                    const priorityLabelClass = {
                        critical: 'bg-red-50 text-red-700 border-red-100',
                        high: 'bg-orange-50 text-orange-700 border-orange-100',
                        medium: 'bg-amber-50 text-amber-700 border-amber-100',
                    }[report.priority] || 'bg-slate-50 text-slate-700 border-slate-100';

                    return `
                        <a href="${report.url}" data-report-id="${report.id}" data-report-alarm-stop class="block rounded border border-slate-200 border-l-4 ${priorityBorder} p-3.5 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-bold text-slate-900 text-sm sm:text-base leading-tight">${escapeHtml(report.incident_type)}</p>
                                    <p class="text-xs text-slate-500 mt-1 font-mono">${escapeHtml(report.tracking_code)} - ${formatReportTime(report.created_at)}</p>
                                </div>
                                <span class="rounded px-2 py-0.5 text-xs font-extrabold uppercase tracking-wide ${priorityLabelClass}">${escapeHtml(report.priority).toUpperCase()}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs border-t border-slate-100 pt-2 text-slate-600">
                                <div>Status: <span class="font-bold text-slate-800">${escapeHtml(report.status_label)}</span></div>
                                <div>Petugas: <span class="font-bold text-slate-800">${escapeHtml(report.assigned_member || 'Belum')}</span></div>
                            </div>
                        </a>
                    `;
                }).join('');
            }

            function updateNotificationUi() {
                if (!('Notification' in window)) {
                    notificationButton.disabled = true;
                    notificationButton.textContent = 'Notifikasi tidak didukung';
                    return;
                }

                if (Notification.permission === 'granted') {
                    notificationButton.disabled = false;
                    notificationButton.textContent = 'Notifikasi aktif';
                    notificationButton.className = 'rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-2.5 text-xs font-bold text-emerald-650 hover:bg-emerald-500/20 transition-colors';
                    return;
                }

                if (Notification.permission === 'denied') {
                    notificationButton.disabled = true;
                    notificationButton.textContent = 'Notifikasi diblokir';
                    notificationButton.className = 'rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-2.5 text-xs font-bold text-red-500 cursor-not-allowed';
                    return;
                }

                notificationButton.disabled = false;
                notificationButton.textContent = 'Aktifkan suara alarm';
                notificationButton.className = 'rounded-lg border border-slate-300 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition-colors';
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
