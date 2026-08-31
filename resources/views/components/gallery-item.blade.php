@props(['item'])

<div data-reveal-child class="bg-[#EFF3EB] rounded-2xl overflow-hidden border border-[#E0E7DC] cursor-pointer group flex flex-col transition-all duration-200 hover:-translate-y-1 hover:shadow-md"
     onclick="openLightbox('{{ $item['url'] ?? $item['thumb'] }}', '{{ addslashes($item['title'] ?? '') }}')">
    
    <!-- Kontainer Gambar -->
    <div class="aspect-[4/3] w-full overflow-hidden bg-[#EFF3EB] relative flex items-center justify-center p-1">
        @php
            $imgSrc = $item['thumb'] ?? $item['url'];
        @endphp
        <img src="{{ $imgSrc }}" 
             alt="{{ $item['title'] ?? 'Dokumentasi Jamaah PT. Zein Internasional' }}" 
             width="600"
             height="450"
             loading="lazy"
             decoding="async"
             class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
        
        <!-- Tag Kategori -->
        <div class="absolute top-3 left-3">
            <span class="px-2.5 py-1 rounded-lg bg-white/90 text-[#1B3B2B] text-[11px] font-semibold tracking-wide shadow-xs">
                {{ $item['category'] ?? 'Dokumentasi' }}
            </span>
        </div>
    </div>

    <!-- Area Keterangan Foto -->
    <div class="p-4 sm:p-5 flex items-start justify-between gap-3 bg-white border-t border-[#E0E7DC]">
        <div class="min-w-0">
            <h3 class="font-serif text-sm font-bold text-[#12271E] group-hover:text-[#1B3B2B] transition-colors line-clamp-2 leading-snug">
                {{ $item['title'] }}
            </h3>
            <span class="text-[11px] text-[#4D5E54] mt-1 block">Klik untuk memperbesar</span>
        </div>
        <!-- Ikon Zoom Bulat -->
        <div class="shrink-0 mt-0.5 w-8 h-8 rounded-full bg-[#EFF3EB] group-hover:bg-[#1B3B2B] group-hover:text-white flex items-center justify-center transition-colors text-[#1B3B2B]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
            </svg>
        </div>
    </div>
</div>
