<x-layouts.jamaah :title="'Beranda Jamaah — PT. Zein Internasional'">

    {{-- ═══════════════════════════════════════════════════════════════
         1. KHUSUS TAMPILAN MOBILE (< 1024px)
         Konsep App Dashboard: Header → Card Ringkasan Statistik → Shortcut → Aktivitas → Informasi
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="lg:hidden px-3.5 sm:px-4 py-4 space-y-4 max-w-lg mx-auto">

        {{-- Pesan Notifikasi Flash --}}
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5 shadow-2xs">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2.5 shadow-2xs">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Mobile Header: Greeting & User Profile Card --}}
        <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 sm:p-4 shadow-2xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center text-lg font-bold shadow-xs border border-[#CCD8C7] overflow-hidden shrink-0">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ $user->initials }}
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="text-[9.5px] uppercase font-bold tracking-wider text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-full border border-emerald-200 inline-block mb-1">
                        Jamaah Terverifikasi
                    </span>
                    <h1 class="text-sm sm:text-base font-bold text-[#12271E] tracking-tight truncate leading-tight">
                        Assalamu'alaikum, {{ explode(' ', $user->name)[0] }}
                    </h1>
                    <p class="text-[11px] text-[#526057] truncate mt-0.5">
                        {{ $user->phone_formatted }}
                    </p>
                </div>
            </div>
            <a href="{{ route('jamaah.profile') }}" 
               class="p-2.5 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] border border-[#CCD8C7] shrink-0 active:scale-95 transition-all" 
               title="Buka Profil Saya">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </a>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
             KONDISI A (MOBILE): ADA BOOKING AKTIF (STATUS BUKAN 'SELESAI')
             ═══════════════════════════════════════════════════════════════ --}}
        @if(($dashboardState ?? 'A') === 'A' && count($activeRegistrations ?? ($registration ? [$registration] : [])) > 0)
            @php $regsToRender = $activeRegistrations ?? ($registration ? collect([$registration]) : collect()); @endphp
            @if($regsToRender->count() > 1)
                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 font-semibold flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Anda memiliki {{ $regsToRender->count() }} Pendaftaran Aktif</span>
                    </div>
                    <span class="text-[10px] text-amber-700 font-normal">Urutan Perhatian</span>
                </div>
            @endif

            @foreach($regsToRender as $regIndex => $registration)
            @php
                $step = $registration->step_number;
                $totalSteps = \App\Models\Registration::TOTAL_STEPS;
                $progress = $registration->progress_percentage;
                $statusKey = $registration->status;

                $statusConfig = [
                    \App\Models\Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN => [
                        'theme' => 'amber',
                        'badge_bg' => 'bg-amber-500/20 text-amber-300 border-amber-400/30',
                        'badge_bg_desktop' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'dot_color' => 'bg-amber-400',
                        'pulse' => true,
                        'icon' => 'doc_search',
                        'state_note' => 'Verifikasi Dokumen Berkas',
                    ],
                    \App\Models\Registration::STATUS_MENUNGGU_PEMBAYARAN_DP => [
                        'theme' => 'gold',
                        'badge_bg' => 'bg-[#C2A264]/25 text-[#E5C88F] border-[#C2A264]/40',
                        'badge_bg_desktop' => 'bg-amber-50 text-amber-900 border-amber-300',
                        'dot_color' => 'bg-[#E5C88F]',
                        'pulse' => true,
                        'icon' => 'banknotes',
                        'state_note' => 'Menunggu Pembayaran DP',
                    ],
                    'dokumen_disetujui' => [
                        'theme' => 'gold',
                        'badge_bg' => 'bg-[#C2A264]/25 text-[#E5C88F] border-[#C2A264]/40',
                        'badge_bg_desktop' => 'bg-amber-50 text-amber-900 border-amber-300',
                        'dot_color' => 'bg-[#E5C88F]',
                        'pulse' => true,
                        'icon' => 'banknotes',
                        'state_note' => 'Menunggu Pembayaran DP',
                    ],
                    \App\Models\Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP => [
                        'theme' => 'sky',
                        'badge_bg' => 'bg-sky-500/20 text-sky-300 border-sky-400/30',
                        'badge_bg_desktop' => 'bg-sky-50 text-sky-800 border-sky-200',
                        'dot_color' => 'bg-sky-400',
                        'pulse' => true,
                        'icon' => 'refresh',
                        'state_note' => 'Verifikasi Setoran DP',
                    ],
                    \App\Models\Registration::STATUS_JAMAAH => [
                        'theme' => 'teal',
                        'badge_bg' => 'bg-teal-500/20 text-teal-200 border-teal-400/30',
                        'badge_bg_desktop' => 'bg-teal-50 text-teal-800 border-teal-200',
                        'dot_color' => 'bg-teal-400',
                        'pulse' => false,
                        'icon' => 'badge_check',
                        'state_note' => 'Calon Jamaah Resmi',
                    ],
                    \App\Models\Registration::STATUS_CICILAN_PELUNASAN => [
                        'theme' => 'indigo',
                        'badge_bg' => 'bg-indigo-500/20 text-indigo-200 border-indigo-400/30',
                        'badge_bg_desktop' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                        'dot_color' => 'bg-indigo-400',
                        'pulse' => true,
                        'icon' => 'trending_up',
                        'state_note' => 'Proses Pelunasan Bertahap',
                    ],
                    \App\Models\Registration::STATUS_LUNAS => [
                        'theme' => 'emerald',
                        'badge_bg' => 'bg-emerald-500/25 text-emerald-200 border-emerald-400/40',
                        'badge_bg_desktop' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                        'dot_color' => 'bg-emerald-400',
                        'pulse' => false,
                        'icon' => 'shield_check',
                        'state_note' => 'Seluruh Biaya Lunas',
                    ],
                    \App\Models\Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN => [
                        'theme' => 'amber',
                        'badge_bg' => 'bg-amber-500/20 text-amber-200 border-amber-400/30',
                        'badge_bg_desktop' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'dot_color' => 'bg-amber-400',
                        'pulse' => true,
                        'icon' => 'document_text',
                        'state_note' => 'Lengkapi Dokumen Keberangkatan',
                    ],
                    \App\Models\Registration::STATUS_BERANGKAT => [
                        'theme' => 'gold_emerald',
                        'badge_bg' => 'bg-gradient-to-r from-[#C2A264]/30 to-emerald-500/30 text-[#E5C88F] border-[#C2A264]/50 shadow-sm shadow-[#C2A264]/20',
                        'badge_bg_desktop' => 'bg-emerald-100 text-[#1B3B2B] border-emerald-300',
                        'dot_color' => 'bg-[#E5C88F]',
                        'pulse' => true,
                        'icon' => 'airplane',
                        'state_note' => 'Siap Berangkat ke Tanah Suci',
                    ],
                    \App\Models\Registration::STATUS_SELESAI => [
                        'theme' => 'purple_gold',
                        'badge_bg' => 'bg-gradient-to-r from-purple-500/25 via-emerald-500/25 to-[#C2A264]/25 text-purple-200 border-purple-400/40 shadow-sm shadow-purple-500/20',
                        'badge_bg_desktop' => 'bg-purple-50 text-purple-900 border-purple-200',
                        'dot_color' => 'bg-purple-400',
                        'pulse' => false,
                        'icon' => 'sparkles',
                        'state_note' => 'Perjalanan Tuntas & Mabrur',
                    ],
                    \App\Models\Registration::STATUS_DIBATALKAN => [
                        'theme' => 'rose',
                        'badge_bg' => 'bg-rose-500/20 text-rose-300 border-rose-400/30',
                        'badge_bg_desktop' => 'bg-red-50 text-red-800 border-red-200',
                        'dot_color' => 'bg-rose-400',
                        'pulse' => false,
                        'icon' => 'x_circle',
                        'state_note' => 'Pendaftaran Dibatalkan',
                    ],
                ];

                $statusMeta = $statusConfig[$statusKey] ?? [
                    'theme' => 'emerald',
                    'badge_bg' => 'bg-white/10 text-emerald-300 border-white/15',
                    'badge_bg_desktop' => 'bg-[#EFF3EB] text-[#1B3B2B] border-[#CCD8C7]',
                    'dot_color' => 'bg-emerald-400',
                    'pulse' => false,
                    'icon' => 'badge_check',
                    'state_note' => 'Dalam Proses',
                ];
            @endphp

            <div class="bg-gradient-to-br from-[#122b1f] via-[#1B3B2B] to-[#0d1f16] text-white rounded-3xl p-4 sm:p-5 shadow-xl border border-white/12 relative overflow-hidden space-y-4">
                {{-- Decorative Glow Accents --}}
                <div class="absolute -top-12 -right-12 w-36 h-36 bg-[#C2A264]/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-36 h-36 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                {{-- Header Card: Status Focal Point & Booking Reference Chip --}}
                <div class="flex items-center justify-between gap-2 border-b border-white/10 pb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $statusMeta['badge_bg'] }} backdrop-blur-md text-[11px] font-bold tracking-wide border shadow-xs truncate max-w-[62%]">
                        <span class="w-2 h-2 rounded-full {{ $statusMeta['dot_color'] }} {{ $statusMeta['pulse'] ? 'animate-pulse' : '' }} shrink-0"></span>
                        @include('jamaah.partials.status-icon', ['icon' => $statusMeta['icon']])
                        <span class="truncate">{{ $registration->status_label }}</span>
                    </span>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-black/40 backdrop-blur-md border border-[#C2A264]/40 shadow-inner shrink-0">
                        <svg class="w-3 h-3 text-[#E5C88F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                        </svg>
                        <span class="text-[11px] font-mono font-bold text-[#E5C88F] tracking-wider">
                            {{ $registration->registration_number }}
                        </span>
                    </div>
                </div>

                {{-- Paket Title & Metadata --}}
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C2A264]"></span>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-[#C2A264]">
                            Paket Pilihan Ibadah
                        </span>
                    </div>
                    <h2 class="text-base sm:text-lg font-bold text-white tracking-tight leading-snug line-clamp-2">
                        {{ $registration->package->name ?? 'Paket Perjalanan Umrah' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                        @if($registration->packageVariant)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/10 text-emerald-200 text-[10.5px] font-semibold border border-white/10">
                                <span>Varian {{ $registration->packageVariant->name }}</span>
                            </span>
                        @endif
                        @if($registration->room_type)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/10 text-emerald-200 text-[10.5px] font-semibold border border-white/10">
                                <span>Kamar {{ ucfirst($registration->room_type) }}</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/10 text-emerald-200 text-[10.5px] font-semibold border border-white/10">
                            <span>{{ $registration->members->count() }} Jamaah</span>
                        </span>
                    </div>
                </div>

                {{-- Grid 4-Blok Ringkasan Statistik (Frosted Glass Cards dengan Contextual Icons) --}}
                <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
                    {{-- Blok 1: Tahap Ibadah --}}
                    <div class="min-w-0 bg-white/[0.07] hover:bg-white/[0.1] transition-colors rounded-2xl p-3 sm:p-3.5 border border-white/10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-emerald-200/90 mb-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a1.125 1.125 0 00.864-1.094V4.606a1.125 1.125 0 00-1.272-1.108l-3.21.755a9 9 0 01-6.086-.71l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                                </svg>
                                <span class="text-[9.5px] uppercase font-bold tracking-wider truncate">
                                    Tahap Ibadah
                                </span>
                            </div>
                            <div class="text-sm sm:text-base font-extrabold text-white truncate" title="Tahap {{ $step }} dari {{ $totalSteps }}">
                                Tahap {{ $step }} dari {{ $totalSteps }}
                            </div>
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-1">
                            {{ $registration->isCompleted() ? 'Perjalanan Tuntas ✓' : ($statusMeta['state_note'] ?? $registration->status_label) }}
                        </span>
                    </div>

                    {{-- Blok 2: Keberangkatan --}}
                    <div class="min-w-0 bg-white/[0.07] hover:bg-white/[0.1] transition-colors rounded-2xl p-3 sm:p-3.5 border border-white/10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-emerald-200/90 mb-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5" />
                                </svg>
                                <span class="text-[9.5px] uppercase font-bold tracking-wider truncate">
                                    Keberangkatan
                                </span>
                            </div>
                            <div class="text-sm sm:text-base font-extrabold text-white truncate">
                                {{ $registration->package->departure_date ? $registration->package->departure_date->translatedFormat('d M Y') : 'Reguler' }}
                            </div>
                        </div>
                        <span class="text-[9.5px] text-[#E5C88F] font-medium block truncate mt-1">
                            {{ $registration->package->duration ?? 9 }} Hari Perjalanan
                        </span>
                    </div>

                    {{-- Blok 3: Sudah Dibayar --}}
                    <div class="min-w-0 bg-white/[0.07] hover:bg-white/[0.1] transition-colors rounded-2xl p-3 sm:p-3.5 border border-white/10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-emerald-200/90 mb-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                                <span class="text-[9.5px] uppercase font-bold tracking-wider truncate">
                                    Sudah Dibayar
                                </span>
                            </div>
                            <div class="text-sm sm:text-base font-extrabold text-emerald-300 mt-0.5 truncate">
                                Rp {{ number_format($registration->invoice->total_paid ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-1">
                            Terverifikasi Sistem
                        </span>
                    </div>

                    {{-- Blok 4: Sisa Pelunasan --}}
                    <div class="min-w-0 bg-white/[0.07] hover:bg-white/[0.1] transition-colors rounded-2xl p-3 sm:p-3.5 border border-white/10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-[#E5C88F] mb-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                                <span class="text-[9.5px] uppercase font-bold tracking-wider truncate">
                                    Sisa Pelunasan
                                </span>
                            </div>
                            <div class="text-sm sm:text-base font-extrabold {{ ($registration->invoice->remaining_balance ?? 0) <= 0 ? 'text-emerald-300' : 'text-amber-300' }} mt-0.5 truncate">
                                @if(($registration->invoice->remaining_balance ?? 0) <= 0)
                                    LUNAS ✓
                                @else
                                    Rp {{ number_format($registration->invoice->remaining_balance, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-1">
                            {{ $registration->members->count() }} Jamaah Terdaftar
                        </span>
                    </div>
                </div>

                {{-- Milestone Stepper & Progress Bar --}}
                @if($registration->status !== 'dibatalkan')
                    <div class="space-y-2 pt-1 bg-black/25 backdrop-blur-xs rounded-2xl p-3 sm:p-3.5 border border-white/10">
                        {{-- Progress Bar Header --}}
                        <div class="flex items-center justify-between text-[11px] text-white/90 font-medium">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#C2A264] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                </svg>
                                <span>Progres Kesiapan Ibadah</span>
                            </span>
                            <div class="flex items-center gap-1.5 font-bold">
                                <span class="text-emerald-300 text-[10px]">Tahap {{ $step }}/{{ $totalSteps }}</span>
                                <span class="text-white/30">&bull;</span>
                                <span class="text-[#E5C88F]">{{ $registration->progress_percentage }}%</span>
                            </div>
                        </div>

                        {{-- 9 Discrete Milestone Segments --}}
                        <div class="grid grid-cols-9 gap-1 py-0.5" title="Tahap {{ $step }} dari {{ $totalSteps }} ({{ $registration->progress_percentage }}%)">
                            @for($i = 1; $i <= $totalSteps; $i++)
                                @php
                                    $isPassed = $i < $step;
                                    $isCurrent = $i === $step;
                                @endphp
                                <div class="relative flex flex-col items-center">
                                    <div class="w-full h-2 rounded-full transition-all duration-300 {{ 
                                        $isCurrent 
                                            ? 'bg-gradient-to-r from-[#C2A264] to-emerald-400 shadow-sm shadow-[#C2A264]/50 ring-1 ring-white/60 animate-pulse' 
                                            : ($isPassed ? 'bg-emerald-400/90 shadow-xs' : 'bg-white/15') 
                                    }}"></div>
                                </div>
                            @endfor
                        </div>

                        {{-- Continuous Micro-Bar --}}
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden border border-white/5">
                            <div class="bg-gradient-to-r from-[#C2A264] via-emerald-400 to-teal-300 h-full rounded-full transition-all duration-500 shadow-sm" style="width: {{ $registration->progress_percentage }}%"></div>
                        </div>
                    </div>
                @endif

                {{-- Tombol Aksi Cepat --}}
                <div class="pt-1 flex items-center gap-2">
                    @if($registration->status === 'menunggu_pembayaran_dp' || $registration->status === 'dokumen_disetujui')
                        <a href="{{ route('jamaah.payment.dp') }}" 
                           class="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-[#C2A264] to-[#B09153] hover:brightness-110 active:scale-[0.98] text-[#12271E] font-extrabold text-xs text-center shadow-md shadow-[#C2A264]/20 transition-all truncate flex items-center justify-center gap-1.5">
                            <span>Bayar DP</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @elseif(in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                        <a href="{{ route('jamaah.payment.pelunasan') }}" 
                           class="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:brightness-110 active:scale-[0.98] text-white font-extrabold text-xs text-center shadow-md shadow-emerald-600/20 transition-all truncate flex items-center justify-center gap-1.5">
                            <span>Setor Pelunasan</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @endif
                    <a href="{{ route('jamaah.my-registration') }}" 
                       class="flex-1 py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/20 active:scale-[0.98] text-white font-semibold text-xs text-center border border-white/20 transition-all truncate flex items-center justify-center gap-1.5">
                        <span>Detail Pendaftaran</span>
                        <svg class="w-3.5 h-3.5 shrink-0 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
            </div>
            @endforeach

        {{-- ═══════════════════════════════════════════════════════════════
             KONDISI B (MOBILE): SEMUA BOOKING SUDAH 'SELESAI' (PUNYA RIWAYAT)
             ═══════════════════════════════════════════════════════════════ --}}
        @elseif(($dashboardState ?? '') === 'B')
            <div class="bg-gradient-to-br from-[#122B1F] via-[#1B3B2B] to-[#0D1E16] text-white rounded-3xl p-5 sm:p-6 shadow-xl border border-[#C2A264]/40 relative overflow-hidden space-y-4">
                {{-- Decorative Glow --}}
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-[#C2A264]/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -left-8 -bottom-8 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 space-y-3.5">
                    {{-- Header Sambutan Selesai --}}
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white/10 text-xl flex items-center justify-center border border-[#C2A264]/30 shadow-inner shrink-0">
                            🕋
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-[#E5C88F] block">
                                Alhamdulillah &bull; Perjalanan Tuntas
                            </span>
                            <h2 class="text-base sm:text-lg font-bold text-white tracking-tight leading-snug mt-0.5">
                                Terima kasih telah menyelesaikan ibadah Umrah bersama PT Zein Internasional 🕋
                            </h2>
                        </div>
                    </div>

                    <p class="text-xs text-emerald-100/80 leading-relaxed">
                        Semoga seluruh rangkaian ibadah Bapak/Ibu <span class="font-bold text-white">{{ explode(' ', $user->name)[0] }}</span> diterima oleh Allah SWT, menjadi ibadah yang mabrur dan penuh keberkahan.
                    </p>

                    {{-- Ringkasan Perjalanan Terakhir --}}
                    @if($latestCompleted)
                        <div class="bg-white/[0.07] rounded-2xl p-3.5 border border-white/10 backdrop-blur-xs space-y-2.5">
                            <div class="flex items-center justify-between gap-2 border-b border-white/10 pb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-500/20 text-purple-200 border border-purple-400/40">
                                        Selesai
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#C2A264]/20 text-[#E5C88F] border border-[#C2A264]/40">
                                        Perjalanan Tuntas &bull; 100%
                                    </span>
                                </div>
                                <span class="text-[9px] font-mono text-zinc-300">{{ $latestCompleted->registration_number }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-white truncate">
                                        {{ $latestCompleted->package->name ?? 'Paket Ibadah' }}
                                    </div>
                                    <div class="text-[11px] text-zinc-300 mt-0.5">
                                        {{ $latestCompleted->packageVariant?->name ? 'Varian ' . $latestCompleted->packageVariant->name . ' • ' : '' }}
                                        Kamar {{ ucfirst($latestCompleted->room_type ?? 'Quad') }} &bull; {{ $latestCompleted->members->count() }} Jamaah
                                    </div>
                                    <div class="text-[10px] text-emerald-300 font-bold mt-1">
                                        Tahap 9 dari 9 &bull; LUNAS ✓
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[10px] text-zinc-300 block">Berangkat</span>
                                    <span class="text-xs font-bold text-white block">
                                        {{ $latestCompleted->package?->departure_date ? \Carbon\Carbon::parse($latestCompleted->package->departure_date)->translatedFormat('d M Y') : 'Selesai' }}
                                    </span>
                                    <a href="{{ route('jamaah.my-registration', ['id' => $latestCompleted->id]) }}" 
                                       class="inline-block mt-1.5 px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[10px] font-semibold border border-white/20 transition-all">
                                        Detail Pendaftaran
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- CTA Section --}}
                    <div class="pt-1 flex flex-col gap-2">
                        {{-- CTA Utama --}}
                        <a href="{{ route('paket') }}" 
                           class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-[#C2A264] to-[#E5C88F] hover:brightness-110 active:scale-[0.98] text-[#12271E] font-extrabold text-xs text-center shadow-md shadow-[#C2A264]/30 transition-all flex items-center justify-center gap-2">
                            <span>Pilih Paket Umrah/Haji Berikutnya</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>

                        {{-- CTA Sekunder --}}
                        <a href="{{ route('jamaah.history') }}" 
                           class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 active:scale-[0.98] text-white font-semibold text-xs text-center border border-white/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-[#E5C88F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Lihat Riwayat Perjalanan Saya</span>
                        </a>
                    </div>
                </div>
            </div>

        {{-- ═══════════════════════════════════════════════════════════════
             KONDISI C (MOBILE): BELUM PERNAH BOOKING SAMA SEKALI (AKUN BARU)
             ═══════════════════════════════════════════════════════════════ --}}
        @else
            <div class="bg-gradient-to-br from-[#1B3B2B] via-[#173527] to-[#10241A] text-white rounded-3xl p-5 sm:p-6 shadow-lg border border-white/10 relative overflow-hidden space-y-3.5 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-white/10 text-[#E5C88F] flex items-center justify-center border border-white/15 shadow-xs">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-[#C2A264] block">Langkah Awal Ibadah</span>
                    <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">Wujudkan Niat Suci Menuju Baitullah</h2>
                    <p class="text-xs text-zinc-300 leading-relaxed max-w-xs mx-auto">
                        Pilih paket ibadah Umrah & Haji Khusus terbaik dengan fasilitas nyaman dan bimbingan resmi sesuai Sunnah.
                    </p>
                </div>
                <div class="pt-1 flex flex-col gap-2">
                    <a href="{{ route('paket') }}" 
                       class="w-full py-3 px-4 rounded-xl bg-[#C2A264] hover:bg-[#b09153] text-[#12271E] font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <span>Pilih Paket Umrah/Haji Pertama Anda</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('jamaah.registration.create') }}" 
                       class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition-all">
                        Daftar Paket Sekarang
                    </a>
                </div>
            </div>
        @endif

        {{-- Mobile Menu Utama / Shortcut Cepat (Grid 4 Kolom) --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Menu Layanan</h2>
                <span class="text-[10px] text-[#526057]">Akses Cepat</span>
            </div>
            <div class="grid grid-cols-4 gap-2">
                
                {{-- 1. Pendaftaran / Dokumen --}}
                <a href="{{ route('jamaah.my-registration') }}" 
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Dokumen</span>
                </a>

                {{-- 2. Katalog Paket & Manasik --}}
                <a href="{{ route('paket') }}" 
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Paket &amp; Manasik</span>
                </a>

                {{-- 3. Pembayaran --}}
                @php
                    $mobilePayUrl = ($registration && ($registration->status === 'menunggu_pembayaran_dp' || $registration->status === 'dokumen_disetujui'))
                        ? route('jamaah.payment.dp')
                        : (($registration && in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                            ? route('jamaah.payment.pelunasan')
                            : route('jamaah.my-registration'));
                @endphp
                <a href="{{ $mobilePayUrl }}" 
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Pembayaran</span>
                </a>

                {{-- 4. Bantuan CS WhatsApp --}}
                <a href="https://wa.me/6281222222562?text=Assalamu%27alaikum%20Zein%20Tour,%20saya%20jamaah%20ingin%20konsultasi" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a.75.75 0 01-.84-.84 5.97 5.97 0 001.057-3.035C4.606 15.688 4 13.928 4 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Bantuan CS</span>
                </a>

            </div>
        </div>

        {{-- Mobile Informasi Jamaah Terdaftar --}}
        @if($registration && $registration->members->isNotEmpty())
            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 shadow-2xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Anggota Terdaftar</span>
                    <span class="text-[10px] font-bold text-[#1B3B2B] bg-[#EFF3EB] px-2 py-0.5 rounded-full border border-[#CCD8C7]">
                        {{ $registration->members->count() }} Orang
                    </span>
                </div>
                <div class="divide-y divide-[#EFF3EB]">
                    @foreach($registration->members as $idx => $member)
                        <div class="py-2 flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-5 h-5 rounded-full bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center font-bold text-[10px] shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-bold text-[#12271E] truncate">{{ $member->full_name }}</span>
                            </div>
                            <span class="text-[10px] text-[#526057] capitalize shrink-0 font-medium">
                                {{ $member->relationship_label ?? $member->gender }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Mobile Panduan & Informasi Penting --}}
        <div class="space-y-2.5">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Panduan Ibadah</h2>

            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 shadow-2xs flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#12271E]">Persyaratan Paspor & Berkas</h3>
                    <p class="text-[11px] text-[#526057] leading-relaxed mt-0.5">
                        Pastikan paspor Anda masih berlaku minimal 8 bulan dari tanggal keberangkatan.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 shadow-2xs flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#12271E]">Izin Resmi PPIU & PIHK</h3>
                    <p class="text-[11px] text-[#526057] leading-relaxed mt-0.5">
                        PT. Zein Internasional memiliki izin PPIU No. U.255/2020 & PIHK No. 599/2021 Kemenag RI.
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. KHUSUS TAMPILAN DESKTOP & TABLET LEBAR (>= 1024px)
         Mempertahankan Desain Desktop yang Sudah Ada Tanpa Perubahan
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="hidden lg:block max-w-6xl mx-auto px-6 lg:px-8 py-10 space-y-8">

        {{-- 1. Kartu Sambutan & Profil Jamaah --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-52 h-52 bg-[#EFF3EB]/70 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                
                {{-- Avatar & Informasi Profil --}}
                <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                    {{-- Inisial Nama / Foto Profil --}}
                    <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center text-xl sm:text-2xl font-bold shadow-md shadow-[#1B3B2B]/20 shrink-0 ring-4 ring-[#EFF3EB] border border-[#CCD8C7] overflow-hidden">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $user->initials }}
                        @endif
                    </div>

                    <div class="min-w-0 space-y-1">
                        {{-- LEVEL 1: Greeting --}}
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#12271E] tracking-tight truncate">
                            Assalamu'alaikum, {{ explode(' ', $user->name)[0] }}
                        </h1>

                        {{-- LEVEL 2: Contact Info --}}
                        <p class="text-xs sm:text-sm text-[#526057] flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span class="truncate">{{ $user->email }}</span>
                            <span class="text-[#CCD8C7] hidden sm:inline">&bull;</span>
                            <span class="font-medium text-[#1B3B2B]">{{ $user->phone_formatted }}</span>
                        </p>

                        {{-- LEVEL 3: Badge Status Akun --}}
                        <div class="pt-1">
                            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/80 px-3 py-0.5 rounded-full border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Akun Terverifikasi
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tautan Cepat / Action Buttons --}}
                <div class="flex items-center gap-3 self-stretch sm:self-start lg:self-center shrink-0 flex-wrap sm:flex-nowrap">
                    <a href="{{ route('jamaah.profile') }}" 
                       class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7] shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <span>Lihat Profil</span>
                    </a>
                    <a href="{{ route('paket') }}" 
                       class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        <span>Pilihan Paket</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Pesan Notifikasi --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs sm:text-sm text-amber-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('warning') }}</span>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
             KONDISI A (DESKTOP): ADA BOOKING AKTIF (STATUS BUKAN 'SELESAI')
             ═══════════════════════════════════════════════════════════════ --}}
        @if(($dashboardState ?? 'A') === 'A' && count($activeRegistrations ?? ($registration ? [$registration] : [])) > 0)
            @php $desktopRegsToRender = $activeRegistrations ?? ($registration ? collect([$registration]) : collect()); @endphp
            @if($desktopRegsToRender->count() > 1)
                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-sm text-amber-900 font-semibold flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Anda memiliki {{ $desktopRegsToRender->count() }} Pendaftaran Aktif</span>
                    </div>
                    <span class="text-xs text-amber-700 font-normal">Diurutkan berdasarkan yang memerlukan perhatian terlebih dahulu</span>
                </div>
            @endif

            @foreach($desktopRegsToRender as $desktopRegIndex => $registration)
            @php
                $step = $registration->step_number;
                $totalSteps = \App\Models\Registration::TOTAL_STEPS;
                $progress = $registration->progress_percentage;
                $isCancelled = $registration->status === 'dibatalkan';
            @endphp
            {{-- Kartu Pendaftaran Aktif --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xs overflow-hidden">
                
                {{-- Header Kartu & Informasi Paket --}}
                <div class="p-6 sm:p-8 border-b border-[#E0E7DC] bg-gradient-to-br from-[#EFF3EB]/50 via-white to-white">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                        
                        {{-- Detail Paket --}}
                        <div class="min-w-0 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#C2A264]"></span>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">
                                    Informasi Paket Pilihan
                                </span>
                            </div>

                            {{-- Nama Paket --}}
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#12271E] tracking-tight leading-tight">
                                {{ $registration->package->name ?? 'Paket Pilihan Umrah' }}
                                @if($registration->packageVariant)
                                    <span class="text-[#526057] font-normal text-lg sm:text-xl block sm:inline">— {{ $registration->packageVariant->name }}</span>
                                @endif
                            </h2>

                            {{-- Metadata Periode, Durasi, Jumlah Jamaah --}}
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm text-[#526057] pt-1">
                                @if($registration->packageVariant)
                                    <span class="inline-flex items-center gap-1.5 bg-[#1B3B2B] text-white px-3 py-1 rounded-xl font-bold">
                                        <span>Varian {{ $registration->packageVariant->name }}</span>
                                    </span>
                                @endif

                                @if($registration->room_type)
                                    <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                        <span>Kamar: {{ ucfirst($registration->room_type) }}</span>
                                    </span>
                                @endif

                                @if($registration->package->departure_date)
                                    <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                                        <span>{{ $registration->package->departure_date->translatedFormat('d F Y') }}</span>
                                    </span>
                                @endif

                                @if($registration->package->duration)
                                    <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $registration->package->duration }} Hari</span>
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                                    <span>Terdaftar untuk {{ $registration->members->count() }} Jamaah</span>
                                </span>
                            </div>
                        </div>

                        {{-- Status Badge & Reference Number Chip --}}
                        <div class="shrink-0 flex flex-col items-end gap-2.5 self-start">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] border border-[#CCD8C7] shadow-2xs font-mono text-xs font-bold">
                                <svg class="w-3.5 h-3.5 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                </svg>
                                <span>{{ $registration->registration_number }}</span>
                            </div>
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs sm:text-sm font-bold {{ $statusMeta['badge_bg_desktop'] }} border shadow-2xs">
                                <span class="w-2.5 h-2.5 rounded-full {{ $statusMeta['dot_color'] }} {{ $statusMeta['pulse'] ? 'animate-pulse' : '' }} shrink-0"></span>
                                @include('jamaah.partials.status-icon', ['icon' => $statusMeta['icon']])
                                <span>{{ $registration->status_label }}</span>
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Konten Rincian & Indikator Tahapan --}}
                <div class="p-6 sm:p-8 space-y-6 sm:space-y-7">
                    
                    {{-- Progres Tahapan --}}
                    @if(!$isCancelled)
                        <div class="space-y-3 bg-[#EFF3EB]/50 p-5 rounded-2xl border border-[#CCD8C7]">
                            <div class="flex items-center justify-between text-xs sm:text-sm font-bold text-[#12271E]">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                    </svg>
                                    <span>Tahap Persiapan Ibadah</span>
                                </span>
                                <span class="text-[#1B3B2B] font-semibold">Tahap {{ $step }} dari {{ \App\Models\Registration::TOTAL_STEPS }} ({{ $registration->progress_percentage }}%)</span>
                            </div>

                            {{-- Desktop 9-Segment Milestone Stepper --}}
                            <div class="grid grid-cols-9 gap-1.5 py-1">
                                @for($i = 1; $i <= $totalSteps; $i++)
                                    @php
                                        $isPassed = $i < $step;
                                        $isCurrent = $i === $step;
                                    @endphp
                                    <div class="group relative flex flex-col items-center">
                                        <div class="w-full h-2.5 rounded-full transition-all duration-300 {{ 
                                            $isCurrent 
                                                ? 'bg-gradient-to-r from-[#C2A264] to-emerald-600 shadow-sm ring-2 ring-[#C2A264]/40 animate-pulse' 
                                                : ($isPassed ? 'bg-emerald-600' : 'bg-[#CCD8C7]/60') 
                                        }}"></div>
                                    </div>
                                @endfor
                            </div>

                            <div class="w-full bg-white rounded-full h-2 overflow-hidden border border-[#CCD8C7]">
                                <div class="bg-gradient-to-r from-[#C2A264] to-[#1B3B2B] h-full rounded-full transition-all duration-500" style="width: {{ $registration->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-900 space-y-1">
                            <span class="font-bold text-red-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Pendaftaran Telah Dibatalkan</span>
                            </span>
                            <p class="text-red-700 text-xs">Pendaftaran ini telah resmi dibatalkan dan tidak memiliki kewajiban pembayaran aktif.</p>
                        </div>
                    @endif

                    {{-- Ringkasan Biaya (3 Kolom Proporsional) --}}
                    @if($registration->invoice)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="min-w-0 bg-[#EFF3EB]/60 p-5 rounded-2xl border border-[#CCD8C7] flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">
                                        Total Biaya
                                    </span>
                                    <svg class="w-4 h-4 text-[#4D5E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-lg sm:text-xl font-extrabold text-[#12271E] block truncate">
                                    Rp {{ number_format($registration->invoice->total_price, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-[#526057] block mt-1">Estimasi seluruh jamaah</span>
                            </div>

                            <div class="min-w-0 bg-emerald-50/70 p-5 rounded-2xl border border-emerald-200 flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-800">
                                        Sudah Dibayar
                                    </span>
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-lg sm:text-xl font-extrabold text-emerald-700 block truncate">
                                    Rp {{ number_format($registration->invoice->total_paid, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-emerald-700/80 block mt-1">Telah diverifikasi keuangan</span>
                            </div>

                            <div class="min-w-0 bg-[#EFF3EB]/60 p-5 rounded-2xl border border-[#CCD8C7] flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">
                                        Sisa Pembayaran
                                    </span>
                                    <svg class="w-4 h-4 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                                </div>
                                <span class="text-lg sm:text-xl font-extrabold {{ $registration->invoice->remaining_balance > 0 ? 'text-[#1B3B2B]' : 'text-emerald-700' }} block truncate">
                                    {{ $registration->invoice->remaining_balance > 0 ? 'Rp ' . number_format($registration->invoice->remaining_balance, 0, ',', '.') : 'Lunas ✓' }}
                                </span>
                                <span class="text-[10px] text-[#526057] block mt-1">
                                    {{ $registration->invoice->remaining_balance > 0 ? 'Sesuai termin pelunasan' : 'Biaya tuntas seluruhnya' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Tombol Aksi Utama --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                        <a href="{{ route('jamaah.my-registration') }}" 
                           class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md shadow-[#1B3B2B]/15">
                            <span>Lihat Rincian Pendaftaran</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>

                        @if($registration->status === 'menunggu_pembayaran_dp' || $registration->status === 'dokumen_disetujui')
                            <a href="{{ route('jamaah.payment.dp') }}" 
                                class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-[#12271E] bg-gradient-to-r from-[#C2A264] to-[#B09153] hover:brightness-105 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                                <span>Bayar DP Sekarang</span>
                            </a>
                        @elseif($registration->status === 'menunggu_verifikasi_pembayaran_dp')
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-sky-800 bg-sky-50 border border-sky-200">
                                <svg class="w-4 h-4 text-sky-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Verifikasi Pembayaran DP</span>
                            </span>
                        @elseif(in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                            <a href="{{ route('jamaah.payment.pelunasan') }}" 
                                class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Setor Pelunasan</span>
                            </a>
                        @endif
                    </div>

                </div>

            </div>
            @endforeach

        {{-- ═══════════════════════════════════════════════════════════════
             KONDISI B (DESKTOP): SEMUA BOOKING SUDAH 'SELESAI' (PUNYA RIWAYAT)
             ═══════════════════════════════════════════════════════════════ --}}
        @elseif(($dashboardState ?? '') === 'B')
            <div class="bg-gradient-to-br from-[#122B1F] via-[#1B3B2B] to-[#0D1E16] text-white rounded-3xl p-8 sm:p-10 shadow-xl border border-[#C2A264]/40 relative overflow-hidden space-y-6">
                {{-- Background decorative elements --}}
                <div class="absolute -right-12 -top-12 w-64 h-64 bg-[#C2A264]/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/10">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-white/10 text-3xl flex items-center justify-center border border-[#C2A264]/40 shadow-inner shrink-0">
                                🕋
                            </div>
                            <div>
                                <span class="text-xs uppercase font-bold tracking-widest text-[#E5C88F] block mb-1">
                                    Alhamdulillah &bull; Perjalanan Ibadah Tuntas
                                </span>
                                <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight">
                                    Terima kasih telah menyelesaikan ibadah Umrah bersama PT Zein Internasional 🕋
                                </h2>
                                <p class="text-xs sm:text-sm text-emerald-100/80 mt-1.5 leading-relaxed max-w-2xl">
                                    Semoga seluruh amal ibadah Bapak/Ibu <span class="font-bold text-white">{{ $user->name }}</span> menjadi haji/umrah yang mabrur, senantiasa dilimpahi keberkahan dan keridhaan Allah SWT.
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-3">
                            <a href="{{ route('paket') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-[#12271E] bg-gradient-to-r from-[#C2A264] to-[#E5C88F] hover:brightness-110 active:scale-[0.98] transition-all shadow-md shadow-[#C2A264]/30">
                                <span>Pilih Paket Umrah/Haji Berikutnya</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                            <a href="{{ route('jamaah.history') }}" 
                               class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-white/10 hover:bg-white/20 active:scale-[0.98] transition-all border border-white/20">
                                <svg class="w-4 h-4 text-[#E5C88F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Lihat Riwayat Perjalanan Saya</span>
                            </a>
                        </div>
                    </div>

                    {{-- Ringkasan Perjalanan Terakhir --}}
                    @if($latestCompleted)
                        <div class="bg-white/[0.07] rounded-2xl p-6 border border-white/10 backdrop-blur-xs space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs uppercase font-bold tracking-wider text-emerald-300">Ringkasan Perjalanan Terakhir</span>
                                    <span class="font-mono text-xs font-bold text-white bg-black/40 px-2.5 py-0.5 rounded-lg border border-[#C2A264]/30">
                                        {{ $latestCompleted->registration_number }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-200 border border-purple-400/40">
                                        Selesai
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#C2A264]/20 text-[#E5C88F] border border-[#C2A264]/40">
                                        Perjalanan Tuntas &bull; 100%
                                    </span>
                                    <a href="{{ route('jamaah.my-registration', ['id' => $latestCompleted->id]) }}" 
                                       class="px-3 py-1 rounded-xl text-xs font-bold text-white bg-white/10 hover:bg-white/20 border border-white/20 transition-all">
                                        Detail Pendaftaran
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-1">
                                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                                    <span class="text-[11px] text-zinc-300 uppercase font-semibold block">Paket Ibadah</span>
                                    <span class="font-bold text-sm text-white block mt-1 truncate">{{ $latestCompleted->package->name ?? 'Paket Ibadah' }}</span>
                                    <span class="text-[11px] text-[#E5C88F] block mt-0.5">{{ $latestCompleted->packageVariant?->name ?? 'Varian Standar' }}</span>
                                </div>

                                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                                    <span class="text-[11px] text-zinc-300 uppercase font-semibold block">Keberangkatan</span>
                                    <span class="font-bold text-sm text-white block mt-1">
                                        {{ $latestCompleted->package?->departure_date ? \Carbon\Carbon::parse($latestCompleted->package->departure_date)->translatedFormat('d M Y') : 'Tuntas' }}
                                    </span>
                                    <span class="text-[11px] text-zinc-300 block mt-0.5">{{ $latestCompleted->package?->duration ?? 9 }} Hari Perjalanan</span>
                                </div>

                                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                                    <span class="text-[11px] text-zinc-300 uppercase font-semibold block">Jumlah Jamaah</span>
                                    <span class="font-bold text-sm text-white block mt-1">{{ $latestCompleted->members->count() }} Orang</span>
                                    <span class="text-[11px] text-zinc-300 block mt-0.5">Kamar {{ ucfirst($latestCompleted->room_type ?? 'Quad') }}</span>
                                </div>

                                <div class="bg-white/5 rounded-xl p-3.5 border border-white/10">
                                    <span class="text-[11px] text-zinc-300 uppercase font-semibold block">Status & Pelunasan</span>
                                    <span class="font-bold text-sm text-emerald-300 block mt-1">
                                        LUNAS ✓
                                    </span>
                                    <span class="text-[11px] text-[#E5C88F] font-medium block mt-0.5">Tahap 9 dari 9 &bull; 100%</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        {{-- ═══════════════════════════════════════════════════════════════
             KONDISI C (DESKTOP): BELUM PERNAH BOOKING SAMA SEKALI (AKUN BARU)
             ═══════════════════════════════════════════════════════════════ --}}
        @else
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-8 sm:p-12 text-center shadow-xs">
                <div class="w-20 h-20 rounded-3xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mx-auto mb-5 shadow-xs border border-[#CCD8C7]">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-[#12271E] mb-2">
                    Belum Ada Pendaftaran
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] max-w-md mx-auto mb-8 leading-relaxed">
                    Anda belum memiliki paket perjalanan ibadah. Yuk, lihat pilihan paket umrah dan haji khusus yang tersedia bersama PT. Zein Internasional.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('paket') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md shadow-[#1B3B2B]/15">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        <span>Pilih Paket Umrah/Haji Pertama Anda</span>
                    </a>
                    <a href="{{ route('jamaah.registration.create') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-[#12271E] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors border border-[#CCD8C7]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span>Daftar Sekarang</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- 3. Panduan Persiapan & Jaminan Layanan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-4 border border-[#CCD8C7]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#12271E] mb-2">Persyaratan Dokumen</h3>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Siapkan Paspor asli (masa berlaku minimal 8 bulan), KTP, Kartu Keluarga, dan Buku Nikah atau Akta Lahir bagi anggota keluarga.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-4 border border-[#CCD8C7]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#12271E] mb-2">Bimbingan & Manasik</h3>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Setiap calon jamaah mendapatkan pembekalan intensif tata cara ibadah sesuai Sunnah sebelum hari keberangkatan.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-4 border border-[#CCD8C7]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#12271E] mb-2">Izin PPIU Resmi</h3>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Izin PPIU No. U.255/2020 oleh Kemenag RI dengan jaminan 5 Pasti Umrah: Pasti Travel, Jadwal, Terbang, Hotel, dan Visa.
                    </p>
                </div>
            </div>

        </div>

    </div>

</x-layouts.jamaah>
