<x-layouts.app title="Lapor Darurat TIMSAR" :hideChrome="true" :fullBleed="true">
    @push('scripts')
        <style>
            /* ── Maxim Theme Aesthetics ── */
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap');
            
            body, .timsar-maxim-app {
                font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
                background-color: #181a20 !important;
                color: #e2e8f0 !important;
            }
            
            /* Custom Scrollbar for Dark Mode */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: #181a20; }
            ::-webkit-scrollbar-thumb { background: #333846; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #4b5265; }

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

            /* Incident Grid Item */
            .incident-card {
                background: #242832;
                border: 1.5px solid #333846;
                border-radius: 1rem;
                padding: 0.875rem 0.5rem;
                cursor: pointer;
                transition: all 0.2s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                user-select: none;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            }
            .incident-card:hover {
                background: #2c303d;
                border-color: #4b5265;
                transform: translateY(-2px);
            }
            .incident-card.active {
                background: rgba(249, 115, 22, 0.15) !important;
                border-color: #f97316 !important;
                color: #ff8a00 !important;
                box-shadow: 0 0 16px rgba(249, 115, 22, 0.25);
            }
            .incident-card.active span.title {
                color: #ffffff !important;
            }

            /* Basarnas Orange Button */
            #submitBtn {
                background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
                color: #ffffff;
                font-weight: 900;
                border-radius: 1.25rem;
                padding: 1.15rem 1.5rem;
                box-shadow: 0 10px 25px -5px rgba(234, 88, 12, 0.4);
                transition: all 0.2s ease;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
            #submitBtn:hover:not(:disabled) {
                filter: brightness(1.1);
                box-shadow: 0 15px 30px -5px rgba(234, 88, 12, 0.5);
                transform: translateY(-1px);
            }
            #submitBtn:active:not(:disabled) {
                transform: scale(0.98);
            }
            #submitBtn.disabled-state {
                background: #333846 !important;
                color: #64748b !important;
                box-shadow: none !important;
                cursor: not-allowed;
            }

            /* Map styling in dark mode */
            #map {
                border-radius: 1rem;
                filter: contrast(1.05) saturate(1.1);
            }
        </style>
    @endpush

    <div class="timsar-maxim-app min-h-screen bg-[#181a20] text-slate-100 flex flex-col justify-between pb-12">
        
        {{-- Maxim-Style Top Navigation Bar --}}
        <header class="sticky top-0 z-50 bg-[#181a20]/95 backdrop-blur-md border-b border-[#242832] px-4 sm:px-8 py-4">
            <div class="mx-auto max-w-4xl flex items-center justify-between">
                <a href="{{ route('public.report') }}" class="flex items-center gap-2 group">
                    <span class="font-black text-2xl tracking-tighter text-white flex items-center">
                        s i g<span class="inline-flex items-center justify-center mx-0.5 h-6 w-6 rounded-full bg-red-600/20 text-red-500 border-2 border-red-500 animate-pulse text-xs">⭕</span>p
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest bg-orange-500 text-black px-2 py-0.5 rounded font-mono ml-1">SAR</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#242832] border border-[#333846] px-4 py-2 text-xs font-extrabold text-slate-200 hover:bg-[#2c303d] hover:text-white transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Login Posko</span>
                    </a>
                </div>
            </div>
        </header>

        {{-- Main Maxim Content Container --}}
        <main class="mx-auto max-w-4xl w-full px-4 sm:px-6 py-6 sm:py-8 space-y-6">
            
            {{-- Title Banner --}}
            <div class="text-center sm:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-extrabold mb-2">
                    <span class="h-2 w-2 rounded-full bg-orange-500 animate-ping"></span>
                    <span>DISPATCH PORTAL BASARNAS 24 JAM</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Panggilan Darurat & Evakuasi</h1>
                <p class="text-xs sm:text-sm font-medium text-slate-400 mt-1">Lengkapi informasi di bawah. Posisi GPS Anda terdeteksi otomatis untuk tim rescue terdekat.</p>
            </div>

            <form method="POST" action="{{ route('public.report.store') }}" id="reportForm" class="space-y-6" novalidate>
                @csrf

                {{-- 1. LOCATION BAR (Maxim Style "Lokasi penjemputan") --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5" id="step2">
                            <span class="h-2 w-2 rounded-full bg-red-500" id="stepNum2"></span>
                            1. Titik Koordinat Lokasi Darurat
                        </span>
                        <span id="locationBadge" class="rounded px-2 py-0.5 text-[10px] font-black bg-slate-700 text-slate-300 uppercase tracking-wide">BELUM AKTIF</span>
                    </div>

                    <div class="maxim-input-card !p-3.5 sm:!p-4" id="locationSection">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-red-500/15 border border-red-500/30 text-red-500 font-black text-lg shadow-inner">
                                    ⭕
                                </span>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Lokasi Kejadian <span class="text-orange-500">*</span></div>
                                    <div id="locationText" class="text-sm sm:text-base font-extrabold text-white truncate mt-0.5">Tekan tombol untuk mendeteksi koordinat GPS</div>
                                </div>
                            </div>
                            <button type="button" id="locateBtn" class="w-full sm:w-auto rounded-xl bg-orange-600 hover:bg-orange-500 active:scale-95 px-5 py-3 text-xs font-black text-white shadow-lg shadow-orange-500/20 transition-all flex items-center justify-center gap-2 shrink-0">
                                <svg id="locateIcon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span id="locateBtnText">Ambil Lokasi Saya</span>
                            </button>
                        </div>

                        {{-- GPS Status Hint --}}
                        <div id="locationHint" class="mt-3.5 hidden rounded-xl bg-[#1e222b] border border-[#333846] p-3 text-xs font-semibold text-slate-300 flex items-center gap-2">
                            <span id="locationHintText">Rekomendasi: aktifkan GPS perangkat Anda untuk akurasi terbaik.</span>
                        </div>

                        {{-- Map Container --}}
                        <div class="mt-4 rounded-xl overflow-hidden border border-[#333846] h-52 sm:h-64 relative shadow-inner">
                            <div id="map" class="h-full w-full"></div>
                        </div>

                        {{-- Hidden GPS Inputs --}}
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="accuracy" id="accuracy">

                        {{-- Summary Pill --}}
                        <div id="reportSummary" class="hidden mt-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-3 text-xs text-emerald-300 grid grid-cols-3 gap-2 text-center font-mono">
                            <div><span class="block text-[9px] text-slate-400">HP</span><span id="summaryPhone" class="font-bold truncate">-</span></div>
                            <div><span class="block text-[9px] text-slate-400">KOORDINAT</span><span id="summaryLocation" class="font-bold truncate">-</span></div>
                            <div><span class="block text-[9px] text-slate-400">AKURASI</span><span id="summaryAccuracy" class="font-bold">-</span></div>
                        </div>
                    </div>
                </div>

                {{-- 2. REPORTER INFO (Maxim Style Stacked Bars) --}}
                <div class="space-y-3">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-wider px-1 flex items-center gap-1.5" id="step1">
                        <span class="h-2 w-2 rounded-full bg-blue-500" id="stepNum1"></span>
                        2. Identitas Pelapor Darurat
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Nama --}}
                        <div class="maxim-input-card flex items-center gap-3.5">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-500/15 border border-blue-500/30 text-blue-400 font-black text-lg">
                                👤
                            </span>
                            <div class="flex-1 min-w-0">
                                <label for="reporter_name" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nama Lengkap <span class="text-orange-500">*</span></label>
                                <input id="reporter_name" name="reporter_name" type="text" value="{{ old('reporter_name') }}" placeholder="Ketik nama Anda" class="maxim-input mt-0.5" required>
                            </div>
                        </div>

                        {{-- Nomor HP --}}
                        <div class="maxim-input-card flex items-start gap-3.5">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-black text-lg mt-0.5">
                                📞
                            </span>
                            <div class="flex-1 min-w-0">
                                <label for="reporter_phone" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nomor HP / WhatsApp <span class="text-orange-500">*</span></label>
                                <input id="reporter_phone" name="reporter_phone" type="tel" value="{{ old('reporter_phone') }}" placeholder="08xxxxxxxxxx" inputmode="tel" autocomplete="tel" maxlength="17" class="maxim-input mt-0.5" required>
                                <p id="phoneHelp" class="mt-1 text-[11px] font-medium text-slate-500">Nomor aktif untuk dihubungi tim rescue.</p>
                                @error('reporter_phone')
                                    <p class="mt-1 text-xs font-bold text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. INCIDENT TYPE (Maxim Style Vehicle/Category Grid) --}}
                <div class="space-y-3" id="incidentTrigger">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                            3. Pilih Jenis Insiden Darurat <span class="text-orange-500">*</span>
                        </span>
                        <span id="incidentLabel" class="text-xs font-bold text-orange-400">Belum dipilih</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3" id="incidentGrid">
                        <div class="incident-card group" data-value="Kecelakaan" data-icon="💥">
                            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">💥</span>
                            <span class="title text-xs font-extrabold text-slate-300 text-center">Kecelakaan</span>
                        </div>
                        <div class="incident-card group" data-value="Orang hilang" data-icon="🔍">
                            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🔍</span>
                            <span class="title text-xs font-extrabold text-slate-300 text-center">Orang Hilang</span>
                        </div>
                        <div class="incident-card group" data-value="Pendaki cedera" data-icon="⛰️">
                            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">⛰️</span>
                            <span class="title text-xs font-extrabold text-slate-300 text-center">Pendaki Cedera</span>
                        </div>
                        <div class="incident-card group" data-value="Banjir" data-icon="🌊">
                            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🌊</span>
                            <span class="title text-xs font-extrabold text-slate-300 text-center">Banjir / Air</span>
                        </div>
                        <div class="incident-card group" data-value="Kebakaran" data-icon="🔥">
                            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🔥</span>
                            <span class="title text-xs font-extrabold text-slate-300 text-center">Kebakaran</span>
                        </div>
                        <div class="incident-card group" data-value="Lainnya" data-icon="🚨">
                            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🚨</span>
                            <span class="title text-xs font-extrabold text-slate-300 text-center">Darurat Lain</span>
                        </div>
                    </div>
                    <input type="hidden" name="incident_type" id="incidentValue" value="{{ old('incident_type') }}" required>
                    <input type="hidden" name="priority" value="{{ old('priority', 'high') }}">
                </div>

                {{-- 4. DESCRIPTION (Maxim Style "Perincian") --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-1">
                        <label for="description" class="block text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            4. Perincian & Kronologi Kejadian <span class="text-orange-500">*</span>
                        </label>
                        <span id="charCount" class="text-xs font-mono text-slate-500">0 / 2000</span>
                    </div>

                    <div class="maxim-input-card !p-4">
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Tuliskan kronologi singkat, kondisi korban, atau patokan lokasi khusus..."
                            class="w-full bg-transparent text-sm sm:text-base font-semibold text-white placeholder-slate-500 focus:outline-none resize-none"
                            required
                        >{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- 5. SUBMIT BUTTON (Orange Basarnas!) --}}
                <div class="pt-2 sticky bottom-4 z-40">
                    <button type="submit" id="submitBtn" class="w-full flex items-center justify-center gap-3 cursor-pointer">
                        <svg class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        <span id="submitBtnText" class="text-base sm:text-lg tracking-wider uppercase font-black">Kirim Laporan Darurat &rarr;</span>
                    </button>
                    <p class="mt-3 text-center text-[11px] font-bold text-slate-500">
                        ⚠️ Peringatan: Penyalahgunaan panggilan darurat Basarnas dilindungi hukum & dikenakan sanksi pidana.
                    </p>
                </div>

            </form>
        </main>

        {{-- Maxim-Style Bottom Footer Bar --}}
        <footer class="mt-8 border-t border-[#242832] bg-[#181a20] py-6 text-center text-slate-500 text-xs">
            <div class="mx-auto max-w-4xl px-4 flex flex-col sm:flex-row items-center justify-between gap-4 font-semibold">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded bg-orange-500 text-black font-black text-[10px]">SG</span>
                    <span class="text-slate-300 font-bold">SIGAP-SAR Dispatch</span>
                    <span>&bull;</span>
                </div>
                <div>&copy; {{ date('Y') }} SIGAP-SAR & Operation Network. Siaga 24 Jam.</div>
            </div>
        </footer>

    </div>

    @push('scripts')
    <script>
    /* ═══════════════ MAP INIT ═══════════════ */
    const defaultPoint = [-8.5833, 116.1167];
    const map = L.map('map').setView(defaultPoint, 13);
    TimsarMap.addTiles(map);
    let marker = L.marker(defaultPoint, { icon: TimsarMap.icon('user') }).addTo(map).bindPopup('Lokasi laporan Anda');

    let locationWatchId   = null;
    let bestPosition      = null;
    let watchStartedAt    = null;
    const targetAccuracy  = 50;
    const maxWatchMs      = 25000;
    let submitAfterLocate = false;

    /* ── Refs ── */
    const reportForm     = document.getElementById('reportForm');
    const locateBtn      = document.getElementById('locateBtn');
    const locateBtnText  = document.getElementById('locateBtnText');
    const locateIcon     = document.getElementById('locateIcon');
    const submitBtn      = document.getElementById('submitBtn');
    const locationText   = document.getElementById('locationText');
    const locationBadge  = document.getElementById('locationBadge');
    const locationHint   = document.getElementById('locationHint');
    const locationHintTx = document.getElementById('locationHintText');
    const locationSection= document.getElementById('locationSection');
    const reportSummary  = document.getElementById('reportSummary');
    const summaryPhone   = document.getElementById('summaryPhone');
    const summaryLocation= document.getElementById('summaryLocation');
    const summaryAccuracy= document.getElementById('summaryAccuracy');

    /* ── Fields & Steps ── */
    const nameInput = document.getElementById('reporter_name');
    const phoneInput = document.getElementById('reporter_phone');
    const descTa = document.getElementById('description');
    const incidentValue = document.getElementById('incidentValue');

    const step1 = document.getElementById('step1');
    const stepNum1 = document.getElementById('stepNum1');
    const step2 = document.getElementById('step2');
    const stepNum2 = document.getElementById('stepNum2');

    function normalizePhone(value) {
        let phone = value.trim().replace(/[^0-9+]/g, '');
        if (phone.startsWith('+62')) phone = `0${phone.slice(3)}`;
        else if (phone.startsWith('62')) phone = `0${phone.slice(2)}`;
        else if (phone.startsWith('8')) phone = `0${phone}`;
        return phone;
    }

    function validatePhone() {
        const normalized = normalizePhone(phoneInput.value);
        const valid = /^08[0-9]{8,11}$/.test(normalized);
        phoneInput.setCustomValidity(phoneInput.value.trim() !== '' && !valid
            ? 'Masukkan nomor HP Indonesia yang valid, misalnya 081234567890 atau +6281234567890.'
            : '');
        return valid;
    }

    function updateReportSummary(pos = bestPosition) {
        if (!reportSummary || !pos) return;

        const { latitude: lat, longitude: lng, accuracy: acc } = pos.coords;
        summaryPhone.textContent = normalizePhone(phoneInput.value) || '-';
        summaryLocation.textContent = `${Number(lat).toFixed(5)}, ${Number(lng).toFixed(5)}`;
        summaryAccuracy.textContent = `${Math.round(acc)} m`;
        reportSummary.classList.remove('hidden');
    }
    function checkFormValidity() {
        let step1Completed = nameInput.value.trim() !== '' &&
                             validatePhone() &&
                             descTa.value.trim() !== '' &&
                             incidentValue.value !== '';

        if (step1Completed) {
            step1.classList.add('text-emerald-600');
            stepNum1.className = 'step-dot bg-emerald-500 text-white';
            stepNum1.innerHTML = 'OK';
        } else {
            step1.classList.remove('text-emerald-600');
            stepNum1.className = 'step-dot bg-slate-200 text-slate-600';
            stepNum1.innerHTML = '1';
        }

        [nameInput, phoneInput, descTa].forEach(el => {
            if (el.value.trim() !== '') {
                el.classList.add('is-valid');
            } else {
                el.classList.remove('is-valid');
            }
        });
    }

    [nameInput, phoneInput, descTa].forEach(input => {
        input.addEventListener('input', checkFormValidity);
    });
    phoneInput.addEventListener('blur', () => {
        const normalized = normalizePhone(phoneInput.value);
        if (/^08[0-9]{8,11}$/.test(normalized)) phoneInput.value = normalized;
        validatePhone();
        if (bestPosition) updateReportSummary(bestPosition);
    });

    /* ═══════════════ GPS LOCATE ═══════════════ */
    function requestLocation() {
        if (!navigator.geolocation) {
            setGPSState('error', 'Browser tidak mendukung deteksi lokasi.');
            return;
        }

        setLocateBtnState('loading');
        setGPSState('loading', 'Mengunci lokasi Anda. Harap tunggu sebentar.');

        if (locationWatchId !== null) navigator.geolocation.clearWatch(locationWatchId);
        bestPosition  = null;
        watchStartedAt = Date.now();

        locationWatchId = navigator.geolocation.watchPosition(
            (pos) => {
                if (!bestPosition || pos.coords.accuracy < bestPosition.coords.accuracy) {
                    bestPosition = pos;
                    applyPosition(pos, false);
                }
                const waited = Date.now() - watchStartedAt;
                if (bestPosition.coords.accuracy <= targetAccuracy || waited >= maxWatchMs) {
                    navigator.geolocation.clearWatch(locationWatchId);
                    locationWatchId = null;
                    applyPosition(bestPosition, true);
                }
            },
            (err) => {
                if (locationWatchId !== null) {
                    navigator.geolocation.clearWatch(locationWatchId);
                    locationWatchId = null;
                }
                if (bestPosition) {
                    applyPosition(bestPosition, true);
                    return;
                }
                setGPSState('error', geolocationErrorMessage(err));
                setLocateBtnState('error');
                submitAfterLocate = false;
                setSubmitBtnState('idle');
            },
            { enableHighAccuracy: true, timeout: 25000, maximumAge: 0 }
        );
    }

    locateBtn.addEventListener('click', requestLocation);

    function applyPosition(pos, isFinal) {
        const { latitude: lat, longitude: lng, accuracy: acc } = pos.coords;
        document.getElementById('latitude').value  = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('accuracy').value  = acc;

        TimsarMap.moveMarker(marker, [lat, lng]);
        map.setView([lat, lng], acc <= 80 ? 17 : 15, { animate: true });
        locationSection.style.borderColor = isFinal ? '#86efac' : '#fde68a';

        if (isFinal) {
            setLocateBtnState('ready');

            step2.classList.add('text-emerald-600');
            stepNum2.className = 'step-dot bg-emerald-500 text-white';
            stepNum2.innerHTML = 'OK';

            const state = acc > 100 ? 'warning' : 'ready';
            const msg = acc > 100
                ? `GPS Aktif (Akurasi sekitar ${Math.round(acc)}m).`
                : `GPS Aktif (Akurasi sekitar ${Math.round(acc)}m).`;
            setGPSState(state, msg);
            updateReportSummary(pos);
            setSubmitBtnState('ready');

            if (submitAfterLocate) {
                submitFormDirect();
            }
        } else {
            const waited = watchStartedAt ? Math.round((Date.now() - watchStartedAt) / 1000) : 0;
            setGPSState('loading', `Mengunci GPS... Akurasi sekitar ${Math.round(acc)}m (${waited}s)`);
            if (submitAfterLocate) {
                setSubmitBtnState('gps_loading');
            }
        }
    }

    function setLocateBtnState(state) {
        locateBtn.className = 'w-full sm:w-auto';
        locateBtn.disabled  = (state === 'loading');

        if (state === 'loading') {
            locateBtn.className = 'state-loading w-full sm:w-auto';
            locateBtnText.textContent = 'Mengambil lokasi';
        } else if (state === 'ready') {
            locateBtn.className = 'state-ready w-full sm:w-auto';
            locateBtnText.textContent = 'Perbarui GPS';
        } else if (state === 'error') {
            locateBtn.className = 'state-error w-full sm:w-auto';
            locateBtnText.textContent = 'Coba Lagi';
        } else {
            locateBtnText.textContent = 'Ambil Lokasi Saya';
        }
    }

    function setGPSState(state, msg) {
        locationText.textContent = msg;
        const labels = {
            idle:    ['Belum Aktif',   'bg-slate-700 text-slate-300'],
            loading: ['Mencari GPS',   'bg-amber-500/20 text-amber-300 border border-amber-500/30'],
            ready:   ['GPS Aktif',     'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'],
            warning: ['Akurasi Rendah','bg-yellow-500/20 text-yellow-300 border border-yellow-500/30'],
            error:   ['GPS Error',     'bg-red-500/20 text-red-300 border border-red-500/30'],
        };
        const [label, cls] = labels[state] ?? labels.idle;
        locationBadge.textContent  = label;
        locationBadge.className    = `rounded px-2 py-0.5 text-[10px] font-black uppercase tracking-wide ${cls}`;

        const hintClasses = {
            loading: 'mt-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 p-3 text-xs font-semibold text-amber-200 flex items-center gap-2',
            ready:   'mt-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-3 text-xs font-semibold text-emerald-200 flex items-center gap-2',
            warning: 'mt-3.5 rounded-xl bg-yellow-500/10 border border-yellow-500/30 p-3 text-xs font-semibold text-yellow-200 flex items-center gap-2',
            error:   'mt-3.5 rounded-xl bg-red-500/10 border border-red-500/30 p-3 text-xs font-semibold text-red-200 flex items-center gap-2',
        };
        locationHint.className = hintClasses[state] ?? 'mt-3.5 hidden rounded-xl bg-[#1e222b] border border-[#333846] p-3 text-xs font-semibold text-slate-300 flex items-center gap-2';
        locationHint.classList.remove('hidden');
        if (locationHintTx) locationHintTx.textContent = msg;
    }

    function setSubmitBtnState(state) {
        if (state === 'ready') {
            submitBtn.className = 'ready-state';
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.769 59.769 0 0 1 3.269 20.875L5.999 12Zm0 0h7.5"/></svg>
                <span>Kirim Laporan Darurat</span>
            `;
        } else if (state === 'submitting') {
            submitBtn.className = 'disabled-state';
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="dot-loader" style="color:#64748b">
                    <span></span><span></span><span></span>
                </div>
                <span>Mengirim laporan...</span>
            `;
        } else if (state === 'gps_loading') {
            submitBtn.className = 'disabled-state';
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="dot-loader" style="color:#64748b">
                    <span></span><span></span><span></span>
                </div>
                <span>Mengunci lokasi...</span>
            `;
        } else {
            submitBtn.className = 'gps-pending-state';
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                <span>Kirim Laporan Darurat</span>
            `;
        }
    }

    /* ═══════════════ SUBMIT BEHAVIOR ═══════════════ */
    reportForm.addEventListener('submit', (e) => {
        e.preventDefault();

        phoneInput.value = normalizePhone(phoneInput.value);
        validatePhone();

        if (!reportForm.checkValidity()) {
            reportForm.reportValidity();
            return;
        }

        if (incidentValue.value === '') {
            const trigger = document.getElementById('incidentTrigger');
            trigger.style.borderColor = '#ef4444';
            trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const lat = document.getElementById('latitude').value;

        if (!lat) {
            submitAfterLocate = true;
            setSubmitBtnState('gps_loading');
            requestLocation();
            locationSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            submitFormDirect();
        }
    });

    function submitFormDirect() {
        if (bestPosition) updateReportSummary(bestPosition);
        setSubmitBtnState('submitting');
        reportForm.submit();
    }

    /* ═══════════════ MAXIM INCIDENT GRID SELECTOR ═══════════════ */
    const incidentCards = document.querySelectorAll('.incident-card');
    const incidentLabel = document.getElementById('incidentLabel');
    const hidIncidentVal = document.getElementById('incidentValue');

    if (hidIncidentVal && hidIncidentVal.value) {
        const activeCard = document.querySelector(`.incident-card[data-value="${hidIncidentVal.value}"]`);
        if (activeCard) {
            activeCard.classList.add('active');
            if (incidentLabel) incidentLabel.textContent = `${activeCard.dataset.icon} ${activeCard.dataset.value}`;
        }
    }

    incidentCards.forEach(card => {
        card.addEventListener('click', () => {
            incidentCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            
            const val = card.dataset.value;
            const icon = card.dataset.icon;
            hidIncidentVal.value = val;
            if (incidentLabel) {
                incidentLabel.textContent = `${icon} ${val}`;
                incidentLabel.className = 'text-xs font-black text-orange-400';
            }

            const trigger = document.getElementById('incidentTrigger');
            if (trigger) trigger.style.borderColor = '';
            checkFormValidity();
        });
    });

    /* ── Char Count ── */
    descTa.addEventListener('input', () => {
        const n = descTa.value.length;
        charCount.textContent = `${n} / 2000 karakter`;
        charCount.style.color = n > 1800 ? '#ef4444' : n > 1500 ? '#f59e0b' : '#94a3b8';
    });

    checkFormValidity();
    </script>
    @endpush
</x-layouts.app>
