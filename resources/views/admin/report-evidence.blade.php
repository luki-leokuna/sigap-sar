<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Audit Telemetri • {{ $report->tracking_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; color: #0f172a !important; padding: 0 !important; }
            .print-break { break-inside: avoid; }
            .print-border { border-color: #cbd5e1 !important; background: #ffffff !important; color: #0f172a !important; box-shadow: none !important; }
            .print-text-dark { color: #0f172a !important; }
            .print-bg-light { background: #f8fafc !important; border-color: #e2e8f0 !important; color: #0f172a !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-orange-500 selection:text-white min-h-screen py-8 px-4">
    @php
        $summary = $evidence['summary'];
        $logs = $evidence['logs'];
        $timeline = $evidence['timeline'];
        $cellPoints = collect($evidence['trail']['cell_points'] ?? []);
        $handovers = collect($evidence['trail']['handovers'] ?? []);
        $distance = $summary['distance_meters'] >= 1000
            ? number_format($summary['distance_meters'] / 1000, 2) . ' km'
            : number_format($summary['distance_meters']) . ' m';
    @endphp

    <main class="mx-auto max-w-5xl bg-white print-border border border-slate-200/80 p-6 sm:p-10 rounded-3xl shadow-xl">
        {{-- Action Bar (No Print) --}}
        <div class="no-print mb-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-slate-50 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-3 w-3 rounded-full bg-orange-500 animate-pulse"></span>
                <span class="text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">SIGAP-SAR • Audit & Telemetry Export</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.show', $report) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-black text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-all shadow-sm">
                    ⬅️ Kembali ke Komando
                </a>
                <button type="button" onclick="window.print()" class="rounded-xl bg-orange-600 hover:bg-orange-500 px-5 py-2 text-xs font-black text-white shadow-md shadow-orange-500/20 transition-all active:scale-95">
                    🖨️ Cetak / Unduh PDF Audit
                </button>
            </div>
        </div>

        {{-- Header Document --}}
        <header class="border-b-2 border-slate-200/80 print-border pb-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded bg-red-50 border border-red-200 print-bg-light text-red-700 font-mono text-[10px] font-black uppercase tracking-widest">RAHASIA / AUDIT RESMI</span>
                        <span class="text-xs font-mono font-bold text-slate-500 print-text-dark">TIMSAR NTB</span>
                    </div>
                    <h1 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 print-text-dark tracking-tight">REKAP BUKTI OPERASI & TELEMETRI</h1>
                    <p class="mt-2 text-sm font-bold text-orange-600 font-mono flex items-center gap-2">
                        <span>ID INCIDENT: <strong class="text-slate-900 print-text-dark">{{ $report->tracking_code }}</strong></span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-700 print-text-dark">{{ $report->incident_type }}</span>
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-4 text-right text-xs text-slate-600 print-text-dark space-y-1 sm:min-w-[220px] shadow-sm">
                    <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Waktu Ekspor Dokumen</p>
                    <p class="font-mono font-black text-slate-900 print-text-dark text-sm">{{ $generatedAt->format('d M Y, H:i:s') }}</p>
                    <div class="pt-2 border-t border-slate-200/80 print-border">
                        <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Status Laporan</p>
                        <p class="font-black text-orange-600 uppercase tracking-wide text-xs mt-0.5">{{ $summary['report_status'] }}</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Grid Data Pelapor & Petugas --}}
        <section class="print-break mt-8 grid gap-6 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200/80 print-border pb-3">
                    <h2 class="text-xs font-mono font-black uppercase tracking-widest text-orange-600">01 // DATA LAPORAN & LOKASI</h2>
                    <span class="text-[10px] font-bold text-slate-500 font-mono">DISPATCH</span>
                </div>
                <dl class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Pelapor</dt><dd class="font-black text-slate-900 print-text-dark text-right">{{ $report->reporter_name }} • {{ $report->reporter_phone }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Koordinat GPS</dt><dd class="font-mono font-bold text-orange-600 text-right">{{ number_format($report->latitude, 7) }}, {{ number_format($report->longitude, 7) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Akurasi Perangkat</dt><dd class="font-mono font-bold text-slate-700 print-text-dark text-right">{{ $report->accuracy ? number_format($report->accuracy) . ' meter' : '-' }}</dd></div>
                    <div class="pt-2 border-t border-slate-200/80 print-border"><dt class="font-bold text-slate-500 print-text-dark mb-1">Deskripsi Darurat:</dt><dd class="text-slate-700 print-text-dark font-medium leading-relaxed bg-white print-bg-light p-3 rounded-xl border border-slate-200/80 print-border shadow-sm">{{ $report->description }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200/80 print-border pb-3">
                    <h2 class="text-xs font-mono font-black uppercase tracking-widest text-blue-600">02 // DATA PETUGAS & PENUGASAN</h2>
                    <span class="text-[10px] font-bold text-slate-500 font-mono">RESCUE UNIT</span>
                </div>
                <dl class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Petugas Pelaksana</dt><dd class="font-black text-slate-900 print-text-dark text-right">{{ $summary['member_name'] }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Status Operasi</dt><dd class="font-black text-emerald-700 text-right uppercase">{{ $summary['assignment_status'] }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Mulai Rekam Telemetri</dt><dd class="font-mono font-bold text-slate-700 print-text-dark text-right">{{ $summary['started_at']?->format('d M Y, H:i:s') ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-bold text-slate-500 print-text-dark">Pembaruan Terakhir</dt><dd class="font-mono font-bold text-orange-600 text-right">{{ $summary['last_at']?->format('d M Y, H:i:s') ?? '-' }}</dd></div>
                </dl>
            </div>
        </section>

        {{-- Ringkasan Mobile Computing --}}
        <section class="print-break mt-8 rounded-2xl border border-slate-200/80 print-border bg-white print-bg-light p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200/80 print-border pb-4 mb-5">
                <div>
                    <h2 class="text-sm font-mono font-black uppercase tracking-widest text-slate-900 print-text-dark">03 // TELEMETRY & MOBILE COMPUTING METRICS</h2>
                    <p class="text-xs text-slate-600 print-text-dark mt-0.5">Statistik observasi sinyal, seluler BTS, dan pergerakan GPS dari perangkat Android.</p>
                </div>
                <span class="px-3 py-1 rounded bg-orange-50 border border-orange-200 text-orange-700 font-mono text-xs font-black shadow-sm">VERIFIED DATA</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-5">
                <div class="rounded-xl border border-blue-200 bg-blue-50 print-bg-light p-4 text-center shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase text-blue-600">Titik GPS</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-mono font-black text-slate-900 print-text-dark">{{ number_format($summary['gps_points']) }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 print-bg-light p-4 text-center shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase text-amber-600">Log BTS</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-mono font-black text-slate-900 print-text-dark">{{ number_format($summary['cell_observations']) }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 print-bg-light p-4 text-center shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase text-emerald-600">Pindah Jaringan</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-mono font-black text-slate-900 print-text-dark">{{ number_format($summary['network_changes']) }}x</p>
                </div>
                <div class="rounded-xl border border-orange-200 bg-orange-50 print-bg-light p-4 text-center shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase text-orange-600">Handover BTS</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-mono font-black text-slate-900 print-text-dark">{{ number_format($summary['handovers']) }}x</p>
                </div>
                <div class="rounded-xl border border-slate-200/80 bg-slate-50 print-bg-light p-4 text-center shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase text-slate-500 print-text-dark">Jalur Terekam</p>
                    <p class="mt-2 text-xl sm:text-2xl font-mono font-black text-orange-600">{{ $distance }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-4 shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase tracking-wider text-slate-500 print-text-dark">🗼 MENARA BTS AWAL (STARTING CELL)</p>
                    <p class="mt-1.5 text-sm font-mono font-black text-slate-900 print-text-dark">
                        @if($summary['first_cell'])
                            {{ $summary['first_cell']['operator'] }} {{ $summary['first_cell']['radio_type'] }} / Cell ID: {{ $summary['first_cell']['cell_id'] }}
                        @else
                            Belum tersedia
                        @endif
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-4 shadow-sm">
                    <p class="text-[10px] font-mono font-black uppercase tracking-wider text-slate-500 print-text-dark">📶 MENARA BTS TERBARU (LATEST CELL)</p>
                    <p class="mt-1.5 text-sm font-mono font-black text-slate-900 print-text-dark">
                        @if($summary['latest_cell'])
                            {{ $summary['latest_cell']['operator'] }} {{ $summary['latest_cell']['radio_type'] }} / Cell ID: {{ $summary['latest_cell']['cell_id'] }}
                        @else
                            Belum tersedia
                        @endif
                    </p>
                </div>
            </div>
        </section>

        {{-- Titik BTS di Peta --}}
        <section class="print-break mt-8 rounded-2xl border border-slate-200/80 print-border bg-white print-bg-light p-6 shadow-sm">
            <h2 class="text-sm font-mono font-black uppercase tracking-widest text-orange-600 border-b border-slate-200/80 print-border pb-3">04 // TITIK OBSERVASI BTS LAPANGAN</h2>
            <div class="mt-4 space-y-3">
                @forelse($cellPoints as $point)
                    <div class="rounded-xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-4 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shadow-sm">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-black uppercase {{ $point['event'] === 'first' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }} mr-2">
                                {{ $point['event'] === 'first' ? 'BTS START' : 'HANDOVER EVENT' }}
                            </span>
                            <strong class="text-slate-900 print-text-dark font-black">{{ $point['cell']['operator'] }} {{ $point['cell']['radio_type'] }}</strong>
                            <span class="text-slate-600 font-mono print-text-dark">/ Cell ID: {{ $point['cell']['cell_id'] }}</span>
                        </div>
                        <div class="text-right font-mono text-xs text-slate-500 print-text-dark">
                            <span class="text-orange-600">{{ number_format($point['latitude'], 7) }}, {{ number_format($point['longitude'], 7) }}</span>
                            <span class="mx-1">•</span>
                            <span>{{ \Illuminate\Support\Carbon::parse($point['observed_at'])->format('d M Y, H:i:s') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 print-bg-light p-4 text-xs font-bold text-slate-500 text-center border border-slate-200/80 shadow-sm">Belum ada observasi perpindahan BTS terekam.</p>
                @endforelse
            </div>
        </section>

        {{-- Timeline Operasi --}}
        <section class="print-break mt-8 rounded-2xl border border-slate-200/80 print-border bg-white print-bg-light p-6 shadow-sm">
            <h2 class="text-sm font-mono font-black uppercase tracking-widest text-emerald-700 border-b border-slate-200/80 print-border pb-3">05 // TIMELINE KRONOLOGI OPERASI</h2>
            <div class="mt-4 space-y-3">
                @foreach($timeline as $item)
                    <div class="grid gap-2 rounded-xl border border-slate-200/80 print-border bg-slate-50 print-bg-light p-4 text-xs sm:text-sm sm:grid-cols-[180px_1fr] items-baseline shadow-sm">
                        <p class="font-mono font-bold text-orange-600">{{ $item['time']->format('d M Y, H:i:s') }}</p>
                        <div>
                            <p class="font-black text-slate-900 print-text-dark text-sm">{{ $item['event'] }} <span class="font-mono font-bold text-xs text-slate-500 print-text-dark">({{ $item['actor'] }})</span></p>
                            @if($item['note'])<p class="mt-1 text-xs text-slate-700 print-text-dark bg-white print-bg-light p-2.5 rounded-lg border border-slate-200/80 print-border font-medium shadow-sm">{{ $item['note'] }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Log Mentah Mobile Computing --}}
        <section class="mt-8 rounded-2xl border border-slate-200/80 print-border bg-white print-bg-light p-6 overflow-hidden shadow-sm">
            <h2 class="text-sm font-mono font-black uppercase tracking-widest text-slate-900 print-text-dark border-b border-slate-200/80 print-border pb-3">06 // DATA MENTAH LOG TELEMETRI LAPANGAN</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/80 print-border text-left text-xs">
                    <thead class="bg-slate-50 print-bg-light font-mono text-[10px] uppercase tracking-wider text-slate-600 print-text-dark">
                        <tr>
                            <th class="px-4 py-3 font-black">Waktu Rekam</th>
                            <th class="px-4 py-3 font-black">Koordinat GPS & Akurasi</th>
                            <th class="px-4 py-3 font-black">Jaringan</th>
                            <th class="px-4 py-3 font-black">Menara Seluler (BTS / Cell ID)</th>
                            <th class="px-4 py-3 font-black">Kekuatan Sinyal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 print-border font-medium text-slate-700 print-text-dark">
                        @forelse($logs as $log)
                            <tr class="align-top hover:bg-slate-50 transition-colors">
                                <td class="whitespace-nowrap px-4 py-3 font-mono font-bold text-slate-900 print-text-dark">{{ $log['recorded_at']?->format('d M Y, H:i:s') }}</td>
                                <td class="px-4 py-3 font-mono leading-relaxed text-slate-700">
                                    {{ number_format($log['latitude'], 6) }}, {{ number_format($log['longitude'], 6) }}<br>
                                    <span class="font-sans text-[10px] text-orange-600 font-bold">Akurasi {{ $log['accuracy'] !== null ? number_format($log['accuracy']) . ' m' : '-' }}</span>
                                </td>
                                <td class="px-4 py-3 font-black text-slate-900 print-text-dark font-mono">{{ strtoupper($log['network_type']) }}</td>
                                <td class="px-4 py-3 leading-relaxed text-slate-700">
                                    @if($log['cell'])
                                        <span class="font-black text-amber-900 print-text-dark">{{ $log['cell']['operator'] }} {{ $log['cell']['radio_type'] }}</span><br>
                                        <span class="font-mono text-[11px] text-slate-900 print-text-dark font-bold">Cell ID: {{ $log['cell']['cell_id'] }}</span><br>
                                        <span class="text-[10px] text-slate-500 print-text-dark font-mono">TAC/LAC: {{ $log['cell']['tac_or_lac'] ?? '-' }} • PCI: {{ $log['cell']['pci_or_psc'] ?? '-' }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Tidak terdeteksi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono leading-relaxed text-slate-700">
                                    @if($log['signal'])
                                        <strong class="text-slate-900 print-text-dark">RSRP: {{ $log['signal']['rsrp_dbm'] ?? '-' }} dBm</strong><br>
                                        <span class="text-[10px] text-slate-500 print-text-dark">RSRQ: {{ $log['signal']['rsrq_db'] ?? '-' }} • SINR: {{ $log['signal']['sinr_db'] ?? '-' }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-12 text-center text-xs font-bold text-slate-500">Belum ada log telemetri dari perangkat Android.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Footer Signature / Audit Trail --}}
        <footer class="mt-12 pt-8 border-t border-slate-200/80 print-border flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-mono">
            <div>
                <p class="font-bold text-slate-600 print-text-dark">SISTEM KOMANDO SIGAP-SAR NTB</p>
                <p>Dokumen ini dihasilkan secara otomatis oleh sistem telemetri posko komando.</p>
            </div>
            <div class="text-right">
                <p>ID VERIFIKASI: <span class="text-orange-600 font-bold">{{ md5($report->tracking_code . $report->created_at) }}</span></p>
                <p>© {{ date('Y') }} TIMSAR NTB • ALL RIGHTS RESERVED</p>
            </div>
        </footer>
    </main>
</body>
</html>
