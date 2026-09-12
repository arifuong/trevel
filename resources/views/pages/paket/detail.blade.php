@php
    $ogImg = !empty($package['thumbnail']) 
        ? (str_starts_with($package['thumbnail'], 'http') ? $package['thumbnail'] : url($package['thumbnail'])) 
        : asset('images/hero-1200.webp');
    $ogDesc = 'Paket ' . $package['name'] . ' (' . ($package['duration'] ?? '12 Hari') . ') mulai dari ' . $package['price_formatted'] . '. Hotel Makkah ' . ($package['hotel_makkah'] ?? 'Bintang 5') . ' & Madinah ' . ($package['hotel_madinah'] ?? 'Bintang 4') . ' bersama PT. Zein Internasional.';
@endphp

<x-layouts.main 
    :title="$package['name'] . ' — PT. Zein Internasional'" 
    :metaDescription="$ogDesc"
    :ogImage="$ogImg"
    ogType="product"
    :company="$company">

    @php
        $whatsapp = $company['whatsapp'] ?? '6281222222562';
        $variantsJson = json_encode($package['variants'] ?? []);
        $hasVariants = !empty($package['variants']);
        
        // Extract days
        preg_match('/(\d+)/', $package['duration'] ?? '9', $m);
        $days = str_pad($m[1] ?? '9', 2, '0', STR_PAD_LEFT);
    @endphp

    <div x-data="{
        variants: {{ $variantsJson }},
        activeIndex: 0,
        lightboxOpen: false,
        lightboxImage: '',
        openLightbox(url) {
            if(url) {
                this.lightboxImage = url;
                this.lightboxOpen = true;
            }
        },
        get activeVariant() {
            return this.variants && this.variants.length > 0 ? this.variants[this.activeIndex] : null;
        }
    }">

        {{-- ═══════════════════════════════════════════════════════════════
             1. HEADER HALAMAN (bg-white)
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border-b border-[#E5E7EB] pt-8 pb-12 sm:pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumb :items="[
                    ['title' => 'Katalog Paket', 'url' => route('paket')],
                    ['title' => $package['name']]
                ]" />

                <div data-reveal class="mt-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="max-w-3xl">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B3B2B] text-white text-[11px] font-semibold uppercase tracking-wide">
                                {{ $package['type_label'] }}
                            </span>
                            @if(!empty($package['badge']))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C2A264] text-white text-[11px] font-semibold uppercase tracking-wide">
                                    {{ $package['badge'] }}
                                </span>
                            @endif
                        </div>
                        <h1 class="font-serif text-2xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight leading-tight">
                            {{ $package['name'] }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-xs text-[#4D5E54]">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                {{ $package['departure_date'] }}
                            </span>
                            <span class="text-zinc-300">•</span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $package['duration'] }}
                            </span>
                        </div>
                    </div>

                    <div class="text-left md:text-right shrink-0 flex flex-col md:items-end gap-2.5">
                        <div>
                            <span class="text-[10px] text-[#4D5E54] uppercase tracking-wider font-bold block mb-0.5">Mulai Dari</span>
                            <span class="font-sans text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#1B3B2B] tracking-tight block">
                                <span x-text="activeVariant ? activeVariant.lowest_price_formatted : '{{ $package['price_formatted'] }}'"></span>
                            </span>
                            <span class="text-[11px] text-[#4D5E54] block mt-0.5">Per Jamaah (Sudah Termasuk Fasilitas)</span>
                        </div>

                        <div>
                            <x-share-button 
                                :url="url()->current()"
                                :title="$package['name'] . ' — PT. Zein Internasional'"
                                :text="'Yuk lihat paket ' . $package['name'] . ' mulai dari ' . $package['price_formatted'] . ' bersama PT. Zein Internasional!'"
                                variant="button"
                                align="right"
                                placement="bottom"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
             2. KONTEN UTAMA (2 Kolom) — BACKGROUND PUTIH BERSIH #FFFFFF
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-12 sm:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                    {{-- Kolom Kiri (8 Kolom) --}}
                    <div class="lg:col-span-8 space-y-10">

                        {{-- Foto Utama Paket --}}
                        <div data-reveal class="relative rounded-3xl overflow-hidden border border-[#E5E7EB] bg-[#FBFDFB] shadow-sm cursor-pointer group flex items-center justify-center"
                             @click="openLightbox(activeVariant && activeVariant.main_photo ? activeVariant.main_photo : '{{ $package['thumbnail'] }}')">
                            @if(!empty($package['thumbnail']))
                                <img :src="activeVariant && activeVariant.main_photo ? activeVariant.main_photo : '{{ $package['thumbnail'] }}'"
                                     alt="{{ $package['name'] }}"
                                     fetchpriority="high"
                                     decoding="async"
                                     class="block w-full h-auto max-h-[750px] object-contain mx-auto transition-transform duration-500 group-hover:scale-[1.01]">
                            @else
                                <div class="w-full h-72 sm:h-96 bg-[#1B3B2B] flex items-center justify-center text-white/60">
                                    <span>PT. Zein Internasional</span>
                                </div>
                            @endif

                            <!-- Zoom Overlay Icon -->
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                <span class="bg-white/90 backdrop-blur-xs text-[#12271E] px-4 py-2 rounded-full text-xs font-semibold shadow-md flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                    Lihat Foto Penuh
                                </span>
                            </div>

                            <!-- Lencana Durasi Hari -->
                            <div class="absolute top-4 right-4 bg-[#1B3B2B] text-white px-3.5 py-2 rounded-xl text-center shadow-lg border border-white/10">
                                <span class="text-lg sm:text-xl font-bold leading-none block">{{ $days }}</span>
                                <span class="text-[9px] uppercase font-semibold tracking-wider text-emerald-300 block leading-none mt-0.5">Hari</span>
                            </div>
                        </div>

                        {{-- Deskripsi Paket Induk --}}
                        <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E5E7EB] shadow-xs">
                            <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                                DESKRIPSI PROGRAM
                            </span>
                            <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#12271E] mb-4">
                                Tentang {{ $package['name'] }}
                            </h2>
                            <p class="text-xs sm:text-sm text-[#526057] leading-relaxed whitespace-pre-line">
                                {{ $package['description'] }}
                            </p>
                        </div>

                        {{-- ═══════════════════════════════════════════════════════════════
                             PILIHAN SUB-PAKET (VIP / BISNIS / EKONOMI)
                        ═══════════════════════════════════════════════════════════════ --}}
                        @if($hasVariants)
                            <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E5E7EB] shadow-xs space-y-6">
                                <div>
                                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                                        PILIHAN SUB-PAKET
                                    </span>
                                    <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#12271E]">
                                        Pilih Varian Paket Sesuai Kebutuhan Anda
                                    </h2>
                                    <p class="text-xs text-[#526057] mt-1">
                                        Setiap varian sub-paket memiliki maskapai, hotel bintang, dan fasilitas eksklusif tersendiri.
                                    </p>
                                </div>

                                {{-- Tab Selector Varian --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <template x-for="(variant, idx) in variants" :key="variant.id">
                                        <button type="button" 
                                                @click="activeIndex = idx"
                                                class="text-left p-5 rounded-2xl transition-all cursor-pointer relative bg-white"
                                                :class="activeIndex === idx 
                                                    ? 'border-2 border-[#1B3B2B] shadow-md ring-1 ring-[#1B3B2B]/10' 
                                                    : 'border border-[#E5E7EB] hover:border-[#1B3B2B]/40 hover:shadow-xs'">
                                            
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <span class="font-bold text-sm sm:text-base text-[#12271E]" x-text="variant.name"></span>
                                                <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full"
                                                      :class="variant.is_sold_out 
                                                        ? 'bg-red-50 text-red-700 border border-red-200' 
                                                        : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60'"
                                                      x-text="variant.status_label"></span>
                                            </div>

                                            <div class="text-[11px] text-[#6B7280] mb-0.5">Mulai dari</div>
                                            <div class="font-sans text-lg sm:text-xl font-extrabold text-[#1B3B2B] tracking-tight" x-text="variant.lowest_price_formatted"></div>

                                            <div class="mt-3 pt-3 border-t border-[#F3F4F6] flex items-center justify-between text-[11px]">
                                                <span class="text-[#6B7280]" x-text="variant.quota + ' Kursi Tersisa'"></span>
                                                <span class="font-semibold text-xs flex items-center gap-1"
                                                      :class="activeIndex === idx ? 'text-[#1B3B2B]' : 'text-gray-400'">
                                                    <span x-text="activeIndex === idx ? 'Aktif Dipilih' : 'Lihat Detail'"></span>
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                </span>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                {{-- Detail Varian Terpilih --}}
                                <template x-if="activeVariant">
                                    <div class="pt-6 border-t border-[#E5E7EB] space-y-8">
                                        
                                        {{-- Header Varian Terpilih (Clean Information Panel) --}}
                                        <div class="bg-[#FBFDFB] p-4 sm:p-5 rounded-2xl border border-[#E5E7EB] shadow-2xs">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block">VARIAN TERPILIH</span>
                                                        <h3 class="text-base sm:text-lg font-bold text-[#12271E]" x-text="'Sub-Paket: ' + activeVariant.name"></h3>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        <span x-text="activeVariant.quota + ' Kursi Tersisa'"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-[#526057] mt-3 pl-0 sm:pl-13 leading-relaxed border-t border-[#E5E7EB]/60 pt-2.5" x-show="activeVariant.description" x-text="activeVariant.description"></p>
                                        </div>

                                        {{-- 1. Pricing Matrix Table (Modern Clean Rate Card) --}}
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-serif font-bold text-base text-[#12271E] flex items-center gap-2">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                                                    Pricing Matrix (Harga Per Tipe Kamar)
                                                </h4>
                                                <span class="text-[11px] text-gray-500 hidden sm:inline-block">Harga transparan per jamaah</span>
                                            </div>
                                            
                                            <template x-if="activeVariant.prices && activeVariant.prices.filter(p => p.is_active).length > 0">
                                                <div class="overflow-x-auto rounded-2xl border border-[#E5E7EB] bg-white shadow-2xs">
                                                    <table class="w-full text-left text-xs sm:text-sm">
                                                        <thead class="bg-[#F9FAF8] text-[#4B5563] font-semibold text-[11px] uppercase tracking-wider border-b border-[#E5E7EB]">
                                                            <tr>
                                                                <th class="py-3.5 px-4 sm:px-6">Tipe Kamar</th>
                                                                <th class="py-3.5 px-4 text-right">Harga Normal</th>
                                                                <th class="py-3.5 px-4 text-right">Harga Promo</th>
                                                                <th class="py-3.5 px-4 sm:px-6 text-right">Harga Berlaku</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-[#F3F4F6] bg-white">
                                                            <template x-for="p in activeVariant.prices.filter(p => p.is_active)" :key="p.id">
                                                                <tr class="hover:bg-[#FBFDFB] transition-colors">
                                                                    <td class="py-4 px-4 sm:px-6 font-semibold text-[#12271E]">
                                                                        <span x-text="p.room_label"></span>
                                                                    </td>
                                                                    <td class="py-4 px-4 text-right font-mono text-xs text-gray-400" :class="p.promo_price ? 'line-through' : 'font-medium text-[#12271E]'" x-text="p.normal_price_formatted"></td>
                                                                    <td class="py-4 px-4 text-right font-mono font-semibold text-emerald-700" x-text="p.promo_price_formatted || '-'"></td>
                                                                    <td class="py-4 px-4 sm:px-6 text-right font-mono font-bold text-base text-[#1B3B2B]" x-text="p.effective_price_formatted"></td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>

                                            <template x-if="!activeVariant.prices || activeVariant.prices.filter(p => p.is_active).length === 0">
                                                <div class="p-4 rounded-2xl bg-[#F9FAF8] border border-[#E5E7EB] text-center text-xs text-gray-500">
                                                    Pilihan harga kamar untuk sub-paket ini belum tersedia.
                                                </div>
                                            </template>
                                        </div>

                                        {{-- 2. Penerbangan / Maskapai --}}
                                        <div class="space-y-3" x-show="activeVariant.airline_departure || activeVariant.airline_return || activeVariant.airline">
                                            <h4 class="font-serif font-bold text-base text-[#12271E] flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                                                Penerbangan & Maskapai
                                            </h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div class="bg-white rounded-2xl p-4 border border-[#E5E7EB] flex items-center gap-3 shadow-2xs">
                                                    <template x-if="activeVariant.airline_departure_logo">
                                                        <div class="w-12 h-12 bg-gray-50 rounded-xl border border-[#E5E7EB] p-1.5 flex items-center justify-center shrink-0">
                                                            <img :src="activeVariant.airline_departure_logo" alt="Airline Departure" class="max-w-full max-h-full object-contain">
                                                        </div>
                                                    </template>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-[#6B7280] block">Penerbangan Berangkat</span>
                                                        <span class="font-semibold text-xs sm:text-sm text-[#12271E]" x-text="activeVariant.airline_departure || activeVariant.airline || 'Sesuai Jadwal'"></span>
                                                    </div>
                                                </div>
                                                <div class="bg-white rounded-2xl p-4 border border-[#E5E7EB] flex items-center gap-3 shadow-2xs">
                                                    <template x-if="activeVariant.airline_return_logo">
                                                        <div class="w-12 h-12 bg-gray-50 rounded-xl border border-[#E5E7EB] p-1.5 flex items-center justify-center shrink-0">
                                                            <img :src="activeVariant.airline_return_logo" alt="Airline Return" class="max-w-full max-h-full object-contain">
                                                        </div>
                                                    </template>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-[#6B7280] block">Penerbangan Kepulangan</span>
                                                        <span class="font-semibold text-xs sm:text-sm text-[#12271E]" x-text="activeVariant.airline_return || activeVariant.airline || 'Sesuai Jadwal'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                                                              {{-- 3. Akomodasi Hotel Bintang (Makkah & Madinah) --}}
                                        <div class="space-y-4" x-data="{ makkahExpanded: false, madinahExpanded: false }">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-serif font-bold text-base text-[#12271E] flex items-center gap-2">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                                                    Akomodasi Hotel Bintang (Makkah & Madinah)
                                                </h4>
                                                <span class="text-[11px] text-gray-500 font-medium hidden sm:inline-block">
                                                    Terintegrasi dengan Master Data Hotel
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                                {{-- KARTU HOTEL MAKKAH --}}
                                                <div class="bg-white rounded-3xl border border-[#E5E7EB] shadow-xs hover:border-[#1B3B2B]/40 hover:shadow-sm transition-all overflow-hidden flex flex-col">
                                                    {{-- Header Ribbon Makkah --}}
                                                    <div class="px-5 py-2.5 bg-[#1B3B2B] text-white flex items-center justify-between">
                                                        <div class="flex items-center gap-2">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                                            <span class="text-[11px] font-bold uppercase tracking-wider">Hotel Makkah</span>
                                                        </div>
                                                        <span class="text-[10px] text-white/80 font-medium tracking-tight">Tanah Suci Makkah</span>
                                                    </div>

                                                    {{-- Foto Cover Makkah --}}
                                                    <div class="relative w-full aspect-[16/9] bg-[#F9FAF8] overflow-hidden border-b border-[#E5E7EB] group">
                                                        <template x-if="activeVariant.hotel_makkah_main_photo || (activeVariant.hotel_makkah_photos && activeVariant.hotel_makkah_photos.length > 0)">
                                                            <img :src="activeVariant.hotel_makkah_main_photo || activeVariant.hotel_makkah_photos[0].url" 
                                                                 :alt="activeVariant.hotel_makkah_name"
                                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer"
                                                                 @click="openLightbox(activeVariant.hotel_makkah_main_photo || activeVariant.hotel_makkah_photos[0].url)">
                                                        </template>
                                                        <template x-if="!activeVariant.hotel_makkah_main_photo && (!activeVariant.hotel_makkah_photos || activeVariant.hotel_makkah_photos.length === 0)">
                                                            <div class="w-full h-full flex flex-col items-center justify-center text-xs text-gray-400 p-4 text-center">
                                                                <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                                                <span>Foto hotel akan segera diperbarui</span>
                                                            </div>
                                                        </template>

                                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 pointer-events-none"></div>

                                                        {{-- Badges di atas cover --}}
                                                        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center justify-between gap-2">
                                                            {{-- Star Rating Badge --}}
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-xs text-amber-800 text-xs font-bold shadow-xs border border-amber-200">
                                                                <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                                <span x-text="activeVariant.hotel_makkah_star ? 'Bintang ' + activeVariant.hotel_makkah_star : 'Bintang 5'"></span>
                                                            </span>

                                                            {{-- Landmark Distance Badge (Ruby Accent) --}}
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#991B1B]/90 backdrop-blur-xs text-white text-xs font-semibold shadow-xs border border-red-300/30"
                                                                  x-show="activeVariant.hotel_makkah_distance">
                                                                <svg class="w-3.5 h-3.5 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                                                </svg>
                                                                <span x-text="activeVariant.hotel_makkah_distance + 'm ke Masjidil Haram'"></span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {{-- Konten Informasi Makkah --}}
                                                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                                                        <div>
                                                            <h5 class="font-serif font-bold text-base sm:text-lg text-[#12271E] leading-snug"
                                                                x-text="activeVariant.hotel_makkah_name || 'Hotel Makkah Setaraf'"></h5>
                                                            <p class="text-xs sm:text-sm text-[#526057] mt-2 leading-relaxed"
                                                               x-show="activeVariant.hotel_makkah_description"
                                                               x-text="activeVariant.hotel_makkah_description"></p>
                                                        </div>

                                                        {{-- Galeri Strip Makkah --}}
                                                        <template x-if="activeVariant.hotel_makkah_photos && activeVariant.hotel_makkah_photos.length > 1">
                                                            <div class="pt-3 border-t border-[#E5E7EB]">
                                                                <span class="text-[10px] uppercase font-bold text-[#6B7280] tracking-wider block mb-2">Galeri Foto Hotel</span>
                                                                <div class="grid grid-cols-4 gap-2">
                                                                    <template x-for="(hp, hIdx) in activeVariant.hotel_makkah_photos.slice(0, 4)" :key="hIdx">
                                                                        <div class="aspect-square rounded-xl overflow-hidden bg-[#F9FAF8] border border-[#E5E7EB] relative group cursor-pointer hover:opacity-95"
                                                                             @click="openLightbox(hp.url)">
                                                                            <img :src="hp.url" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="Foto Hotel Makkah">
                                                                            <template x-if="hIdx === 3 && activeVariant.hotel_makkah_photos.length > 4">
                                                                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-xs"
                                                                                     x-text="'+' + (activeVariant.hotel_makkah_photos.length - 3)">
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        {{-- Fasilitas Chips Makkah --}}
                                                        <template x-if="activeVariant.makkah_facilities && activeVariant.makkah_facilities.length > 0">
                                                            <div class="pt-3 border-t border-[#E5E7EB]">
                                                                <div class="flex items-center justify-between mb-2">
                                                                    <span class="text-[10px] uppercase font-bold text-[#6B7280] tracking-wider">Fasilitas Pilihan:</span>
                                                                    <button type="button" 
                                                                            x-show="activeVariant.makkah_facilities.length > 4"
                                                                            @click="makkahExpanded = !makkahExpanded"
                                                                            class="text-[11px] font-semibold text-[#1B3B2B] hover:text-[#12271E] cursor-pointer inline-flex items-center gap-1 transition-colors">
                                                                        <span x-text="makkahExpanded ? 'Tampilkan Sedikit' : '+' + (activeVariant.makkah_facilities.length - 4) + ' Lainnya'"></span>
                                                                        <svg class="w-3 h-3 transition-transform duration-200" :class="makkahExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                                                    </button>
                                                                </div>
                                                                <div class="flex flex-wrap gap-2">
                                                                    <template x-for="(fac, fIdx) in activeVariant.makkah_facilities" :key="fac">
                                                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-[#F9FAF8] text-[#12271E] border border-[#E5E7EB] shadow-2xs hover:bg-[#F3F4F6] transition-colors"
                                                                             x-show="makkahExpanded || fIdx < 4">
                                                                            <svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                            <span x-text="fac"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- KARTU HOTEL MADINAH --}}
                                                <div class="bg-white rounded-3xl border border-[#E5E7EB] shadow-xs hover:border-[#1B3B2B]/40 hover:shadow-sm transition-all overflow-hidden flex flex-col">
                                                    {{-- Header Ribbon Madinah --}}
                                                    <div class="px-5 py-2.5 bg-[#12271E] text-white flex items-center justify-between">
                                                        <div class="flex items-center gap-2">
                                                            <span class="w-2 h-2 rounded-full bg-teal-300"></span>
                                                            <span class="text-[11px] font-bold uppercase tracking-wider">Hotel Madinah</span>
                                                        </div>
                                                        <span class="text-[10px] text-white/80 font-medium tracking-tight">Kota Madinah Al-Munawwarah</span>
                                                    </div>

                                                    {{-- Foto Cover Madinah --}}
                                                    <div class="relative w-full aspect-[16/9] bg-[#F9FAF8] overflow-hidden border-b border-[#E5E7EB] group">
                                                        <template x-if="activeVariant.hotel_madinah_main_photo || (activeVariant.hotel_madinah_photos && activeVariant.hotel_madinah_photos.length > 0)">
                                                            <img :src="activeVariant.hotel_madinah_main_photo || activeVariant.hotel_madinah_photos[0].url" 
                                                                 :alt="activeVariant.hotel_madinah_name"
                                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer"
                                                                 @click="openLightbox(activeVariant.hotel_madinah_main_photo || activeVariant.hotel_madinah_photos[0].url)">
                                                        </template>
                                                        <template x-if="!activeVariant.hotel_madinah_main_photo && (!activeVariant.hotel_madinah_photos || activeVariant.hotel_madinah_photos.length === 0)">
                                                            <div class="w-full h-full flex flex-col items-center justify-center text-xs text-gray-400 p-4 text-center">
                                                                <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                                                <span>Foto hotel akan segera diperbarui</span>
                                                            </div>
                                                        </template>

                                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 pointer-events-none"></div>

                                                        {{-- Badges di atas cover --}}
                                                        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center justify-between gap-2">
                                                            {{-- Star Rating Badge --}}
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-xs text-amber-800 text-xs font-bold shadow-xs border border-amber-200">
                                                                <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                                <span x-text="activeVariant.hotel_madinah_star ? 'Bintang ' + activeVariant.hotel_madinah_star : 'Bintang 4/5'"></span>
                                                            </span>

                                                            {{-- Landmark Distance Badge (Ruby Accent) --}}
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#991B1B]/90 backdrop-blur-xs text-white text-xs font-semibold shadow-xs border border-red-300/30"
                                                                  x-show="activeVariant.hotel_madinah_distance">
                                                                <svg class="w-3.5 h-3.5 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                                                </svg>
                                                                <span x-text="activeVariant.hotel_madinah_distance + 'm ke Masjid Nabawi'"></span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {{-- Konten Informasi Madinah --}}
                                                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                                                        <div>
                                                            <h5 class="font-serif font-bold text-base sm:text-lg text-[#12271E] leading-snug"
                                                                x-text="activeVariant.hotel_madinah_name || 'Hotel Madinah Setaraf'"></h5>
                                                            <p class="text-xs sm:text-sm text-[#526057] mt-2 leading-relaxed"
                                                               x-show="activeVariant.hotel_madinah_description"
                                                               x-text="activeVariant.hotel_madinah_description"></p>
                                                        </div>

                                                        {{-- Galeri Strip Madinah --}}
                                                        <template x-if="activeVariant.hotel_madinah_photos && activeVariant.hotel_madinah_photos.length > 1">
                                                            <div class="pt-3 border-t border-[#E5E7EB]">
                                                                <span class="text-[10px] uppercase font-bold text-[#6B7280] tracking-wider block mb-2">Galeri Foto Hotel</span>
                                                                <div class="grid grid-cols-4 gap-2">
                                                                    <template x-for="(hp, hIdx) in activeVariant.hotel_madinah_photos.slice(0, 4)" :key="hIdx">
                                                                        <div class="aspect-square rounded-xl overflow-hidden bg-[#F9FAF8] border border-[#E5E7EB] relative group cursor-pointer hover:opacity-95"
                                                                             @click="openLightbox(hp.url)">
                                                                            <img :src="hp.url" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="Foto Hotel Madinah">
                                                                            <template x-if="hIdx === 3 && activeVariant.hotel_madinah_photos.length > 4">
                                                                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-xs"
                                                                                     x-text="'+' + (activeVariant.hotel_madinah_photos.length - 3)">
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        {{-- Fasilitas Chips Madinah --}}
                                                        <template x-if="activeVariant.madinah_facilities && activeVariant.madinah_facilities.length > 0">
                                                            <div class="pt-3 border-t border-[#E5E7EB]">
                                                                <div class="flex items-center justify-between mb-2">
                                                                    <span class="text-[10px] uppercase font-bold text-[#6B7280] tracking-wider">Fasilitas Pilihan:</span>
                                                                    <button type="button" 
                                                                            x-show="activeVariant.madinah_facilities.length > 4"
                                                                            @click="madinahExpanded = !madinahExpanded"
                                                                            class="text-[11px] font-semibold text-[#1B3B2B] hover:text-[#12271E] cursor-pointer inline-flex items-center gap-1 transition-colors">
                                                                        <span x-text="madinahExpanded ? 'Tampilkan Sedikit' : '+' + (activeVariant.madinah_facilities.length - 4) + ' Lainnya'"></span>
                                                                        <svg class="w-3 h-3 transition-transform duration-200" :class="madinahExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                                                    </button>
                                                                </div>
                                                                <div class="flex flex-wrap gap-2">
                                                                    <template x-for="(fac, fIdx) in activeVariant.madinah_facilities" :key="fac">
                                                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-[#F9FAF8] text-[#12271E] border border-[#E5E7EB] shadow-2xs hover:bg-[#F3F4F6] transition-colors"
                                                                             x-show="madinahExpanded || fIdx < 4">
                                                                            <svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                            <span x-text="fac"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 4. Biaya Sudah Termasuk & Belum Termasuk --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-2xs space-y-3">
                                                <h5 class="font-bold text-sm text-[#12271E] flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-[#1B3B2B]"></span>
                                                    Biaya Sudah Termasuk
                                                </h5>
                                                <ul class="space-y-2 text-xs text-[#374151]">
                                                    <template x-for="inc in (activeVariant.includes && activeVariant.includes.length > 0 ? activeVariant.includes : ['Tiket Pesawat PP', 'Visa Umrah', 'Hotel Makkah & Madinah', 'Makan 3x Sehari', 'Handling & Perlengkapan'])" :key="inc">
                                                        <li class="flex items-start gap-2">
                                                            <svg class="w-4 h-4 text-[#1B3B2B] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                            <span x-text="inc"></span>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>

                                            <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-2xs space-y-3">
                                                <h5 class="font-bold text-sm text-[#12271E] flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                                    Biaya Belum Termasuk
                                                </h5>
                                                <ul class="space-y-2 text-xs text-[#374151]">
                                                    <template x-for="exc in (activeVariant.excludes && activeVariant.excludes.length > 0 ? activeVariant.excludes : ['Pembuatan / Perpanjangan Paspor', 'Vaksin Meningitis', 'Pengeluaran Pribadi', 'Kelebihan Bagasi'])" :key="exc">
                                                        <li class="flex items-start gap-2">
                                                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            <span x-text="exc"></span>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </template>

                            </div>
                        @endif
                    </div>

                    {{-- Kolom Kanan: Kartu Booking Sticky (4 Kolom) — Premium Sticky Summary Card --}}
                    <div class="lg:col-span-4 lg:sticky lg:top-36 space-y-6">
                        
                        <div data-reveal class="bg-white rounded-[20px] p-6 sm:p-7 border border-[#E5E7EB] shadow-[0_4px_24px_-4px_rgba(0,0,0,0.06)]">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280] block mb-1">
                                <span x-text="activeVariant ? 'Biaya ' + activeVariant.name : 'Biaya Paket Ibadah'"></span>
                            </span>
                            <span class="font-sans text-2xl sm:text-3xl font-extrabold text-[#1B3B2B] tracking-tight block mb-5">
                                <span x-text="activeVariant ? activeVariant.lowest_price_formatted : '{{ $package['price_formatted'] }}'"></span>
                            </span>

                            {{-- Detail Informasi dengan Dotted Leader Lines --}}
                            <div class="space-y-3 pb-6 border-b border-[#E5E7EB] text-xs">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-gray-500 shrink-0">Durasi</span>
                                    <span class="mx-2 flex-1 border-b border-dotted border-gray-300"></span>
                                    <span class="font-bold text-[#12271E] text-right">{{ $package['duration'] }}</span>
                                </div>
                                <div class="flex items-baseline justify-between" x-show="activeVariant">
                                    <span class="text-gray-500 shrink-0">Sub-Paket</span>
                                    <span class="mx-2 flex-1 border-b border-dotted border-gray-300"></span>
                                    <span class="font-bold text-[#12271E] text-right" x-text="activeVariant ? activeVariant.name : '-'"></span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-gray-500 shrink-0">Hotel Makkah</span>
                                    <span class="mx-2 flex-1 border-b border-dotted border-gray-300"></span>
                                    <span class="font-bold text-[#12271E] text-right" x-text="activeVariant ? (activeVariant.hotel_makkah_name || 'Bintang 5 Ring 1') : '{{ $package['hotel_makkah'] ?? '-' }}'"></span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-gray-500 shrink-0">Hotel Madinah</span>
                                    <span class="mx-2 flex-1 border-b border-dotted border-gray-300"></span>
                                    <span class="font-bold text-[#12271E] text-right" x-text="activeVariant ? (activeVariant.hotel_madinah_name || 'Bintang 4/5 Ring 1') : '{{ $package['hotel_madinah'] ?? '-' }}'"></span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-gray-500 shrink-0">Maskapai</span>
                                    <span class="mx-2 flex-1 border-b border-dotted border-gray-300"></span>
                                    <span class="font-bold text-[#12271E] text-right" x-text="activeVariant ? (activeVariant.airline_departure || 'Saudia Airlines') : '{{ $package['airline'] }}'"></span>
                                </div>
                            </div>

                            <div class="pt-5 space-y-3">
                                @if(!empty($package['is_sold_out']))
                                    <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-center space-y-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-600 text-white">
                                            Sold Out
                                        </span>
                                        <p class="text-xs font-semibold text-red-800">Kuota Kursi Telah Habis</p>
                                    </div>
                                    <a href="{{ route('paket') }}"
                                       class="w-full h-12 inline-flex items-center justify-center px-4 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-all duration-200 shadow-md hover:shadow-lg">
                                        Lihat Paket yang Masih Tersedia &rarr;
                                    </a>
                                @else
                                    @auth
                                        <a :href="'{{ route('jamaah.registration.create') }}?package_id={{ $package['id'] }}' + (activeVariant ? '&variant_id=' + activeVariant.id : '')"
                                           class="w-full h-12 inline-flex items-center justify-center gap-2 px-5 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-all duration-200 shadow-md hover:shadow-lg cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                            <span x-text="activeVariant ? 'Daftar ' + activeVariant.name + ' Sekarang' : 'Daftar Paket Ini Sekarang'"></span>
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}"
                                           class="w-full h-12 inline-flex items-center justify-center gap-2 px-5 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-all duration-200 shadow-md hover:shadow-lg cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                            <span>Daftar Sekarang Online</span>
                                        </a>
                                    @endauth

                                    {{-- WhatsApp Button (Secondary Lighter Style) --}}
                                    <a :href="'https://wa.me/{{ $whatsapp }}?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20paket%20{{ urlencode($package['name']) }}' + (activeVariant ? '%20varian%20' + encodeURIComponent(activeVariant.name) : '')"
                                       target="_blank"
                                       class="w-full h-11 inline-flex items-center justify-center gap-2 px-4 rounded-full text-xs sm:text-sm font-semibold text-[#1B3B2B] bg-[#F4F7F2] hover:bg-[#EAF1E8] transition-colors border border-[#E5E7EB]">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.274.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                                        <span>Pesan Kursi via WhatsApp</span>
                                    </a>

                                    {{-- Share Button --}}
                                    <x-share-button 
                                        :url="url()->current()"
                                        :title="$package['name'] . ' — PT. Zein Internasional'"
                                        :text="'Yuk lihat paket ' . $package['name'] . ' mulai dari ' . $package['price_formatted'] . ' bersama PT. Zein Internasional!'"
                                        variant="full"
                                        align="center"
                                        placement="top"
                                    />

                                    {{-- Back to Packages Button (Secondary style) --}}
                                    <a href="{{ route('paket') }}"
                                       class="w-full h-11 inline-flex items-center justify-center px-4 rounded-full text-xs font-semibold text-gray-600 hover:text-[#12271E] bg-white hover:bg-gray-50 transition-colors border border-[#E5E7EB]">
                                        &larr; Lihat Pilihan Paket Lain
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        {{-- Lightbox Modal --}}
        <div x-show="lightboxOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/90 backdrop-blur-md flex items-center justify-center p-2 sm:p-4 md:p-6" 
             style="display: none;"
             @keydown.escape.window="lightboxOpen = false">
            
            <div class="relative max-w-[95vw] max-h-[92vh] flex flex-col items-center justify-center" @click.outside="lightboxOpen = false">
                <button type="button" 
                        @click="lightboxOpen = false"
                        class="absolute -top-10 right-0 sm:-top-12 sm:right-0 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition-colors cursor-pointer z-10">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="rounded-2xl overflow-hidden shadow-2xl bg-black/50 border border-white/10 flex items-center justify-center p-1">
                    <img :src="lightboxImage" class="max-w-[95vw] max-h-[90vh] w-auto h-auto object-contain rounded-xl mx-auto" alt="Preview Foto">
                </div>
            </div>
        </div>

    </div>

</x-layouts.main>
