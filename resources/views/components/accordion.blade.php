@props([
    'title',
    'index' => 0,
])

<div class="border border-[#E0E7DC] rounded-2xl bg-white overflow-hidden transition-all duration-200 shadow-xs">
    
    <!-- Tombol Header Accordion (Buka-Tutup dengan Klik) -->
    <button type="button" 
            @click="activeIndex = (activeIndex === {{ $index }} ? null : {{ $index }})" 
            class="w-full flex items-center justify-between px-6 py-4.5 text-left font-serif font-bold text-sm sm:text-base text-gray-700 bg-white transition-colors duration-200 cursor-pointer hover:bg-[#F9FAF8]"
            :class="{ 'bg-[#F4F7F2] border-b border-[#E0E7DC]': activeIndex === {{ $index }} }"
            :aria-expanded="activeIndex === {{ $index }}">
        
        <!-- Judul Kategori: Berwarna sama seperti teks konten (#374151 / text-gray-700) -->
        <span class="pr-4 leading-snug font-serif font-bold text-sm sm:text-base text-gray-700 block transition-colors"
              style="color: #374151 !important;">
            {{ $title }}
        </span>

        <!-- Ikon Indikator Aktif / Non-Aktif -->
        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors duration-200"
             :class="activeIndex === {{ $index }} ? 'bg-[#1B3B2B] text-white shadow-xs' : 'bg-[#EFF3EB] text-[#1B3B2B]'">
            <svg class="w-4 h-4 transition-transform duration-200" 
                 :class="{ 'rotate-180 text-white': activeIndex === {{ $index }}, 'text-[#1B3B2B]': activeIndex !== {{ $index }} }" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor" 
                 stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </div>
    </button>

    <!-- Konten Accordion (Terbuka Berdasarkan State activeIndex, Tanpa Hover) -->
    <div x-show="activeIndex === {{ $index }}" x-collapse>
        <div class="px-6 py-5 text-xs sm:text-sm text-gray-700 leading-relaxed bg-[#FDFEFC]"
             style="color: #374151;">
            {{ $slot }}
        </div>
    </div>
</div>
