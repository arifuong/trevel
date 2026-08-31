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
                        PT. ZEIN INTERNASIONAL (Zeintour) adalah salah satu perusahaan penyelenggara perjalanan Ibadah Umrah yang didirikan pada 19 Oktober 2012 di Bandung oleh H. Zenal Abidin, dengan motivasi membangun dua kebaikan (kebaikan dunia dan akhirat).
                    </p>

                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Berdasarkan motivasi tersebut, Zeintour bertekad untuk menjadi pelayan para tamu Allah SWT, memberikan kemudahan atas segala hal yang berkaitan dengan proses pelaksanaan ibadahnya, mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, sampai kembali ke tanah air dengan ikhlas dan tawakal kepadaNya, agar semua rangkaian ibadahnya diterima Allah SWT.
                    </p>

                    <!-- Legalitas Izin Resmi -->
                    <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="bg-[#EFF3EB] rounded-2xl p-3.5 border border-[#E0E7DC]">
                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Penyelenggara Perjalanan Ibadah Umrah (PPIU)</span>
                            <span class="text-[#1B3B2B] font-mono font-bold text-xs mt-0.5 block">IZIN KEMENAG RI NOMOR U.255 TAHUN 2020</span>
                        </div>
                        <div class="bg-[#EFF3EB] rounded-2xl p-3.5 border border-[#E0E7DC]">
                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Penyelenggara Ibadah Haji Khusus (PIHK)</span>
                            <span class="text-[#1B3B2B] font-mono font-bold text-xs mt-0.5 block">IZIN KEMENAG RI NOMOR 599 TAHUN 2021</span>
                        </div>
                    </div>
                </div>

                <div data-reveal class="lg:col-span-5">
                    <div class="rounded-3xl overflow-hidden border border-[#E0E7DC] shadow-md bg-[#EFF3EB] aspect-[4/3] sm:aspect-[16/11]">
                        <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format,webp&fit=crop&w=800&q=75" 
                             alt="PT. Zein Internasional" 
                             width="800"
                             height="600"
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            {{-- Strip Statistik --}}
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
         2. VISI & MISI ZEINTOUR (bg-[#EFF3EB])
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                
                {{-- Visi --}}
                <div data-reveal class="lg:col-span-5 bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        VISI ZEINTOUR
                    </span>
                    <blockquote class="font-serif text-xl sm:text-2xl font-bold text-[#12271E] leading-snug italic">
                        "Menjadi penyelenggara Haji dan Umrah dengan pelayanan terbaik berbasis Al Qur'an di Indonesia."
                    </blockquote>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed pt-2">
                        Komitmen kami dalam membimbing ibadah para tamu Allah SWT berlandaskan nilai-nilai murni Al-Qur'an dan Sunnah Rasulullah ﷺ.
                    </p>
                </div>

                {{-- Misi --}}
                <div data-reveal class="lg:col-span-7 space-y-6">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        MISI ZEINTOUR
                    </span>
                    
                    <div class="space-y-4">
                        @php
                            $misiList = [
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

                        @foreach($misiList as $misi)
                            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-[#E0E7DC] flex items-start gap-4 shadow-xs">
                                <span class="font-serif text-2xl sm:text-3xl font-extrabold text-[#1B3B2B]/20 tracking-tighter leading-none shrink-0 pt-0.5">
                                    {{ $misi['num'] }}
                                </span>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm text-[#12271E] font-medium leading-relaxed">{{ $misi['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         3. TUJUAN BERDIRINYA ZEINTOUR (bg-white)
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
                    $tujuanList = [
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

                @foreach($tujuanList as $tujuan)
                    <div data-reveal-child class="bg-[#EFF3EB] rounded-3xl p-6 sm:p-8 border border-[#E0E7DC] flex flex-col justify-between shadow-xs">
                        <div>
                            <span class="font-serif text-4xl sm:text-5xl font-extrabold text-[#1B3B2B]/30 tracking-tighter leading-none block mb-4" aria-hidden="true">
                                {{ $tujuan['num'] }}
                            </span>
                            <p class="text-xs sm:text-sm text-[#12271E] font-medium leading-relaxed">{{ $tujuan['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         4. KEUNGGULAN ZEINTOUR (bg-[#EFF3EB])
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-[#EFF3EB] border-b border-[#E0E7DC]">
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
                            'icon' => 'tag',
                            'title' => 'Harga Paket Terjangkau',
                            'desc' => 'Harga paket relatif lebih murah dengan pelayanan terbaik.'
                        ],
                        [
                            'icon' => 'adjustments',
                            'title' => 'Pilihan Paket Fleksibel',
                            'desc' => 'Disediakan pilihan paket sesuai dengan kemampuan/kebutuhan jamaah.'
                        ],
                        [
                            'icon' => 'sparkles',
                            'title' => 'Fasilitas Umrah Sunnah',
                            'desc' => 'Memfasilitasi jamaah untuk melakukan Umrah sunnah.'
                        ],
                        [
                            'icon' => 'bolt',
                            'title' => 'Fast Track Imigrasi',
                            'desc' => 'Layanan cepat imigrasi di bandara Soekarno Hatta.'
                        ],
                        [
                            'icon' => 'coffee',
                            'title' => 'Lounge Bandara',
                            'desc' => 'Di bandara disediakan lounge umrah.'
                        ],
                        [
                            'icon' => 'academic-cap',
                            'title' => 'Pembimbing Profesional',
                            'desc' => 'Pembimbing yang profesional di bidangnya.'
                        ],
                    ];
                @endphp

                @foreach($keunggulanList as $keunggulan)
                    <div data-reveal-child class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E0E7DC] flex flex-col justify-between shadow-xs">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center mb-4">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
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
         5. BANNER AJAKAN KONSULTASI (bg-[#1B3B2B])
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
