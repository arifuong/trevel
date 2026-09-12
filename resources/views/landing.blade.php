<x-layouts.main :title="'PT. Zein Internasional — Pengalaman Ibadah Umrah yang Khusyuk & Terpercaya'" :company="$company">
    
    <!-- ════════════════════════════════════════
         1. HERO SECTION 
         ════════════════════════════════════════ -->
    <x-hero :company="$company" />


    <!-- ════════════════════════════════════════
         2. TENTANG KAMI 
         ════════════════════════════════════════ -->
    <section id="profil" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Kolom Kiri (Teks, Paragraf, Tombol Kapsul, Direktur) -->
                <div data-reveal class="lg:col-span-6 space-y-6">
                    
                    <!-- Label Atas -->
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B]">
                        </span>
                    </div>

                    <!-- Headline Serif Dua Baris -->
                    <h2 class="font-serif text-2xl sm:text-4xl lg:text-[2.6rem] font-bold text-[#12271E] tracking-tight leading-[1.2]">
                        PT. ZEIN INTERNASIONAL<br class="hidden sm:inline"> (Zeintour)
                    </h2>

                    <!-- Paragraf 1 (Teks Utama) -->
                    <p class="text-xs sm:text-sm text-[#12271E] font-medium leading-relaxed">
                        PT. ZEIN INTERNASIONAL (Zeintour) adalah salah satu perusahaan penyelenggara perjalanan Ibadah Umrah yang didirikan pada 19 Oktober 2012 di Bandung oleh H. Zenal Abidin, dengan motivasi membangun dua kebaikan (kebaikan dunia dan akhirat).
                    </p>

                    <!-- Paragraf 2 (Deskripsi Profil) -->
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Berdasarkan motivasi tersebut, Zeintour bertekad untuk menjadi pelayan para tamu Allah SWT, memberikan kemudahan atas segala hal yang berkaitan dengan proses pelaksanaan ibadahnya, mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, sampai kembali ke tanah air dengan ikhlas dan tawakal kepadaNya, agar semua rangkaian ibadahnya diterima Allah SWT.
                    </p>

                    <!-- Baris Aksi: Tombol Kapsul Hijau + Widget Profil Pendiri -->
                    <div class="pt-3 flex flex-wrap items-center gap-6">
                        
                        <!-- Tombol Kapsul Hijau dengan Ikon Panah Bulat -->
                        <a href="{{ route('profil') }}" 
                           class="inline-flex items-center gap-3 pl-6 pr-2.5 py-2.5 rounded-full text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-xs group">
                            <span>Profil Lengkap</span>
                            <div class="w-7 h-7 rounded-full bg-white/20 group-hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </div>
                        </a>

                        <!-- Widget Profil Pimpinan -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center font-serif font-bold text-sm ring-2 ring-[#E0E7DC]">
                                ZA
                            </div>
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-[#4D5E54] font-bold block leading-none mb-0.5">Pimpinan Perusahaan</span>
                                <span class="font-serif text-sm font-bold text-[#12271E] tracking-wide block leading-none italic">H. ZENAL ABIDIN, M.Si</span>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Kolom Kanan (Video YouTube Embed Responsif 16:9 / Fallback) -->
                <div data-reveal class="lg:col-span-6">
                    @php
                        $youtubeEmbedUrl = $company['youtube_embed_url'] ?? null;
                        $videoTitle = $company['video_title'] ?? 'Profil PT. Zein Internasional';
                    @endphp

                    @if(!empty($youtubeEmbedUrl))
                        <div class="relative rounded-3xl overflow-hidden shadow-xl border border-[#E0E7DC] aspect-video bg-[#12271E]"
                             x-data="{ isPlaying: false }">
                            <template x-if="!isPlaying">
                                <div class="relative w-full h-full group cursor-pointer"
                                     @click="isPlaying = true">
                                    @php
                                        $youtubeVideoId = $company['youtube_video_id'] ?? null;
                                        $thumbnail = $youtubeVideoId 
                                            ? "https://img.youtube.com/vi/{$youtubeVideoId}/hqdefault.jpg" 
                                            : ($company['hero_image_url'] ?? 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=1280&q=75');
                                    @endphp
                                    <img src="{{ $thumbnail }}" 
                                         alt="{{ $videoTitle }}" 
                                         width="640" 
                                         height="360" 
                                         loading="lazy" 
                                         decoding="async" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                    
                                    <!-- Subtle dark overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-black/20 group-hover:from-black/50 transition-colors"></div>

                                    <!-- Video Title Badge at Top -->
                                    <div class="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold text-white bg-black/60 backdrop-blur-sm border border-white/20">
                                            <svg class="w-3.5 h-3.5 text-black-500 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            <span>Video Profil</span>
                                        </span>
                                    </div>

                                    <!-- YouTube Play Button in Center -->
                                    <div class="absolute inset-0 flex items-center justify-center z-10">
                                        <button type="button" 
                                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-black-600/90 text-white flex items-center justify-center shadow-2xl group-hover:scale-110 group-hover:bg-red-600 active:scale-95 transition-all duration-300 border-2 border-white/60 backdrop-blur-xs focus:outline-none cursor-pointer"
                                                aria-label="Putar video {{ $videoTitle }}">
                                            <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current ml-1" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Bottom duration/hint label -->
                                    <div class="absolute bottom-4 inset-x-4 flex items-center justify-center pointer-events-none z-10">
                                        <span class="text-xs font-semibold text-white/90 bg-black/50 px-3 py-1 rounded-full backdrop-blur-xs">
                                            Klik untuk Memutar Video
                                        </span>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="isPlaying">
                                <iframe
                                    src="{{ $youtubeEmbedUrl }}{{ str_contains($youtubeEmbedUrl, '?') ? '&' : '?' }}autoplay=1&rel=0"
                                    title="{{ $videoTitle }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                    class="w-full h-full rounded-3xl"
                                ></iframe>
                            </template>
                        </div>
                    @else
                        <!-- Fallback: Placeholder Gambar jika link YouTube belum diisi -->
                        <div class="relative rounded-3xl overflow-hidden shadow-lg border border-[#E0E7DC] aspect-video bg-[#EFF3EB]">
                            <img src="https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?auto=format&fm=webp&fit=crop&w=600&q=75" 
                                 alt="Pelataran Masjid Nabawi Madinah" 
                                 width="600"
                                 height="450"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover">
                            
                            <!-- Lapisan Gelap -->
                            <div class="absolute inset-0 bg-black/15"></div>

                            <!-- Tombol Putar Video Bulat -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <button type="button"
                                        onclick="openLightbox('https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?auto=format&fm=webp&fit=crop&w=1200&q=85', 'Dokumentasi Manasik & Perjalanan Jamaah PT. Zein Internasional')"
                                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-[#1B3B2B]/90 text-white flex items-center justify-center shadow-2xl hover:scale-105 transition-transform duration-200 cursor-pointer border-2 border-white/40 backdrop-blur-xs"
                                        aria-label="Putar video dokumentasi">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 fill-current ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         3. SECTION 1 — PAKET PILIHAN (Jelajahi Paket Pilihan Kami)
         ════════════════════════════════════════ -->
    <section id="paket" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Judul Section Rata Tengah -->
            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    PAKET PILIHAN
                </span>
                <h2 class="font-sans text-2xl sm:text-4xl lg:text-5xl font-extrabold text-[#12271E] tracking-tight">
                    Jelajahi Paket Pilihan Kami
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Pilihan paket perjalanan ibadah Umrah dan Haji Khusus dan bimbingan Sunnah.
                </p>
            </div>

            <!-- Grid 3 Kolom Kartu Paket -->
            <div data-reveal-stagger="100" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($packages as $package)
                    <x-package-card :package="$package" :company="$company" />
                @endforeach
            </div>

            <!-- Strip Tautan di Bawah Paket -->
            <div data-reveal class="mt-14 text-center">
                <p class="text-xs sm:text-sm text-[#526057]">
                    Pengalaman Umrah yang Aman dan Terpercaya. 
                    <a href="{{ route('paket') }}" class="text-[#1B3B2B] font-bold underline ml-1 hover:text-[#132E22]">Amankan Kursi Anda Sekarang &rarr;</a>
                </p>
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         4. SECTION 2 — LEGALITAS & SERTIFIKASI
         ════════════════════════════════════════ -->
    <section id="legalitas" class="relative py-20 sm:py-28 fixed-bg-section overflow-hidden" style="background-image: url('{{ asset('images/hero-1920.webp') }}');">
        <!-- Universal Parallax Background Layer (Always visible, hardware-accelerated, no-repeat) -->
        <div class="parallax-bg-layer" aria-hidden="true">
            <div class="parallax-bg-img" style="background-image: url('{{ asset('images/hero-1920.webp') }}');"></div>
        </div>

        <!-- Overlay Tipis Transparan Alami -->
        <div class="absolute -top-[2px] -bottom-[2px] inset-x-0 bg-gradient-to-b from-[#06140D]/45 via-[#06140D]/35 to-[#06140D]/45 pointer-events-none z-1" aria-hidden="true"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#E8C882] block mb-2 drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    LEGALITAS & SERTIFIKASI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-white tracking-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                    Terdaftar Resmi di Kementerian Agama RI
                </h2>
                <p class="text-xs sm:text-sm text-white font-medium mt-3 leading-relaxed max-w-xl mx-auto drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    Keamanan dan kepastian keberangkatan Anda terjamin dengan izin operasional resmi dan akreditasi A.
                </p>
            </div>
            
            <div data-reveal-stagger="100" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($legalities as $legality)
                    <div data-reveal-child class="bg-white/95 backdrop-blur-xs rounded-2xl p-6 sm:p-7 border border-white/40 flex flex-col justify-between shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-[#1B3B2B] bg-[#EAF1E8] px-2.5 py-1 rounded-md inline-block mb-3.5">
                                {{ $legality['badge'] }}
                            </span>
                            <h3 class="font-serif text-base font-bold text-[#12271E] mb-1">{{ $legality['title'] }}</h3>
                            <p class="text-xs font-mono font-bold text-[#1B3B2B] mb-2.5">{{ $legality['number'] }}</p>
                            <p class="text-xs text-[#526057] leading-relaxed">{{ $legality['description'] }}</p>
                        </div>
                        <div class="pt-3.5 mt-3.5 border-t border-[#E0E7DC] flex items-center justify-between text-xs">
                            <span class="text-[#1B3B2B] font-bold">Terverifikasi</span>
                            <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('legalitas') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-white bg-black/30 hover:bg-black/45 border border-white/40 backdrop-blur-xs px-5 py-2.5 rounded-full transition-all duration-200 shadow-lg hover:scale-102">
                    <span>Lihat Dokumen SK & Prinsip 5 Pasti Umrah</span>
                    <span>&rarr;</span>
                </a>
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         SECTION — AUTHORIZED / SERTIFIKASI & OTORISASI
         ════════════════════════════════════════ -->
    <section id="authorized" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC] scroll-mt-28 lg:scroll-mt-36">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    AUTHORIZED / SERTIFIKASI &amp; OTORISASI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Tersertifikasi &amp; Terotorisasi Resmi
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Perusahaan kami sudah tersertifikasi dan diotorisasi oleh lembaga-lembaga resmi berikut.
                </p>
            </div>

            <div data-reveal-stagger="80" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 sm:gap-6 max-w-6xl mx-auto">
                @foreach($authorized as $auth)
                    <div data-reveal-child class="group bg-white rounded-2xl p-5 sm:p-6 border border-[#E0E7DC] hover:border-[#1B3B2B]/40 hover:shadow-md transition-all duration-300 flex flex-col justify-between items-center text-center shadow-xs">
                        <div class="w-full flex flex-col items-center">
                            <div class="h-16 sm:h-20 w-full flex items-center justify-center mb-3.5">
                                <img src="{{ asset($auth['logo']) }}" 
                                     alt="{{ $auth['name'] }}" 
                                     width="160"
                                     height="80"
                                     loading="lazy"
                                     decoding="async"
                                     class="max-h-full max-w-full object-contain filter grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                            </div>
                            <div class="min-h-[2.5rem] flex items-center justify-center w-full">
                                <h3 class="font-serif font-bold text-xs sm:text-sm text-[#12271E] group-hover:text-[#1B3B2B] transition-colors leading-snug">
                                    {{ $auth['name'] }}
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         SECTION — PARTNERSHIP (MITRA KERJA SAMA)
         ════════════════════════════════════════ -->
    <section id="partnership" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC] scroll-mt-28 lg:scroll-mt-36">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    PARTNERSHIP
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Mitra Kerja Sama Kami
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Kami bekerja sama dengan yang terbaik. Kami bangga dengan mitra kami, yang bekerja bersama untuk memberikan layanan yang terbaik.
                </p>
            </div>

            <!-- Group 1: Hotel / Akomodasi -->
            <div data-reveal class="mb-14">
                <div class="flex items-center gap-3 mb-6 pb-2.5 border-b border-[#CCD8C7]">
                    <h3 class="text-xs sm:text-sm font-serif font-bold text-[#12271E]">
                        {{ $partners['hotel']['title'] }}
                    </h3>
                    <span class="text-[10px] uppercase tracking-wider text-[#4D5E54] font-medium">
                        (4 Mitra Pilihan)
                    </span>
                </div>
                <div data-reveal-stagger="80" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($partners['hotel']['items'] as $item)
                        <div data-reveal-child class="group bg-white rounded-2xl p-5 sm:p-6 border border-[#E0E7DC] hover:border-[#1B3B2B]/40 hover:shadow-md transition-all duration-300 flex flex-col justify-between items-center text-center">
                            <div class="w-full flex flex-col items-center">
                                <div class="h-16 sm:h-20 w-full flex items-center justify-center mb-3.5">
                                    <img src="{{ asset($item['logo']) }}" 
                                         alt="{{ $item['name'] }}" 
                                         width="160"
                                         height="80"
                                         loading="lazy"
                                         decoding="async"
                                         class="max-h-full max-w-full object-contain filter grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                                </div>
                                <div class="min-h-[2.5rem] flex items-center justify-center w-full">
                                    <h4 class="font-serif font-bold text-xs sm:text-sm text-[#12271E] group-hover:text-[#1B3B2B] transition-colors leading-snug">
                                        {{ $item['name'] }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Group 2: Maskapai Penerbangan -->
            <div data-reveal class="mb-14">
                <div class="flex items-center gap-3 mb-6 pb-2.5 border-b border-[#CCD8C7]">
                    <h3 class="text-xs sm:text-sm font-serif font-bold text-[#12271E]">
                        {{ $partners['airlines']['title'] }}
                    </h3>
                    <span class="text-[10px] uppercase tracking-wider text-[#4D5E54] font-medium">
                        (5 Maskapai Internasional)
                    </span>
                </div>
                <div data-reveal-stagger="80" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
                    @foreach($partners['airlines']['items'] as $item)
                        <div data-reveal-child class="group bg-white rounded-2xl p-5 sm:p-6 border border-[#E0E7DC] hover:border-[#1B3B2B]/40 hover:shadow-md transition-all duration-300 flex flex-col justify-between items-center text-center">
                            <div class="w-full flex flex-col items-center">
                                <div class="h-16 sm:h-20 w-full flex items-center justify-center mb-3.5">
                                    <img src="{{ asset($item['logo']) }}" 
                                         alt="{{ $item['name'] }}" 
                                         width="160"
                                         height="80"
                                         loading="lazy"
                                         decoding="async"
                                         class="max-h-full max-w-full object-contain filter grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                                </div>
                                <div class="min-h-[2.5rem] flex items-center justify-center w-full">
                                    <h4 class="font-serif font-bold text-xs sm:text-sm text-[#12271E] group-hover:text-[#1B3B2B] transition-colors leading-snug">
                                        {{ $item['name'] }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Group 3: Bank -->
            <div data-reveal>
                <div class="flex items-center gap-3 mb-6 pb-2.5 border-b border-[#CCD8C7]">
                    <h3 class="text-xs sm:text-sm font-serif font-bold text-[#12271E]">
                        {{ $partners['bank']['title'] }}
                    </h3>
                    <span class="text-[10px] uppercase tracking-wider text-[#4D5E54] font-medium">
                        (3 Bank Mitra)
                    </span>
                </div>
                <div data-reveal-stagger="80" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 max-w-4xl mx-auto">
                    @foreach($partners['bank']['items'] as $item)
                        <div data-reveal-child class="group bg-white rounded-2xl p-5 sm:p-6 border border-[#E0E7DC] hover:border-[#1B3B2B]/40 hover:shadow-md transition-all duration-300 flex flex-col justify-between items-center text-center">
                            <div class="w-full flex flex-col items-center">
                                <div class="h-16 sm:h-20 w-full flex items-center justify-center mb-3.5">
                                    <img src="{{ asset($item['logo']) }}" 
                                         alt="{{ $item['name'] }}" 
                                         width="160"
                                         height="80"
                                         loading="lazy"
                                         decoding="async"
                                         class="max-h-full max-w-full object-contain filter grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                                </div>
                                <div class="min-h-[2.5rem] flex items-center justify-center w-full">
                                    <h4 class="font-serif font-bold text-xs sm:text-sm text-[#12271E] group-hover:text-[#1B3B2B] transition-colors leading-snug">
                                        {{ $item['name'] }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         5. ALUR PENDAFTARAN (Skema Pendaftaran)
         ════════════════════════════════════════ -->
    <section id="skema-pendaftaran" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC] scroll-mt-28 lg:scroll-mt-36">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                <!-- Kiri: Info Sticky -->
                <div data-reveal class="lg:col-span-4 lg:sticky lg:top-36 space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        ALUR PENDAFTARAN
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#12271E] tracking-tight leading-tight">
                        7 Tahap Menuju Tanah Suci
                    </h2>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Proses registrasi yang transparan dan terstruktur, didampingi konsultan resmi dari awal hingga kepulangan.
                    </p>
                </div>

                <!-- Kanan: Daftar Tahapan -->
                <div class="lg:col-span-8">
                    <x-stepper :steps="$registrationSteps" />
                </div>
            </div>

            <!-- Box Konsultasi Dokumen: [ICON] [JUDUL + DESKRIPSI] [TOMBOL] -->
            <div data-reveal class="mt-12 sm:mt-16">
                <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-[#F6FAF7] border border-[#DCE7DF] p-5 sm:p-7 md:p-8 hover:border-[#1B3B2B]/30 hover:shadow-md transition-all duration-300 group">
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5 sm:gap-6 lg:gap-8">
                        
                        <!-- Kiri: Icon Dokumen / Konsultasi -->
                        <div class="shrink-0 flex items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white border border-[#DCE7DF] text-[#1B3B2B] flex items-center justify-center shadow-xs group-hover:scale-105 group-hover:bg-[#1B3B2B] group-hover:text-white transition-all duration-300">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 9h5.25m-5.25 3h3m-6.75-15h6.75l4.5 4.5v12.75a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V5.25A2.25 2.25 0 016 3z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Tengah/Kiri: Teks (Judul di atas, Deskripsi tepat di bawahnya) -->
                        <div class="flex-1 space-y-1 text-left">
                            <h3 class="font-serif text-base sm:text-lg lg:text-xl font-bold text-[#12271E] tracking-tight">
                                Butuh panduan pengurusan paspor & dokumen?
                            </h3>
                            <p class="text-xs sm:text-sm text-[#526057] leading-relaxed max-w-2xl">
                                Konsultan resmi Zeintour siap mendampingi verifikasi berkas, pembuatan paspor baru, hingga suntik meningitis agar ibadah Anda tenang dan lancar.
                            </p>
                        </div>

                        <!-- Kanan: Tombol Konsultasi Dokumen -->
                        <div class="shrink-0 w-full sm:w-auto">
                            <a href="https://wa.me/{{ $company['whatsapp'] ?? '6281222222562' }}?text=Assalamu%27alaikum,%20mohon%20panduan%20dokumen%20pendaftaran%20Umrah" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all duration-200 shadow-xs hover:shadow-md group/btn">
                                <span>Konsultasi Dokumen</span>
                                <svg class="w-4 h-4 text-[#C2A264] transition-transform duration-200 group-hover/btn:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════
         CONTINUOUS FIXED BACKGROUND GROUP:
         6. SKEMA PERJALANAN + 7. LAYANAN SAUDI + 8. PERSYARATAN & HAK
         Single continuous background & overlay — 100% seamless, zero lines
         ═══════════════════════════════════════════════════════════════ -->
    <div class="relative fixed-bg-section overflow-hidden" style="background-image: url('{{ asset('images/hero-1920.webp') }}');">
        <!-- Universal Parallax Background Layer (Always visible, hardware-accelerated, no-repeat) -->
        <div class="parallax-bg-layer" aria-hidden="true">
            <div class="parallax-bg-img" style="background-image: url('{{ asset('images/hero-1920.webp') }}');"></div>
        </div>

        <!-- Single Unified Dark Overlay across all 3 sections -->
        <div class="absolute inset-0 bg-[#06140D]/40 pointer-events-none z-1" aria-hidden="true"></div>

        <!-- ════════════════════════════════════════
             6. SKEMA PERJALANAN (Udara & Darat)
             ════════════════════════════════════════ -->
        <section id="skema-perjalanan" class="relative z-10 py-20 sm:py-28 scroll-mt-28 lg:scroll-mt-36">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#E8C882] block mb-2 drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    SKEMA TRANSPORTASI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-white tracking-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                    Transportasi Umrah (Udara & Darat)
                </h2>
                <p class="text-xs sm:text-sm text-white font-medium mt-3 leading-relaxed max-w-xl mx-auto drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    Standar layanan transportasi lengkap mulai dari bandara keberangkatan dan kedatangan hingga armada di Tanah Suci.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <!-- TRANSPORTASI UDARA -->
                <div data-reveal class="bg-white/95 backdrop-blur-xs rounded-3xl p-7 sm:p-9 border border-white/40 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                            </div>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#12271E]">
                                    TRANSPORTASI UDARA
                                </h3>
                            </div>
                        </div>

                        <div class="mb-5 pb-3 border-b border-[#E0E7DC]">
                            <h4 class="font-serif font-bold text-sm text-[#1B3B2B]">
                                Di Bandara Keberangkatan dan Kedatangan
                            </h4>
                        </div>

                        <ul class="space-y-3.5 text-xs sm:text-sm text-[#374151]">
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Jamaah didampingi oleh tour leader dari Indonesia.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Jamaah dijemput oleh petugas handling di bandara Jeddah atau Madinah.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Bagasi diangkut oleh porter yang telah disiapkan oleh petugas handling.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Jamaah diberikan makanan berupa catering/snack.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- TRANSPORTASI DARAT -->
                <div data-reveal class="bg-white/95 backdrop-blur-xs rounded-3xl p-7 sm:p-9 border border-white/40 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                            </div>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#12271E]">
                                    TRANSPORTASI DARAT
                                </h3>
                            </div>
                        </div>

                        <div class="mb-5 pb-3 border-b border-[#E0E7DC]">
                            <h4 class="font-serif font-bold text-sm text-[#1B3B2B]">
                                Bus Syariah Kinglong & Mercedes Benz (49 Seat)
                            </h4>
                        </div>

                        <ul class="space-y-3.5 text-xs sm:text-sm text-[#374151]">
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Disediakan Bus Syariah jenis <strong>Kinglong & Mercedes Benz</strong> dengan kapasitas <strong>49 seat</strong>, full AC dan memiliki toilet di dalamnya untuk mengantar jamaah ke hotel, tempat ibadah dan tempat-tempat ziarah.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Disediakan snack pada setiap perjalanan.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                <span class="leading-relaxed">Didampingi Muthawif / Guide yang berpengalaman.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         7. SECTION 3 — PELAYANAN DI ARAB SAUDI
         ════════════════════════════════════════ -->
    <section id="layanan-saudi" class="relative z-10 py-20 sm:py-28 scroll-mt-28 lg:scroll-mt-36">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#E8C882] block mb-2 drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    PELAYANAN DI ARAB SAUDI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-white tracking-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                    Pelayanan di Arab Saudi
                </h2>
                <p class="text-xs sm:text-sm text-white font-medium mt-3 leading-relaxed max-w-xl mx-auto drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                    Standar pelayanan hotel, bimbingan ibadah & ziarah, serta destinasi tempat ziarah bersejarah yang dikunjungi di Madinah, Makkah, dan Jeddah.
                </p>
            </div>

            <!-- Baris 1: Pelayanan di Hotel & Ibadah dan Ziarah (2 Kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch mb-10">
                
                <!-- PELAYANAN DI HOTEL -->
                <div data-reveal class="bg-white/95 backdrop-blur-xs rounded-3xl p-7 sm:p-9 border border-white/40 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                            </div>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#12271E]">
                                    PELAYANAN DI HOTEL
                                </h3>
                            </div>
                        </div>

                        <div class="mb-5 pb-3 border-b border-[#E0E7DC]">
                            <h4 class="font-serif font-bold text-sm text-[#1B3B2B]">
                                Kenyamanan & Fasilitas Akomodasi
                            </h4>
                        </div>

                        <ul class="space-y-3.5 text-xs sm:text-sm text-[#374151]">
                            @foreach($saudiServices['hotel']['points'] as $pt)
                                <li class="flex items-start gap-3">
                                    <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                    <span class="leading-relaxed">{{ $pt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- IBADAH DAN ZIARAH -->
                <div data-reveal class="bg-white/95 backdrop-blur-xs rounded-3xl p-7 sm:p-9 border border-white/40 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            </div>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#12271E]">
                                    IBADAH DAN ZIARAH
                                </h3>
                            </div>
                        </div>

                        <div class="mb-5 pb-3 border-b border-[#E0E7DC]">
                            <h4 class="font-serif font-bold text-sm text-[#1B3B2B]">
                                Bimbingan & Pendampingan Ibadah
                            </h4>
                        </div>

                        <ul class="space-y-3.5 text-xs sm:text-sm text-[#374151]">
                            @foreach($saudiServices['ibadah_ziarah']['points'] as $pt)
                                <li class="flex items-start gap-3">
                                    <span class="w-2 h-2 rounded-full bg-[#1B3B2B] mt-1.5 shrink-0"></span>
                                    <span class="leading-relaxed">{{ $pt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Baris 2: TEMPAT-TEMPAT ZIARAH YANG DIKUNJUNGI -->
            <div data-reveal class="bg-white/95 backdrop-blur-xs rounded-[22px] sm:rounded-3xl p-6 sm:p-9 lg:p-10 border border-white/40 shadow-xl">
                
                {{-- HEADER SECTION --}}
                <div class="flex items-start sm:items-center gap-3.5 sm:gap-4 mb-7 sm:mb-9 pb-5 sm:pb-6 border-b border-[#E8ECE5]">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-[#F4F8F5] border border-[#DCE7DF] text-[#1B3B2B] flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-lg sm:text-2xl font-bold text-[#12271E] tracking-tight">
                            TEMPAT-TEMPAT ZIARAH YANG DIKUNJUNGI
                        </h3>
                        <p class="text-xs sm:text-sm text-[#526057] mt-0.5 leading-relaxed">
                            Destinasi napak tilas sejarah Islam selama berada di Tanah Suci
                        </p>
                    </div>
                </div>

                {{-- CARD MADINAH, MAKKAH, JEDDAH --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-7 items-stretch">
                    
                    {{-- 1. Madinah --}}
                    <div class="group bg-white rounded-2xl p-6 sm:p-7 border border-[#E5E9E2] shadow-[0_2px_12px_-2px_rgba(27,59,43,0.04)] hover:border-emerald-600/40 hover:shadow-[0_10px_24px_-4px_rgba(27,59,43,0.08)] hover:-translate-y-1 transition-all duration-250 flex flex-col h-full">
                        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-[#E8ECE5]">
                            <h4 class="font-serif font-bold text-xl sm:text-2xl text-[#12271E] group-hover:text-[#1B3B2B] transition-colors tracking-tight">
                                Madinah
                            </h4>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-[#F4F8F5] border border-[#DCE7DF] px-2.5 py-1 rounded-full shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                6 Tempat
                            </span>
                        </div>
                        <ul class="space-y-3 text-xs sm:text-sm text-[#374151] flex-1">
                            @foreach($saudiServices['tempat_ziarah']['locations']['Madinah'] as $loc)
                                <li class="flex items-center gap-2.5 group/item">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B] shrink-0 group-hover/item:scale-125 group-hover/item:bg-emerald-600 transition-all"></span>
                                    <span class="font-medium text-[#12271E] leading-relaxed">{{ $loc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- 2. Makkah --}}
                    <div class="group bg-white rounded-2xl p-6 sm:p-7 border border-[#E5E9E2] shadow-[0_2px_12px_-2px_rgba(27,59,43,0.04)] hover:border-emerald-600/40 hover:shadow-[0_10px_24px_-4px_rgba(27,59,43,0.08)] hover:-translate-y-1 transition-all duration-250 flex flex-col h-full">
                        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-[#E8ECE5]">
                            <h4 class="font-serif font-bold text-xl sm:text-2xl text-[#12271E] group-hover:text-[#1B3B2B] transition-colors tracking-tight">
                                Makkah
                            </h4>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-[#F4F8F5] border border-[#DCE7DF] px-2.5 py-1 rounded-full shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                11 Tempat
                            </span>
                        </div>
                        <ul class="space-y-3 text-xs sm:text-sm text-[#374151] flex-1">
                            @foreach($saudiServices['tempat_ziarah']['locations']['Makkah'] as $loc)
                                <li class="flex items-center gap-2.5 group/item">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B] shrink-0 group-hover/item:scale-125 group-hover/item:bg-emerald-600 transition-all"></span>
                                    <span class="font-medium text-[#12271E] leading-relaxed">{{ $loc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- 3. Jeddah --}}
                    <div class="group bg-white rounded-2xl p-6 sm:p-7 border border-[#E5E9E2] shadow-[0_2px_12px_-2px_rgba(27,59,43,0.04)] hover:border-emerald-600/40 hover:shadow-[0_10px_24px_-4px_rgba(27,59,43,0.08)] hover:-translate-y-1 transition-all duration-250 flex flex-col h-full">
                        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-[#E8ECE5]">
                            <h4 class="font-serif font-bold text-xl sm:text-2xl text-[#12271E] group-hover:text-[#1B3B2B] transition-colors tracking-tight">
                                Jeddah
                            </h4>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-[#F4F8F5] border border-[#DCE7DF] px-2.5 py-1 rounded-full shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                4 Tempat
                            </span>
                        </div>
                        <ul class="space-y-3 text-xs sm:text-sm text-[#374151] flex-1">
                            @foreach($saudiServices['tempat_ziarah']['locations']['Jeddah'] as $loc)
                                <li class="flex items-center gap-2.5 group/item">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B] shrink-0 group-hover/item:scale-125 group-hover/item:bg-emerald-600 transition-all"></span>
                                    <span class="font-medium text-[#12271E] leading-relaxed">{{ $loc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         8. SECTION 4 — PERSYARATAN & HAK JAMAAH
         ════════════════════════════════════════ -->
    <section id="hak-jamaah" class="relative z-10 py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                <!-- Kiri: Info Sticky -->
                <div data-reveal class="lg:col-span-4 lg:sticky lg:top-36 space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#E8C882] block drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                        HAK & PERSYARATAN
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-white tracking-tight leading-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                        Persyaratan & Hak Jamaah
                    </h2>
                    <p class="text-xs sm:text-sm text-white font-medium leading-relaxed drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                        Transparansi penuh: mulai syarat administrasi, kebijakan pembatalan, hak sebelum berangkat, selama di Saudi, hingga kepulangan.
                    </p>
                    <div class="inline-flex items-center gap-2 text-xs text-white bg-black/30 px-3.5 py-1.5 rounded-lg border border-white/30 backdrop-blur-xs shadow-md">
                        <span class="font-bold text-[#E8C882]">{{ count($pilgrimRights) }} topik</span>
                        <span>wajib diketahui sebelum pendaftaran.</span>
                    </div>
                </div>

                <!-- Kanan: Accordion (6 Kategori) -->
                <div class="lg:col-span-8 space-y-3.5" x-data="{ activeIndex: 0 }">
                    @foreach($pilgrimRights as $index => $item)
                        <x-accordion :title="$item['title']" :index="$index">
                            {!! $item['content'] !!}
                        </x-accordion>
                    @endforeach
                </div>

            </div>

        </div>
    </section>
    </div>


    <!-- ════════════════════════════════════════
         9. SECTION 5 — DOKUMENTASI PERJALANAN (Galeri)
         ════════════════════════════════════════ -->
    <section id="galeri" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
                <div class="max-w-xl">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                        DOKUMENTASI PERJALANAN
                    </span>
                    <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                        Galeri Perjalanan Jamaah
                    </h2>
                    <p class="text-xs sm:text-sm text-[#4D5E54] mt-2 leading-relaxed">
                        Momen khidmat dan kebersamaan jamaah PT. Zein Internasional di Makkah dan Madinah.
                    </p>
                </div>
                <a href="{{ route('galeri') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline shrink-0">
                    Lihat Galeri Lengkap &rarr;
                </a>
            </div>

            <!-- Grid Foto dengan Tinggi Kartu Seragam (Equal Height) -->
            <div data-reveal-stagger="100" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch mb-12">
                @foreach($galleries as $item)
                    <div class="h-full">
                        <x-gallery-item :item="$item" />
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         10. KONTAK & BANNER RESERVASI
         ════════════════════════════════════════ -->
    <section id="kontak" class="py-20 sm:py-28 bg-white border-t border-[#E5E7EB]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Siap Mewujudkan Ibadah Anda?
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Kunjungi kantor kami atau hubungi konsultan kami untuk konsultasi jadwal dan estimasi biaya.
                </p>
            </div>

            <x-contact-card :company="$company" />

            <!-- Banner CTA Hijau -->
            <div data-reveal class="mt-16 bg-[#1B3B2B] rounded-3xl p-8 sm:p-14 text-center text-white shadow-xl relative overflow-hidden border border-[#1B3B2B]">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-[#E8C882]/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative max-w-xl mx-auto space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-emerald-300 block">
                    </span>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white tracking-tight">
                        Wujudkan Rencana Ibadah Umrah & Haji Anda
                    </h3>
                    <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed">
                        Jadwal keberangkatan telah dibuka. Dapatkan kepastian seat, penerbangan langsung, dan hotel pelataran.
                    </p>
                    <div class="pt-3 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-xs sm:text-sm font-semibold text-[#1B3B2B] bg-white hover:bg-zinc-100 transition-all duration-200 shadow-md hover:shadow-lg cursor-pointer">
                            <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            <span>Daftar Akun Online</span>
                        </a>
                        <a href="https://wa.me/{{ $company['whatsapp'] ?? '6281222222562' }}?text=Assalamu%27alaikum%20PT.%20Zein%20Internasional,%20saya%20ingin%20mendaftar%20Umrah/Haji" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-xs sm:text-sm font-medium text-white bg-white/10 hover:bg-white/20 border border-white/30 transition-all duration-200 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4 fill-current text-emerald-300" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.274.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                            <span>Daftar via WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</x-layouts.main>
