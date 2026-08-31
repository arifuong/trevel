<x-layouts.main :title="$package['name'] . ' — PT. Zein Internasional'" :company="$company">

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
        <div class="bg-white border-b border-[#E0E7DC] pt-8 pb-12 sm:pb-16">
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

                    <div class="text-left md:text-right shrink-0">
                        <span class="text-[10px] text-[#4D5E54] uppercase tracking-wider font-bold block mb-0.5">Mulai Dari</span>
                        <span class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#1B3B2B] tracking-tight block">
                            <span x-text="activeVariant ? activeVariant.lowest_price_formatted : '{{ $package['price_formatted'] }}'"></span>
                        </span>
                        <span class="text-[11px] text-[#4D5E54] block mt-0.5">Per Jamaah (Sudah Termasuk Fasilitas)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
             2. KONTEN UTAMA (2 Kolom)
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-16 sm:py-24 bg-[#EFF3EB]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                    {{-- Kolom Kiri (8 Kolom) --}}
                    <div class="lg:col-span-8 space-y-10">

                        {{-- Foto Utama Paket --}}
                        <div data-reveal class="relative rounded-3xl overflow-hidden border border-[#E0E7DC] bg-[#EFF3EB] shadow-md cursor-pointer group flex items-center justify-center"
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
                        <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs">
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
                            <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs space-y-6">
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
                                                class="text-left p-5 rounded-2xl border-2 transition-all cursor-pointer relative"
                                                :class="activeIndex === idx ? 'border-[#1B3B2B] bg-[#EFF3EB] shadow-sm' : 'border-[#E0E7DC] bg-white hover:border-[#CCD8C7] hover:bg-[#F8FAF7]'">
                                            
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <span class="font-bold text-sm sm:text-base text-[#12271E]" x-text="variant.name"></span>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                                      :class="variant.is_sold_out ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-800'"
                                                      x-text="variant.status_label"></span>
                                            </div>

                                            <div class="text-[11px] text-[#526057] mb-1">Mulai dari</div>
                                            <div class="font-serif text-base sm:text-lg font-bold text-[#1B3B2B]" x-text="variant.lowest_price_formatted"></div>

                                            <div class="mt-3 pt-3 border-t border-[#E0E7DC]/60 flex items-center justify-between text-[11px]">
                                                <span class="text-[#526057]" x-text="variant.quota + ' Kursi'"></span>
                                                <span class="font-semibold text-[#1B3B2B] flex items-center gap-1">
                                                    <span x-text="activeIndex === idx ? 'Aktif Dipilih' : 'Lihat Detail'"></span>
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                                </span>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                {{-- Detail Varian Terpilih --}}
                                <template x-if="activeVariant">
                                    <div class="pt-6 border-t border-[#E0E7DC] space-y-8">
                                        
                                        {{-- Header Varian --}}
                                        <div class="bg-[#EFF3EB] p-5 sm:p-6 rounded-2xl border border-[#CCD8C7]">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                <div>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] block">Varian Terpilih</span>
                                                    <h3 class="text-lg sm:text-xl font-bold text-[#12271E]" x-text="'Sub-Paket: ' + activeVariant.name"></h3>
                                                </div>
                                                <div class="text-left sm:text-right">
                                                    <span class="text-[10px] text-[#526057] uppercase font-bold block">Ketersediaan</span>
                                                    <span class="font-bold text-xs px-2.5 py-1 rounded-full bg-white border border-[#CCD8C7] inline-block mt-0.5 text-[#1B3B2B]" x-text="activeVariant.quota + ' Kursi Tersisa'"></span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-[#526057] mt-3 leading-relaxed" x-show="activeVariant.description" x-text="activeVariant.description"></p>
                                        </div>

                                        {{-- 1. Pricing Matrix Table --}}
                                        <div class="space-y-3">
                                            <h4 class="font-serif font-bold text-base text-[#12271E] flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                                                Pricing Matrix (Harga Per Tipe Kamar)
                                            </h4>
                                            
                                            <template x-if="activeVariant.prices && activeVariant.prices.filter(p => p.is_active).length > 0">
                                                <div class="overflow-x-auto rounded-2xl border border-[#E0E7DC]">
                                                    <table class="w-full text-left text-xs sm:text-sm">
                                                        <thead class="bg-[#EFF3EB] text-[#12271E] font-bold text-[11px] uppercase tracking-wider border-b border-[#E0E7DC]">
                                                            <tr>
                                                                <th class="py-3 px-4 sm:px-6">Tipe Kamar</th>
                                                                <th class="py-3 px-4 text-right">Harga Normal</th>
                                                                <th class="py-3 px-4 text-right">Harga Promo</th>
                                                                <th class="py-3 px-4 text-right">Harga Berlaku</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-[#E0E7DC] bg-white">
                                                            <template x-for="p in activeVariant.prices.filter(p => p.is_active)" :key="p.id">
                                                                <tr class="hover:bg-[#F8FAF7]">
                                                                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-[#12271E]" x-text="p.room_label"></td>
                                                                    <td class="py-3.5 px-4 text-right font-mono" :class="p.promo_price ? 'line-through text-[#526057]' : 'font-bold text-[#12271E]'" x-text="p.normal_price_formatted"></td>
                                                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-700 font-bold" x-text="p.promo_price_formatted || '-'"></td>
                                                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-base text-[#1B3B2B]" x-text="p.effective_price_formatted"></td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>

                                            <template x-if="!activeVariant.prices || activeVariant.prices.filter(p => p.is_active).length === 0">
                                                <div class="p-4 rounded-xl bg-[#EFF3EB] border border-[#E0E7DC] text-center text-xs text-[#526057]">
                                                    Pilihan harga kamar untuk sub-paket ini belum tersedia.
                                                </div>
                                            </template>
                                        </div>

                                        {{-- 2. Penerbangan / Maskapai --}}
                                        <div class="space-y-3" x-show="activeVariant.airline_departure || activeVariant.airline_return">
                                            <h4 class="font-serif font-bold text-base text-[#12271E] flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                                                Penerbangan & Maskapai
                                            </h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div class="bg-[#EFF3EB] rounded-2xl p-4 border border-[#E0E7DC] flex items-center gap-3">
                                                    <template x-if="activeVariant.airline_departure_logo">
                                                        <img :src="activeVariant.airline_departure_logo" alt="Airline Departure" class="h-8 w-auto max-w-[80px] object-contain shrink-0">
                                                    </template>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Penerbangan Berangkat</span>
                                                        <span class="font-semibold text-xs sm:text-sm text-[#12271E]" x-text="activeVariant.airline_departure || 'Sesuai Jadwal'"></span>
                                                    </div>
                                                </div>
                                                <div class="bg-[#EFF3EB] rounded-2xl p-4 border border-[#E0E7DC] flex items-center gap-3">
                                                    <template x-if="activeVariant.airline_return_logo">
                                                        <img :src="activeVariant.airline_return_logo" alt="Airline Return" class="h-8 w-auto max-w-[80px] object-contain shrink-0">
                                                    </template>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Penerbangan Pulang</span>
                                                        <span class="font-semibold text-xs sm:text-sm text-[#12271E]" x-text="activeVariant.airline_return || 'Sesuai Jadwal'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 3. Akomodasi Hotel Makkah & Madinah --}}
                                        <div class="space-y-4">
                                            <h4 class="font-serif font-bold text-base text-[#12271E] flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                                                Akomodasi Hotel
                                            </h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                {{-- Hotel Makkah --}}
                                                <div class="bg-[#EFF3EB] rounded-2xl p-5 border border-[#E0E7DC] space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[10px] uppercase font-bold text-[#1B3B2B]">Hotel Makkah</span>
                                                        <span class="text-xs font-bold text-[#C2A264]" x-text="activeVariant.hotel_makkah_star || 'Bintang 4/5'"></span>
                                                    </div>
                                                    <h5 class="font-bold text-sm sm:text-base text-[#12271E]" x-text="activeVariant.hotel_makkah_name || 'Hotel Setaraf'"></h5>
                                                    <p class="text-xs text-[#526057]" x-show="activeVariant.hotel_makkah_description" x-text="activeVariant.hotel_makkah_description"></p>
                                                    
                                                    {{-- Foto Hotel Makkah --}}
                                                    <template x-if="activeVariant.hotel_makkah_photos && activeVariant.hotel_makkah_photos.length > 0">
                                                        <div class="pt-2">
                                                            <div class="columns-2 sm:columns-3 gap-2.5 space-y-2.5">
                                                                <template x-for="(hp, hIdx) in activeVariant.hotel_makkah_photos" :key="hIdx">
                                                                    <div class="break-inside-avoid rounded-xl overflow-hidden border border-[#CCD8C7] bg-white shadow-2xs cursor-pointer hover:opacity-90 hover:shadow-md transition-all group"
                                                                         @click="openLightbox(hp.url)">
                                                                        <img :src="hp.url" class="block w-full h-auto rounded-xl transition-transform duration-300 group-hover:scale-[1.02]" :alt="hp.category || 'Foto Hotel Makkah'">
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- Fasilitas Makkah --}}
                                                    <template x-if="activeVariant.makkah_facilities && activeVariant.makkah_facilities.length > 0">
                                                        <div class="pt-2 mt-2 border-t border-[#E0E7DC]">
                                                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-1.5">Fasilitas Hotel:</span>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                <template x-for="fac in activeVariant.makkah_facilities" :key="fac">
                                                                    <span class="text-[11px] bg-white px-2.5 py-0.5 rounded-lg border border-[#CCD8C7] text-[#12271E] font-medium" x-text="fac"></span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>

                                                {{-- Hotel Madinah --}}
                                                <div class="bg-[#EFF3EB] rounded-2xl p-5 border border-[#E0E7DC] space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[10px] uppercase font-bold text-[#1B3B2B]">Hotel Madinah</span>
                                                        <span class="text-xs font-bold text-[#C2A264]" x-text="activeVariant.hotel_madinah_star || 'Bintang 4/5'"></span>
                                                    </div>
                                                    <h5 class="font-bold text-sm sm:text-base text-[#12271E]" x-text="activeVariant.hotel_madinah_name || 'Hotel Setaraf'"></h5>
                                                    <p class="text-xs text-[#526057]" x-show="activeVariant.hotel_madinah_description" x-text="activeVariant.hotel_madinah_description"></p>
                                                    
                                                    {{-- Foto Hotel Madinah --}}
                                                    <template x-if="activeVariant.hotel_madinah_photos && activeVariant.hotel_madinah_photos.length > 0">
                                                        <div class="pt-2">
                                                            <div class="columns-2 sm:columns-3 gap-2.5 space-y-2.5">
                                                                <template x-for="(hp, hIdx) in activeVariant.hotel_madinah_photos" :key="hIdx">
                                                                    <div class="break-inside-avoid rounded-xl overflow-hidden border border-[#CCD8C7] bg-white shadow-2xs cursor-pointer hover:opacity-90 hover:shadow-md transition-all group"
                                                                         @click="openLightbox(hp.url)">
                                                                        <img :src="hp.url" class="block w-full h-auto rounded-xl transition-transform duration-300 group-hover:scale-[1.02]" :alt="hp.category || 'Foto Hotel Madinah'">
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- Fasilitas Madinah --}}
                                                    <template x-if="activeVariant.madinah_facilities && activeVariant.madinah_facilities.length > 0">
                                                        <div class="pt-2 mt-2 border-t border-[#E0E7DC]">
                                                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-1.5">Fasilitas Hotel:</span>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                <template x-for="fac in activeVariant.madinah_facilities" :key="fac">
                                                                    <span class="text-[11px] bg-white px-2.5 py-0.5 rounded-lg border border-[#CCD8C7] text-[#12271E] font-medium" x-text="fac"></span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 4. Biaya Sudah Termasuk & Belum Termasuk --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-white rounded-2xl p-6 border border-[#E0E7DC] shadow-2xs space-y-3">
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

                                            <div class="bg-white rounded-2xl p-6 border border-[#E0E7DC] shadow-2xs space-y-3">
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

                        {{-- Rencana Perjalanan Itinerary --}}
                        @if(!empty($package['itinerary']) && count($package['itinerary']) > 0)
                            <div data-reveal class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-xs">
                                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                                    RENCANA PERJALANAN
                                </span>
                                <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#12271E] mb-8">
                                    Rencana Perjalanan Ibadah
                                </h2>

                                <div class="relative">
                                    <div class="absolute left-6 top-0 bottom-0 w-px bg-[#E0E7DC]"></div>

                                    <div class="space-y-6">
                                        @foreach($package['itinerary'] as $itin)
                                            <div data-reveal-child class="relative flex gap-5 items-start">
                                                <div class="relative z-10 shrink-0 w-12 h-12 rounded-full bg-[#1B3B2B] text-white flex flex-col items-center justify-center shadow-md">
                                                    <span class="text-xs font-bold leading-none">{{ preg_replace('/[^0-9]/', '', $itin['day']) ?: $itin['day'] }}</span>
                                                    <span class="text-[8px] uppercase font-semibold leading-none text-emerald-300 mt-0.5">Hari</span>
                                                </div>

                                                <div class="bg-[#EFF3EB] rounded-2xl p-5 border border-[#E0E7DC] flex-1">
                                                    <h4 class="font-serif font-bold text-sm sm:text-base text-[#12271E] mb-2">
                                                        {{ $itin['title'] }}
                                                    </h4>
                                                    @if(!empty($itin['desc']))
                                                        <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                                                            {{ $itin['desc'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Kolom Kanan: Kartu Booking Sticky (4 Kolom) --}}
                    <div class="lg:col-span-4 lg:sticky lg:top-36 space-y-6">
                        
                        <div data-reveal class="bg-white rounded-3xl p-7 sm:p-8 border border-[#E0E7DC] shadow-lg">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">
                                <span x-text="activeVariant ? 'Biaya ' + activeVariant.name : 'Biaya Paket Ibadah'"></span>
                            </span>
                            <span class="font-serif text-2xl sm:text-3xl font-bold text-[#1B3B2B] tracking-tight block mb-4">
                                <span x-text="activeVariant ? activeVariant.lowest_price_formatted : '{{ $package['price_formatted'] }}'"></span>
                            </span>

                            <div class="space-y-3 pb-6 border-b border-[#E0E7DC] text-xs text-[#526057]">
                                <div class="flex justify-between">
                                    <span class="text-[#4D5E54]">Durasi</span>
                                    <span class="font-bold text-[#12271E]">{{ $package['duration'] }}</span>
                                </div>
                                <div class="flex justify-between" x-show="activeVariant">
                                    <span class="text-[#4D5E54]">Sub-Paket</span>
                                    <span class="font-bold text-[#12271E]" x-text="activeVariant ? activeVariant.name : '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#4D5E54]">Hotel Makkah</span>
                                    <span class="font-bold text-[#12271E]" x-text="activeVariant ? (activeVariant.hotel_makkah_name || 'Bintang 5 Ring 1') : '{{ $package['hotel_makkah'] ?? '-' }}'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#4D5E54]">Hotel Madinah</span>
                                    <span class="font-bold text-[#12271E]" x-text="activeVariant ? (activeVariant.hotel_madinah_name || 'Bintang 4/5 Ring 1') : '{{ $package['hotel_madinah'] ?? '-' }}'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#4D5E54]">Maskapai</span>
                                    <span class="font-bold text-[#12271E]" x-text="activeVariant ? (activeVariant.airline_departure || 'Saudia Airlines') : '{{ $package['airline'] }}'"></span>
                                </div>
                            </div>

                            <div class="pt-6 space-y-3">
                                @if(!empty($package['is_sold_out']))
                                    <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-center space-y-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-600 text-white">
                                            Sold Out
                                        </span>
                                        <p class="text-xs font-semibold text-red-800">Kuota Kursi Telah Habis</p>
                                    </div>
                                    <a href="{{ route('paket') }}"
                                       class="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-sm">
                                        Lihat Paket yang Masih Tersedia &rarr;
                                    </a>
                                @else
                                    @auth
                                        <a :href="'{{ route('jamaah.registration.create') }}?package_id={{ $package['id'] }}' + (activeVariant ? '&variant_id=' + activeVariant.id : '')"
                                           class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                            <span x-text="activeVariant ? 'Daftar ' + activeVariant.name + ' Sekarang' : 'Daftar Paket Ini Sekarang'"></span>
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}"
                                           class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                            <span>Daftar Sekarang Online</span>
                                        </a>
                                    @endauth

                                    <a :href="'https://wa.me/{{ $whatsapp }}?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20paket%20{{ urlencode($package['name']) }}' + (activeVariant ? '%20varian%20' + encodeURIComponent(activeVariant.name) : '')"
                                       target="_blank"
                                       class="w-full inline-flex items-center justify-center py-3 px-4 rounded-full text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors border border-[#E0E7DC]">
                                        Pesan Kursi via WhatsApp
                                    </a>
                                    <a href="{{ route('paket') }}"
                                       class="w-full inline-flex items-center justify-center py-3 px-4 rounded-full text-xs font-semibold text-[#12271E] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors">
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
