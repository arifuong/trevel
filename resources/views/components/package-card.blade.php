@props(['package', 'company' => null])

@php
    $phone = $company['whatsapp'] ?? '6281222222562';
    // Extract days number from duration
    preg_match('/(\d+)/', $package['duration'] ?? '9', $m);
    $days = str_pad($m[1] ?? '9', 2, '0', STR_PAD_LEFT);
@endphp

<div data-reveal-child class="qahira-card overflow-hidden flex flex-col justify-between p-4 sm:p-5">

    <div>
        <!-- Kontainer Foto dengan Lencana Hari di Kanan Atas -->
        <div class="relative rounded-xl overflow-hidden mb-5 bg-[#EFF3EB] border border-[#E0E7DC] aspect-[16/10] flex items-center justify-center p-1">
            @if(!empty($package['thumbnail']))
                @php
                    $thumbSrc = $package['thumbnail'];
                @endphp
                <img src="{{ $thumbSrc }}" 
                     alt="{{ $package['name'] }}" 
                     width="600"
                     height="375"
                     loading="lazy"
                     decoding="async"
                     class="w-full h-full object-contain transition-transform duration-300 hover:scale-105">
            @else
                <div class="w-full h-full bg-[#1B3B2B] flex items-center justify-center text-white/50">
                    <span class="text-xs">PT. Zein Internasional</span>
                </div>
            @endif

            @if(!empty($package['is_sold_out']))
                <!-- Lencana Sold Out -->
                <div class="absolute top-3 left-3 bg-red-600 text-white px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-md">
                    Sold Out
                </div>
            @endif

            <!-- Lencana Durasi Hari -->
            <div class="absolute top-3 right-3 bg-[#1B3B2B] text-white px-3 py-1.5 rounded-xl text-center shadow-md border border-white/10">
                <span class="text-base sm:text-lg font-bold leading-none block">{{ $days }}</span>
                <span class="text-[9px] uppercase font-semibold tracking-wider text-emerald-300 block leading-none mt-0.5">Hari</span>
            </div>
        </div>

        <!-- Nama Paket (Serif Tebal) -->
        <h3 class="font-serif text-lg sm:text-xl font-bold text-[#12271E] leading-snug mb-2">
            {{ $package['name'] }}
        </h3>

        <!-- Deskripsi Singkat 2 Baris -->
        <p class="text-xs text-[#526057] leading-relaxed mb-5 line-clamp-2">
            {{ $package['description'] }}
        </p>

        <!-- Daftar Fasilitas -->
        <div class="space-y-2.5 mb-6 pt-3 border-t border-[#E0E7DC]">
            
            <div class="flex items-center gap-2.5 text-xs text-[#374151]">
                <svg class="w-4 h-4 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">Termasuk Paspor & Visa</span>
            </div>

            <div class="flex items-center gap-2.5 text-xs text-[#374151]">
                <svg class="w-4 h-4 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                <span class="truncate font-medium">Makkah - {{ $package['hotel_makkah'] ?? 'Pullman Zamzam (★5)' }}</span>
            </div>

            <div class="flex items-center gap-2.5 text-xs text-[#374151]">
                <svg class="w-4 h-4 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                <span class="truncate font-medium">Madinah - {{ $package['hotel_madinah'] ?? 'Grand Plaza (★4)' }}</span>
            </div>

            <div class="flex items-center gap-2.5 text-xs text-[#374151]">
                <svg class="w-4 h-4 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v1.069m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                <span class="font-medium">Koper 22" & Perlengkapan Umrah</span>
            </div>

            <div class="flex items-center gap-2.5 text-xs text-[#374151]">
                <svg class="w-4 h-4 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                <span class="truncate font-medium">{{ $package['airline'] ?? 'Penerbangan Langsung Saudia / Garuda' }}</span>
            </div>

            <div class="flex items-center gap-2.5 text-xs text-[#374151]">
                <svg class="w-4 h-4 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">3 Kali Makan Menu Nusantara</span>
            </div>

        </div>
    </div>

    <!-- Kotak Bagian Bawah (Mulai Dari + Harga + Tombol Detail Program) -->
    <div class="bg-white rounded-xl p-4 border border-[#E0E7DC] text-center shadow-xs">
        <span class="text-[10px] uppercase tracking-wider text-[#4D5E54] font-semibold block mb-0.5">
            Mulai Dari
        </span>
        <span class="font-serif text-lg sm:text-xl font-bold text-[#12271E] tracking-tight block mb-3">
            {{ $package['price_formatted'] }}
        </span>

        <a href="{{ route('paket.detail', $package['slug']) }}" 
           class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-lg text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-xs">
            Detail Program
        </a>
    </div>

</div>
