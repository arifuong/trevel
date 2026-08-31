@props([
    'title' => 'Portal Layanan Jamaah — PT. Zein Internasional',
    'activeTab' => 'home'
])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#1B3B2B">

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

        /* Safe area inset support */
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
         HEADER ATAS LAYAR DESKTOP & TABLET
         ═══════════════════════════════════════════════════════════════ --}}
    <header class="bg-[#1B3B2B] text-white shadow-sm sticky top-0 z-40 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                {{-- Logo & Identitas Portal --}}
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none" aria-label="Kembali ke Beranda Zein Tour">
                        <img src="{{ asset('images/logo-zein.webp') }}" 
                             alt="Logo Zein Tour" 
                             class="h-8 sm:h-9 w-auto brightness-0 invert object-contain" 
                             width="120" 
                             height="36">
                        <div class="hidden sm:block border-l border-white/20 pl-3">
                            <span class="text-xs font-bold tracking-tight text-white block leading-none">Portal Jamaah</span>
                            <span class="text-[10px] text-emerald-300/80 block leading-none mt-0.5 font-medium">PT. Zein Internasional</span>
                        </div>
                    </a>

                    {{-- Menu Navigasi Layar Komputer --}}
                    <nav class="hidden md:flex items-center space-x-1 pl-3 border-l border-white/10 text-xs">
                        <a href="{{ route('jamaah.dashboard') }}" 
                           class="px-3.5 py-2 rounded-full font-semibold transition-all {{ request()->routeIs('jamaah.dashboard') ? 'bg-white/15 text-white shadow-2xs' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                            Beranda
                        </a>
                        <a href="{{ route('paket') }}" 
                           class="px-3.5 py-2 rounded-full font-semibold transition-all {{ request()->routeIs('paket*') ? 'bg-white/15 text-white shadow-2xs' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                            Paket Umrah
                        </a>
                        <a href="{{ route('jamaah.my-registration') }}" 
                           class="px-3.5 py-2 rounded-full font-semibold transition-all {{ request()->routeIs('jamaah.my-registration') || request()->routeIs('jamaah.registration.*') ? 'bg-white/15 text-white shadow-2xs' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                            Pendaftaran Saya
                        </a>
                        <a href="{{ route('jamaah.profile') }}" 
                           class="px-3.5 py-2 rounded-full font-semibold transition-all {{ request()->routeIs('jamaah.profile*') ? 'bg-white/15 text-white shadow-2xs' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                            Profil Saya
                        </a>
                        <a href="https://wa.me/6281222222562?text=Assalamu%27alaikum%20Zein%20Tour,%20saya%20membutuhkan%20informasi%20bantuan%20ibadah" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="px-3.5 py-2 rounded-full font-semibold text-white/75 hover:text-white hover:bg-white/10 transition-all inline-flex items-center gap-1.5">
                            <span>Bantuan</span>
                            <svg class="w-3 h-3 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        </a>
                    </nav>
                </div>

                {{-- Profil & Tombol Keluar --}}
                <div class="flex items-center gap-3">
                    @php $currentUser = auth()->user(); @endphp
                    <a href="{{ route('jamaah.profile') }}" 
                       title="Buka Profil Saya"
                       class="hidden sm:flex items-center gap-2.5 bg-white/10 hover:bg-white/20 rounded-full py-1 pl-1.5 pr-3.5 border border-white/10 transition-all">
                        <div class="w-7 h-7 rounded-full bg-emerald-400/20 text-emerald-200 border border-emerald-300/30 flex items-center justify-center font-bold text-xs overflow-hidden">
                            @if($currentUser?->avatar_url)
                                <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover">
                            @else
                                {{ $currentUser?->initials ?? 'J' }}
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-white truncate max-w-[130px]">{{ $currentUser?->name ?? 'Jamaah' }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                title="Keluar dari Akun"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-white/80 bg-white/10 hover:bg-red-500/20 hover:text-red-200 hover:border-red-400/40 border border-white/10 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </header>

    {{-- Wadah Konten Utama --}}
    <main class="flex-grow w-full pb-safe md:pb-12 animate-pageIn">
        {{ $slot }}
    </main>

    {{-- Footer Layar Desktop --}}
    <footer class="hidden md:block bg-white border-t border-[#E0E7DC] py-6 text-center text-xs text-[#526057]/70">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} PT. Zein Internasional — PPIU Resmi Kemenag RI No. U.255/2020.</p>
            <div class="flex items-center gap-4 text-xs font-medium text-[#1B3B2B]">
                <a href="{{ route('home') }}" class="hover:underline">Beranda Website</a>
                <a href="{{ route('paket') }}" class="hover:underline">Paket Umrah</a>
                <a href="{{ route('kontak') }}" class="hover:underline">Bantuan & Kontak</a>
            </div>
        </div>
    </footer>

    {{-- ═══════════════════════════════════════════════════════════════
         NAVIGASI BAWAH LAYAR MOBILE (JAMAAH)
         ═══════════════════════════════════════════════════════════════ --}}
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-lg border-t border-[#E0E7DC] shadow-[0_-8px_20px_rgba(27,59,43,0.06)]"
         style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom, 0px));">
        
        <div class="grid grid-cols-5 items-center h-16 px-1.5 max-w-md mx-auto relative">
            
            {{-- 1. Beranda --}}
            @php $isHome = request()->routeIs('jamaah.dashboard'); @endphp
            <a href="{{ route('jamaah.dashboard') }}" 
               class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isHome ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
               aria-label="Beranda">
                <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isHome ? 'scale-110' : 'group-active:scale-95' }}">
                    <svg class="w-5 h-5 {{ $isHome ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    @if($isHome)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ $isHome ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                    Beranda
                </span>
            </a>

            {{-- 2. Paket --}}
            @php $isPaket = request()->routeIs('paket*'); @endphp
            <a href="{{ route('paket') }}" 
               class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isPaket ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
               aria-label="Paket Umrah">
                <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isPaket ? 'scale-110' : 'group-active:scale-95' }}">
                    <svg class="w-5 h-5 {{ $isPaket ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    @if($isPaket)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ $isPaket ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                    Paket
                </span>
            </a>

            {{-- 3. TOMBOL AKSI UTAMA TENGAH (DAFTAR / STATUS) --}}
            @php 
                $hasActiveReg = auth()->user()?->hasActiveRegistration();
                $centerUrl = $hasActiveReg ? route('jamaah.my-registration') : route('jamaah.registration.create');
            @endphp
            <div class="flex flex-col items-center justify-center relative -top-3">
                <a href="{{ $centerUrl }}" 
                   class="w-12 h-12 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center shadow-lg shadow-[#1B3B2B]/35 border-4 border-white hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none"
                   title="{{ $hasActiveReg ? 'Lihat Pendaftaran Saya' : 'Daftar Paket Baru' }}"
                   aria-label="{{ $hasActiveReg ? 'Lihat Pendaftaran Saya' : 'Daftar Paket Baru' }}">
                    @if($hasActiveReg)
                        <svg class="w-5 h-5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    @endif
                </a>
                <span class="text-[9.5px] font-bold text-[#1B3B2B] tracking-tight mt-0.5">
                    {{ $hasActiveReg ? 'Status' : 'Daftar' }}
                </span>
            </div>

            {{-- 4. Pendaftaran Saya --}}
            @php $isStatus = request()->routeIs('jamaah.my-registration') || request()->routeIs('jamaah.registration.*') || request()->routeIs('jamaah.payment.*'); @endphp
            <a href="{{ route('jamaah.my-registration') }}" 
               class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isStatus ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
               aria-label="Pendaftaran Saya">
                <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isStatus ? 'scale-110' : 'group-active:scale-95' }}">
                    <svg class="w-5 h-5 {{ $isStatus ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                    </svg>
                    @if($isStatus)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ $isStatus ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                    Pendaftaran
                </span>
            </a>

            {{-- 5. Profil Saya --}}
            @php $isProfile = request()->routeIs('jamaah.profile*'); @endphp
            <a href="{{ route('jamaah.profile') }}" 
               class="flex flex-col items-center justify-center h-full min-h-[44px] py-1 text-center transition-all group {{ $isProfile ? 'text-[#1B3B2B]' : 'text-[#526057] hover:text-[#1B3B2B]' }}"
               aria-label="Profil Saya">
                <div class="relative p-1 rounded-xl transition-transform duration-200 {{ $isProfile ? 'scale-110' : 'group-active:scale-95' }}">
                    <svg class="w-5 h-5 {{ $isProfile ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    @if($isProfile)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-[#1B3B2B] rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ $isProfile ? 'font-bold text-[#1B3B2B]' : 'font-medium text-[#526057]' }}">
                    Profil
                </span>
            </a>

        </div>
    </nav>

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
