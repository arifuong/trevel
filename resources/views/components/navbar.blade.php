@props(['company' => null])

@php
    $currentRoute = request()->route()?->getName() ?? '';

    $isHome = $currentRoute === 'home';
    $isProfil = $currentRoute === 'profil';
    $isLegalitas = $currentRoute === 'legalitas';
    $isPaket = str_starts_with($currentRoute, 'paket');
    $isGaleri = $currentRoute === 'galeri';
    $isLayanan = false;
    $isKontak = $currentRoute === 'kontak';

    $activeClass = 'text-[#12271E] font-semibold transition-colors py-2 flex items-center gap-1 relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-[2px] after:bg-[#1B3B2B] after:rounded-full';
    $inactiveClass = 'text-[#4D5E54] hover:text-[#12271E] font-medium transition-colors py-2 flex items-center gap-1';

    $phone = $company['whatsapp'] ?? '6281222222562';
    $displayPhone = $company['phone'] ?? '+62821 2148 3337';
    $fbUrl = $company['social_media']['facebook'] ?? 'https://id-id.facebook.com/PTZeinInternasional/';
    $ytUrl = $company['social_media']['youtube'] ?? 'https://www.youtube.com/@ZEINTV7';
    $igUrl = $company['social_media']['instagram'] ?? 'https://www.instagram.com/zeintour_official/';

    /**
     * SINGLE SOURCE OF TRUTH untuk Navigasi (Desktop & Mobile)
     * Urutan: Beranda, Profil, Legalitas, Paket, Galeri, Layanan (dropdown), Kontak
     */
    $navItems = [
        [
            'title' => 'Beranda',
            'url' => $isHome ? '#hero' : route('home'),
            'is_active' => $isHome,
        ],
        [
            'title' => 'Profil',
            'url' => $isHome ? '#profil' : route('profil'),
            'is_active' => $isProfil,
        ],
        [
            'title' => 'Legalitas',
            'url' => $isHome ? '#legalitas' : route('legalitas'),
            'is_active' => $isLegalitas,
        ],
        [
            'title' => 'Paket',
            'url' => $isHome ? '#paket' : route('paket'),
            'is_active' => $isPaket,
        ],
        [
            'title' => 'Galeri',
            'url' => route('galeri'),
            'is_active' => $isGaleri,
        ],
        [
            'title' => 'Layanan',
            'is_active' => $isLayanan,
            'children' => [
                [
                    'title' => 'Skema Pendaftaran',
                    'url' => $isHome ? '#skema-pendaftaran' : route('home') . '#skema-pendaftaran',
                    'is_active' => false,
                ],
                [
                    'title' => 'Skema Perjalanan',
                    'url' => $isHome ? '#skema-perjalanan' : route('home') . '#skema-perjalanan',
                    'is_active' => false,
                ],
                [
                    'title' => 'Layanan di Saudi',
                    'url' => $isHome ? '#layanan-saudi' : route('home') . '#layanan-saudi',
                    'is_active' => false,
                ],
                [
                    'title' => 'Hak & Syarat Jamaah',
                    'url' => $isHome ? '#hak-jamaah' : route('home') . '#hak-jamaah',
                    'is_active' => false,
                ],
            ],
        ],
        [
            'title' => 'Kontak',
            'url' => $isHome ? '#kontak' : route('kontak'),
            'is_active' => $isKontak,
        ],
    ];
@endphp

<header id="navbar" 
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-200"
        x-data="{ mobileOpen: false, layananDropdown: false, atTop: true }"
        @close-mobile-menu.window="mobileOpen = false"
        @scroll.window="atTop = (window.scrollY === 0)">
    
    <!-- ═══════════════════════════════════════════════════════════════
         1. TOP BAR (Info Kiri | Ticker Registrasi Tengah | Medsos Kanan)
    ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-[#122B1F] text-white/85 text-[11px] py-2 px-4 sm:px-6 lg:px-8 border-b border-white/10"
         :style="atTop ? 'transform: translateY(0); opacity: 1; max-height: 50px; transition: transform 350ms ease, opacity 350ms ease, max-height 350ms ease;' : 'transform: translateY(-100%); opacity: 0; max-height: 0; padding-top: 0; padding-bottom: 0; border-bottom-width: 0; overflow: hidden; transition: transform 350ms ease, opacity 350ms ease, max-height 350ms ease, padding 350ms ease;'">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            
            <!-- Kiri: Link Info dipisah garis vertikal (Desktop/Tablet) -->
            <div class="hidden md:flex items-center gap-3 text-xs overflow-hidden whitespace-nowrap">
                <a href="{{ route('paket') }}" class="hover:text-white transition-colors">Paket Umrah</a>
                <span class="text-white/20">|</span>
                <a href="{{ route('paket') }}" class="hover:text-white transition-colors">Program Haji</a>
                <span class="text-white/20">|</span>
                <a href="{{ route('kontak') }}" class="hover:text-white transition-colors">Layanan Jamaah</a>
            </div>

            <!-- Tengah: Pendaftaran Resmi (Clickable) -->
            <div class="text-center text-xs overflow-hidden">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-white/90 hover:text-white transition-colors group">
                    <span class="text-[10px] tracking-wider uppercase font-bold text-[#C2A264] bg-white/10 px-2 py-0.5 rounded-full border border-white/15">Resmi</span>
                    <span class="font-medium text-white group-hover:text-emerald-300 transition-colors">Daftar Umrah & Haji Khusus &rarr;</span>
                </a>
            </div>

            <!-- Kanan: Media Sosial -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0 text-xs">
                <a href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-white transition-colors" aria-label="Facebook">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <span class="hidden sm:inline text-[11px]">Facebook</span>
                </a>
                <span class="text-white/20 hidden sm:inline">|</span>
                <a href="{{ $ytUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-white transition-colors" aria-label="YouTube">
                    <svg class="w-3.5 h-3.5 fill-current text-red-500" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    <span class="hidden sm:inline text-[11px]">YouTube</span>
                </a>
                <span class="text-white/20 hidden sm:inline">|</span>
                <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 hover:text-white transition-colors" aria-label="Instagram">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    <span class="hidden sm:inline text-[11px]">Instagram</span>
                </a>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
         2. NAVBAR UTAMA (Latar Putih Bersih, Navigasi Proporsional)
    ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-white/95 backdrop-blur-md border-b border-[#E0E7DC]/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
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

                <!-- Tengah: Menu Navigasi Desktop (Proporsional & Clean) -->
                <nav class="hidden lg:flex items-center gap-7 lg:gap-8 text-[13px]" aria-label="Navigasi Utama">
                    @foreach($navItems as $item)
                        @if(empty($item['children']))
                            <a href="{{ $item['url'] }}" 
                               data-nav-item
                               class="{{ $item['is_active'] ? $activeClass : $inactiveClass }}">
                                <span>{{ $item['title'] }}</span>
                            </a>
                        @else
                            <!-- Dropdown {{ $item['title'] }} -->
                            <div class="relative" @mouseenter="layananDropdown = true" @mouseleave="layananDropdown = false" @click.outside="layananDropdown = false">
                                <button class="{{ $item['is_active'] ? $activeClass : $inactiveClass }} cursor-pointer" 
                                        data-nav-item
                                        aria-haspopup="true" 
                                        :aria-expanded="layananDropdown ? 'true' : 'false'">
                                    <span>{{ $item['title'] }}</span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $item['is_active'] ? 'text-[#12271E]' : 'text-[#526057]/60' }}" 
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
                                     class="absolute left-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-[#E0E7DC] p-1.5 z-50 text-xs"
                                     style="display: none;">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ $child['url'] }}" 
                                           data-nav-item 
                                           class="block px-3.5 py-2.5 rounded-xl {{ $child['is_active'] ? 'text-[#1B3B2B] bg-[#EFF3EB] font-semibold' : 'text-[#526057] hover:text-[#12271E] hover:bg-[#F8FAF7]' }} font-medium transition-colors">
                                            {{ $child['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </nav>

                <!-- Kanan: Layanan Pelanggan + Auth Buttons + Tombol Mobile -->
                <div class="flex items-center gap-2.5 sm:gap-3.5">
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
                           class="hidden sm:inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold text-[#12271E] bg-[#EFF3EB] hover:bg-[#E0E7DC] active:scale-[0.97] border border-[#CCD8C7] transition-all focus-visible:ring-2 focus-visible:ring-[#1B3B2B] focus-visible:ring-offset-2">
                            <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="hidden sm:inline-flex items-center gap-1.5 px-4.5 py-2 rounded-full text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.97] transition-all shadow-xs focus-visible:ring-2 focus-visible:ring-[#1B3B2B] focus-visible:ring-offset-2">
                            <span>Daftar</span>
                        </a>
                    @endauth

                    <!-- Tombol Drawer Mobile -->
                    <button type="button" 
                            @click="mobileOpen = !mobileOpen"
                            class="inline-flex items-center justify-center lg:hidden w-10 h-10 rounded-xl bg-[#F4F6F2] text-[#12271E] hover:bg-[#EAEFE8] active:scale-95 border border-[#E0E7DC] transition-all cursor-pointer shrink-0 z-30 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B] focus:ring-offset-2"
                            :aria-expanded="mobileOpen ? 'true' : 'false'"
                            aria-controls="mobile-nav-drawer"
                            aria-label="Toggle menu navigasi">
                        <!-- Icon Hamburger -->
                        <svg x-show="!mobileOpen" class="w-5 h-5 shrink-0 text-[#12271E]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                        <!-- Icon X -->
                        <svg x-show="mobileOpen" class="w-5 h-5 shrink-0 text-[#12271E]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Drawer Mobile (Clean & Spacious) -->
    <div id="mobile-nav-drawer"
         x-show="mobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-white border-b border-[#E0E7DC] px-6 pt-4 pb-7 space-y-1.5 text-sm shadow-xl"
         style="display: none;">
        
        @foreach($navItems as $item)
            @if(empty($item['children']))
                <a href="{{ $item['url'] }}" 
                   data-nav-item 
                   @click="mobileOpen = false" 
                   class="block px-3 py-2.5 rounded-xl {{ $item['is_active'] ? 'text-[#1B3B2B] bg-[#EFF3EB] font-semibold' : 'text-[#526057] hover:text-[#12271E] hover:bg-[#F8FAF7]' }} transition-colors">
                    {{ $item['title'] }}
                </a>
            @else
                <!-- Submenu {{ $item['title'] }} Mobile -->
                <div class="py-2 border-t border-b border-[#E0E7DC]/60 my-2 space-y-1" 
                     x-data="{ mobileSubmenuOpen: {{ $item['is_active'] ? 'true' : 'false' }} }">
                    <button type="button" 
                            @click="mobileSubmenuOpen = !mobileSubmenuOpen" 
                            class="w-full flex items-center justify-between px-3 py-2 text-left cursor-pointer rounded-xl hover:bg-[#F8FAF7]">
                        <span class="text-[11px] font-bold text-[#526057] uppercase tracking-wider">{{ $item['title'] }}</span>
                        <svg class="w-3.5 h-3.5 text-[#526057]/60 transition-transform duration-200" 
                             :class="{ 'rotate-180': mobileSubmenuOpen }" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="mobileSubmenuOpen" x-collapse class="pl-3 space-y-1 pt-1">
                        @foreach($item['children'] as $child)
                            <a href="{{ $child['url'] }}" 
                               data-nav-item 
                               @click="mobileOpen = false" 
                               class="block px-3 py-2 rounded-lg {{ $child['is_active'] ? 'text-[#1B3B2B] font-semibold bg-[#EFF3EB]' : 'text-[#526057] hover:text-[#12271E]' }} text-xs transition-colors">
                                {{ $child['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        
        <div class="pt-4 space-y-2.5">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" 
                       class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-xs text-white bg-[#1B3B2B] hover:bg-[#132E22] text-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        <span>Panel Admin</span>
                    </a>
                @else
                    <a href="{{ route('jamaah.dashboard') }}" 
                       class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-xs text-white bg-[#1B3B2B] hover:bg-[#132E22] text-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <span>Dashboard Jamaah</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl font-medium text-xs text-[#526057] bg-white border border-[#E0E7DC] hover:text-[#12271E] text-center cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" 
                   class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-xs text-[#12271E] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] text-center transition-all">
                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    <span>Login Jamaah</span>
                </a>
                <a href="{{ route('register') }}" 
                   class="w-full flex items-center justify-center py-3 px-4 rounded-xl font-semibold text-xs text-white bg-[#1B3B2B] hover:bg-[#132E22] text-center shadow-xs transition-all">
                    Daftar Akun Baru
                </a>
            @endauth

            <a href="https://wa.me/{{ $phone }}?text=Assalamu%27alaikum%20PT.%20Zein%20Internasional,%20saya%20ingin%20mendaftar%20Umrah/Haji" 
               target="_blank"
               class="w-full flex items-center justify-center py-2.5 px-4 rounded-xl font-medium text-xs text-[#526057] hover:text-[#12271E] text-center">
                Bantuan WhatsApp
            </a>
        </div>
    </div>
</header>
