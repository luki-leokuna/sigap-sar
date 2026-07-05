<x-layouts.app title="Arsip & Riwayat PUSKO - TIMSAR" :hideChrome="true" :fullBleed="true">
    @push('scripts')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            body, .timsar-maxim-admin {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
            }
            /* Custom dark scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: #181a20; }
            ::-webkit-scrollbar-thumb { background: #333846; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #4b5265; }
        </style>
    @endpush

    <div class="timsar-maxim-admin min-h-screen bg-[#181a20] text-slate-100 flex flex-col">
        
        {{-- ── TOP TACTICAL COMMAND BAR (HEADER) ── --}}
        <header class="bg-[#1e222b] border-b border-[#333846] px-4 sm:px-6 py-3.5 shrink-0 flex items-center justify-between gap-4 shadow-xl z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 text-white font-black text-lg shadow-lg shadow-blue-500/20 border border-blue-400/30">
                    📂
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/20 border border-blue-500/30 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-blue-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span> ARSIP OPERASI PUSKO
                        </span>
                        <h1 class="text-base sm:text-lg font-black text-white truncate">Riwayat & Log Laporan</h1>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 hidden sm:block truncate mt-0.5">Database pusat untuk laporan insiden yang telah selesai ditangani atau dibatalkan oleh posko</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-[#242832] hover:bg-[#2c303d] border border-[#333846] hover:border-orange-500 hover:text-orange-400 px-4 py-2 text-xs font-bold text-slate-200 transition-all flex items-center gap-2 shadow-sm">
                    <span>&larr;</span> <span>Kembali ke Radar Posko</span>
                </a>
            </div>
        </header>

        {{-- ── MAIN ARCHIVE CONTENT ── --}}
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6 space-y-6">
            
            {{-- ── STATS COMMAND CARDS ── --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-5 shadow-xl relative overflow-hidden group hover:border-slate-500 transition-all">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-slate-500/10 blur-2xl"></div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Arsip</span>
                    <p class="mt-2 text-3xl sm:text-4xl font-black text-white font-mono tracking-tight">{{ $stats['total'] }}</p>
                    <span class="mt-2 inline-block text-[10px] font-bold text-slate-300 bg-[#242832] border border-[#333846] px-2 py-0.5 rounded-md">Semua Catatan</span>
                </div>
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/50 transition-all">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl"></div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400">Misi Selesai</span>
                    <p class="mt-2 text-3xl sm:text-4xl font-black text-emerald-400 font-mono tracking-tight">{{ $stats['completed'] }}</p>
                    <span class="mt-2 inline-block text-[10px] font-bold text-emerald-300 bg-emerald-500/15 border border-emerald-500/30 px-2 py-0.5 rounded-md">Berhasil Ditangani</span>
                </div>
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-5 shadow-xl relative overflow-hidden group hover:border-red-500/50 transition-all">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-red-500/10 blur-2xl"></div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-red-400">Dibatalkan</span>
                    <p class="mt-2 text-3xl sm:text-4xl font-black text-red-400 font-mono tracking-tight">{{ $stats['cancelled'] }}</p>
                    <span class="mt-2 inline-block text-[10px] font-bold text-red-300 bg-red-500/15 border border-red-500/30 px-2 py-0.5 rounded-md">Palsu / Dibatalkan</span>
                </div>
                <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-5 shadow-xl relative overflow-hidden group hover:border-blue-500/50 transition-all">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl"></div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-400">Bulan Ini</span>
                    <p class="mt-2 text-3xl sm:text-4xl font-black text-blue-400 font-mono tracking-tight">{{ $stats['this_month'] }}</p>
                    <span class="mt-2 inline-block text-[10px] font-bold text-blue-300 bg-blue-500/15 border border-blue-500/30 px-2 py-0.5 rounded-md">Periode Aktif</span>
                </div>
            </div>

            {{-- ── FILTER & SEARCH BAR ── --}}
            <div class="rounded-3xl border border-[#333846] bg-[#1e222b] p-6 shadow-xl space-y-5">
                <div class="flex flex-wrap items-center gap-2 border-b border-[#333846] pb-4" aria-label="Filter status laporan">
                    <span class="text-xs font-black uppercase text-slate-400 mr-2">Filter Status:</span>
                    @foreach(['all' => 'Semua', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                        <a href="{{ route('admin.reports.index', array_filter(['status' => $value, 'q' => $filters['q'], 'date_from' => $filters['date_from'], 'date_to' => $filters['date_to']])) }}" class="rounded-xl px-4 py-2 text-xs font-black transition-all shadow-sm {{ $filters['status'] === $value ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-500/20 border border-orange-500/50' : 'border border-[#333846] bg-[#242832] text-slate-300 hover:bg-[#2c303d] hover:text-white' }}">{{ $label }}</a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 md:grid-cols-[minmax(220px,1fr)_180px_180px_auto]">
                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    <div>
                        <label for="q" class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-slate-400">Cari Kata Kunci</label>
                        <input id="q" name="q" value="{{ $filters['q'] }}" type="search" class="h-11 w-full rounded-xl border border-[#333846] bg-[#242832] px-3.5 text-xs text-white placeholder-slate-500 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono shadow-sm" placeholder="Kode, nama, HP, atau kejadian...">
                    </div>
                    <div>
                        <label for="date_from" class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-slate-400">Dari Tanggal</label>
                        <input id="date_from" name="date_from" value="{{ $filters['date_from'] }}" type="date" class="h-11 w-full rounded-xl border border-[#333846] bg-[#242832] px-3.5 text-xs text-white outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono shadow-sm">
                    </div>
                    <div>
                        <label for="date_to" class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-slate-400">Sampai Tanggal</label>
                        <input id="date_to" name="date_to" value="{{ $filters['date_to'] }}" type="date" class="h-11 w-full rounded-xl border border-[#333846] bg-[#242832] px-3.5 text-xs text-white outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono shadow-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="h-11 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 px-5 text-xs font-black text-white shadow-md shadow-orange-500/20 transition-all">🔍 Terapkan</button>
                        <a href="{{ route('admin.reports.index') }}" class="grid h-11 place-items-center rounded-xl border border-[#333846] bg-[#242832] hover:bg-[#2c303d] px-4 text-xs font-black text-slate-300 transition-all shadow-sm">Reset</a>
                    </div>
                </form>
            </div>

            {{-- ── REPORTS TABLE / CARDS ── --}}
            <div class="overflow-hidden rounded-3xl border border-[#333846] bg-[#1e222b] shadow-xl">
                <div class="flex items-center justify-between border-b border-[#333846] px-6 py-4 bg-[#242832]">
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-blue-500/20 text-blue-400 font-black text-sm border border-blue-500/30 shadow-sm">📂</span>
                        <div>
                            <h2 class="text-sm font-black text-white uppercase tracking-wider">Database Hasil Pencarian</h2>
                            <p class="text-xs font-semibold text-slate-400">{{ $reports->total() }} laporan terarsip</p>
                        </div>
                    </div>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[900px] text-left text-sm">
                        <thead class="bg-[#242832]/90 text-[10px] uppercase font-black tracking-wider text-slate-400 border-b border-[#333846]">
                            <tr>
                                <th class="px-6 py-4">Laporan / Kode Tracking</th>
                                <th class="px-6 py-4">Pelapor</th>
                                <th class="px-6 py-4">Status & Catatan</th>
                                <th class="px-6 py-4">Petugas Penanggung Jawab</th>
                                <th class="px-6 py-4">Waktu Penutupan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#333846]">
                            @forelse($reports as $report)
                                @php($closedAt = $report->closed_at ?? $report->activeAssignment?->completed_at ?? $report->updated_at)
                                <tr class="hover:bg-[#242832] transition-colors group">
                                    <td class="px-6 py-4">
                                        <p class="font-black text-white group-hover:text-orange-400 transition-colors">{{ $report->incident_type }}</p>
                                        <span class="mt-1 inline-block font-mono text-[11px] font-bold text-slate-300 bg-[#181a20] px-2 py-0.5 rounded border border-[#333846]">{{ $report->tracking_code }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-white text-xs">{{ $report->reporter_name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $report->reporter_phone }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-md px-2.5 py-1 text-[10px] font-black uppercase tracking-wider border {{ $report->status === \App\Models\Report::STATUS_COMPLETED ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-red-500/20 text-red-300 border-red-500/40' }}">
                                            {{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}
                                        </span>
                                        @if($report->closure_notes)<p class="mt-1.5 max-w-56 truncate text-[11px] text-slate-400 font-medium" title="{{ $report->closure_notes }}">💬 "{{ $report->closure_notes }}"</p>@endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-orange-400">
                                        {{ $report->activeAssignment?->member?->name ?? $report->assignedMember?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        <p class="font-bold text-white">{{ $closedAt->format('d M Y') }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $closedAt->format('H:i') }} WITA</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center gap-1 rounded-xl bg-[#242832] hover:bg-orange-600 hover:text-white px-3.5 py-2 text-xs font-black text-slate-200 transition-all border border-[#333846] shadow-sm">
                                            <span>Detail</span> &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-16 text-center text-xs font-bold text-slate-400">Tidak ada laporan arsip yang sesuai dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="divide-y divide-[#333846] md:hidden">
                    @forelse($reports as $report)
                        @php($closedAt = $report->closed_at ?? $report->activeAssignment?->completed_at ?? $report->updated_at)
                        <a href="{{ route('admin.reports.show', $report) }}" class="block p-5 hover:bg-[#242832] transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-black text-white text-base">{{ $report->incident_type }}</p>
                                    <span class="mt-1 inline-block font-mono text-[10px] font-bold text-slate-300 bg-[#181a20] px-2 py-0.5 rounded border border-[#333846]">{{ $report->tracking_code }}</span>
                                </div>
                                <span class="shrink-0 rounded-md px-2 py-1 text-[10px] font-black uppercase tracking-wider border {{ $report->status === \App\Models\Report::STATUS_COMPLETED ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-red-500/20 text-red-300 border-red-500/40' }}">{{ \App\Http\Controllers\PublicTrackingController::statusLabel($report->status) }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-slate-300 bg-[#242832] p-3 rounded-2xl border border-[#333846] shadow-sm">
                                <div><span class="block text-[10px] font-black uppercase text-slate-400">Pelapor</span><strong class="text-white">{{ $report->reporter_name }}</strong></div>
                                <div><span class="block text-[10px] font-black uppercase text-slate-400">Ditutup Pada</span><strong class="text-white font-mono">{{ $closedAt->format('d M Y, H:i') }}</strong></div>
                            </div>
                            @if($report->closure_notes)<p class="mt-3 line-clamp-2 text-xs text-slate-400 italic">💬 "{{ $report->closure_notes }}"</p>@endif
                        </a>
                    @empty
                        <p class="px-6 py-16 text-center text-xs font-bold text-slate-400">Tidak ada laporan arsip yang sesuai dengan filter.</p>
                    @endforelse
                </div>
            </div>

            @if($reports->hasPages())
                <div class="pt-2">{{ $reports->links() }}</div>
            @endif
        </main>
    </div>
</x-layouts.app>
