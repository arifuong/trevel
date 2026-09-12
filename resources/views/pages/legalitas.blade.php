<x-layouts.main :title="'Legalitas & Izin Resmi — PT. ZEIN INTERNASIONAL (ZEIN TOUR)'" :company="$company">

    {{-- ═══════════════════════════════════════════════════════════════
         1. HEADER HALAMAN & IDENTITAS PERUSAHAAN (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-[#E0E7DC] pt-8 pb-14 sm:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['title' => 'Legalitas & Izin Resmi']]" />

            <div data-reveal class="max-w-3xl mt-6 space-y-4">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                    LEGALITAS & PERIZINAN RESMI
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight leading-[1.2]">
                    Kepastian Hukum & Izin Resmi Kemenag RI
                </h1>
                <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                    PT. ZEIN INTERNASIONAL (Merek Usaha: ZEIN TOUR) adalah penyelenggara perjalanan ibadah Umrah dan Haji Khusus resmi yang berlandaskan hukum sah, terdaftar di Kementerian Agama Republik Indonesia, Kemenkumham RI, dan sistem perizinan berusaha nasional.
                </p>
            </div>

            <!-- Ringkasan Identitas Perusahaan -->
            <div data-reveal class="mt-8 bg-[#EFF3EB] rounded-3xl p-6 sm:p-8 shadow-[0_4px_20px_rgba(0,0,0,0.04)]">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 text-xs">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-0.5">Nama Perusahaan</span>
                        <strong class="text-sm font-serif font-bold text-[#12271E] block">PT. ZEIN INTERNASIONAL</strong>
                        <span class="text-[11px] text-[#526057]">Merek Usaha: <strong>ZEIN TOUR</strong></span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-0.5">Pimpinan Perusahaan</span>
                        <strong class="text-sm font-serif font-bold text-[#12271E] block">H. ZENAL ABIDIN, M.Si</strong>
                        <span class="text-[11px] text-[#526057]">Direktur Utama</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-0.5">Izin Umrah (PPIU)</span>
                        <strong class="text-xs font-mono font-bold text-[#1B3B2B] block">NOMOR U.255 TAHUN 2020</strong>
                        <span class="text-[11px] text-[#526057]">Kementerian Agama RI</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-0.5">Izin Haji Khusus (PIHK)</span>
                        <strong class="text-xs font-mono font-bold text-[#1B3B2B] block">NOMOR 599 TAHUN 2021</strong>
                        <span class="text-[11px] text-[#526057]">Kementerian Agama RI</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. DAFTAR DOKUMEN LEGALITAS RESMI (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    DOKUMEN RESMI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Dokumen Legalitas & SK Perizinan
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed">
                    Data resmi kepemilikan izin, akreditasi, akta pendirian, dan perizinan berusaha PT. ZEIN INTERNASIONAL.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($legalities as $legality)
                    <div data-reveal-child class="bg-white rounded-3xl p-6 sm:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] uppercase font-bold text-[#1B3B2B] bg-[#EAF1E8] px-2.5 py-1 rounded-md">
                                    {{ $legality['badge'] }}
                                </span>
                                <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="font-serif text-base font-bold text-[#12271E] mb-1">{{ $legality['title'] }}</h3>
                            <p class="text-xs font-mono font-bold text-[#1B3B2B] mb-3">{{ $legality['number'] }}</p>
                            <p class="text-xs text-[#526057] leading-relaxed">{{ $legality['description'] }}</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-[#E8EDE5] flex items-center justify-between text-xs text-[#4D5E54]">
                            <span>Status:</span>
                            <span class="text-[#1B3B2B] font-bold">Aktif & Terverifikasi</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         3. PRINSIP 5 PASTI UMRAH (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 sm:py-28 bg-white border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="max-w-2xl mb-14">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                    STANDAR KEMENAG RI
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-[#12271E] tracking-tight">
                    Komitmen 5 Pasti Umrah
                </h2>
                <p class="text-xs sm:text-sm text-[#4D5E54] mt-2 leading-relaxed">
                    Kami menerapkan 5 pilar kepastian yang diwajibkan oleh Kementerian Agama RI demi keamanan seluruh jamaah.
                </p>
            </div>

            <div class="space-y-4">
                @php
                    $pastiList = [
                        [
                            'num' => '01',
                            'title' => 'Pasti Travelnya Berizin',
                            'desc' => 'PT. ZEIN INTERNASIONAL memiliki izin operasional resmi PPIU NOMOR U.255 TAHUN 2020 dan PIHK NOMOR 599 TAHUN 2021 dari Kementerian Agama RI yang dapat diverifikasi secara publik melalui Siskopatuh.'
                        ],
                        [
                            'num' => '02',
                            'title' => 'Pasti Jadwal Keberangkatannya',
                            'desc' => 'Jadwal tanggal dan jam keberangkatan telah ditetapkan sejak awal pendaftaran. Jamaah mendapatkan kepastian maskapai dan nomor penerbangan sebelum pelunasan.'
                        ],
                        [
                            'num' => '03',
                            'title' => 'Pasti Terbangnya & Maskapainya',
                            'desc' => 'Tiket pesawat menggunakan penerbangan langsung (Direct Flight) dengan maskapai ternama seperti Saudia Airlines, Garuda Indonesia, dan Turkish Airlines.'
                        ],
                        [
                            'num' => '04',
                            'title' => 'Pasti Hotelnya & Lokasinya',
                            'desc' => 'Nama hotel di Makkah dan Madinah tertulis jelas dalam akad, dengan standar bintang 4 atau 5 di area ring 1 pelataran masjid.'
                        ],
                        [
                            'num' => '05',
                            'title' => 'Pasti Visanya',
                            'desc' => 'Pengurusan Visa Umrah dan Visa Haji Mujamalah diproses langsung melalui sistem resmi Kedutaan Besar Kerajaan Arab Saudi dan terdaftar di e-Hajj.'
                        ],
                    ];
                @endphp

                @foreach($pastiList as $pasti)
                    <div data-reveal-child class="bg-[#EFF3EB] rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-[0_4px_20px_rgba(0,0,0,0.05)] transition-all duration-300 flex items-start gap-5">
                        <span class="font-serif text-3xl sm:text-4xl font-extrabold text-[#1B3B2B]/30 tracking-tighter leading-none shrink-0 pt-0.5" aria-hidden="true">
                            {{ $pasti['num'] }}
                        </span>
                        <div class="flex-1">
                            <h3 class="font-serif text-base sm:text-lg font-bold text-[#12271E] mb-1.5">{{ $pasti['title'] }}</h3>
                            <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">{{ $pasti['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         4. VERIFIKASI DOKUMEN / BANNER CTA (bg-[#1B3B2B])
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 bg-[#1B3B2B] text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5">
            <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-emerald-300 block">
                TRANSPARANSI PENUH
            </span>
            <h2 class="font-serif text-2xl sm:text-4xl font-bold tracking-tight text-white">
                Ingin Cek Validitas Izin Kami di Kemenag RI?
            </h2>
            <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed max-w-2xl mx-auto">
                Silakan hubungi customer service kami atau kunjungi kantor pusat kami untuk melihat berkas legalitas asli, SK Akreditasi, dan MoU hotel resmi.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                <a href="{{ route('kontak') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-full text-xs sm:text-sm font-semibold text-[#1B3B2B] bg-white hover:bg-zinc-100 transition-colors shadow-sm">
                    Kunjungi Kantor Kami
                </a>
                <a href="https://wa.me/{{ $company['whatsapp'] ?? '6281222222562' }}?text=Assalamu%27alaikum,%20saya%20ingin%20meminta%20informasi%20legalitas%20PT.%20Zein%20Internasional" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-full text-xs sm:text-sm font-medium text-white bg-white/10 hover:bg-white/20 border border-white/30 transition-colors">
                    Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </section>

</x-layouts.main>
