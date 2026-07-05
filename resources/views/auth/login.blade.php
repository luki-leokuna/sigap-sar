<x-layouts.app title="Login Posko SIGAP-SAR">

    @push('scripts')
        <style>
            @keyframes pulseGlow {
                0%, 100% { opacity: 0.4; transform: scale(1); }
                50% { opacity: 0.8; transform: scale(1.05); }
            }
            .animate-pulse-glow { animation: pulseGlow 4s ease-in-out infinite; }
            
            .form-input-tactical {
                width: 100%;
                padding: 0.75rem 1rem 0.75rem 2.75rem;
                border-radius: 0.75rem;
                border: 1px solid #cbd5e1;
                font-size: 0.875rem;
                font-weight: 600;
                color: #0f172a;
                background: #ffffff;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                transition: all 0.2s ease;
                outline: none;
            }
            .form-input-tactical:focus {
                border-color: #f97316;
                box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
                background: #ffffff;
            }
            .form-input-tactical:focus + .form-icon {
                color: #f97316;
            }
            .form-icon {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #64748b;
                pointer-events: none;
                transition: color 0.2s ease;
            }
        </style>
    @endpush

    <div class="mx-auto max-w-6xl px-4 py-8 sm:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            {{-- Left Column: Tactical Hero Portal --}}
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 border border-orange-200 px-3.5 py-1.5 text-xs font-black text-orange-700 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-orange-500 animate-ping"></span>
                    <span>SISTEM KOMANDO TANGGAP DARURAT v3.0</span>
                </div>
                
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-slate-900 leading-tight">
                    Pusat Kendali & <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 via-amber-600 to-red-600">Operasi Rescue SAR</span>
                </h1>
                
                <p class="text-sm sm:text-base text-slate-600 font-medium max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Platform navigasi taktis terintegrasi untuk pemantauan koordinat anggota rescue secara real-time, pengiriman tim tanggap darurat, dan analisis lokasi kejadian insiden.
                </p>

                {{-- Tactical Live Stats Grid --}}
                <div class="grid grid-cols-3 gap-3 sm:gap-4 pt-4 max-w-lg mx-auto lg:mx-0">
                    <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-4 shadow-lg backdrop-blur-md">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status Sinyal</div>
                        <div class="text-lg sm:text-2xl font-black text-emerald-600 flex items-center justify-center lg:justify-start gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>ONLINE</span>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-4 shadow-lg backdrop-blur-md">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Peta Komando</div>
                        <div class="text-lg sm:text-2xl font-black text-orange-600">CARTO-V</div>
                    </div>
                    <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-4 shadow-lg backdrop-blur-md">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Keamanan</div>
                        <div class="text-lg sm:text-2xl font-black text-amber-600">ENCRYPTED</div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Glassmorphic Login Card --}}
            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-2xl relative overflow-hidden">
                    
                    {{-- Decorative Top Glow --}}
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-orange-500/10 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
                    
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200/80">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-orange-600 via-amber-600 to-red-600 text-lg font-black text-white shadow-md shadow-orange-500/20">SG</span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">Otorisasi Posko</h2>
                            <p class="text-xs text-slate-500 font-semibold">Silakan masukkan kredensial akses komando</p>
                        </div>
                    </div>

                    <form 
                        method="POST" 
                        action="{{ route('login.store') }}" 
                        class="space-y-5"
                        id="loginForm"
                    >
                        @csrf
                        
                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2" for="email">ID / Email Petugas</label>
                            <div class="relative">
                                <input 
                                    id="email"
                                    name="email" 
                                    type="email"
                                    value="{{ old('email') }}" 
                                    placeholder="nama@timsar.test"
                                    class="form-input-tactical" 
                                    required
                                >
                                <svg class="form-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2" for="password">Kata Sandi</label>
                            <div class="relative">
                                <input 
                                    id="password"
                                    name="password" 
                                    type="password"
                                    value="" 
                                    placeholder="••••••••"
                                    class="form-input-tactical" 
                                    required
                                >
                                <svg class="form-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" id="loginBtn" class="w-full rounded-xl bg-gradient-to-r from-orange-600 via-amber-600 to-red-600 py-3 text-sm font-black text-white shadow-md shadow-orange-500/20 hover:brightness-110 active:scale-[0.98] transition-all">
                            MASUK SISTEM KOMANDO &rarr;
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('loginForm').addEventListener('submit', () => {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = 'OTENTIKASI BERJALAN...';
            btn.className = 'w-full rounded-xl bg-slate-200 py-3 text-sm font-black text-slate-500 cursor-not-allowed border border-slate-300 shadow-none';
        });
    </script>
    @endpush
</x-layouts.app>
