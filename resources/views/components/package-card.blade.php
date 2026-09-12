@props(['package', 'company' => null])

@php
    // Extract days number from duration
    preg_match('/(\d+)/', $package['duration'] ?? '9', $m);
    $days = str_pad($m[1] ?? '9', 2, '0', STR_PAD_LEFT);

    // Hitung kuota terisi / jumlah terjual dari database
    $bookedSeats = isset($package['seats_total'], $package['seats_available']) 
        ? max(0, $package['seats_total'] - $package['seats_available']) 
        : 0;

    $soldDisplay = !empty($package['sold_count']) 
        ? $package['sold_count'] 
        : ($bookedSeats > 0 ? "{$bookedSeats}+ terjual" : '120+ terjual');

    $rating = $package['rating'] ?? '4.9';
    $thumbSrc = !empty($package['thumbnail']) 
        ? $package['thumbnail'] 
        : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=600&q=75';

    // Info ringkas yang sudah ada untuk dibagikan tanpa mengarang data baru
    $infoBits = array_filter([
        $package['duration'] ?? null,
        !empty($package['hotel_makkah']) ? 'Hotel ' . $package['hotel_makkah'] : null,
        !empty($package['airline']) ? 'Maskapai ' . $package['airline'] : null,
        !empty($package['departure_date']) && $package['departure_date'] !== '-' ? 'Jadwal ' . $package['departure_date'] : null,
        !empty($package['price_formatted']) ? 'Mulai ' . $package['price_formatted'] : null,
    ]);
    $shareInfo = !empty($package['summary']) ? $package['summary'] : implode(' • ', $infoBits);
@endphp

{{-- CARD CONTAINER (Vertikal, Background Putih, Tanpa Border Luar, Shadow Sangat Halus & Natural) --}}
<div data-reveal-child class="group bg-white rounded-2xl shadow-[0_4px_25px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_35px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 ease-out flex flex-col justify-between h-full">

    {{-- 1. AREA GAMBAR (Paling Atas, Full Width, Rasio Konsisten, Border-radius Atas Saja) --}}
    <a href="{{ route('paket.detail', $package['slug']) }}" class="relative block w-full aspect-[16/10] overflow-hidden rounded-t-2xl bg-[#EFF3EB]">
        <img src="{{ $thumbSrc }}" 
             alt="{{ $package['name'] }}" 
             width="600"
             height="375"
             loading="lazy"
             decoding="async"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">

        {{-- Pill Tanggal Keberangkatan di Sudut Kiri Bawah Foto (Persis Screenshot Referensi) --}}
        @if(!empty($package['departure_date']) && $package['departure_date'] !== '-')
            <span class="absolute bottom-2.5 left-2.5 bg-black/65 backdrop-blur-xs text-white text-[11px] font-medium px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-xs">
                <svg class="w-3.5 h-3.5 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <span>{{ $package['departure_date'] }}</span>
            </span>
        @endif

        {{-- Status Sold Out jika kuota habis --}}
        @if(!empty($package['is_sold_out']))
            <span class="absolute top-2.5 left-2.5 bg-red-600 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md shadow-xs z-10">
                Sold Out
            </span>
        @endif
    </a>

    {{-- INFORMASI DI BAWAH GAMBAR (Hierarchy & Spacing Persis Screenshot) --}}
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between">

        <div>
            {{-- 1. Rating + Jumlah Terjual --}}
            <div class="flex items-center gap-1.5 text-xs text-[#526057] mb-2">
                <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500 shrink-0" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-bold text-[#12271E]">{{ $rating }}</span>
                <span class="text-zinc-300">•</span>
                <span>{{ $soldDisplay }}</span>
            </div>

            {{-- 2. Nama Paket --}}
            <h3 class="text-base sm:text-lg font-bold text-[#12271E] leading-snug mb-3 line-clamp-2">
                <a href="{{ route('paket.detail', $package['slug']) }}" class="hover:text-[#1B3B2B] transition-colors">
                    {{ $package['name'] }}
                </a>
            </h3>

            {{-- 3. Badge Informasi Paket (Durasi & Maskapai) --}}
            <div class="flex flex-wrap items-center gap-2 mb-3">
                @if(!empty($package['duration']))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#2D4A3A] text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                        </svg>
                        <span>{{ $package['duration'] }}</span>
                    </span>
                @endif

                @if(!empty($package['airline']))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#2D4A3A] text-xs font-medium max-w-full truncate">
                        <svg class="w-3.5 h-3.5 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        <span class="truncate">{{ $package['airline'] }}</span>
                    </span>
                @endif
            </div>

            {{-- 4. Informasi Hotel (List Sederhana) --}}
            <div class="space-y-1.5 mb-2">
                @if(!empty($package['hotel_makkah']))
                    <div class="flex items-center gap-2 text-xs font-semibold text-[#4A5750] truncate">
                        <svg class="w-3.5 h-3.5 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        <span class="truncate tracking-wide">{{ Str::upper($package['hotel_makkah']) }}</span>
                    </div>
                @endif

                @if(!empty($package['hotel_madinah']))
                    <div class="flex items-center gap-2 text-xs font-semibold text-[#4A5750] truncate">
                        <svg class="w-3.5 h-3.5 text-[#1B3B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        <span class="truncate tracking-wide">{{ Str::upper($package['hotel_madinah']) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- BAGIAN BAWAH KARTU (Divider, Harga, Sisa Kuota, Tombol) --}}
        <div class="mt-auto">
            {{-- 5. Divider Tipis --}}
            <div class="border-t border-[#E8EDE5] my-3.5"></div>

            {{-- 6. Harga --}}
            <div>
                @if(!empty($package['old_price']))
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs text-zinc-400 line-through">{{ $package['old_price'] }}</span>
                        @if(!empty($package['savings']) || !empty($package['discount']))
                            <span class="text-[10px] font-bold text-[#1B8344] bg-[#EAF5EC] px-2 py-0.5 rounded-md">
                                {{ !empty($package['savings']) ? (str_starts_with($package['savings'], 'Hemat') ? $package['savings'] : 'Hemat ' . $package['savings']) : $package['discount'] }}
                            </span>
                        @endif
                    </div>
                @endif
                <div class="text-xl sm:text-2xl font-extrabold text-[#1B3B2B] tracking-tight">
                    {{ $package['price_formatted'] }}
                </div>
            </div>

            {{-- 7. Sisa Kuota --}}
            <div class="flex items-center gap-1.5 text-xs text-[#526057] mt-1.5 font-medium">
                <svg class="w-4 h-4 text-[#526057] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span>
                    @if(isset($package['seats_available'], $package['seats_total']) && $package['seats_total'] > 0)
                        Sisa {{ $package['seats_available'] }} dari {{ $package['seats_total'] }} kursi
                    @elseif(!empty($package['is_sold_out']))
                        Kuota Habis (Sold Out)
                    @else
                        Sisa 20 dari 20 kursi
                    @endif
                </span>
            </div>

            {{-- 8. Tombol Detail Program & Tombol Share --}}
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('paket.detail', $package['slug']) }}" 
                   class="flex-1 h-11 px-4 rounded-xl text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#142E21] flex items-center justify-center gap-1.5 transition-all shadow-xs cursor-pointer active:scale-[0.99]">
                    <span>Detail Program</span>
                    <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>

                <x-share-button 
                    :url="route('paket.detail', $package['slug'])"
                    :title="$package['name']"
                    :summary="$shareInfo"
                    variant="card"
                    align="right"
                    placement="top"
                />
            </div>
        </div>

    </div>

</div>
