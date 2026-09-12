<x-layouts.main :title="'Profil Perusahaan — PT. Zein Internasional (Zeintour)'" :company="$company">

    {{-- ═══════════════════════════════════════════════════════════════
         1. HEADER HALAMAN & PENGANTAR (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-[#E0E7DC] pt-8 pb-14 sm:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['title' => 'Profil Perusahaan']]" />

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center mt-6">
                <div data-reveal class="lg:col-span-7 space-y-5">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        PROFIL PERUSAHAAN
                    </span>
                    <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight leading-[1.2]">
                        PT. ZEIN INTERNASIONAL
                    </h1>
                    
                    <p class="text-xs sm:text-sm text-[#12271E] font-medium leading-relaxed">
                        {{ $company['about_summary'] ?? 'PT. ZEIN INTERNASIONAL (Zeintour) adalah salah satu perusahaan penyelenggara perjalanan Ibadah Umrah yang didirikan pada 19 Oktober 2012 di Bandung oleh H. Zenal Abidin, dengan motivasi membangun dua kebaikan (kebaikan dunia dan akhirat).' }}
                    </p>

                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        {{ $company['about_description'] ?? 'Berdasarkan motivasi tersebut, Zeintour bertekad untuk menjadi pelayan para tamu Allah SWT, memberikan kemudahan atas segala hal yang berkaitan dengan proses pelaksanaan ibadahnya, mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, sampai kembali ke tanah air dengan ikhlas dan tawakal kepadaNya, agar semua rangkaian ibadahnya diterima Allah SWT.' }}
                    </p>

                    <!-- Legalitas Izin Resmi -->
                    <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="bg-[#EFF3EB] rounded-2xl p-3.5 border border-[#E0E7DC]">
                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Penyelenggara Perjalanan Ibadah Umrah (PPIU)</span>
                            <span class="text-[#1B3B2B] font-mono font-bold text-xs mt-0.5 block">{{ $company['ppiu_number'] ?? $company['ppiu'] ?? 'IZIN KEMENAG RI NOMOR U.255 TAHUN 2020' }}</span>
                        </div>
                        <div class="bg-[#EFF3EB] rounded-2xl p-3.5 border border-[#E0E7DC]">
                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Penyelenggara Ibadah Haji Khusus (PIHK)</span>
                            <span class="text-[#1B3B2B] font-mono font-bold text-xs mt-0.5 block">{{ $company['pihk_number'] ?? $company['pihk'] ?? 'IZIN KEMENAG RI NOMOR 599 TAHUN 2021' }}</span>
                        </div>
                    </div>
                </div>

                <div data-reveal class="lg:col-span-5">
                    @php
                        $heroImg = !empty($profileHero?->thumbnail_url)
                            ? $profileHero->thumbnail_url
                            : ($company['hero_image_url'] ?? 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format,webp&fit=crop&w=800&q=75');
                        $heroTitle = $profileHero?->title ?? 'Gedung Kantor Pusat PT. Zein Internasional';
                    @endphp
                    <div class="rounded-3xl overflow-hidden border border-[#E0E7DC] shadow-md bg-[#EFF3EB] aspect-[4/3] sm:aspect-[16/11]">
                        <img src="{{ $heroImg }}" 
                             alt="{{ $heroTitle }}" 
                             width="800"
                             height="600"
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            {{-- Strip Statistik (Horizontal Row Rapi & Bersih) --}}
            <div data-reveal class="mt-14 pt-8 border-t border-[#E0E7DC]">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
                    @foreach($company['stats'] ?? [] as $stat)
                        <div class="flex items-baseline gap-2">
                            <span class="font-serif text-3xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                                {{ $stat['number'] }}{{ $stat['suffix'] ?? '' }}
                            </span>
                            <span class="text-xs text-[#4D5E54] leading-tight">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. VISI & MISI ZEINTOUR (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                
                {{-- Visi --}}
                <div data-reveal class="lg:col-span-5 bg-white rounded-3xl p-7 sm:p-9 shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_6px_25px_rgba(0,0,0,0.06)] transition-all duration-300 space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        VISI ZEINTOUR
                    </span>
                    <blockquote class="font-serif text-xl sm:text-2xl font-bold text-[#12271E] leading-snug italic">
                        "{{ $company['visi'] ?? 'Menjadi penyelenggara Haji dan Umrah dengan pelayanan terbaik berbasis Al Qur\'an di Indonesia.' }}"
                    </blockquote>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed pt-2">
                        Komitmen kami dalam membimbing ibadah para tamu Allah SWT berlandaskan nilai-nilai murni Al-Qur'an dan Sunnah Rasulullah ﷺ.
                    </p>
                </div>

                {{-- Misi --}}
                <div data-reveal class="lg:col-span-7 space-y-6">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                            MISI ZEINTOUR
                        </span>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#12271E] tracking-tight mt-1">
                            Misi Utama Pelayanan Ibadah
                        </h2>
                    </div>
                    
                    <div class="space-y-4">
                        @php
                            $misiList = $company['misi'] ?? [
                                [
                                    'num' => '01',
                                    'desc' => 'Membantu para calon Jemaah Umrah/Haji dalam pelaksanaan ibadahnya agar benar dan sempurna untuk mencapai ibadah yang mabrur.'
                                ],
                                [
                                    'num' => '02',
                                    'desc' => 'Mengembangkan perusahaan penyelenggara perjalanan ibadah Umrah/Haji yang baik, serta menjadi pembimbing ibadah yang siqah dengan pelayanan prima.'
                                ],
                                [
                                    'num' => '03',
                                    'desc' => "Mengembangkan Ukhuwah Islamiyah, Silaturrahim dan kerja sama untuk mencapai kehidupan yang rahmatan lil 'alamin."
                                ],
                            ];
                        @endphp

                        @foreach($misiList as $idx => $misi)
                            @php
                                $mNum = is_array($misi) && isset($misi['num']) ? $misi['num'] : sprintf('%02d', $idx + 1);
                                $mDesc = is_array($misi) ? ($misi['desc'] ?? '') : $misi;
                            @endphp
                            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_6px_25px_rgba(0,0,0,0.06)] transition-all duration-300 flex items-start gap-4">
                                <span class="font-serif text-2xl sm:text-3xl font-extrabold text-[#1B3B2B]/30 tracking-tighter leading-none shrink-0 pt-0.5" aria-hidden="true">
                                    {{ $mNum }}
                                </span>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm text-[#12271E] font-medium leading-relaxed">{{ $mDesc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         3. VIDEO PROFIL PERUSAHAAN (bg-[#122B1F])
    ═══════════════════════════════════════════════════════════════ --}}
    @php
        $videoEmbedUrl = $profileVideo?->embed_url ?? ($company['youtube_embed_url'] ?? null);
        $videoTitle = $profileVideo?->title ?? ($company['video_title'] ?? 'Profil PT. Zein Internasional');
        $videoCaption = $profileVideo?->caption ?? 'Saksikan komitmen dedikasi kami dalam melayani para tamu Allah menuju Baitullah dengan bimbingan ibadah yang mabrur.';
    @endphp
    @if(!empty($videoEmbedUrl))
    <section class="py-16 sm:py-24 bg-[#122B1F] text-white border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div data-reveal class="lg:col-span-5 space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-emerald-300 block">
                        VIDEO PROFIL RESMI
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-white tracking-tight leading-tight">
                        {{ $videoTitle }}
                    </h2>
                    <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed">
                        {{ $videoCaption }}
                    </p>
                    <div class="pt-2">
                        <a href="https://www.youtube.com/@ZEINTV7" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-300 hover:text-emerald-200 transition-colors">
                            <span>Kunjungi Saluran YouTube Resmi (@ZEINTV7)</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <div data-reveal class="lg:col-span-7">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/15 aspect-video bg-black"
                         x-data="{ isPlaying: false }">
                        <template x-if="!isPlaying">
                            <div class="relative w-full h-full group cursor-pointer"
                                 @click="isPlaying = true">
                                @php
                                    $videoThumb = $profileVideo?->thumbnail_url ?? (!empty($company['youtube_video_id']) ? "https://img.youtube.com/vi/{$company['youtube_video_id']}/hqdefault.jpg" : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=1280&q=75');
                                @endphp
                                <img src="{{ $videoThumb }}" 
                                     alt="{{ $videoTitle }}" 
                                     width="640" 
                                     height="360" 
                                     loading="lazy" 
                                     decoding="async" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-black/20 group-hover:from-black/50 transition-colors"></div>

                                <div class="absolute inset-0 flex items-center justify-center z-10">
                                    <button type="button" 
                                            class="w-16 h-16 sm:w-18 sm:h-18 rounded-full bg-red-600/90 text-white flex items-center justify-center shadow-2xl group-hover:scale-110 group-hover:bg-red-600 active:scale-95 transition-all duration-300 border-2 border-white/60 backdrop-blur-xs focus:outline-none cursor-pointer"
                                            aria-label="Putar video {{ $videoTitle }}">
                                        <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current ml-1" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="absolute bottom-4 inset-x-4 flex items-center justify-center pointer-events-none z-10">
                                    <span class="text-xs font-semibold text-white/90 bg-black/50 px-3.5 py-1 rounded-full backdrop-blur-xs border border-white/10">
                                        Klik untuk Memutar Video Profil
                                    </span>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="isPlaying">
                            <iframe
                                src="{{ $videoEmbedUrl }}{{ str_contains($videoEmbedUrl, '?') ? '&' : '?' }}autoplay=1&rel=0"
                                title="{{ $videoTitle }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                                class="w-full h-full rounded-3xl"
                            ></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         4. TUJUAN BERDIRINYA ZEINTOUR (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    TUJUAN PERUSAHAAN
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Tujuan Berdirinya Zeintour
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Landasan niat dan arah pengembangan PT. Zein Internasional.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                @php
                    $tujuanList = $company['tujuan'] ?? [
                        [
                            'num' => '01',
                            'title' => 'Dua Kebaikan',
                            'desc' => 'Mengelola usaha penyelenggara perjalanan ibadah yang berdimensi dua kebaikan.'
                        ],
                        [
                            'num' => '02',
                            'title' => 'Pendapatan Barokah',
                            'desc' => 'Menjadi salah satu sumber pendapatan yang barokah.'
                        ],
                        [
                            'num' => '03',
                            'title' => 'Pengembangan Usaha',
                            'desc' => 'Menjadi pintu masuk untuk mengembangkan berbagai usaha lain yang berkaitan.'
                        ],
                    ];
                @endphp

                @foreach($tujuanList as $idx => $tujuan)
                    @php
                        $tNum = is_array($tujuan) && isset($tujuan['num']) ? $tujuan['num'] : sprintf('%02d', $idx + 1);
                        $tDesc = is_array($tujuan) ? ($tujuan['desc'] ?? '') : $tujuan;
                    @endphp
                    <div data-reveal-child class="bg-[#EFF3EB] rounded-3xl p-6 sm:p-8 shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_6px_25px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <span class="font-serif text-4xl sm:text-5xl font-extrabold text-[#1B3B2B]/30 tracking-tighter leading-none block mb-4" aria-hidden="true">
                                {{ $tNum }}
                            </span>
                            <p class="text-xs sm:text-sm text-[#12271E] font-medium leading-relaxed">{{ $tDesc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         5. KEUNGGULAN ZEINTOUR (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    MENGAPA MEMILIH KAMI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Keunggulan Zeintour
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Standar keunggulan pelayanan yang kami persembahkan untuk kenyamanan ibadah Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $keunggulanList = [
                        [
                            'icon' => 'currency',
                            'title' => 'Harga Paket Terjangkau',
                            'desc' => 'Harga paket relatif lebih murah dengan pelayanan terbaik.'
                        ],
                        [
                            'icon' => 'sliders',
                            'title' => 'Pilihan Paket Fleksibel',
                            'desc' => 'Disediakan pilihan paket sesuai dengan kemampuan/kebutuhan jamaah.'
                        ],
                        [
                            'icon' => 'kaaba',
                            'title' => 'Fasilitas Umrah Sunnah',
                            'desc' => 'Memfasilitasi jamaah untuk melakukan Umrah sunnah.'
                        ],
                        [
                            'icon' => 'bolt',
                            'title' => 'Fast Track Imigrasi',
                            'desc' => 'Layanan cepat imigrasi di bandara Soekarno Hatta.'
                        ],
                        [
                            'icon' => 'lounge',
                            'title' => 'Lounge Bandara',
                            'desc' => 'Di bandara disediakan lounge umrah.'
                        ],
                        [
                            'icon' => 'guide',
                            'title' => 'Pembimbing Profesional',
                            'desc' => 'Pembimbing yang profesional di bidangnya.'
                        ],
                    ];
                @endphp

                @foreach($keunggulanList as $keunggulan)
                    <div data-reveal-child class="bg-white rounded-3xl p-6 sm:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_6px_25px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center mb-4">
                                @if($keunggulan['icon'] === 'currency')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @elseif($keunggulan['icon'] === 'sliders')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                                @elseif($keunggulan['icon'] === 'kaaba')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 0c-3 0-5 2.2-5 5.5V21h10V9.5C17 6.2 15 4 12 4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M10 21v-4a2 2 0 0 1 4 0v4" /><path stroke-linecap="round" stroke-linejoin="round" d="M4 12v9m16-9v9M2 21h20" /></svg>
                                @elseif($keunggulan['icon'] === 'bolt')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                                @elseif($keunggulan['icon'] === 'lounge')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693l-1.57-.393m15.6 0a4.5 4.5 0 01-.2 1.408M4.2 15.3a4.5 4.5 0 00-.2 1.408M4 16.708V20.25a.75.75 0 00.75.75h14.5a.75.75 0 00.75-.75v-3.542" /></svg>
                                @elseif($keunggulan['icon'] === 'guide')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                @endif
                            </div>
                            <h3 class="font-serif text-base font-bold text-[#12271E] mb-2">{{ $keunggulan['title'] }}</h3>
                            <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">{{ $keunggulan['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         6. BANNER AJAKAN KONSULTASI (bg-[#1B3B2B])
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 bg-[#1B3B2B] text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5">
            <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-emerald-300 block">
                KONSULTASI GRATIS
            </span>
            <h2 class="font-serif text-2xl sm:text-4xl font-bold tracking-tight text-white">
                Siap Melangkah Menuju Baitullah Bersama Zeintour?
            </h2>
            <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed max-w-2xl mx-auto">
                Hubungi konsultan ibadah kami untuk informasi jadwal manasik, ketersediaan seat, atau konsultasi paket Umrah dan Haji Khusus.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                <a href="{{ route('paket') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-full text-xs sm:text-sm font-semibold text-[#1B3B2B] bg-white hover:bg-zinc-100 transition-colors shadow-sm">
                    Lihat Pilihan Paket
                </a>
                <a href="https://wa.me/{{ $company['whatsapp'] ?? '6281222222562' }}?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20paket%20Umrah%20Zeintour" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-full text-xs sm:text-sm font-medium text-white bg-white/10 hover:bg-white/20 border border-white/30 transition-colors">
                    Konsultasi via WhatsApp
                </a>
            </div>
        </div>
    </section>

</x-layouts.main>