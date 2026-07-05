<x-layouts.app title="Login Posko SIGAP-SAR - Tema Maxim" :hideChrome="true" :fullBleed="true">
    @push('scripts')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            
            body, .timsar-maxim-login {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
            }

            /* Maxim Card Pill Style */
            .maxim-input-card {
                background: #242832;
                border: 1px solid #333846;
                border-radius: 1.25rem;
                padding: 1rem 1.25rem;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }
            .maxim-input-card:focus-within {
                border-color: #f97316;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15), 0 8px 20px rgba(0, 0, 0, 0.3);
            }
            
            .maxim-input {
                background: transparent;
                border: none;
                width: 100%;
                color: #ffffff;
                font-weight: 700;
                font-size: 0.95rem;
                outline: none;
            }
            .maxim-input::placeholder {
                color: #64748b;
                font-weight: 500;
            }

            /* Basarnas Orange Button */
            #loginBtn {
                background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
                color: #ffffff;
                font-weight: 900;
                border-radius: 1.25rem;
                padding: 1.15rem 1.5rem;
                box-shadow: 0 10px 25px -5px rgba(234, 88, 12, 0.4);
                transition: all 0.2s ease;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
            #loginBtn:hover:not(:disabled) {
                filter: brightness(1.1);
                box-shadow: 0 15px 30px -5px rgba(234, 88, 12, 0.5);
                transform: translateY(-1px);
            }
            #loginBtn:active:not(:disabled) {
                transform: scale(0.98);
            }
            #loginBtn:disabled {
                background: #333846 !important;
                color: #64748b !important;
                box-shadow: none !important;
                cursor: not-allowed;
            }
        </style>
    @endpush

    <div class="timsar-maxim-login min-h-screen bg-[#181a20] text-slate-100 flex flex-col justify-between p-4 sm:p-6">
        
        {{-- Top Bar / Back Button --}}
        <div class="max-w-md mx-auto w-full pt-4 sm:pt-8 flex items-center justify-between">
            <a href="{{ route('public.report') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-slate-400 hover:text-white transition-all bg-[#242832] border border-[#333846] px-3.5 py-2 rounded-xl">
                <span>&larr;</span>
                <span>Kembali ke Lapor Darurat</span>
            </a>
            <span class="text-[11px] font-mono font-bold bg-orange-500/10 text-orange-400 border border-orange-500/20 px-2.5 py-1 rounded-lg">POSKO 24 JAM</span>
        </div>

        {{-- Main Login Card (Centered Maxim Style) --}}
        <div class="max-w-md mx-auto w-full my-auto py-8">
            <div class="rounded-3xl bg-[#1e222b] border border-[#333846] p-6 sm:p-8 shadow-2xl relative overflow-hidden space-y-6">
                
                {{-- Decorative Glow --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Maxim Style Logo & Header --}}
                <div class="text-center space-y-3">
                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-3xl bg-gradient-to-br from-orange-600 via-amber-600 to-red-600 text-white font-black text-2xl shadow-lg shadow-orange-500/20 mb-1 border border-orange-400/30">
                        SG
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-white tracking-tight">Login Posko & Anggota</h1>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Otorisasi jaringan komando & operasi SAR</p>
                    </div>
                </div>

                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="rounded-2xl bg-red-500/10 border border-red-500/30 p-3.5 text-xs font-bold text-red-400 flex items-center gap-2.5">
                        <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-red-500 text-white font-black text-xs">!</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" id="loginForm" class="space-y-4">
                    @csrf
                    
                    {{-- ID / Email Petugas --}}
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400 px-1" for="email">ID / Email Petugas <span class="text-orange-500">*</span></label>
                        <div class="maxim-input-card flex items-center gap-3.5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-400 font-black text-base">
                                📧
                            </span>
                            <div class="flex-1 min-w-0">
                                <input 
                                    id="email"
                                    name="email" 
                                    type="email"
                                    value="{{ old('email') }}" 
                                    placeholder="nama@timsar.com"
                                    class="maxim-input" 
                                    required
                                    autofocus
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Kata Sandi --}}
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400 px-1" for="password">Kata Sandi <span class="text-orange-500">*</span></label>
                        <div class="maxim-input-card flex items-center gap-3.5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-orange-500/15 border border-orange-500/30 text-orange-400 font-black text-base">
                                🔒
                            </span>
                            <div class="flex-1 min-w-0">
                                <input 
                                    id="password"
                                    name="password" 
                                    type="password"
                                    placeholder="••••••••••••"
                                    class="maxim-input" 
                                    required
                                >
                            </div>
                            <button type="button" id="togglePassword" class="text-slate-400 hover:text-white px-1 text-base focus:outline-none" title="Intip Sandi">
                                👁️
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me & Help --}}
                    <div class="flex items-center justify-between text-xs px-1 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 font-semibold select-none">
                            <input type="checkbox" name="remember" class="rounded bg-[#242832] border-[#333846] text-orange-500 focus:ring-0 focus:ring-offset-0 h-4 w-4">
                            <span>Ingat sesi saya</span>
                        </label>
                        <span class="text-slate-500 font-semibold">Terkunci 256-bit</span>
                    </div>

                    {{-- Submit Button (Orange Basarnas!) --}}
                    <div class="pt-3">
                        <button type="submit" id="loginBtn" class="w-full flex items-center justify-center gap-3 cursor-pointer font-black tracking-wider uppercase text-base sm:text-lg">
                            <span>MASUK SISTEM KOMANDO &rarr;</span>
                        </button>
                    </div>
                </form>

                <div class="border-t border-[#333846] pt-4 text-center">
                    <p class="text-[11px] font-semibold text-slate-500">
                        🛡️ Akses khusus personil Basarnas & relawan terdaftar. Segala aktivitas di dalam portal dipantau oleh server pusat.
                    </p>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <footer class="text-center text-slate-600 text-xs py-4 font-semibold">
            &copy; {{ date('Y') }} SIGAP-SAR Command Center &bull;
        </footer>

    </div>

    @push('scripts')
    <script>
        document.getElementById('loginForm').addEventListener('submit', () => {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span>OTENTIKASI BERJALAN...</span>';
        });

        const toggleBtn = document.getElementById('togglePassword');
        const passInput = document.getElementById('password');
        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', () => {
                const isPass = passInput.type === 'password';
                passInput.type = isPass ? 'text' : 'password';
                toggleBtn.textContent = isPass ? '🙈' : '👁️';
            });
        }
    </script>
    @endpush
</x-layouts.app>
