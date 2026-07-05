<x-layouts.app title="Detail Laporan {{ $report->tracking_code }}" :hideChrome="true" :fullBleed="true">
    @php
        $assignment = $report->activeAssignment;
        $memberLocation = $assignment?->member?->memberLocation;
        $memberOnline = $memberLocation?->last_seen_at?->gt(now()->subSeconds(90)) ?? false;
        $trackingUrl = route('public.tracking', $report->tracking_code);
        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $report->latitude . ',' . $report->longitude;
        $directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $report->latitude . ',' . $report->longitude;
        $phoneLink = 'tel:' . preg_replace('/[^\d+]/', '', $report->reporter_phone);
        $evidenceSummary = $evidence['summary'];
        $mobileLogs = $evidence['logs'];
        $evidenceUrl = route('admin.reports.evidence', $report);
        $isClosed = in_array($report->status, [\App\Models\Report::STATUS_COMPLETED, \App\Models\Report::STATUS_CANCELLED], true);
        $closedAt = $report->closed_at ?? ($isClosed ? $report->updated_at : null);
        $timeline = collect([
            ['label' => 'Laporan masuk', 'time' => $report->created_at, 'note' => $report->reporter_name],
            ['label' => 'Petugas ditugaskan', 'time' => $assignment?->assigned_at, 'note' => $assignment?->member?->name],
            ['label' => 'Tugas diterima', 'time' => $assignment?->accepted_at, 'note' => $assignment?->member?->name],
            ['label' => 'Petugas mulai menuju lokasi', 'time' => $assignment?->started_at, 'note' => $assignment?->member?->name],
            ['label' => 'Petugas sampai lokasi', 'time' => $assignment?->arrived_at, 'note' => $assignment?->member?->name],
            ['label' => 'Laporan selesai', 'time' => $assignment?->completed_at, 'note' => $assignment?->member?->name],
            ['label' => 'Laporan dibatalkan', 'time' => $report->status === \App\Models\Report::STATUS_CANCELLED ? $report->updated_at : null, 'note' => 'Dibatalkan posko'],
        ])->filter(fn ($item) => $item['time']);
    @endphp

    @push('scripts')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            body, .timsar-maxim-admin {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
            }
            /* ── Activity Timeline Styling ── */
            .timeline-container {
                position: relative;
                padding-left: 2rem;
            }
            .timeline-container::before {
                content: '';
                position: absolute;
                left: 7px;
                top: 8px;
                bottom: 8px;
                width: 2px;
                background-color: #333846;
            }
            .timeline-item {
                position: relative;
                margin-bottom: 1.75rem;
            }
            .timeline-item:last-child {
                margin-bottom: 0;
            }
            .timeline-dot {
                position: absolute;
                left: -29px;
                top: 4px;
                width: 16px;
                height: 16px;
                border-radius: 9999px;
                background-color: #333846;
                border: 3px solid #181a20;
                box-shadow: 0 0 0 1.5px #4b5265;
                transition: all 0.25s ease;
            }
            .timeline-item.active .timeline-dot {
                background-color: #f97316;
                box-shadow: 0 0 0 1.5px #f97316;
            }
            @keyframes pulse-ring {
                0% { transform: scale(0.95); opacity: 0.5; }
                50% { transform: scale(1.4); opacity: 0.25; }
                100% { transform: scale(0.95); opacity: 0.5; }
            }
            .timeline-item.active:first-child .timeline-dot::after {
                content: '';
                position: absolute;
                inset: -4px;
                border-radius: 9999px;
                border: 2.5px solid #f97316;
                animation: pulse-ring 2s infinite ease-in-out;
            }

            /* Details chevron rotation */
            .details-indicator-arrow {
                transition: transform 0.2s ease;
            }
            details[open] .details-indicator-arrow {
                transform: rotate(180deg);
            }

            /* Custom Map Popup */
            .leaflet-popup-content-wrapper {
                background: #1e222b !important;
                border-radius: 0.75rem !important;
                box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5) !important;
                border: 1px solid #333846 !important;
                padding: 0.25rem !important;
                color: #fff !important;
            }
            .leaflet-popup-tip { background: #1e222b !important; }
            .leaflet-popup-content {
                font-family: 'Outfit', inherit !important;
                font-size: 0.85rem !important;
                color: #e2e8f0 !important;
                line-height: 1.5 !important;
                margin: 0.5rem 0.75rem !important;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: #181a20; }
            ::-webkit-scrollbar-thumb { background: #333846; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #4b5265; }
            .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #1e222b; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #333846; border-radius: 99px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5265; }
        </style>
    @endpush

    <div class="timsar-maxim-admin min-h-screen bg-[#181a20] text-slate-100 flex flex-col">
        
        {{-- ── TOP TACTICAL COMMAND BAR (HEADER) ── --}}
        <header class="bg-[#1e222b] border-b border-[#333846] px-4 sm:px-6 py-3.5 shrink-0 flex flex-col gap-3 md:flex-row md:items-center md:justify-between shadow-xl z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-orange-600 to-amber-600 text-white font-black text-base shadow-lg shadow-orange-500/20 border border-orange-400/30 font-mono">
                    {{ substr($report->tracking_code, -4) }}
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full {{ $isClosed ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30' }} border px-2 py-0.5 text-[10px] font-black uppercase tracking-wider">
                            <span class="h-1.5 w-1.5 rounded-full {{ $isClosed ? 'bg-emerald-500' : 'bg-red-500 animate-pulse' }}"></span>
                            {{ $isClosed ? 'REKAP OPERASI SELESAI' : 'POSKO OPERASI AKTIF' }}
                        </span>
                        <span id="reportStatusBadge" class="inline-flex rounded-md bg-[#242832] border border-[#333846] px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-orange-400">
                            {{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}
                        </span>
                        <h1 class="text-base sm:text-xl font-black text-white truncate">{{ $report->incident_type }}</h1>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 truncate mt-0.5 flex items-center gap-2">
                        <span>Pelapor: <strong class="text-white">{{ $report->reporter_name }}</strong></span>
                        <span class="text-slate-600">•</span>
                        <span>Kode: <strong class="text-orange-400 font-mono">{{ $report->tracking_code }}</strong></span>
                        <span class="text-slate-600">•</span>
                        <span class="font-mono">{{ $report->created_at->format('d M Y, H:i') }} WITA</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @unless($isClosed)
                    <a href="{{ $phoneLink }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-red-600 hover:bg-red-500 px-3.5 py-2 text-xs font-black text-white transition-all shadow-md shadow-red-500/20 active:scale-95">
                        <span>📞</span> <span>Hubungi Pelapor</span>
                    </a>
                @endunless
                <a href="{{ $trackingUrl }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-[#333846] bg-[#242832] px-3.5 py-2 text-xs font-black text-slate-200 transition-all hover:border-orange-500 hover:text-orange-400 shadow-sm active:scale-95">
                    <span>📡</span> <span>Lacak Publik</span>
                </a>
                <a href="{{ $mapsUrl }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-[#333846] bg-[#242832] px-3.5 py-2 text-xs font-black text-slate-200 transition-all hover:border-orange-500 hover:text-orange-400 shadow-sm active:scale-95">
                    <span>🗺️</span> <span>G-Maps</span>
                </a>
                @unless($isClosed)
                    <a href="{{ $directionsUrl }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-[#333846] bg-[#242832] px-3.5 py-2 text-xs font-black text-slate-200 transition-all hover:border-orange-500 hover:text-orange-400 shadow-sm active:scale-95">
                        <span>🧭</span> <span>Navigasi</span>
                    </a>
                @endunless
                <a href="{{ $evidenceUrl }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 px-3.5 py-2 text-xs font-black text-white transition-all shadow-md shadow-orange-500/20 active:scale-95">
                    <span>🖨️</span> <span>Cetak Bukti Audit</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#242832] hover:bg-[#2c303d] border border-[#333846] hover:border-orange-500 px-4 py-2 text-xs font-black text-slate-200 transition-all active:scale-95">
                    <span>⬅️</span> <span>Radar Posko</span>
                </a>
            </div>
        </header>

        {{-- ── TWO COLUMN TACTICAL MAIN PANEL ── --}}
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_400px]">

            {{-- Left column --}}
            <div class="flex flex-col gap-6">

                {{-- Detail Laporan --}}
                <div class="order-1 rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start border-b border-[#333846] pb-5">
                        <div class="space-y-1.5">
                            <span class="inline-block text-[10px] font-mono font-black px-2.5 py-1 rounded-md bg-orange-500/20 text-orange-400 uppercase tracking-widest border border-orange-500/40">ID INCIDENT: {{ $report->tracking_code }}</span>
                            <h2 class="text-2xl font-black text-white leading-tight mt-1">{{ $report->incident_type }}</h2>
                            <p class="text-sm text-slate-300 leading-relaxed pt-1 font-medium">{{ $report->description }}</p>
                        </div>
                        <span class="inline-flex shrink-0 self-start rounded-xl bg-red-500/20 border border-red-500/40 px-4 py-1.5 text-xs font-black text-red-400 shadow-sm">
                            {{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}
                        </span>
                    </div>

                    {{-- Form Parameters Grid --}}
                    <div class="mt-5 grid gap-4 grid-cols-2 sm:grid-cols-4">
                        <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] flex flex-col justify-between shadow-sm">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Pelapor</span>
                                <p class="text-sm font-black text-white truncate">{{ $report->reporter_name }}</p>
                            </div>
                            <p class="text-xs text-orange-400 mt-2 font-mono font-bold">{{ $report->reporter_phone }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] flex flex-col justify-between shadow-sm">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Akurasi GPS</span>
                                <p class="text-sm font-black text-white font-mono">{{ $report->accuracy ? number_format($report->accuracy) . ' meter' : '-' }}</p>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 font-semibold">Radius Deviasi</p>
                        </div>
                        <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] flex flex-col justify-between shadow-sm">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Prioritas</span>
                                <p class="text-sm font-black text-white flex items-center gap-2">
                                    <span class="inline-block h-2 w-2 rounded-full {{ $report->priority === 'critical' ? 'bg-red-500 animate-ping' : ($report->priority === 'high' ? 'bg-orange-500' : 'bg-yellow-500') }}"></span>
                                    <span>{{ strtoupper($report->priority) }}</span>
                                </p>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 font-semibold">Tingkat Darurat</p>
                        </div>
                        <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] flex flex-col justify-between shadow-sm">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Petugas Rescue</span>
                                <p class="text-sm font-black text-orange-400 truncate">{{ $report->assignedMember?->name ?? 'Belum ditunjuk' }}</p>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 font-semibold">Pelaksana Lapangan</p>
                        </div>
                    </div>

                    @if($isClosed)
                        <div class="mt-5 rounded-2xl border {{ $report->status === \App\Models\Report::STATUS_COMPLETED ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : 'border-red-500/40 bg-red-500/10 text-red-300' }} p-5 shadow-sm">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-black text-white">
                                        {{ $report->status === \App\Models\Report::STATUS_COMPLETED ? '✅ Operasi selesai dan masuk riwayat resmi' : '🚨 Laporan dibatalkan dan masuk riwayat posko' }}
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-slate-400 font-mono">
                                        Ditutup {{ $closedAt?->format('d M Y, H:i') ?? '-' }}
                                        @if($report->closedBy) oleh <strong class="text-white">{{ $report->closedBy->name }}</strong> @endif
                                    </p>
                                </div>
                                <a href="{{ $evidenceUrl }}" target="_blank" class="inline-flex items-center justify-center rounded-xl bg-orange-600 hover:bg-orange-500 px-4 py-2.5 text-xs font-black text-white shadow-md shadow-orange-500/20 transition-all">
                                    🖨️ Cetak Bukti Operasi
                                </a>
                            </div>
                            @if($report->closure_notes)
                                <div class="mt-4 rounded-xl border border-[#333846] bg-[#242832] p-3.5 text-xs sm:text-sm text-slate-300 shadow-sm">
                                    <span class="font-black text-orange-400">💬 Catatan Penutupan:</span> {{ $report->closure_notes }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Map Container --}}
                <div class="order-2 flex flex-col overflow-hidden rounded-3xl border border-[#333846] bg-[#1e222b] shadow-xl">
                    <div class="flex flex-col gap-4 border-b border-[#333846] bg-[#242832] px-6 py-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <span class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>🛰️</span>
                                <span>{{ $isClosed ? 'Peta Bukti Perjalanan Petugas' : 'Visual Peta Operasi & Tracking Realtime' }}</span>
                            </span>
                            <div class="mt-2 flex flex-wrap gap-4 text-xs font-bold text-slate-300">
                                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-5 rounded bg-blue-500 shadow-sm shadow-blue-500/50"></span>Jalur Ditempuh</span>
                                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-5 rounded bg-red-500 shadow-sm shadow-red-500/50"></span>{{ $isClosed ? 'Rute Hasil Hitung' : 'Rute Navigasi' }}</span>
                                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></span>Pemancar BTS</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-xs">
                            <div class="rounded-xl border border-[#333846] bg-[#181a20] px-3.5 py-2 shadow-sm">
                                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Jalur GPS</span>
                                <p id="trailDistanceText" class="mt-0.5 font-mono font-black text-orange-400 text-sm">-</p>
                            </div>
                            <div class="rounded-xl border border-[#333846] bg-[#181a20] px-3.5 py-2 shadow-sm">
                                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Titik Log</span>
                                <p id="trailPointText" class="mt-0.5 font-mono font-black text-blue-400 text-sm">-</p>
                            </div>
                            <div class="rounded-xl border border-[#333846] bg-[#181a20] px-3.5 py-2 shadow-sm">
                                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Node BTS</span>
                                <p id="trailNetworkText" class="mt-0.5 font-mono font-black text-emerald-400 text-sm">-</p>
                            </div>
                        </div>
                    </div>
                    <div id="reportMap" class="h-[450px] min-h-[450px] w-full z-10 lg:h-[500px] xl:h-[550px]"></div>
                </div>

                {{-- Bukti Mobile Computing --}}
                <div class="order-3 rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl">
                    <div class="flex flex-col gap-3 border-b border-[#333846] pb-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-sm font-black text-white uppercase tracking-wider">Bukti Audit & Telemetri Mobile Computing</h2>
                            <p class="mt-1 text-xs font-semibold text-slate-400">
                                {{ $isClosed ? 'Bukti perjalanan yang tersimpan selama operasi berlangsung.' : 'Ringkasan perpindahan lokasi, observasi menara seluler (BTS), dan status GPS petugas.' }}
                            </p>
                        </div>
                        <span id="evidenceLastSeenText" class="rounded-full bg-orange-500/20 border border-orange-500/40 px-3.5 py-1 text-xs font-black text-orange-400 font-mono shadow-sm">
                            {{ $evidenceSummary['last_at'] ? 'Update ' . $evidenceSummary['last_at']->format('H:i:s') : 'Belum ada ping' }}
                        </span>
                    </div>

                    {{-- Telemetry Dashboard Cards --}}
                    <div class="mt-5 grid gap-4 grid-cols-2 lg:grid-cols-5">
                        <div class="rounded-2xl border border-blue-500/30 bg-blue-500/10 p-4 flex flex-col justify-between shadow-sm">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-blue-400">Titik GPS</span>
                            <p id="evidenceGpsText" class="mt-2 text-2xl sm:text-3xl font-mono font-black text-white">{{ number_format($evidenceSummary['gps_points']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 flex flex-col justify-between shadow-sm">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-amber-400">Log BTS</span>
                            <p id="evidenceCellText" class="mt-2 text-2xl sm:text-3xl font-mono font-black text-white">{{ number_format($evidenceSummary['cell_observations']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 flex flex-col justify-between shadow-sm">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-400">Pindah Jaringan</span>
                            <p id="evidenceNetworkText" class="mt-2 text-2xl sm:text-3xl font-mono font-black text-white">{{ number_format($evidenceSummary['network_changes']) }}x</p>
                        </div>
                        <div class="rounded-2xl border border-orange-500/30 bg-orange-500/10 p-4 flex flex-col justify-between shadow-sm">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-orange-400">Handover BTS</span>
                            <p id="evidenceHandoverText" class="mt-2 text-2xl sm:text-3xl font-mono font-black text-white">{{ number_format($evidenceSummary['handovers']) }}x</p>
                        </div>
                        <div class="rounded-2xl border border-[#333846] bg-[#242832] p-4 flex flex-col justify-between col-span-2 lg:col-span-1 shadow-sm">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Jalur Terekam</span>
                            <p id="evidenceDistanceText" class="mt-2 text-xl sm:text-2xl font-mono font-black text-orange-400">
                                {{ $evidenceSummary['distance_meters'] >= 1000 ? number_format($evidenceSummary['distance_meters'] / 1000, 2) . ' km' : number_format($evidenceSummary['distance_meters']) . ' m' }}
                            </p>
                        </div>
                    </div>

                    {{-- Cell info box --}}
                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-[#333846] bg-[#242832] p-4 flex items-center gap-3.5 shadow-sm">
                            <div class="p-2.5 rounded-xl bg-[#181a20] border border-[#333846] text-orange-400 shrink-0 shadow-sm">
                                📡
                            </div>
                            <div>
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Menara BTS Awal</span>
                                <p id="evidenceFirstCellText" class="mt-1 text-xs sm:text-sm font-black text-white font-mono">
                                    @if($evidenceSummary['first_cell'])
                                        {{ $evidenceSummary['first_cell']['operator'] }} {{ $evidenceSummary['first_cell']['radio_type'] }} / Cell {{ $evidenceSummary['first_cell']['cell_id'] }}
                                    @else
                                        Belum ada data BTS
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-[#333846] bg-[#242832] p-4 flex items-center gap-3.5 shadow-sm">
                            <div class="p-2.5 rounded-xl bg-[#181a20] border border-[#333846] text-emerald-400 shrink-0 shadow-sm">
                                📶
                            </div>
                            <div>
                                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Menara BTS Terbaru</span>
                                <p id="evidenceLatestCellText" class="mt-1 text-xs sm:text-sm font-black text-white font-mono">
                                    @if($evidenceSummary['latest_cell'])
                                        {{ $evidenceSummary['latest_cell']['operator'] }} {{ $evidenceSummary['latest_cell']['radio_type'] }} / Cell {{ $evidenceSummary['latest_cell']['cell_id'] }}
                                    @else
                                        Belum ada data BTS
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bukti BTS details --}}
                <details open class="order-4 rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl group">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>🗼</span>
                                <span>Log Menara BTS Lapangan</span>
                            </h2>
                            <p class="mt-1 text-xs font-semibold text-slate-400">Daftar serving cell menara seluler Android yang terekam pada perlintasan rute.</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span id="handoverCountText" class="rounded-xl bg-amber-500/20 border border-amber-500/40 px-3 py-1 text-xs font-black text-amber-300 font-mono shadow-sm">
                                0 titik BTS
                            </span>
                            <svg class="details-indicator-arrow h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </summary>
                    <div id="handoverTimeline" class="mt-5 space-y-2.5 border-t border-[#333846] pt-5 max-h-[320px] overflow-y-auto custom-scrollbar pr-1">
                        <div class="rounded-2xl bg-[#242832] border border-[#333846] p-6 text-center text-xs font-bold text-slate-400">Belum ada data BTS dari aplikasi Android anggota.</div>
                    </div>
                </details>

                {{-- Log Table --}}
                <details class="order-5 rounded-3xl border border-[#333846] bg-[#1e222b] shadow-xl overflow-hidden group">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-6 py-5 bg-[#242832]">
                        <div>
                            <h2 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>📑</span>
                                <span>Data Mentah Log Telemetri GPS & Jaringan</span>
                            </h2>
                            <p class="mt-1 text-xs font-semibold text-slate-400">
                                <span id="mobileLogCountText" class="font-black text-orange-400">Menampilkan {{ $mobileLogs->count() }} log terbaru</span>. {{ $isClosed ? 'Data arsip operasi dari perangkat Android.' : 'Live feed dari perangkat Android.' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full {{ $isClosed ? 'bg-[#181a20] text-slate-300 border-[#333846]' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' }} px-3 py-1 text-[10px] font-black uppercase tracking-wider border font-mono shadow-sm">
                                @unless($isClosed)<span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>@endunless {{ $isClosed ? 'Arsip' : 'Auto Refresh' }}
                            </span>
                            <svg class="details-indicator-arrow h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </summary>
                    <div class="max-h-[400px] overflow-auto border-t border-[#333846] custom-scrollbar">
                        <table class="min-w-full divide-y divide-[#333846] text-left text-xs sm:text-sm">
                            <thead class="sticky top-0 z-10 bg-[#181a20] text-[10px] uppercase font-black tracking-wider text-slate-400 border-b border-[#333846]">
                                <tr>
                                    <th class="px-6 py-3.5">Waktu</th>
                                    <th class="px-6 py-3.5">Koordinat GPS & Akurasi</th>
                                    <th class="px-6 py-3.5">Jaringan</th>
                                    <th class="px-6 py-3.5">Menara Seluler (BTS)</th>
                                    <th class="px-6 py-3.5">Kekuatan Sinyal</th>
                                </tr>
                            </thead>
                            <tbody id="mobileLogTableBody" class="divide-y divide-[#333846] bg-[#1e222b] font-medium text-slate-300">
                                @forelse($mobileLogs as $log)
                                    <tr class="align-top hover:bg-[#242832] transition-colors">
                                        <td class="whitespace-nowrap px-6 py-3.5 font-bold text-white font-mono">{{ $log['recorded_at']?->format('H:i:s') }}<br><span class="font-normal text-[10px] text-slate-400">{{ $log['recorded_at']?->format('d M Y') }}</span></td>
                                        <td class="px-6 py-3.5 font-mono text-slate-300 leading-normal">
                                            {{ number_format($log['latitude'], 6) }}, {{ number_format($log['longitude'], 6) }}
                                            <br><span class="font-sans text-[10px] text-orange-400 font-bold">Akurasi {{ $log['accuracy'] !== null ? number_format($log['accuracy']) . ' m' : '-' }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-3.5 font-black text-white">{{ strtoupper($log['network_type']) }}</td>
                                        <td class="px-6 py-3.5 text-slate-300 leading-normal">
                                            @if($log['cell'])
                                                <span class="font-black text-amber-300">{{ $log['cell']['operator'] }} {{ $log['cell']['radio_type'] }}</span>
                                                <br><span class="font-mono text-[10px] text-slate-400">Cell {{ $log['cell']['cell_id'] }}</span>
                                                <br><span class="text-[10px] text-slate-500">TAC/LAC {{ $log['cell']['tac_or_lac'] ?? '-' }} - PCI {{ $log['cell']['pci_or_psc'] ?? '-' }}</span>
                                            @else
                                                <span class="text-slate-500 italic">Tidak terdeteksi</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-3.5 font-mono text-slate-300 leading-normal">
                                            @if($log['signal'])
                                                RSRP {{ $log['signal']['rsrp_dbm'] ?? '-' }} dBm<br>
                                                <span class="text-[10px] text-slate-400">RSRQ {{ $log['signal']['rsrq_db'] ?? '-' }} / SINR {{ $log['signal']['sinr_db'] ?? '-' }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-xs font-bold text-slate-400">Belum ada log mobile computing dari petugas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>

            {{-- Right column (Sidebar Komando) --}}
            <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">

                @if($isClosed)
                <div class="rounded-3xl border border-emerald-500/40 bg-[#1e222b] p-6 shadow-xl">
                    <h2 class="text-sm font-black text-emerald-400 uppercase tracking-wider border-b border-[#333846] pb-4 flex items-center gap-2">
                        <span>🛡️</span>
                        <span>Rekap Penutupan Operasi</span>
                    </h2>
                    <div class="mt-4 space-y-4">
                        <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] shadow-sm">
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400">Status Akhir</span>
                            <p class="mt-1 text-lg font-black text-white">{{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-400 font-mono">{{ $closedAt?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] shadow-sm">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Petugas Penanganan</span>
                            <p class="mt-1 text-sm font-black text-white">{{ $assignment?->member?->name ?? $report->assignedMember?->name ?? '-' }}</p>
                            <p class="mt-1 text-xs text-orange-400 font-mono font-bold">{{ $assignment?->member?->phone ?? $report->assignedMember?->phone ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Jalur Bukti</span>
                                <p class="mt-1 font-mono text-sm font-black text-orange-400">{{ $evidenceSummary['distance_meters'] >= 1000 ? number_format($evidenceSummary['distance_meters'] / 1000, 2) . ' km' : number_format($evidenceSummary['distance_meters']) . ' m' }}</p>
                            </div>
                            <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Titik GPS</span>
                                <p class="mt-1 font-mono text-sm font-black text-white">{{ number_format($evidenceSummary['gps_points']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Log BTS</span>
                                <p class="mt-1 font-mono text-sm font-black text-white">{{ number_format($evidenceSummary['cell_observations']) }}</p>
                            </div>
                            <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pindah Jaringan</span>
                                <p class="mt-1 font-mono text-sm font-black text-white">{{ number_format($evidenceSummary['network_changes']) }}x</p>
                            </div>
                        </div>

                        <a href="{{ $evidenceUrl }}" target="_blank" class="flex items-center justify-center rounded-xl bg-orange-600 hover:bg-orange-500 py-3.5 text-xs sm:text-sm font-black text-white transition-all shadow-md shadow-orange-500/20 active:scale-95">
                            🖨️ Cetak Bukti Operasi
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-center rounded-xl border border-[#333846] bg-[#242832] hover:bg-[#2c303d] py-3.5 text-xs sm:text-sm font-black text-slate-200 transition-all active:scale-95 shadow-sm">
                            📋 Buka Riwayat Laporan
                        </a>
                    </div>
                </div>
                @else
                {{-- Monitoring Petugas --}}
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl">
                    <h2 class="text-sm font-black text-white uppercase tracking-wider border-b border-[#333846] pb-4 flex items-center justify-between">
                        <span class="flex items-center gap-2"><span>🧑‍🚒</span><span>Petugas Lapangan</span></span>
                        @if($assignment?->member)
                            <span class="inline-flex h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                        @endif
                    </h2>
                    @if($assignment?->member)
                        <div class="mt-4 space-y-3.5">
                            <div class="rounded-2xl bg-[#242832] p-4 border border-[#333846] flex items-center gap-3.5 shadow-sm">
                                <div class="h-11 w-11 rounded-xl bg-orange-500/20 border border-orange-500/40 flex items-center justify-center font-black text-orange-400 text-sm uppercase shrink-0 shadow-sm">
                                    {{ substr($assignment->member->name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-white truncate leading-tight">{{ $assignment->member->name }}</p>
                                    <p class="text-xs text-orange-400 font-mono font-bold mt-1">{{ $assignment->member->phone }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Status Tugas</span>
                                    <p id="assignmentStatusText" class="text-xs sm:text-sm font-black text-white leading-tight">
                                        {{ \App\Http\Controllers\PublicTrackingController::assignmentLabel($assignment->status) }}
                                    </p>
                                </div>
                                <div class="rounded-2xl {{ $memberOnline ? 'bg-emerald-500/15 border border-emerald-500/40' : 'bg-[#242832] border border-[#333846]' }} p-3.5 flex flex-col justify-between shadow-sm">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Koneksi GPS</span>
                                    <p id="memberOnlineText" class="text-xs sm:text-sm font-black leading-none {{ $memberOnline ? 'text-emerald-400' : 'text-slate-500' }}">
                                        {{ $memberOnline ? 'Online' : 'Offline' }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Sisa Jarak</span>
                                    <p id="assignmentDistanceText" class="text-xs sm:text-sm font-mono font-black text-orange-400 leading-tight">
                                        {{ $assignment->distance_meters ? number_format($assignment->distance_meters / 1000, 2) . ' km' : '-' }}
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Estimasi ETA</span>
                                    <p id="assignmentDurationText" class="text-xs sm:text-sm font-mono font-black text-blue-400 leading-tight">
                                        {{ $assignment->duration_seconds ? round($assignment->duration_seconds / 60) . ' menit' : '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-[#242832] p-3.5 border border-[#333846] shadow-sm">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Pembaruan GPS</span>
                                <p id="memberLastSeenText" class="text-xs sm:text-sm font-black text-white">
                                    {{ $memberLocation?->last_seen_at?->diffForHumans() ?? '-' }}
                                </p>
                                <p id="memberGpsMetaText" class="text-[10px] text-slate-400 mt-1 font-mono leading-tight">
                                    {{ $memberLocation?->network_type ?? 'unknown' }}{{ $memberLocation?->accuracy ? ' - akurasi ' . number_format($memberLocation->accuracy) . ' m' : '' }}
                                </p>
                            </div>

                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $assignment->member->phone) }}" class="flex items-center justify-center gap-2 w-full rounded-xl bg-orange-600 hover:bg-orange-500 py-3 text-xs sm:text-sm font-black text-white transition-all shadow-md shadow-orange-500/20 active:scale-95">
                                <span>📞 Hubungi Petugas</span>
                            </a>
                            @unless($isClosed)
                                <form method="POST" action="{{ route('admin.reports.cancel-assignment', $report) }}" onsubmit="return confirm('Batalkan petugas yang sedang ditugaskan? Laporan tetap aktif dan bisa ditugaskan ulang.');">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-amber-500/40 bg-amber-500/15 hover:bg-amber-600 hover:text-white py-3 text-xs font-black text-amber-300 transition-all active:scale-95 shadow-sm">
                                        ⚠️ Batalkan Petugas
                                    </button>
                                    <p class="mt-2 text-[10px] font-semibold text-slate-400 text-center">Menghentikan alarm petugas dan membuka penugasan ulang.</p>
                                </form>
                            @endunless
                        </div>
                    @else
                        <p class="mt-4 rounded-2xl bg-[#242832] border border-[#333846] p-6 text-xs font-bold text-slate-400 text-center shadow-sm">Belum ada petugas ditugaskan untuk laporan ini.</p>
                    @endif
                </div>
                @endif

                @unless(in_array($report->status, [\App\Models\Report::STATUS_COMPLETED, \App\Models\Report::STATUS_CANCELLED], true))
                {{-- Anggota Terdekat --}}
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl">
                    <h2 class="text-sm font-black text-white uppercase tracking-wider border-b border-[#333846] pb-3 flex items-center gap-2">
                        <span>📡</span>
                        <span>Rekomendasi Anggota Terdekat</span>
                    </h2>
                    <p class="text-xs font-semibold text-slate-400 mt-2">Radius pencarian dihitung otomatis dari koordinat GPS terakhir anggota.</p>
                    <div class="mt-4 space-y-3">
                        @forelse($nearestMembers as $member)
                            <form method="POST" action="{{ route('admin.reports.assign-member', $report) }}" class="rounded-2xl border border-[#333846] p-4 bg-[#242832] hover:bg-[#2c303d] hover:border-orange-500 transition-all shadow-sm group">
                                @csrf
                                <input type="hidden" name="member_id" value="{{ $member->id }}">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="min-w-0">
                                        <p class="font-black text-white text-sm truncate leading-tight group-hover:text-orange-400 transition-colors">{{ $member->name }}</p>
                                        <p class="text-xs text-slate-400 mt-1.5 font-mono font-bold">
                                            {{ strtoupper($member->network_type) }} • <span class="text-orange-400">{{ number_format($member->distance_meters) }} m</span>
                                        </p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider border {{ $member->is_online ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-[#181a20] text-slate-400 border-[#333846]' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $member->is_online ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500' }}"></span>
                                        {{ $member->is_online ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                                <button type="submit" class="mt-4 w-full rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white py-2.5 text-center text-xs font-black transition-all active:scale-95 shadow-sm">
                                    🚀 Tugaskan Anggota Ini
                                </button>
                            </form>
                        @empty
                            <div class="rounded-2xl bg-[#242832] border border-[#333846] p-6 text-xs font-bold text-slate-400 text-center shadow-sm">Belum ada anggota dengan lokasi aktif.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Batalkan Laporan --}}
                <form method="POST" action="{{ route('admin.reports.cancel', $report) }}" class="rounded-3xl border border-red-500/40 bg-[#1e222b] p-6 space-y-4 shadow-xl">
                    @csrf
                    <div>
                        <h2 class="font-black text-red-400 text-sm uppercase tracking-wider flex items-center gap-2">
                            <span>🛑</span>
                            <span>Batalkan Laporan</span>
                        </h2>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Gunakan bila terkonfirmasi laporan palsu atau evakuasi batal dilakukan.</p>
                    </div>
                    <div>
                        <label for="closure_notes" class="block text-[11px] font-black text-slate-300 uppercase tracking-wider">Alasan Pembatalan <span class="text-red-400">*</span></label>
                        <textarea id="closure_notes" name="closure_notes" rows="3" minlength="10" maxlength="500" required class="mt-2 w-full rounded-xl border border-[#333846] bg-[#242832] px-3.5 py-2.5 text-xs sm:text-sm text-white outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-slate-500 font-mono shadow-sm" placeholder="Jelaskan alasan detail pembatalan laporan disini..."></textarea>
                        @error('closure_notes')
                            <p class="mt-1.5 text-xs font-bold text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-red-500/20 border border-red-500/40 hover:bg-red-600 hover:text-white py-3 text-center text-xs font-black text-red-300 shadow-sm transition-all active:scale-95">
                        ⚠️ Batalkan Laporan Permanen
                    </button>
                </form>
                @endunless

                {{-- Timeline Operasi --}}
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl">
                    <h2 class="text-sm font-black text-white uppercase tracking-wider border-b border-[#333846] pb-4 flex items-center gap-2">
                        <span>⏱️</span>
                        <span>Kronologi Operasi</span>
                    </h2>
                    <div class="mt-5 timeline-container">
                        @forelse($timeline as $item)
                            <div class="timeline-item active">
                                <span class="timeline-dot"></span>
                                <div class="ml-4">
                                    <p class="font-black text-white text-sm leading-snug">{{ $item['label'] }}</p>
                                    <p class="text-xs text-slate-400 mt-1 font-mono">{{ $item['time']->format('d M Y H:i') }} • Peringkat: <span class="font-bold text-orange-400">{{ $item['note'] }}</span></p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs font-bold text-slate-400 text-center py-4">Belum ada aktivitas terekam.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </main>
    </div>

    @push('scripts')
        <script>
            const reportPoint = [{{ $report->latitude }}, {{ $report->longitude }}];
            const trailUrl = @json($assignment ? route('admin.assignments.trail', $assignment) : null);
            const mobileLogUrl = @json($assignment ? route('admin.assignments.mobile-log', $assignment) : null);
            const reportIsClosed = @json($isClosed);
            const map = L.map('reportMap').setView(reportPoint, 14);
            TimsarMap.addTiles(map);
            L.marker(reportPoint, { icon: TimsarMap.icon('incident') }).addTo(map).bindPopup('<strong>Lokasi laporan</strong>');

            let memberMarker = null;
            let memberAccuracyCircle = null;
            let routeLine = null;
            let routeSignature = '';
            let routeFitted = false;
            let trailLines = [];
            let trailSignature = '';
            let cellMarkers = [];

            @if($report->activeAssignment?->member?->memberLocation)
                const memberPoint = [{{ $report->activeAssignment->member->memberLocation->latitude }}, {{ $report->activeAssignment->member->memberLocation->longitude }}];
                memberMarker = L.marker(memberPoint, { icon: TimsarMap.icon('member') }).addTo(map).bindPopup('<strong>{{ $isClosed ? 'Posisi terakhir petugas' : 'Petugas ditugaskan' }}</strong>');
            @endif
            @if($report->activeAssignment?->route_geometry_json)
                const routeGeometry = @json($report->activeAssignment->route_geometry_json);
                if (routeGeometry?.coordinates?.length) {
                    routeLine = L.polyline(routeGeometry.coordinates.map((point) => [point[1], point[0]]), TimsarMap.routeOptions()).addTo(map);
                    routeSignature = JSON.stringify(routeGeometry.coordinates);
                    map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });
                    routeFitted = true;
                }
            @endif

            function formatDistance(meters) {
                if (!meters) return '-';
                return meters >= 1000 ? `${(meters / 1000).toFixed(2)} km` : `${Math.round(meters)} m`;
            }

            function formatDuration(seconds) {
                if (!seconds) return '-';
                return `${Math.max(1, Math.round(seconds / 60))} menit`;
            }

            function geometryToLatLngs(geometry) {
                if (!geometry || !geometry.coordinates) return [];
                return geometry.coordinates.map((point) => [point[1], point[0]]);
            }

            function clearTrailLines() {
                trailLines.forEach((line) => line.remove());
                trailLines = [];
                cellMarkers.forEach((marker) => marker.remove());
                cellMarkers = [];
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function cellLabel(cell) {
                if (!cell) return '-';
                return `${cell.operator || 'Operator'} ${cell.radio_type || 'CELL'} / ${cell.cell_id || '-'}`;
            }

            function signalLabel(point) {
                const values = [];
                if (point.rsrp_dbm !== null && point.rsrp_dbm !== undefined) values.push(`RSRP ${point.rsrp_dbm} dBm`);
                if (point.signal_dbm !== null && point.signal_dbm !== undefined) values.push(`Sinyal ${point.signal_dbm} dBm`);
                if (point.accuracy !== null && point.accuracy !== undefined) values.push(`GPS ${Math.round(point.accuracy)} m`);
                return values.length ? values.join(' - ') : 'Detail sinyal belum tersedia';
            }

            function localTime(value) {
                if (!value) return '-';
                return new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }

            function localDate(value) {
                if (!value) return '-';
                return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }

            function distanceText(meters) {
                const value = Number(meters || 0);
                return value >= 1000 ? `${(value / 1000).toFixed(2)} km` : `${Math.round(value)} m`;
            }

            function evidenceCellText(cell) {
                if (!cell) return 'Belum ada data BTS';
                return `${cell.operator || 'Operator'} ${cell.radio_type || 'CELL'} / Cell ${cell.cell_id || '-'}`;
            }

            function renderMobileLogs(logs) {
                const body = document.getElementById('mobileLogTableBody');
                if (!body) return;

                document.getElementById('mobileLogCountText')?.replaceChildren(
                    document.createTextNode(`Menampilkan ${logs?.length ?? 0} log terbaru`)
                );

                if (!logs?.length) {
                    body.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm font-semibold text-slate-400">Belum ada log mobile computing dari petugas.</td></tr>';
                    return;
                }

                body.innerHTML = logs.map((log) => {
                    const cell = log.cell;
                    const signal = log.signal;
                    return `
                        <tr class="align-top hover:bg-[#242832] transition-colors">
                            <td class="whitespace-nowrap px-4 py-3 font-bold text-white">${escapeHtml(localTime(log.recorded_at_iso))}<br><span class="font-normal text-[10px] text-slate-400">${escapeHtml(localDate(log.recorded_at_iso))}</span></td>
                            <td class="px-4 py-3 font-mono text-slate-300 leading-normal">
                                ${Number(log.latitude).toFixed(6)}, ${Number(log.longitude).toFixed(6)}
                                <br><span class="font-sans text-[10px] text-orange-400 font-bold">Akurasi ${log.accuracy !== null && log.accuracy !== undefined ? Math.round(log.accuracy) + ' m' : '-'}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-extrabold text-white">${escapeHtml(String(log.network_type || 'unknown').toUpperCase())}</td>
                            <td class="px-4 py-3 text-slate-300 leading-normal">
                                ${cell ? `
                                    <span class="font-black text-amber-300">${escapeHtml(cell.operator || 'Operator')} ${escapeHtml(cell.radio_type || 'CELL')}</span>
                                    <br><span class="font-mono text-[10px] text-slate-400">Cell ${escapeHtml(cell.cell_id || '-')}</span>
                                    <br><span class="text-[10px] text-slate-500 font-medium">TAC/LAC ${escapeHtml(cell.tac_or_lac || '-')} - PCI ${escapeHtml(cell.pci_or_psc || '-')}</span>
                                ` : '<span class="text-slate-500 italic">Tidak tersedia</span>'}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-slate-300 leading-normal">
                                ${signal ? `RSRP ${escapeHtml(signal.rsrp_dbm ?? '-')} dBm<br><span class="text-[10px] text-slate-400">RSRQ ${escapeHtml(signal.rsrq_db ?? '-')} / SINR ${escapeHtml(signal.sinr_db ?? '-')}</span>` : '-'}
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function setEvidenceSummary(summary) {
                if (!summary) return;

                document.getElementById('evidenceGpsText')?.replaceChildren(document.createTextNode(Number(summary.gps_points || 0).toLocaleString('id-ID')));
                document.getElementById('evidenceCellText')?.replaceChildren(document.createTextNode(Number(summary.cell_observations || 0).toLocaleString('id-ID')));
                document.getElementById('evidenceNetworkText')?.replaceChildren(document.createTextNode(`${Number(summary.network_changes || 0).toLocaleString('id-ID')}x`));
                document.getElementById('evidenceHandoverText')?.replaceChildren(document.createTextNode(`${Number(summary.handovers || 0).toLocaleString('id-ID')}x`));
                document.getElementById('evidenceDistanceText')?.replaceChildren(document.createTextNode(distanceText(summary.distance_meters)));
                document.getElementById('evidenceFirstCellText')?.replaceChildren(document.createTextNode(evidenceCellText(summary.first_cell)));
                document.getElementById('evidenceLatestCellText')?.replaceChildren(document.createTextNode(evidenceCellText(summary.latest_cell)));
                document.getElementById('evidenceLastSeenText')?.replaceChildren(document.createTextNode(summary.last_at ? `Update ${localTime(summary.last_at)}` : 'Belum ada ping'));
            }

            function setTrailData(trail) {
                const signature = JSON.stringify([trail?.segments ?? [], trail?.handovers ?? [], trail?.cell_points ?? []]);
                if (signature === trailSignature) return;

                trailSignature = signature;
                clearTrailLines();

                (trail?.segments ?? []).forEach((segment) => {
                    const latLngs = (segment.points ?? []).map((point) => [point.latitude, point.longitude]);
                    if (latLngs.length < 2) return;

                    trailLines.push(L.polyline(latLngs, TimsarMap.trailOptions()).addTo(map));
                });

                (trail?.cell_points ?? []).forEach((point) => {
                    const isFirst = point.event === 'first';
                    const marker = L.marker([point.latitude, point.longitude], {
                        icon: TimsarMap.icon('cell', { pulse: false }),
                    }).addTo(map).bindPopup(`
                        <strong>${isFirst ? 'BTS awal terekam' : 'BTS berubah'}</strong><br>
                        <span class="text-xs text-slate-200">${escapeHtml(cellLabel(point.cell))}</span><br>
                        <span class="text-xs text-slate-400">${escapeHtml(signalLabel(point))}</span><br>
                        <span class="text-xs text-slate-400">${escapeHtml(new Date(point.observed_at).toLocaleString('id-ID'))}</span>
                    `);
                    cellMarkers.push(marker);
                });

                const pointCount = trail?.summary?.point_count ?? 0;
                const travelled = pointCount > 0
                    ? (trail.summary.distance_meters > 0 ? formatDistance(trail.summary.distance_meters) : '0 m')
                    : '-';
                document.getElementById('trailDistanceText')?.replaceChildren(document.createTextNode(travelled));
                document.getElementById('trailPointText')?.replaceChildren(document.createTextNode(`${pointCount} titik`));
                const handovers = trail?.handovers ?? [];
                const cellPoints = trail?.cell_points ?? [];
                document.getElementById('trailNetworkText')?.replaceChildren(document.createTextNode(`${handovers.length}x BTS`));
                document.getElementById('handoverCountText').textContent = `${cellPoints.length} titik BTS / ${handovers.length} handover`;
                document.getElementById('handoverTimeline').innerHTML = cellPoints.length
                    ? cellPoints.slice().reverse().map((point) => `
                        <button type="button" class="w-full rounded-xl border border-[#333846] bg-[#181a20] p-3.5 text-left hover:bg-[#242832] transition-all hover:border-orange-500" data-cell-lat="${point.latitude}" data-cell-lng="${point.longitude}">
                            <span class="block text-xs font-black text-amber-300">${point.event === 'first' ? 'BTS Awal' : 'BTS Berubah'} - ${escapeHtml(cellLabel(point.cell))}</span>
                            <span class="mt-1 block text-xs text-slate-300 font-medium">${escapeHtml(new Date(point.observed_at).toLocaleString('id-ID'))} • ${escapeHtml(signalLabel(point))}</span>
                            <span class="mt-1.5 block font-mono text-[10px] text-slate-400">Koordinat: ${Number(point.latitude).toFixed(5)}, ${Number(point.longitude).toFixed(5)}</span>
                        </button>
                    `).join('')
                    : '<p class="rounded-lg bg-[#242832] p-4 text-center text-xs text-slate-400">Belum ada data BTS dari aplikasi Android anggota.</p>';

                document.querySelectorAll('[data-cell-lat]').forEach((button) => {
                    button.addEventListener('click', () => map.setView([
                        Number(button.dataset.cellLat),
                        Number(button.dataset.cellLng),
                    ], 17, { animate: true }));
                });
            }

            async function refreshTrail() {
                if (!trailUrl) return;

                try {
                    const res = await fetch(trailUrl, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;

                    setTrailData(await res.json());
                } catch (error) {
                    //
                }
            }

            async function refreshMobileLog() {
                if (!mobileLogUrl) return;

                try {
                    const res = await fetch(mobileLogUrl, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;

                    const data = await res.json();
                    setEvidenceSummary(data.summary);
                    renderMobileLogs(data.logs);
                } catch (error) {
                    //
                }
            }

            async function refreshReportDetail() {
                const res = await fetch('{{ route('public.tracking.data', $report->tracking_code) }}', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;

                const data = await res.json();
                document.getElementById('reportStatusBadge').textContent = data.report.status_label;

                if (data.assignment) {
                    document.getElementById('assignmentStatusText')?.replaceChildren(document.createTextNode(data.assignment.status_label));
                    document.getElementById('assignmentDistanceText')?.replaceChildren(document.createTextNode(formatDistance(data.assignment.distance_meters)));
                    document.getElementById('assignmentDurationText')?.replaceChildren(document.createTextNode(formatDuration(data.assignment.duration_seconds)));
                }

                if (data.member) {
                    const onlineText = document.getElementById('memberOnlineText');
                    if (onlineText) {
                        onlineText.textContent = data.member.is_online ? 'Online' : 'Offline';
                        onlineText.className = `font-black ${data.member.is_online ? 'text-emerald-400' : 'text-slate-500'}`;
                    }

                    document.getElementById('memberLastSeenText')?.replaceChildren(document.createTextNode(
                        data.member.last_seen_at ? new Date(data.member.last_seen_at).toLocaleTimeString('id-ID') : '-'
                    ));
                    document.getElementById('memberGpsMetaText')?.replaceChildren(document.createTextNode(
                        `${data.member.network_type || 'unknown'}${data.member.accuracy ? ' - akurasi ' + Math.round(data.member.accuracy) + ' m' : ''}`
                    ));

                    if (data.member.latitude && data.member.longitude) {
                        const point = [data.member.latitude, data.member.longitude];
                        if (!memberMarker) {
                            memberMarker = L.marker(point, { icon: TimsarMap.icon('member') }).addTo(map).bindPopup('<strong>Petugas ditugaskan</strong>');
                        } else {
                            TimsarMap.moveMarker(memberMarker, point);
                        }

                        if (data.member.accuracy) {
                            if (!memberAccuracyCircle) {
                                memberAccuracyCircle = L.circle(point, {
                                    radius: data.member.accuracy,
                                    color: '#10b981',
                                    fillColor: '#10b981',
                                    fillOpacity: 0.08,
                                    weight: 1,
                                }).addTo(map);
                            } else {
                                memberAccuracyCircle.setLatLng(point);
                                memberAccuracyCircle.setRadius(data.member.accuracy);
                            }
                        }
                    }
                }

                const latLngs = geometryToLatLngs(data.assignment?.route_geometry);
                const nextSignature = JSON.stringify(data.assignment?.route_geometry?.coordinates ?? []);
                if (latLngs.length && nextSignature !== routeSignature) {
                    routeSignature = nextSignature;
                    if (!routeLine) {
                        routeLine = L.polyline(latLngs, TimsarMap.routeOptions()).addTo(map);
                    } else {
                        routeLine.setLatLngs(latLngs);
                    }

                    if (!routeFitted) {
                        map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });
                        routeFitted = true;
                    }
                }
            }

            refreshTrail();
            refreshMobileLog();
            if (!reportIsClosed) {
                refreshReportDetail();
                setInterval(refreshReportDetail, 3000);
                setInterval(refreshTrail, 5000);
                setInterval(refreshMobileLog, 10000);
            }
        </script>
    @endpush
</x-layouts.app>
