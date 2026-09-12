<x-layouts.main :title="'Galeri Dokumentasi — PT. Zein Internasional'" :company="$company">

    {{-- ═══════════════════════════════════════════════════════════════
         1. HEADER HALAMAN (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white" x-data="{ mediaType: 'all' }">
        <div class="border-b border-[#E5E7EB] pt-8 pb-12 sm:pb-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumb :items="[['title' => 'Galeri Foto & Video']]" />

                <div data-reveal class="mt-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="max-w-2xl space-y-3">
                        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight">
                            Momen Ibadah & Kebersamaan Jamaah
                        </h1>
                        <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                            Kumpulan foto dan dokumentasi kegiatan manasik, pelaksanaan ibadah di Makkah & Madinah, hingga napak tilas sejarah Islam.
                        </p>
                    </div>

                    {{-- Tab Filter Tipe Media (Semua / Foto / Video) --}}
                    <div class="flex items-center gap-1.5 bg-[#F9FAF8] p-1.5 rounded-2xl border border-[#E5E7EB] self-start md:self-auto shrink-0 shadow-2xs">
                        <button type="button" 
                                @click="mediaType = 'all'"
                                :class="mediaType === 'all' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                                class="px-4 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                            Semua
                        </button>
                        <button type="button" 
                                @click="mediaType = 'photo'"
                                :class="mediaType === 'photo' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                                class="px-4 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                            Foto Saja
                        </button>
                        <button type="button" 
                                @click="mediaType = 'video'"
                                :class="mediaType === 'video' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'text-[#526057] hover:text-[#12271E] font-semibold'"
                                class="px-4 py-1.5 text-xs rounded-full transition-all cursor-pointer">
                            Video Saja
                        </button>
                    </div>
                </div>

                {{-- Baris Info Meta --}}
                <div data-reveal class="mt-8 pt-4 border-t border-[#E5E7EB] flex flex-wrap items-center justify-between text-xs text-[#526057] gap-2">
                    <span class="flex items-center gap-2 font-semibold text-[#12271E]">
                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Dokumentasi Resmi Keberangkatan PT. Zein Internasional
                    </span>
                    <span class="text-gray-500">Klik foto untuk melihat ukuran penuh / video untuk memutar</span>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. GRID FOTO & VIDEO (bg-white)
        ═══════════════════════════════════════════════════════════ --}}
        <div class="bg-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                    @foreach($galleries as $item)
                        @php
                            $itemType = ($item instanceof \App\Models\Gallery)
                                ? $item->type
                                : ($item['type'] ?? 'photo');
                        @endphp
                        <div class="h-full"
                             x-show="mediaType === 'all' || mediaType === '{{ $itemType }}'"
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

</x-layouts.main>

