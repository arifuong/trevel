@props(['title' => 'Pusat Kendali Admin — PT. Zein Internasional'])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#122B1F">

    @php
        $manifestPath = public_path('build/manifest.json');
        $compiledCssContent = '';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
            $cssEntry = $manifest['resources/css/app.css']['file'] ?? null;
            if ($cssEntry && file_exists(public_path('build/' . $cssEntry))) {
                $compiledCssContent = file_get_contents(public_path('build/' . $cssEntry));
            }
        }
    @endphp

    @if(!empty($compiledCssContent))
        <style>{!! $compiledCssContent !!}</style>
    @else
        @vite(['resources/css/app.css'])
    @endif

    <style>
        @keyframes pageFadeIn {
            0% { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .animate-pageIn {
            animation: pageFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .pb-safe {
            padding-bottom: calc(4.5rem + env(safe-area-inset-bottom, 0px));
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-pageIn {
                animation: none !important;
                transform: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#F8FAF7] text-[#526057] min-h-screen flex flex-col selection:bg-[#1B3B2B] selection:text-white">

    {{-- ═══════════════════════════════════════════════════════════════
         DESKTOP & TABLET SIDEBAR (NAVIGASI KIRI)
         ═══════════════════════════════════════════════════════════════ --}}
    <aside class="hidden lg:flex flex-col w-64 fixed inset-y-0 left-0 bg-[#122B1F] text-white z-30 border-r border-white/10">
        
        {{-- Logo & Brand --}}
        <div class="h-16 flex items-center px-6 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group focus:outline-none" aria-label="Beranda Admin">
                <img src="{{ asset('images/logo-zein.webp') }}" 
                     alt="Logo Zein Tour" 
                     class="h-8 w-auto brightness-0 invert object-contain" 
                     width="110" 
                     height="32">
                <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-400 bg-white/10 px-2 py-0.5 rounded border border-white/10">
                    Admin
                </span>
            </a>
        </div>

        {{-- Menu Navigasi --}}
        <div class="flex-grow py-6 px-4 space-y-1 overflow-y-auto">
            <div class="text-[10px] font-bold uppercase tracking-wider text-white/40 px-3 mb-2">
                Menu Utama
            </div>

            {{-- 1. Beranda --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#1B3B2B] text-white shadow-xs border border-emerald-500/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                </svg>
                <span>Beranda</span>
            </a>

            {{-- 2. Manajemen Jamaah --}}
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#1B3B2B] text-white shadow-xs border border-emerald-500/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                <span>Manajemen Jamaah</span>
            </a>

            {{-- 3. Paket Umrah --}}
            <a href="{{ route('admin.packages.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.packages.*') ? 'bg-[#1B3B2B] text-white shadow-xs border border-emerald-500/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                </svg>
                <span>Paket Umrah</span>
            </a>

            {{-- 3. Verifikasi Dokumen & Pendaftaran --}}
            <a href="{{ route('admin.registrations.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.registrations.*') ? 'bg-[#1B3B2B] text-white shadow-xs border border-emerald-500/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span>Verifikasi Dokumen</span>
                </div>
            </a>

            {{-- 4. Verifikasi Pembayaran --}}
            <a href="{{ route('admin.payments.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-[#1B3B2B] text-white shadow-xs border border-emerald-500/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                    <span>Verifikasi Pembayaran</span>
                </div>
            </a>

            {{-- 5. Validasi Pembatalan --}}
            <a href="{{ route('admin.cancellations.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.cancellations.*') ? 'bg-[#1B3B2B] text-white shadow-xs border border-emerald-500/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <span>Validasi Pembatalan</span>
                </div>
            </a>
        </div>

        {{-- Sidebar Bagian Bawah --}}
        <div class="p-4 border-t border-white/10 space-y-3">
            <a href="{{ route('home') }}" target="_blank" 
               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <span class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    <span>Lihat Website</span>
                </span>
                <span class="text-[10px] text-white/40">&rarr;</span>
            </a>

            <div class="flex items-center justify-between pt-2 border-t border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-xs border border-emerald-400/30">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="truncate max-w-[100px]">
                        <span class="text-xs font-semibold text-white block leading-tight truncate">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="text-[9px] text-emerald-300 block leading-tight">Pengelola</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" 
                            title="Keluar dari Panel Admin"
                            class="p-1.5 rounded-lg text-white/60 hover:text-red-300 hover:bg-red-500/20 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ═══════════════════════════════════════════════════════════════
         KONTEN UTAMA (OFFSET DESKTOP + HEADER MOBILE)
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="lg:pl-64 flex flex-col flex-grow min-h-screen">

        {{-- Header Atas Layar Mobile --}}
        <header class="lg:hidden bg-[#122B1F] text-white sticky top-0 z-30 px-4 h-14 flex items-center justify-between shadow-sm border-b border-white/10">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-zein.webp') }}" 
                     alt="Logo Zein Tour" 
                     class="h-7 w-auto brightness-0 invert object-contain" 
                     width="90" 
                     height="28">
                <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-400 bg-white/10 px-1.5 py-0.5 rounded border border-white/10">
                    Admin
                </span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" 
                   class="p-2 text-white/70 hover:text-white rounded-lg transition-colors"
                   title="Lihat Website">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-white/70 hover:text-red-300 rounded-lg transition-colors cursor-pointer" title="Keluar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    </button>
                </form>
            </div>
        </header>

        {{-- Bar Atas Layar Desktop --}}
        <div class="hidden lg:flex items-center justify-between bg-white border-b border-[#E0E7DC] px-8 py-3.5 sticky top-0 z-20 shadow-2xs">
            <div class="flex items-center gap-2 text-xs text-[#526057]">
                <span class="font-bold text-[#12271E]">PT. Zein Internasional</span>
                <span>/</span>
                <span>Pusat Kendali Pengelolaan Umrah & Haji</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-[#526057] bg-[#EFF3EB] px-3 py-1 rounded-full font-medium border border-[#E0E7DC]">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        {{-- Area Konten --}}
        <main class="flex-grow p-4 sm:p-6 lg:p-8 pb-safe lg:pb-8 animate-pageIn">
            {{-- Notifikasi Berhasil --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="font-medium leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            {{-- Notifikasi Kendala --}}
            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-800 flex items-start gap-3 shadow-2xs">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <div class="font-medium leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            {{ $slot }}
        </main>

        {{-- Footer Layar Desktop --}}
        <footer class="hidden lg:block bg-white border-t border-[#E0E7DC] py-4 text-center text-xs text-[#526057]/70">
            &copy; {{ date('Y') }} PT. Zein Internasional — Sistem Administrasi Resmi
        </footer>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         NAVIGASI BAWAH LAYAR MOBILE (ADMIN)
         ═══════════════════════════════════════════════════════════════ --}}
    @php
        $isAdminHome = request()->routeIs('admin.dashboard');
        $isAdminPaket = request()->routeIs('admin.packages.*');
        $isAdminReg = request()->routeIs('admin.registrations.*');
        $isOtherActive = request()->routeIs('admin.users.*', 'admin.payments.*', 'admin.cancellations.*');
    @endphp

    <div x-data="{ showMoreMenu: false }" class="lg:hidden">
        
        {{-- Backdrop Drawer "Fitur Lainnya" --}}
        <div x-show="showMoreMenu" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showMoreMenu = false"
             class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs"
             style="display: none;"></div>

        {{-- Bottom Sheet / Drawer "Fitur Lainnya" --}}
        <div x-show="showMoreMenu" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-250"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 inset-x-0 z-50 bg-white rounded-t-3xl border-t border-[#E0E7DC] shadow-2xl overflow-hidden max-h-[80vh] flex flex-col"
             style="display: none; padding-bottom: max(1.25rem, env(safe-area-inset-bottom, 0px));">
            
            {{-- Handle Bar Drawer --}}
            <div class="pt-3 pb-2 flex justify-center cursor-pointer" @click="showMoreMenu = false">
                <div class="w-12 h-1.5 rounded-full bg-[#CCD8C7]"></div>
            </div>

            {{-- Header Drawer --}}
            <div class="px-6 py-3 border-b border-[#E0E7DC] flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-[#12271E]">Fitur Lainnya</h3>
                    <p class="text-[11px] text-[#526057] mt-0.5">Kelola seluruh modul administrasi & layanan jamaah.</p>
                </div>
                <button type="button" 
                        @click="showMoreMenu = false" 
                        class="p-1.5 rounded-xl text-[#526057] hover:bg-[#EFF3EB] transition-colors"
                        aria-label="Tutup Menu">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- List Menu Fitur Lainnya --}}
            <div class="p-4 space-y-2 overflow-y-auto max-h-[60vh]">
                
                {{-- 1. Manajemen Jamaah --}}
                @php $isUserActive = request()->routeIs('admin.users.*'); @endphp
                <a href="{{ route('admin.users.index') }}" 
                   @click="showMoreMenu = false"
                   class="flex items-center gap-3.5 p-3 rounded-2xl transition-all {{ $isUserActive ? 'bg-[#EFF3EB] text-[#1B3B2B] font-bold border border-[#CCD8C7]' : 'text-[#12271E] hover:bg-[#F8FAF7]' }}">
                    <div class="w-10 h-10 rounded-xl {{ $isUserActive ? 'bg-[#1B3B2B] text-white' : 'bg-[#EFF3EB] text-[#1B3B2B]' }} flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <div class="text-xs font-bold leading-tight">Manajemen Jamaah</div>
                        <div class="text-[11px] text-[#526057] font-normal mt-0.5">Kelola dan pantau seluruh data jamaah terdaftar</div>
                    </div>
                    @if($isUserActive)
                        <span class="w-2 h-2 rounded-full bg-[#1B3B2B] shrink-0"></span>
                    @endif
                </a>

                {{-- 2. Verifikasi Pembayaran --}}
                @php $isPayActive = request()->routeIs('admin.payments.*'); @endphp
                <a href="{{ route('admin.payments.index') }}" 
                   @click="showMoreMenu = false"
                   class="flex items-center gap-3.5 p-3 rounded-2xl transition-all {{ $isPayActive ? 'bg-[#EFF3EB] text-[#1B3B2B] font-bold border border-[#CCD8C7]' : 'text-[#12271E] hover:bg-[#F8FAF7]' }}">
                    <div class="w-10 h-10 rounded-xl {{ $isPayActive ? 'bg-[#1B3B2B] text-white' : 'bg-[#EFF3EB] text-[#1B3B2B]' }} flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <div class="text-xs font-bold leading-tight">Verifikasi Pembayaran</div>
                        <div class="text-[11px] text-[#526057] font-normal mt-0.5">Cek dan validasi setoran DP & cicilan pelunasan</div>
                    </div>
                    @if($isPayActive)
                        <span class="w-2 h-2 rounded-full bg-[#1B3B2B] shrink-0"></span>
                    @endif
                </a>

                {{-- 3. Validasi Pembatalan --}}
                @php $isCancelActive = request()->routeIs('admin.cancellations.*'); @endphp
                <a href="{{ route('admin.cancellations.index') }}" 
                   @click="showMoreMenu = false"
                   class="flex items-center gap-3.5 p-3 rounded-2xl transition-all {{ $isCancelActive ? 'bg-[#EFF3EB] text-[#1B3B2B] font-bold border border-[#CCD8C7]' : 'text-[#12271E] hover:bg-[#F8FAF7]' }}">
                    <div class="w-10 h-10 rounded-xl {{ $isCancelActive ? 'bg-[#1B3B2B] text-white' : 'bg-[#EFF3EB] text-[#1B3B2B]' }} flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <div class="text-xs font-bold leading-tight">Validasi Pembatalan</div>
                        <div class="text-[11px] text-[#526057] font-normal mt-0.5">Tinjau pengajuan pembatalan & hitung refund</div>
                    </div>
                    @if($isCancelActive)
                        <span class="w-2 h-2 rounded-full bg-[#1B3B2B] shrink-0"></span>
                    @endif
                </a>

            </div>

        </div>

        {{-- Bar Navigasi Bawah Mobile (4 Tab Utama: Beranda, Paket, Dokumen, Fitur Lainnya) --}}
        <nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-lg border-t border-[#E0E7DC] shadow-[0_-8px_20px_rgba(18,43,31,0.08)]"
             style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom, 0px));">
            
            <div class="grid grid-cols-4 items-center h-16 px-1 max-w-md mx-auto relative">
                
                {{-- 1. Beranda --}}
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isAdminHome ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
                   aria-label="Beranda Admin">
                    <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isAdminHome ? 'scale-110' : 'group-active:scale-95' }}">
                        <svg class="w-5 h-5 {{ $isAdminHome ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                        @if($isAdminHome)
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 {{ $isAdminHome ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                        Beranda
                    </span>
                </a>

                {{-- 2. Paket --}}
                <a href="{{ route('admin.packages.index') }}" 
                   class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isAdminPaket ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
                   aria-label="Kelola Paket Umrah">
                    <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isAdminPaket ? 'scale-110' : 'group-active:scale-95' }}">
                        <svg class="w-5 h-5 {{ $isAdminPaket ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                        </svg>
                        @if($isAdminPaket)
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 {{ $isAdminPaket ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                        Paket
                    </span>
                </a>

                {{-- 3. Dokumen --}}
                <a href="{{ route('admin.registrations.index') }}" 
                   class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isAdminReg ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
                   aria-label="Verifikasi Dokumen">
                    <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isAdminReg ? 'scale-110' : 'group-active:scale-95' }}">
                        <svg class="w-5 h-5 {{ $isAdminReg ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        @if($isAdminReg)
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 {{ $isAdminReg ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                        Dokumen
                    </span>
                </a>

                {{-- 4. Fitur Lainnya (Drawer Toggle) --}}
                <button type="button" 
                        @click="showMoreMenu = !showMoreMenu"
                        class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group cursor-pointer {{ $isOtherActive ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
                        aria-label="Buka Menu Fitur Lainnya">
                    <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isOtherActive ? 'scale-110' : 'group-active:scale-95' }}">
                        <svg class="w-5 h-5 {{ $isOtherActive ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                        @if($isOtherActive)
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 {{ $isOtherActive ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                        Fitur Lainnya
                    </span>
                </button>

            </div>
        </nav>

    </div>

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
