<x-layouts.main :title="'Galeri Dokumentasi — PT. Zein Internasional'" :company="$company">

    {{-- ═══════════════════════════════════════════════════════════════
         1. HEADER HALAMAN (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-[#E0E7DC] pt-8 pb-14 sm:pb-20" x-data="{ cat: 'all' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['title' => 'Galeri Foto & Video']]" />

            <div data-reveal class="mt-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                        GALERI & DOKUMENTASI
                    </span>
                    <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight">
                        Momen Ibadah & Kebersamaan Jamaah
                    </h1>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Kumpulan foto dan dokumentasi kegiatan manasik, pelaksanaan ibadah di Makkah & Madinah, hingga napak tilas sejarah Islam.
                    </p>
                </div>

                {{-- Tab Filter Kategori --}}
                <div class="flex items-center gap-1.5 bg-[#EFF3EB] p-1.5 rounded-full border border-[#E0E7DC] self-start md:self-auto shrink-0">
                    <button type="button" 
                            @click="cat = 'all'"
                            :class="cat === 'all' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                            class="px-3.5 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                        Semua
                    </button>
                    <button type="button" 
                            @click="cat = 'Umrah'"
                            :class="cat === 'Umrah' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                            class="px-3.5 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                        Umrah
                    </button>
                    <button type="button" 
                            @click="cat = 'Haji'"
                            :class="cat === 'Haji' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                            class="px-3.5 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                        Haji Khusus
                    </button>
                    <button type="button" 
                            @click="cat = 'Manasik'"
                            :class="cat === 'Manasik' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                            class="px-3.5 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                        Manasik
                    </button>
                </div>
            </div>

            {{-- Baris Info Meta --}}
            <div data-reveal class="mt-8 pt-4 border-t border-[#E0E7DC] flex flex-wrap items-center justify-between text-xs text-[#4D5E54]">
                <span class="flex items-center gap-2 font-bold text-[#12271E]">
                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Dokumentasi Resmi Keberangkatan PT. Zein Internasional
                </span>
                <span>Klik foto untuk melihat ukuran penuh</span>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. GRID FOTO (bg-[#EFF3EB])
        ═══════════════════════════════════════════════════════════ --}}
        <div class="bg-[#EFF3EB] py-14 sm:py-20 mt-6 border-t border-[#E0E7DC]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                    @foreach($galleries as $item)
                        <div x-show="cat === 'all' || cat === '{{ $item['category'] ?? '' }}'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <x-gallery-item :item="$item" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         3. VIDEO DOKUMENTASI (bg-[#122B1F])
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-20 bg-[#122B1F] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <div data-reveal class="lg:col-span-5 space-y-4">
                    <span class="text-[10px] sm:text-[11px] font-bold text-emerald-300 uppercase tracking-[0.25em] block">
                        RANGKUMAN VIDEO DOKUMENTASI
                    </span>
                    <h2 class="font-serif text-2xl sm:text-4xl font-bold leading-tight">
                        Saksikan Pengalaman Jamaah Kami di Tanah Suci
                    </h2>
                    <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed">
                        Lihat bagaimana kami membimbing para jamaah dari prosesi manasik, perjalanan di pesawat, fasilitas hotel, hingga pelaksanaan tawaf dan sa'i.
                    </p>
                    <div class="pt-2">
                        <a href="https://www.youtube.com/@ZEINTV7" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-300 hover:underline">
                            <span>Kunjungi Saluran YouTube Resmi (@ZEINTV7) &rarr;</span>
                        </a>
                    </div>
                </div>

                <div data-reveal class="lg:col-span-7">
                    <div class="aspect-video rounded-3xl overflow-hidden border border-white/10 relative bg-black/40 cursor-pointer group"
                         onclick="openLightbox('https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format,webp&fit=crop&w=1200&q=80', 'Dokumentasi Video Perjalanan PT. Zein Internasional')">
                        <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format,webp&fit=crop&w=1200&q=80" 
                             alt="Thumbnail Video" 
                             width="1200"
                             height="675"
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform border border-white/30">
                                <svg class="w-6 h-6 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.main>
