@props(['item'])

@php
    $isModel = $item instanceof \App\Models\Gallery;

    $title = $isModel ? $item->title : ($item['title'] ?? '');
    $caption = $isModel ? $item->caption : ($item['caption'] ?? null);
    $type = $isModel ? $item->type : ($item['type'] ?? 'photo');
    $imgSrc = $isModel ? $item->thumbnail_url : ($item['thumb'] ?? ($item['url'] ?? ''));
    $fullUrl = $isModel ? $item->thumbnail_url : ($item['url'] ?? $imgSrc);
    $embedUrl = $isModel ? $item->embed_url : ($item['embed_url'] ?? null);

    // If type is video or embedUrl exists
    $isVideo = ($type === 'video') || !empty($embedUrl);

    $clickHandler = $isVideo && $embedUrl 
        ? "openVideoModal('{$embedUrl}', '" . addslashes($title) . "')"
        : "openLightbox('{$fullUrl}', '" . addslashes($title) . "')";
@endphp

<div data-reveal-child 
     class="h-full flex flex-col justify-between bg-white rounded-2xl overflow-hidden border border-[#E5E7EB] hover:border-[#1B3B2B]/30 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 cursor-pointer group"
     onclick="{{ $clickHandler }}">
    
    <!-- 1. Kontainer Gambar (Rasio Tetap 4:3 dengan object-cover & Subtle Zoom Overlay) -->
    <div class="aspect-[4/3] w-full overflow-hidden bg-zinc-900 relative">
        <img src="{{ $imgSrc }}" 
             alt="{{ $title ?: 'Dokumentasi Jamaah PT. Zein Internasional' }}" 
             width="600"
             height="450"
             loading="lazy"
             decoding="async"
             class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-300 ease-out">
        
        <!-- Overlay Tipis saat Hover (Transisi 300ms) -->
        <div class="absolute inset-0 bg-black/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

        @if($isVideo)
            <!-- Tag Badge Video -->
            <div class="absolute top-3 left-3 z-10 flex items-center gap-1.5">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-600/90 text-white text-[10px] font-bold tracking-wider uppercase shadow-md border border-white/20 backdrop-blur-xs">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span>Video</span>
                </span>
            </div>

            <!-- Ikon Play Video di Tengah Foto -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#1B3B2B]/90 text-white shadow-xl flex items-center justify-center transform group-hover:scale-105 transition-all duration-300 border-2 border-white/60 backdrop-blur-xs">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-current ml-0.5" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </div>
        @else
            <!-- Ikon Zoom Interaktif di Atas Foto (Overlay saat Hover, elegan & rapi) -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-11 h-11 rounded-full bg-white/90 text-[#1B3B2B] shadow-lg flex items-center justify-center transform scale-90 group-hover:scale-100 opacity-0 group-hover:opacity-100 transition-all duration-300 border border-white/50 backdrop-blur-xs">
                    <svg class="w-4 h-4 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                    </svg>
                </div>
            </div>
        @endif
    </div>

    <!-- 2. Area Keterangan Foto / Video -->
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between bg-white border-t border-[#E5E7EB]">
        <div>
            <h3 class="font-sans text-sm sm:text-base font-bold text-[#12271E] group-hover:text-[#1B3B2B] transition-colors line-clamp-2 leading-snug">
                {{ $title }}
            </h3>
            @if(!empty($caption))
                <p class="text-xs text-[#526057] line-clamp-2 mt-1.5 leading-relaxed">
                    {{ $caption }}
                </p>
            @endif
        </div>

        <div class="mt-3 pt-2.5 border-t border-[#F3F4F6] flex items-center justify-between text-xs text-[#526057]">
            @if($isVideo)
                <span class="font-semibold text-red-600 flex items-center gap-1.5">
                    <svg class="w-4 h-4 fill-current text-red-600" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <span>Tonton Video</span>
                </span>
            @else
                <span class="font-medium text-[#1B3B2B] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Lihat Foto Penuh</span>
                </span>
            @endif
            <span class="text-[#1B3B2B] font-bold transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
        </div>
    </div>

</div>
