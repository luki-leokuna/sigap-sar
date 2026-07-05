<x-layouts.app title="Riwayat Laporan TIMSAR">
    <section class="space-y-6 mx-auto max-w-7xl px-2 sm:px-4 py-4">
        
        {{-- ── TACTICAL ARCHIVE HEADER ── --}}
        <header class="flex flex-col gap-4 border-b border-slate-200/80 pb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-slate-700 border border-slate-300 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>ARSIP OPERASI TIMSAR</span>
                </div>
                <h1 class="mt-2 text-2xl font-black text-slate-900 sm:text-4xl tracking-tight">Riwayat & Log Laporan</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600 font-semibold">Database pusat untuk laporan insiden yang telah selesai ditangani atau dibatalkan oleh posko.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white hover:border-orange-500 hover:text-orange-600 px-5 py-2.5 text-xs font-black text-slate-700 shadow-sm transition-all">
                &larr; Kembali ke Radar Posko
            </a>
        </header>

        {{-- ── STATS COMMAND CARDS ── --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-slate-300 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-slate-100 blur-2xl"></div>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Total Arsip</span>
                <p class="mt-2 text-3xl sm:text-5xl font-black text-slate-900 font-mono tracking-tight">{{ $stats['total'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">Semua Catatan</span>
            </div>
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/50 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-50 blur-2xl"></div>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Misi Selesai</span>
                <p class="mt-2 text-3xl sm:text-5xl font-black text-emerald-600 font-mono tracking-tight">{{ $stats['completed'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">Berhasil Ditangani</span>
            </div>
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-red-500/50 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-red-50 blur-2xl"></div>
                <span class="text-[10px] font-black uppercase tracking-wider text-red-600">Dibatalkan</span>
                <p class="mt-2 text-3xl sm:text-5xl font-black text-red-600 font-mono tracking-tight">{{ $stats['cancelled'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md">Palsu / Dibatalkan</span>
            </div>
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl relative overflow-hidden group hover:border-blue-500/50 transition-all">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-blue-50 blur-2xl"></div>
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600">Bulan Ini</span>
                <p class="mt-2 text-3xl sm:text-5xl font-black text-blue-600 font-mono tracking-tight">{{ $stats['this_month'] }}</p>
                <span class="mt-2 inline-block text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">Periode Aktif</span>
            </div>
        </div>

        {{-- ── FILTER & SEARCH BAR ── --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl space-y-5">
            <div class="flex flex-wrap items-center gap-2 border-b border-slate-200/80 pb-4" aria-label="Filter status laporan">
                <span class="text-xs font-black uppercase text-slate-500 mr-2">Filter Status:</span>
                @foreach(['all' => 'Semua', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                    <a href="{{ route('admin.reports.index', array_filter(['status' => $value, 'q' => $filters['q'], 'date_from' => $filters['date_from'], 'date_to' => $filters['date_to']])) }}" class="rounded-xl px-4 py-2 text-xs font-black transition-all shadow-sm {{ $filters['status'] === $value ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">{{ $label }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 md:grid-cols-[minmax(220px,1fr)_180px_180px_auto]">
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                <div>
                    <label for="q" class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-slate-700">Cari Kata Kunci</label>
                    <input id="q" name="q" value="{{ $filters['q'] }}" type="search" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-xs text-slate-900 placeholder-slate-400 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono shadow-sm" placeholder="Kode, nama, HP, atau kejadian...">
                </div>
                <div>
                    <label for="date_from" class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-slate-700">Dari Tanggal</label>
                    <input id="date_from" name="date_from" value="{{ $filters['date_from'] }}" type="date" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-xs text-slate-900 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono shadow-sm">
                </div>
                <div>
                    <label for="date_to" class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-slate-700">Sampai Tanggal</label>
                    <input id="date_to" name="date_to" value="{{ $filters['date_to'] }}" type="date" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-xs text-slate-900 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono shadow-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="h-11 rounded-xl bg-orange-600 hover:bg-orange-500 px-5 text-xs font-black text-white shadow-md shadow-orange-500/20 transition-all">🔍 Terapkan</button>
                    <a href="{{ route('admin.reports.index') }}" class="grid h-11 place-items-center rounded-xl border border-slate-300 bg-slate-50 hover:bg-slate-100 px-4 text-xs font-black text-slate-700 transition-all shadow-sm">Reset</a>
                </div>
            </form>
        </div>

        {{-- ── REPORTS TABLE / CARDS ── --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200/80 px-6 py-4 bg-slate-50">
                <div class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-blue-100 text-blue-600 font-black text-sm border border-blue-200 shadow-sm">📂</span>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Database Hasil Pencarian</h2>
                        <p class="text-xs font-semibold text-slate-500">{{ $reports->total() }} laporan terarsip</p>
                    </div>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-slate-50/90 text-[10px] uppercase font-black tracking-wider text-slate-600 border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-4">Laporan / Kode Tracking</th>
                            <th class="px-6 py-4">Pelapor</th>
                            <th class="px-6 py-4">Status & Catatan</th>
                            <th class="px-6 py-4">Petugas Penanggung Jawab</th>
                            <th class="px-6 py-4">Waktu Penutupan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                        @forelse($reports as $report)
                            @php($closedAt = $report->closed_at ?? $report->activeAssignment?->completed_at ?? $report->updated_at)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <p class="font-black text-slate-900 group-hover:text-orange-600 transition-colors">{{ $report->incident_type }}</p>
                                    <span class="mt-1 inline-block font-mono text-[11px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-300">{{ $report->tracking_code }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 text-xs">{{ $report->reporter_name }}</p>
                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $report->reporter_phone }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-md px-2.5 py-1 text-[10px] font-black uppercase tracking-wider border {{ $report->status === \App\Models\Report::STATUS_COMPLETED ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                        {{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}
                                    </span>
                                    @if($report->closure_notes)<p class="mt-1.5 max-w-56 truncate text-[11px] text-slate-600 font-medium" title="{{ $report->closure_notes }}">💬 "{{ $report->closure_notes }}"</p>@endif
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-orange-600">
                                    {{ $report->activeAssignment?->member?->name ?? $report->assignedMember?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    <p class="font-bold text-slate-900">{{ $closedAt->format('d M Y') }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $closedAt->format('H:i') }} WITA</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-orange-600 hover:text-white px-3.5 py-2 text-xs font-black text-slate-700 transition-all border border-slate-300 shadow-sm">
                                        <span>Detail</span> &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-16 text-center text-xs font-bold text-slate-500">Tidak ada laporan arsip yang sesuai dengan filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="divide-y divide-slate-200/80 md:hidden">
                @forelse($reports as $report)
                    @php($closedAt = $report->closed_at ?? $report->activeAssignment?->completed_at ?? $report->updated_at)
                    <a href="{{ route('admin.reports.show', $report) }}" class="block p-5 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-black text-slate-900 text-base">{{ $report->incident_type }}</p>
                                <span class="mt-1 inline-block font-mono text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-300">{{ $report->tracking_code }}</span>
                            </div>
                            <span class="shrink-0 rounded-md px-2 py-1 text-[10px] font-black uppercase tracking-wider border {{ $report->status === \App\Models\Report::STATUS_COMPLETED ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">{{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div><span class="block text-[10px] font-black uppercase text-slate-500">Pelapor</span><strong class="text-slate-900">{{ $report->reporter_name }}</strong></div>
                            <div><span class="block text-[10px] font-black uppercase text-slate-500">Ditutup Pada</span><strong class="text-slate-900 font-mono">{{ $closedAt->format('d M Y, H:i') }}</strong></div>
                        </div>
                        @if($report->closure_notes)<p class="mt-3 line-clamp-2 text-xs text-slate-600 italic">💬 "{{ $report->closure_notes }}"</p>@endif
                    </a>
                @empty
                    <p class="px-6 py-16 text-center text-xs font-bold text-slate-500">Tidak ada laporan arsip yang sesuai dengan filter.</p>
                @endforelse
            </div>
        </div>

        @if($reports->hasPages())
            <div class="pt-2">{{ $reports->links() }}</div>
        @endif
    </section>
</x-layouts.app>
