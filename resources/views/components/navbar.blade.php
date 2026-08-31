@props(['company' => null])

@php
    $currentRoute = request()->route()?->getName() ?? '';

    $isHome = $currentRoute === 'home';
    $isProfil = $currentRoute === 'profil';
    $isLegalitas = $currentRoute === 'legalitas';
    $isPaket = str_starts_with($currentRoute, 'paket');
    $isLayanan = $currentRoute === 'galeri';
    $isKontak = $currentRoute === 'kontak';

    $activeClass = 'text-[#1B3B2B] font-semibold transition-colors flex items-center gap-1';
    $inactiveClass = 'text-[#526057] hover:text-[#1B3B2B] transition-colors flex items-center gap-1';

    $phone = $company['whatsapp'] ?? '6281222222562';
    $displayPhone = $company['phone'] ?? '+62821 2148 3337';
    $fbUrl = $company['social_media']['facebook'] ?? 'https://id-id.facebook.com/PTZeinInternasional/';
    $ytUrl = $company['social_media']['youtube'] ?? 'https://www.youtube.com/@ZEINTV7';
    $igUrl = $company['social_media']['instagram'] ?? 'https://www.instagram.com/zeintour_official/';
@endphp

<header id="navbar" 
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-200"
        x-data="{ mobileOpen: false, layananDropdown: false }"
        @close-mobile-menu.window="mobileOpen = false">
    
    <!-- ═══════════════════════════════════════════════════════════════
         1. TOP BAR (Info Kiri | Ticker YouTube Tengah | Medsos Kanan)
    ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-[#122B1F] text-white/85 text-[11px] py-2 px-4 sm:px-6 lg:px-8 border-b border-white/10">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            
            <!-- Kiri: Link Info dipisah garis vertikal (Desktop/Tablet) -->
            <div class="hidden md:flex items-center gap-3 text-xs overflow-hidden whitespace-nowrap">
                <a href="{{ route('paket') }}" class="hover:text-white transition-colors">Paket Umrah</a>
                <span class="text-white/30">|</span>
                <a href="{{ route('paket') }}" class="hover:text-white transition-colors">Program Haji</a>
                <span class="text-white/30">|</span>
                <a href="{{ route('kontak') }}" class="hover:text-white transition-colors">Layanan Jamaah</a>
            </div>

            <!-- Tengah: Ticker Pendaftaran Resmi (Clickable) -->
            <div class="text-center text-xs overflow-hidden">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors group">
                    <span class="font-bold tracking-wider text-white underline hover:text-emerald-300 transition-colors">DAFTAR SEKARANG</span>
                </a>
            </div>

            <!-- Kanan: Media Sosial -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0 text-xs">
                <a href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-white transition-colors" aria-label="Facebook">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <span class="hidden sm:inline text-[11px]">Facebook</span>
                </a>
                <span class="text-white/30 hidden sm:inline">|</span>
                <a href="{{ $ytUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-white transition-colors" aria-label="YouTube">
                    <svg class="w-3.5 h-3.5 fill-current text-red-500" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    <span class="hidden sm:inline text-[11px]">YouTube</span>
                </a>
                <span class="text-white/30 hidden sm:inline">|</span>
                <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-white transition-colors" aria-label="Instagram">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    <span class="hidden sm:inline text-[11px]">Instagram</span>
                </a>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
         2. NAVBAR UTAMA (Latar Putih, Menu Teks Bersih & Layanan Pelanggan)
    ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-white border-b border-[#E0E7DC] shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Kiri: Logo Resmi PT. Zein -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 py-1" aria-label="Beranda PT. Zein Internasional">
                    <img src="{{ asset('images/logo-zein.webp') }}" 
                         alt="PT. Zein Internasional" 
                         width="160" 
                         height="66" 
                         class="h-10 sm:h-12 w-auto object-contain">
                </a>

                <!-- Tengah: Menu Navigasi -->
                <nav class="hidden lg:flex items-center space-x-6 lg:space-x-8 text-xs" aria-label="Navigasi Utama">
                    
                    <!-- Beranda -->
                    <a href="{{ $isHome ? '#hero' : route('home') }}" 
                       data-nav-item
                       class="{{ $isHome ? $activeClass : $inactiveClass }}">
                        <span>Beranda</span>
                    </a>

                    <!-- Profil -->
                    <a href="{{ $isHome ? '#profil' : route('profil') }}" 
                       data-nav-item
                       class="{{ $isProfil ? $activeClass : $inactiveClass }}">
                        <span>Profil</span>
                    </a>

                    <!-- Legalitas -->
                    <a href="{{ $isHome ? '#legalitas' : route('legalitas') }}" 
                       data-nav-item
                       class="{{ $isLegalitas ? $activeClass : $inactiveClass }}">
                        <span>Legalitas</span>
                    </a>

                    <!-- Paket -->
                    <a href="{{ $isHome ? '#paket' : route('paket') }}" 
                       data-nav-item
                       class="{{ $isPaket ? $activeClass : $inactiveClass }}">
                        <span>Paket</span>
                    </a>

                    <!-- Dropdown Layanan -->
                    <div class="relative" @mouseenter="layananDropdown = true" @mouseleave="layananDropdown = false" @click.outside="layananDropdown = false">
                        <button class="{{ $isLayanan ? $activeClass : $inactiveClass }} cursor-pointer" 
                                data-nav-item
                                aria-haspopup="true" 
                                :aria-expanded="layananDropdown ? 'true' : 'false'">
                            <span>Layanan</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isLayanan ? 'text-[#1B3B2B]' : 'text-zinc-400' }}" 
                                 :class="{ 'rotate-180': layananDropdown }" 
                                 fill="none" 
                                 viewBox="0 0 24 24" 
                                 stroke="currentColor" 
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="layananDropdown" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 mt-1 w-56 rounded-2xl bg-white shadow-xl border border-[#E0E7DC] py-2 z-50 text-xs"
                             style="display: none;">
                            <a href="{{ $isHome ? '#skema-pendaftaran' : route('home') . '#skema-pendaftaran' }}" data-nav-item class="block px-4 py-2 text-[#526057] hover:text-[#1B3B2B] font-medium">
                                Skema Pendaftaran
                            </a>
                            <a href="{{ $isHome ? '#skema-perjalanan' : route('home') . '#skema-perjalanan' }}" data-nav-item class="block px-4 py-2 text-[#526057] hover:text-[#1B3B2B] font-medium">
                                Skema Perjalanan
                            </a>
                            <a href="{{ $isHome ? '#layanan-saudi' : route('home') . '#layanan-saudi' }}" data-nav-item class="block px-4 py-2 text-[#526057] hover:text-[#1B3B2B] font-medium">
                                Layanan di Saudi
                            </a>
                            <a href="{{ $isHome ? '#hak-jamaah' : route('home') . '#hak-jamaah' }}" data-nav-item class="block px-4 py-2 text-[#526057] hover:text-[#1B3B2B] font-medium">
                                Hak & Syarat Jamaah
                            </a>
                            <a href="{{ route('galeri') }}" data-nav-item class="block px-4 py-2 {{ $isLayanan ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057]' }} hover:text-[#1B3B2B] font-medium">
                                Galeri Foto
                            </a>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <a href="{{ $isHome ? '#kontak' : route('kontak') }}" 
                       data-nav-item
                       class="{{ $isKontak ? $activeClass : $inactiveClass }}">
                        <span>Kontak</span>
                    </a>
                </nav>

                <!-- Kanan: Layanan Pelanggan + Auth Buttons + Tombol Mobile -->
                <div class="flex items-center gap-3 sm:gap-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-xs focus-visible:ring-2 focus-visible:ring-[#1B3B2B] focus-visible:ring-offset-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                <span>Panel Admin</span>
                            </a>
                        @else
                            <a href="{{ route('jamaah.dashboard') }}" 
                               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-xs focus-visible:ring-2 focus-visible:ring-[#1B3B2B] focus-visible:ring-offset-2">
                                <span class="w-5 h-5 rounded-full bg-white/20 text-white flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span>Dashboard Jamaah</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="hidden sm:inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] active:scale-[0.97] border border-[#CCD8C7] transition-all shadow-2xs focus-visible:ring-2 focus-visible:ring-[#1B3B2B] focus-visible:ring-offset-2">
                            <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                            <span>LOGIN</span>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.97] transition-all shadow-xs focus-visible:ring-2 focus-visible:ring-[#1B3B2B] focus-visible:ring-offset-2">
                            <span>DAFTAR</span>
                        </a>
                    @endauth

                    <!-- Tombol Drawer Mobile -->
                    <button type="button" 
                            @click="mobileOpen = !mobileOpen"
                            class="lg:hidden p-2 text-[#12271E] hover:text-[#1B3B2B] transition-colors cursor-pointer rounded-lg focus-visible:ring-2 focus-visible:ring-[#1B3B2B]"
                            aria-label="Buka navigasi">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Drawer Mobile -->
    <div x-show="mobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-white border-b border-[#E0E7DC] px-5 pt-3 pb-6 space-y-2 text-sm shadow-xl"
         style="display: none;">
        
        <a href="{{ $isHome ? '#hero' : route('home') }}" data-nav-item @click="mobileOpen = false" class="block py-1.5 {{ $isHome ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057] hover:text-[#1B3B2B]' }}">Beranda</a>
        <a href="{{ $isHome ? '#profil' : route('profil') }}" data-nav-item @click="mobileOpen = false" class="block py-1.5 {{ $isProfil ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057] hover:text-[#1B3B2B]' }}">Profil</a>
        <a href="{{ $isHome ? '#legalitas' : route('legalitas') }}" data-nav-item @click="mobileOpen = false" class="block py-1.5 {{ $isLegalitas ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057] hover:text-[#1B3B2B]' }}">Legalitas</a>
        <a href="{{ $isHome ? '#paket' : route('paket') }}" data-nav-item @click="mobileOpen = false" class="block py-1.5 {{ $isPaket ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057] hover:text-[#1B3B2B]' }}">Paket</a>
        
        <div class="py-2 border-t border-b border-zinc-100 my-2 space-y-1">
            <div class="py-1 text-[10px] font-bold text-zinc-400 uppercase">Layanan</div>
            <a href="{{ $isHome ? '#skema-pendaftaran' : route('home') . '#skema-pendaftaran' }}" data-nav-item @click="mobileOpen = false" class="block py-1 text-[#526057] hover:text-[#1B3B2B] text-xs">Skema Pendaftaran</a>
            <a href="{{ $isHome ? '#skema-perjalanan' : route('home') . '#skema-perjalanan' }}" data-nav-item @click="mobileOpen = false" class="block py-1 text-[#526057] hover:text-[#1B3B2B] text-xs">Skema Perjalanan</a>
            <a href="{{ $isHome ? '#layanan-saudi' : route('home') . '#layanan-saudi' }}" data-nav-item @click="mobileOpen = false" class="block py-1 text-[#526057] hover:text-[#1B3B2B] text-xs">Layanan di Saudi</a>
            <a href="{{ $isHome ? '#hak-jamaah' : route('home') . '#hak-jamaah' }}" data-nav-item @click="mobileOpen = false" class="block py-1 text-[#526057] hover:text-[#1B3B2B] text-xs">Hak & Syarat Jamaah</a>
            <a href="{{ route('galeri') }}" data-nav-item @click="mobileOpen = false" class="block py-1 {{ $isLayanan ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057] hover:text-[#1B3B2B]' }} text-xs">Galeri Foto</a>
        </div>

        <a href="{{ $isHome ? '#kontak' : route('kontak') }}" data-nav-item @click="mobileOpen = false" class="block py-1.5 {{ $isKontak ? 'text-[#1B3B2B] font-semibold' : 'text-[#526057] hover:text-[#1B3B2B]' }}">Kontak</a>
        
        <div class="pt-3 space-y-2">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" 
                       class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs text-white bg-[#1B3B2B] hover:bg-[#132E22] text-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        <span>Panel Admin</span>
                    </a>
                @else
                    <a href="{{ route('jamaah.dashboard') }}" 
                       class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs text-white bg-[#1B3B2B] hover:bg-[#132E22] text-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <span>Dashboard Jamaah</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl font-medium text-xs text-[#526057] bg-white border border-[#E0E7DC] hover:text-[#1B3B2B] text-center cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" 
                   class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] text-center transition-all">
                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    <span>LOGIN JAMAAH</span>
                </a>
                <a href="{{ route('register') }}" 
                   class="w-full flex items-center justify-center py-3 px-4 rounded-xl font-bold text-xs text-white bg-[#1B3B2B] hover:bg-[#132E22] text-center shadow-xs transition-all">
                    DAFTAR AKUN BARU
                </a>
            @endauth

            <a href="https://wa.me/{{ $phone }}?text=Assalamu%27alaikum%20PT.%20Zein%20Internasional,%20saya%20ingin%20mendaftar%20Umrah/Haji" 
               target="_blank"
               class="w-full flex items-center justify-center py-2.5 px-4 rounded-xl font-medium text-xs text-[#526057] hover:text-[#1B3B2B] text-center">
                Bantuan WhatsApp
            </a>
        </div>
    </div>
</header>
