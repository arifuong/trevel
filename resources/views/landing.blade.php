<x-layouts.main :title="'PT. Zein Internasional — Pengalaman Ibadah Umrah yang Khusyuk & Terpercaya'" :company="$company">
    
    <!-- ════════════════════════════════════════
         1. HERO SECTION & 3 KOLOM FITUR SINGKAT (MENYATU TANPA JARAK)
         ════════════════════════════════════════ -->
    <x-hero :company="$company" />


    <!-- ════════════════════════════════════════
         2. TENTANG KAMI / PROFIL (Rasio 45:55 Dua Kolom)
         ════════════════════════════════════════ -->
    <section id="profil" class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Kolom Kiri (Teks, Paragraf, Tombol Kapsul, Direktur) -->
                <div data-reveal class="lg:col-span-6 space-y-6">
                    
                    <!-- Label Atas -->
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B]">
                            PROFIL PERUSAHAAN
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

                <!-- Kolom Kanan (Foto dengan Sudut Bulat Besar + Tombol Putar Video) -->
                <div data-reveal class="lg:col-span-6">
                    <div class="relative rounded-3xl overflow-hidden shadow-lg border border-[#E0E7DC] aspect-[4/3] sm:aspect-[16/11] bg-[#EFF3EB]">
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
                <h2 class="font-serif text-2xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight">
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
    <section id="legalitas" class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    LEGALITAS & SERTIFIKASI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Terdaftar Resmi di Kementerian Agama RI
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Keamanan dan kepastian keberangkatan Anda terjamin dengan izin operasional resmi dan akreditasi A.
                </p>
            </div>
            
            <div data-reveal-stagger="100" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($legalities as $legality)
                    <div data-reveal-child class="bg-white rounded-2xl p-6 sm:p-7 border border-[#E0E7DC] flex flex-col justify-between shadow-xs">
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
                <a href="{{ route('legalitas') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline">
                    Lihat Dokumen SK & Prinsip 5 Pasti Umrah &rarr;
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
    <section id="partnership" class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC] scroll-mt-28 lg:scroll-mt-36">
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

                    <div class="pt-2 bg-[#EFF3EB] rounded-2xl p-5 border border-[#E0E7DC]">
                        <p class="text-xs text-[#12271E] font-medium mb-3">Butuh panduan pengurusan paspor & dokumen?</p>
                        <a href="https://wa.me/{{ $company['whatsapp'] ?? '6281222222562' }}?text=Assalamu%27alaikum,%20mohon%20panduan%20dokumen%20pendaftaran%20Umrah" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-xs">
                            Konsultasi Dokumen
                        </a>
                    </div>
                </div>

                <!-- Kanan: Daftar Tahapan -->
                <div class="lg:col-span-8">
                    <x-stepper :steps="$registrationSteps" />
                </div>
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         6. SKEMA PERJALANAN (Udara & Darat)
         ════════════════════════════════════════ -->
    <section id="skema-perjalanan" class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC] scroll-mt-28 lg:scroll-mt-36">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    SKEMA TRANSPORTASI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Transportasi Umrah (Udara & Darat)
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Standar layanan transportasi lengkap mulai dari bandara keberangkatan dan kedatangan hingga armada di Tanah Suci.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <!-- TRANSPORTASI UDARA -->
                <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs flex flex-col justify-between">
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
                <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs flex flex-col justify-between">
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
    <section id="layanan-saudi" class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC] scroll-mt-28 lg:scroll-mt-36">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    PELAYANAN DI ARAB SAUDI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Pelayanan di Arab Saudi
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Standar pelayanan hotel, bimbingan ibadah & ziarah, serta destinasi tempat ziarah bersejarah yang dikunjungi di Madinah, Makkah, dan Jeddah.
                </p>
            </div>

            <!-- Baris 1: Pelayanan di Hotel & Ibadah dan Ziarah (2 Kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch mb-10">
                
                <!-- PELAYANAN DI HOTEL -->
                <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs flex flex-col justify-between">
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
                <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs flex flex-col justify-between">
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
            <div data-reveal class="bg-white rounded-3xl p-7 sm:p-10 border border-[#E0E7DC] shadow-xs">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-[#E0E7DC]">
                    <div class="w-11 h-11 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#12271E]">
                            TEMPAT-TEMPAT ZIARAH YANG DIKUNJUNGI
                        </h3>
                        <p class="text-xs text-[#526057]">
                            Destinasi napak tilas sejarah Islam selama berada di Tanah Suci
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- 1. Madinah -->
                    <div class="bg-[#EFF3EB] rounded-2xl p-5 sm:p-6 border border-[#E0E7DC]">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#CCD8C7]">
                            <h4 class="font-serif font-bold text-base text-[#12271E]">
                                Madinah
                            </h4>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-white px-2.5 py-1 rounded-full border border-[#CCD8C7]">
                                6 Tempat
                            </span>
                        </div>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-[#374151]">
                            @foreach($saudiServices['tempat_ziarah']['locations']['Madinah'] as $loc)
                                <li class="flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B] shrink-0"></span>
                                    <span class="font-medium text-[#12271E]">{{ $loc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- 2. Makkah -->
                    <div class="bg-[#EFF3EB] rounded-2xl p-5 sm:p-6 border border-[#E0E7DC]">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#CCD8C7]">
                            <h4 class="font-serif font-bold text-base text-[#12271E]">
                                Makkah
                            </h4>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-white px-2.5 py-1 rounded-full border border-[#CCD8C7]">
                                11 Tempat
                            </span>
                        </div>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-[#374151]">
                            @foreach($saudiServices['tempat_ziarah']['locations']['Makkah'] as $loc)
                                <li class="flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B] shrink-0"></span>
                                    <span class="font-medium text-[#12271E]">{{ $loc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- 3. Jeddah -->
                    <div class="bg-[#EFF3EB] rounded-2xl p-5 sm:p-6 border border-[#E0E7DC]">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#CCD8C7]">
                            <h4 class="font-serif font-bold text-base text-[#12271E]">
                                Jeddah
                            </h4>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-white px-2.5 py-1 rounded-full border border-[#CCD8C7]">
                                4 Tempat
                            </span>
                        </div>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-[#374151]">
                            @foreach($saudiServices['tempat_ziarah']['locations']['Jeddah'] as $loc)
                                <li class="flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B] shrink-0"></span>
                                    <span class="font-medium text-[#12271E]">{{ $loc }}</span>
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
    <section id="hak-jamaah" class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                <!-- Kiri: Info Sticky -->
                <div data-reveal class="lg:col-span-4 lg:sticky lg:top-36 space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        HAK & PERSYARATAN
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#12271E] tracking-tight leading-tight">
                        Persyaratan & Hak Jamaah
                    </h2>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Transparansi penuh: mulai syarat administrasi, kebijakan pembatalan, hak sebelum berangkat, selama di Saudi, hingga kepulangan.
                    </p>
                    <div class="text-xs text-[#4D5E54] pt-1">
                        <span class="font-bold text-[#12271E]">{{ count($pilgrimRights) }} topik</span> wajib diketahui sebelum pendaftaran.
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

            <!-- Grid Foto -->
            <div data-reveal-stagger="100" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($galleries as $item)
                    <x-gallery-item :item="$item" />
                @endforeach
            </div>

        </div>
    </section>


    <!-- ════════════════════════════════════════
         10. KONTAK & BANNER RESERVASI
         ════════════════════════════════════════ -->
    <section id="kontak" class="py-20 sm:py-28 bg-[#EFF3EB]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    HUBUNGI KAMI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Siap Mewujudkan Ibadah Anda?
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Kunjungi kantor kami atau hubungi konsultan kami untuk konsultasi jadwal dan estimasi biaya.
                </p>
            </div>

            <x-contact-card :company="$company" />

            <!-- Banner CTA Hijau -->
            <div data-reveal class="mt-16 bg-[#1B3B2B] rounded-3xl p-8 sm:p-14 text-center text-white shadow-xl">
                <div class="max-w-xl mx-auto space-y-4">
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
                           class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-xs sm:text-sm font-semibold text-[#1B3B2B] bg-white hover:bg-zinc-100 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            <span>Daftar Akun Online</span>
                        </a>
                        <a href="https://wa.me/{{ $company['whatsapp'] ?? '6281222222562' }}?text=Assalamu%27alaikum%20PT.%20Zein%20Internasional,%20saya%20ingin%20mendaftar%20Umrah/Haji" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center px-7 py-3.5 rounded-full text-xs sm:text-sm font-medium text-white bg-white/10 hover:bg-white/20 border border-white/30 transition-colors">
                            Daftar via WhatsApp
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</x-layouts.main>
