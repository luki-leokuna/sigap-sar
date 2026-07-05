<x-layouts.app title="Lacak {{ $report->tracking_code }}">
    <section class="space-y-6 mx-auto max-w-7xl px-2 sm:px-4 py-4">
        {{-- Status Header Panel --}}
        <div id="statusPanel" class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xl">
            <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between border-b border-slate-200/80">
                <div class="min-w-0 space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <span id="statusBadge" class="rounded-full bg-slate-100 px-3.5 py-1 text-xs font-black uppercase text-slate-700 border border-slate-300">Memuat status</span>
                        <span id="liveIndicator" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Menghubungkan Sinyal
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-black leading-tight text-slate-900 tracking-tight">{{ $report->incident_type }}</h1>
                    <p id="statusMessage" class="max-w-3xl text-sm font-semibold leading-relaxed text-slate-600">Memeriksa perkembangan laporan Anda pada jaringan komando SAR...</p>
                </div>

                <div class="shrink-0 rounded-2xl border border-slate-200/80 bg-slate-50 p-4 shadow-sm flex items-center justify-between lg:flex-col lg:items-end gap-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Kode Tracking Operasi</p>
                    <div class="flex items-center gap-3">
                        <p class="font-mono text-base sm:text-lg font-black text-orange-600 tracking-wider">{{ $report->tracking_code }}</p>
                        <button id="copyTrackingButton" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-black text-slate-700 hover:border-orange-500 hover:text-orange-600 transition-all shadow-sm">Salin</button>
                    </div>
                </div>
            </div>

            {{-- Visual Timeline Steps --}}
            <div class="px-4 py-6 sm:px-8 bg-slate-50/80 border-t border-slate-200/80">
                <div class="grid grid-cols-6 gap-2" aria-label="Tahapan penanganan laporan">
                    @foreach(['Diterima', 'Petugas', 'Menuju', 'Tiba', 'Ditangani', 'Selesai'] as $index => $step)
                        <div class="min-w-0 text-center group">
                            <span data-status-step="{{ $index }}" class="mx-auto grid h-8 w-8 place-items-center rounded-xl border-2 border-slate-300 bg-white text-xs font-black text-slate-500 shadow-sm transition-all">{{ $index + 1 }}</span>
                            <p data-status-step-label="{{ $index }}" class="mt-2 truncate text-[10px] font-bold text-slate-500 sm:text-xs transition-all">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="closurePanel" class="hidden rounded-2xl border p-5 shadow-xl backdrop-blur-xl" role="status">
            <p id="closureTitle" class="font-black text-lg"></p>
            <p id="closureText" class="mt-1 text-sm font-semibold opacity-90"></p>
        </div>

        {{-- Map & Rescue Team Detail Grid --}}
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xl flex flex-col">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200/80 px-6 py-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Radar Pemantauan Taktis</h2>
                        <p id="mapStatusText" class="text-xs font-semibold text-slate-500">Menunggu transmisi posisi petugas rescue.</p>
                    </div>
                    <button id="focusMapButton" type="button" class="shrink-0 rounded-xl border border-slate-300 bg-slate-50 px-4 py-2 text-xs font-black text-slate-700 hover:border-orange-500 hover:text-orange-600 transition-all shadow-sm">Pusatkan Radar</button>
                </div>
                <div class="relative flex-1">
                    <div id="map" class="h-[55vh] min-h-[400px] max-h-[680px] w-full"></div>
                    <div class="pointer-events-none absolute bottom-4 left-4 z-[500] rounded-2xl border border-slate-200/80 bg-white/95 px-4 py-2.5 text-xs font-extrabold text-slate-700 shadow-lg flex items-center gap-4">
                        <span class="inline-flex items-center gap-2"><span class="h-2 w-5 rounded-full bg-blue-500 shadow-sm shadow-blue-500/50"></span>Jalur Dilewati</span>
                        <span class="inline-flex items-center gap-2"><span class="h-2 w-5 rounded-full bg-orange-500 shadow-sm shadow-orange-500/50"></span>Rute Operasi</span>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                {{-- Rescue Member Card --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl space-y-5">
                    <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-200/80">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200">Unit Ditugaskan</span>
                            <p id="memberName" class="mt-2 text-xl font-black text-slate-900">Menunggu penugasan</p>
                            <p id="memberMeta" class="mt-1 text-xs font-semibold text-slate-500">Posko komando sedang memproses laporan.</p>
                        </div>
                        <span id="memberOnlineBadge" class="hidden rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-[10px] font-black uppercase text-emerald-700">Online</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">Jarak Tersisa</p>
                            <p id="distanceText" class="mt-1 text-xl font-black text-orange-600 font-mono">-</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">Estimasi Tiba</p>
                            <p id="durationText" class="mt-1 text-xl font-black text-emerald-600 font-mono">-</p>
                        </div>
                    </div>

                    <a id="memberCallLink" href="#" class="hidden w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3.5 text-sm font-black text-white shadow-md shadow-emerald-500/20 hover:brightness-110 transition-all">
                        <span>📞 Hubungi Petugas Lapangan</span>
                    </a>
                </div>

                {{-- Report Detail Card --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl space-y-4">
                    <p class="text-xs font-black uppercase tracking-wider text-slate-500 border-b border-slate-200/80 pb-3">Arsip Laporan Darurat</p>
                    <p class="text-sm font-semibold leading-relaxed text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 shadow-sm">{{ $report->description }}</p>
                    <div class="grid grid-cols-2 gap-3 text-xs pt-1">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 shadow-sm">
                            <p class="font-bold text-slate-500 text-[10px] uppercase">Waktu Lapor</p>
                            <p class="mt-1 font-black text-slate-900">{{ $report->created_at?->timezone('Asia/Makassar')->format('d M Y, H.i') }} WITA</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 shadow-sm">
                            <p class="font-bold text-slate-500 text-[10px] uppercase">Koordinat GPS</p>
                            <p class="mt-1 font-black text-emerald-600">Terkunci Presisi</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button id="shareTrackingButton" type="button" class="rounded-2xl border border-slate-300 bg-white px-4 py-3.5 text-xs font-black text-slate-700 hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all shadow-sm">Bagikan Status</button>
                    <a href="{{ route('public.report') }}" class="rounded-2xl bg-gradient-to-r from-orange-600 to-red-600 px-4 py-3.5 text-center text-xs font-black text-white hover:brightness-110 transition-all shadow-md shadow-orange-500/20">Buat Laporan Lain</a>
                </div>

                <details class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl group">
                    <summary class="cursor-pointer text-xs font-black text-slate-700 group-open:text-orange-600 transition-colors list-none flex items-center justify-between">
                        <span>🛰️ Log Perjalanan Petugas Rescue</span>
                        <span class="text-xs">▼</span>
                    </summary>
                    <p id="trailText" class="mt-3 text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 leading-relaxed shadow-sm">Belum ada perjalanan terekam oleh satelit komando.</p>
                </details>
            </aside>
        </div>
    </section>

    @push('scripts')
        <script>
            const trackingCode = @json($report->tracking_code);
            const reportPoint = [{{ $report->latitude }}, {{ $report->longitude }}];
            const map = L.map('map').setView(reportPoint, 15);
            TimsarMap.addTiles(map);
            new ResizeObserver(() => map.invalidateSize()).observe(map.getContainer());
            window.addEventListener('load', () => {
                map.invalidateSize();
                window.setTimeout(() => map.invalidateSize(), 300);
            });
            const reportMarker = L.marker(reportPoint, { icon: TimsarMap.icon('incident') })
                .addTo(map)
                .bindPopup('<strong>Lokasi kejadian</strong>');
            let memberMarker = null;
            let memberAccuracyCircle = null;
            let routeLine = null;
            let routeSignature = '';
            let routeFitted = false;
            let trailLines = [];
            let trailSignature = '';
            let latestMemberPoint = null;

            const statusBadge = document.getElementById('statusBadge');
            const statusMessage = document.getElementById('statusMessage');
            const liveIndicator = document.getElementById('liveIndicator');
            const memberName = document.getElementById('memberName');
            const memberMeta = document.getElementById('memberMeta');
            const memberOnlineBadge = document.getElementById('memberOnlineBadge');
            const memberCallLink = document.getElementById('memberCallLink');
            const distanceText = document.getElementById('distanceText');
            const durationText = document.getElementById('durationText');
            const mapStatusText = document.getElementById('mapStatusText');
            const closurePanel = document.getElementById('closurePanel');
            const closureTitle = document.getElementById('closureTitle');
            const closureText = document.getElementById('closureText');

            const statusConfig = {
                new: { step: 0, badge: 'Laporan diterima', message: 'Posko telah menerima laporan Anda dan sedang memilih petugas yang dapat membantu.', tone: 'amber' },
                assigned: { step: 1, badge: 'Petugas ditugaskan', message: 'Petugas telah dipilih dan sedang memeriksa detail penugasan.', tone: 'blue' },
                accepted: { step: 1, badge: 'Tugas diterima', message: 'Petugas telah menerima tugas dan sedang bersiap menuju lokasi.', tone: 'blue' },
                on_the_way: { step: 2, badge: 'Petugas menuju lokasi', message: 'Petugas sedang dalam perjalanan. Posisi dan perkiraan tiba diperbarui otomatis.', tone: 'red' },
                arrived: { step: 3, badge: 'Petugas telah tiba', message: 'Petugas sudah berada di sekitar lokasi kejadian.', tone: 'emerald' },
                handling: { step: 4, badge: 'Sedang ditangani', message: 'Petugas sedang melakukan penanganan di lokasi.', tone: 'emerald' },
                completed: { step: 5, badge: 'Penanganan selesai', message: 'Laporan telah selesai ditangani oleh petugas.', tone: 'slate' },
                cancelled: { step: -1, badge: 'Laporan dibatalkan', message: 'Laporan ini telah ditutup oleh posko.', tone: 'slate' },
            };

            const tones = {
                amber: 'bg-amber-100 text-amber-800',
                blue: 'bg-blue-100 text-blue-800',
                red: 'bg-red-100 text-red-800',
                emerald: 'bg-emerald-100 text-emerald-800',
                slate: 'bg-slate-200 text-slate-800',
            };

            function formatDistance(meters) {
                if (!meters) return '-';
                return meters >= 1000 ? `${(meters / 1000).toFixed(1)} km` : `${Math.round(meters)} m`;
            }

            function formatDuration(seconds) {
                if (!seconds) return '-';
                return seconds >= 60 ? `${Math.max(1, Math.round(seconds / 60))} menit` : `${seconds} detik`;
            }

            function formatTime(value) {
                if (!value) return '-';
                return new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }

            function geometryToLatLngs(geometry) {
                if (!geometry?.coordinates) return [];
                return geometry.coordinates.map((point) => [point[1], point[0]]);
            }

            function updateStatus(data) {
                const effectiveStatus = data.assignment?.status || data.report.status;
                const config = statusConfig[effectiveStatus] || statusConfig.new;
                statusBadge.textContent = config.badge;
                statusBadge.className = `rounded-full px-3 py-1 text-xs font-black uppercase ${tones[config.tone]}`;
                statusMessage.textContent = config.message;
                document.title = `${config.badge} - ${trackingCode}`;

                document.querySelectorAll('[data-status-step]').forEach((element) => {
                    const index = Number(element.dataset.statusStep);
                    const active = config.step >= 0 && index <= config.step;
                    element.className = active
                        ? 'mx-auto grid h-7 w-7 place-items-center rounded-full border-2 border-red-600 bg-red-600 text-[10px] font-black text-white'
                        : 'mx-auto grid h-7 w-7 place-items-center rounded-full border-2 border-slate-200 bg-white text-[10px] font-black text-slate-400';
                    document.querySelector(`[data-status-step-label="${index}"]`).className = active
                        ? 'mt-1 truncate text-[9px] font-black text-slate-800 sm:text-[11px]'
                        : 'mt-1 truncate text-[9px] font-bold text-slate-400 sm:text-[11px]';
                });

                const closed = ['completed', 'cancelled'].includes(data.report.status);
                closurePanel.classList.toggle('hidden', !closed);
                if (closed) {
                    const completed = data.report.status === 'completed';
                    closurePanel.className = `rounded-xl border p-4 shadow-sm ${completed ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-slate-300 bg-slate-100 text-slate-800'}`;
                    closureTitle.textContent = completed ? 'Penanganan telah selesai' : 'Laporan telah dibatalkan';
                    closureText.textContent = data.report.closure_notes || (completed ? 'Terima kasih telah menggunakan layanan TIMSAR.' : 'Hubungi posko jika Anda masih membutuhkan bantuan.');
                }
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
                    const points = (segment.points ?? []).map((point) => [point.latitude, point.longitude]);
                    if (points.length >= 2) trailLines.push(L.polyline(points, TimsarMap.trailOptions()).addTo(map));
                });

                const pointCount = trail?.summary?.point_count ?? 0;
                const distance = trail?.summary?.distance_meters > 0 ? formatDistance(trail.summary.distance_meters) : '0 m';
                document.getElementById('trailText').textContent = pointCount > 0
                    ? `${distance} perjalanan terekam dari ${pointCount} pembaruan posisi.`
                    : 'Petugas belum memulai perjalanan.';
            }

            async function refreshTrail() {
                try {
                    const response = await fetch('{{ route('public.tracking.trail', $report->tracking_code) }}', { headers: { 'Accept': 'application/json' } });
                    if (response.ok) setTrailData(await response.json());
                } catch (_) {
                    // Status utama tetap dapat digunakan saat riwayat belum tersedia.
                }
            }

            function updateMember(data) {
                if (!data.member) {
                    memberName.textContent = 'Menunggu penugasan';
                    memberMeta.textContent = 'Posko sedang memilih petugas.';
                    memberOnlineBadge.classList.add('hidden');
                    memberCallLink.classList.add('hidden');
                    return;
                }

                memberName.textContent = data.member.name;
                memberMeta.textContent = data.member.last_seen_at
                    ? `Posisi diperbarui pukul ${formatTime(data.member.last_seen_at)}`
                    : 'Menunggu posisi petugas.';
                memberOnlineBadge.textContent = data.member.is_online ? 'Terhubung' : 'Pembaruan tertunda';
                memberOnlineBadge.className = data.member.is_online
                    ? 'rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase text-emerald-700'
                    : 'rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-black uppercase text-amber-700';

                if (data.member.phone) {
                    memberCallLink.href = `tel:${String(data.member.phone).replace(/[^\d+]/g, '')}`;
                    memberCallLink.classList.remove('hidden');
                    memberCallLink.classList.add('flex');
                }

                if (data.member.latitude && data.member.longitude) {
                    latestMemberPoint = [data.member.latitude, data.member.longitude];
                    mapStatusText.textContent = data.member.is_online ? 'Posisi petugas diperbarui otomatis.' : 'Menampilkan posisi terakhir petugas.';
                    if (!memberMarker) {
                        memberMarker = L.marker(latestMemberPoint, { icon: TimsarMap.icon('member') })
                            .addTo(map)
                            .bindPopup('<strong>Posisi petugas</strong>');
                    } else {
                        TimsarMap.moveMarker(memberMarker, latestMemberPoint);
                    }

                    if (data.member.accuracy) {
                        if (!memberAccuracyCircle) {
                            memberAccuracyCircle = L.circle(latestMemberPoint, {
                                radius: data.member.accuracy,
                                color: '#16a34a',
                                fillColor: '#22c55e',
                                fillOpacity: 0.08,
                                weight: 1,
                            }).addTo(map);
                        } else {
                            memberAccuracyCircle.setLatLng(latestMemberPoint);
                            memberAccuracyCircle.setRadius(data.member.accuracy);
                        }
                    }
                }
            }

            function updateRoute(geometry) {
                const points = geometryToLatLngs(geometry);
                const signature = JSON.stringify(geometry?.coordinates ?? []);
                if (!points.length) {
                    routeLine?.remove();
                    routeLine = null;
                    routeSignature = '';
                    return;
                }
                if (signature === routeSignature) return;

                routeSignature = signature;
                if (!routeLine) routeLine = L.polyline(points, TimsarMap.routeOptions()).addTo(map);
                else routeLine.setLatLngs(points);

                if (!routeFitted) {
                    map.fitBounds(routeLine.getBounds(), { padding: [36, 36] });
                    routeFitted = true;
                }
            }

            async function refreshTracking() {
                try {
                    const response = await fetch('{{ route('public.tracking.data', $report->tracking_code) }}', { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error('Tracking unavailable');
                    const data = await response.json();
                    updateStatus(data);
                    updateMember(data);
                    distanceText.textContent = formatDistance(data.assignment?.distance_meters);
                    durationText.textContent = formatDuration(data.assignment?.duration_seconds);
                    updateRoute(data.assignment?.route_geometry);
                    liveIndicator.innerHTML = '<span class="h-2 w-2 rounded-full bg-emerald-500"></span>Diperbarui otomatis';
                } catch (_) {
                    liveIndicator.innerHTML = '<span class="h-2 w-2 rounded-full bg-amber-500"></span>Mencoba terhubung kembali';
                }
            }

            document.getElementById('focusMapButton').addEventListener('click', () => {
                if (routeLine) map.fitBounds(routeLine.getBounds(), { padding: [36, 36] });
                else if (latestMemberPoint) map.setView(latestMemberPoint, 17);
                else map.setView(reportPoint, 16);
            });

            document.getElementById('copyTrackingButton').addEventListener('click', async (event) => {
                await navigator.clipboard.writeText(trackingCode);
                event.currentTarget.textContent = 'Tersalin';
                window.setTimeout(() => { event.currentTarget.textContent = 'Salin'; }, 1800);
            });

            document.getElementById('shareTrackingButton').addEventListener('click', async () => {
                const shareData = { title: `Status laporan ${trackingCode}`, text: `Lacak perkembangan laporan TIMSAR ${trackingCode}`, url: window.location.href };
                if (navigator.share) await navigator.share(shareData);
                else {
                    await navigator.clipboard.writeText(window.location.href);
                    document.getElementById('shareTrackingButton').textContent = 'Tautan tersalin';
                }
            });

            refreshTracking();
            refreshTrail();
            setInterval(refreshTracking, 3000);
            setInterval(refreshTrail, 5000);
        </script>
    @endpush
</x-layouts.app>
