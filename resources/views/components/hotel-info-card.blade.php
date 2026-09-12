@props([
    'hotel' => null,
    'city' => null, // 'makkah' | 'madinah'
    'variant' => 'full', // 'full' | 'compact'
    'name' => null,
    'starRating' => null,
    'distance' => null,
    'landmark' => null,
    'description' => null,
    'mainPhoto' => null,
    'photos' => [],
    'facilities' => [],
    'maxFacilities' => 5,
])

@php
    // Extract data from Hotel model or fallback to props
    $hotelName = $name ?? ($hotel->name ?? 'Nama Hotel Belum Diatur');
    $hotelCity = strtolower($city ?? ($hotel->city ?? 'makkah'));
    $hotelStar = $starRating ?? ($hotel->star_rating ?? '4');
    $hotelDistance = $distance ?? ($hotel->distance_to_haram ?? null);
    $hotelDesc = $description ?? ($hotel->description ?? '');
    
    // Landmark name fallback
    $landmarkDefault = $hotelCity === 'madinah' ? 'Masjid Nabawi' : 'Masjidil Haram';
    $hotelLandmark = $landmark ?? $landmarkDefault;

    // Photos
    $hotelMainPhoto = $mainPhoto;
    if (!$hotelMainPhoto && $hotel) {
        $hotelMainPhoto = $hotel->main_photo ? asset('storage/' . $hotel->main_photo) : null;
    }
    
    $hotelPhotos = !empty($photos) ? $photos : ($hotel ? $hotel->photos : []);

    // Facilities
    $hotelFacilities = !empty($facilities) ? $facilities : ($hotel ? $hotel->facilities->pluck('name')->toArray() : []);
    if ($hotelFacilities instanceof \Illuminate\Support\Collection) {
        $hotelFacilities = $hotelFacilities->pluck('name')->toArray();
    }

    // SVG Icons Helper for Facilities
    $getFacilityIcon = function($facName) {
        $nameLower = strtolower($facName);

        if (str_contains($nameLower, 'ac') || str_contains($nameLower, 'udara')) {
            // Snowflake / AC
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l3 3m-3-3l-3 3m0 12l3 3m0 0l3-3m-9-6h18m-18 0l3-3m-3 3l3 3m12-6l3 3m0 0l-3 3"/></svg>';
        }
        if (str_contains($nameLower, 'wi-fi') || str_contains($nameLower, 'wifi') || str_contains($nameLower, 'internet')) {
            // Wifi icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/></svg>';
        }
        if (str_contains($nameLower, 'lift') || str_contains($nameLower, 'elevator')) {
            // Lift / Elevator icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg>';
        }
        if (str_contains($nameLower, 'keluarga') || str_contains($nameLower, 'family')) {
            // Family / Users icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>';
        }
        if (str_contains($nameLower, 'makan') || str_contains($nameLower, 'restoran') || str_contains($nameLower, 'dining')) {
            // Cutlery / Restaurant icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>';
        }
        if (str_contains($nameLower, 'renang') || str_contains($nameLower, 'pool')) {
            // Swimming / Waves icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>';
        }
        if (str_contains($nameLower, 'gym') || str_contains($nameLower, 'kebugaran') || str_contains($nameLower, 'fitness')) {
            // Dumbbell icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 9h15M4.5 15h15M3 10.5h18M3 13.5h18"/></svg>';
        }
        if (str_contains($nameLower, 'laundry') || str_contains($nameLower, 'cuci')) {
            // Laundry / Sparkles icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>';
        }
        if (str_contains($nameLower, 'musholla') || str_contains($nameLower, 'sholat') || str_contains($nameLower, 'ibadah')) {
            // Mosque dome icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>';
        }
        if (str_contains($nameLower, 'parkir') || str_contains($nameLower, 'parking')) {
            // Parking P icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h6a3 3 0 010 6h-6m0-6v10.5m0-10.5h-1.5"/></svg>';
        }
        if (str_contains($nameLower, '24 jam') || str_contains($nameLower, 'layanan kamar') || str_contains($nameLower, 'room service')) {
            // 24-hour bell icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>';
        }
        if (str_contains($nameLower, 'brankas') || str_contains($nameLower, 'safe') || str_contains($nameLower, 'keamanan')) {
            // Safe / Key lock icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>';
        }
        if (str_contains($nameLower, 'ka\'bah') || str_contains($nameLower, 'kabah') || str_contains($nameLower, 'haram')) {
            // Ka'bah / Holy shrine icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>';
        }
        if (str_contains($nameLower, 'pemandangan') || str_contains($nameLower, 'kota') || str_contains($nameLower, 'view')) {
            // Skyline / View icon
            return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>';
        }

        // Generic Fallback Icon (Check badge)
        return '<svg class="w-3.5 h-3.5 shrink-0 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl sm:rounded-3xl border border-[#E0E7DC] shadow-xs hover:border-[#CCD8C7] transition-all overflow-hidden flex flex-col']) }}
     x-data="{ expanded: false }">
    
    {{-- Header Ribbon Kota --}}
    <div class="px-5 py-2.5 border-b border-[#E0E7DC] flex items-center justify-between {{ $hotelCity === 'makkah' ? 'bg-[#1B3B2B] text-white' : 'bg-[#12271E] text-white' }}">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full {{ $hotelCity === 'makkah' ? 'bg-emerald-400' : 'bg-teal-300' }}"></span>
            <span class="text-[11px] font-bold uppercase tracking-wider">
                Hotel {{ ucfirst($hotelCity) }}
            </span>
        </div>
        <span class="text-[10px] text-white/80 font-medium tracking-tight">
            {{ $hotelCity === 'makkah' ? 'Tanah Suci Makkah' : 'Kota Madinah Al-Munawwarah' }}
        </span>
    </div>

    {{-- Foto Cover / Thumbnail (Jika Tersedia) --}}
    @if($hotelMainPhoto)
    <div class="relative w-full aspect-[16/9] bg-[#EFF3EB] overflow-hidden border-b border-[#E0E7DC] group">
        <img src="{{ $hotelMainPhoto }}" 
             alt="{{ $hotelName }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 pointer-events-none"></div>

        {{-- Badges di Atas Foto --}}
        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center justify-between gap-2">
            {{-- Star Rating Badge (Gold Luxury) --}}
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-xs text-amber-800 text-xs font-bold shadow-xs border border-amber-200">
                <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>Bintang {{ $hotelStar }}</span>
            </span>

            {{-- Landmark / Distance Badge (Distingtif: Ruby / Red Accent PT Zein) --}}
            @if($hotelDistance)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#991B1B]/90 backdrop-blur-xs text-white text-xs font-semibold shadow-xs border border-red-300/30">
                <svg class="w-3.5 h-3.5 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
                <span>{{ $hotelDistance }}m ke {{ $hotelLandmark }}</span>
            </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Konten Utama Kartu --}}
    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
        
        <div>
            {{-- Jika tidak ada foto cover, tampilkan badges di atas judul --}}
            @if(!$hotelMainPhoto)
            <div class="flex flex-wrap items-center gap-2 mb-3">
                {{-- Star Rating Badge --}}
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200/80 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>Bintang {{ $hotelStar }}</span>
                </span>

                {{-- Distance Badge --}}
                @if($hotelDistance)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    <span>{{ $hotelDistance }}m ke {{ $hotelLandmark }}</span>
                </span>
                @endif
            </div>
            @endif

            {{-- Nama Hotel (Judul Utama) --}}
            <h4 class="font-serif font-bold text-base sm:text-lg text-[#12271E] leading-snug">
                {{ $hotelName }}
            </h4>

            {{-- Deskripsi Hotel --}}
            @if($hotelDesc)
            <p class="text-xs sm:text-sm text-[#526057] mt-2 leading-relaxed {{ $variant === 'compact' ? 'line-clamp-2' : '' }}">
                {{ $hotelDesc }}
            </p>
            @endif
        </div>

        {{-- Strip Galeri Foto Tambahan (Hanya di mode Full jika ada foto) --}}
        @if($variant === 'full' && count($hotelPhotos) > 1)
        <div class="pt-3 border-t border-[#E0E7DC]/60">
            <span class="text-[10px] uppercase font-bold text-[#4D5E54] tracking-wider block mb-2">Galeri Foto Hotel</span>
            <div class="grid grid-cols-4 gap-2">
                @foreach(collect($hotelPhotos)->take(4) as $idx => $photo)
                    @php
                        $photoUrl = is_array($photo) ? ($photo['url'] ?? asset('storage/' . ($photo['photo_path'] ?? ''))) : (is_object($photo) ? asset('storage/' . $photo->photo_path) : $photo);
                    @endphp
                    <div class="aspect-square rounded-xl overflow-hidden bg-[#EFF3EB] border border-[#CCD8C7] relative group cursor-pointer hover:opacity-95">
                        <img src="{{ $photoUrl }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="Foto Hotel">
                        @if($idx === 3 && count($hotelPhotos) > 4)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-xs">
                                +{{ count($hotelPhotos) - 3 }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Seksi Fasilitas Hotel (CHIP/TAG TERSTRUKTUR DENGAN IKON) --}}
        @if(!empty($hotelFacilities) && count($hotelFacilities) > 0)
        <div class="pt-3 border-t border-[#E0E7DC]">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] uppercase font-bold text-[#4D5E54] tracking-wider">
                    Fasilitas Pilihan:
                </span>
                @if(count($hotelFacilities) > $maxFacilities)
                <button type="button" 
                        @click="expanded = !expanded" 
                        class="text-[11px] font-semibold text-[#1B3B2B] hover:text-[#12271E] cursor-pointer inline-flex items-center gap-1 transition-colors">
                    <span x-text="expanded ? 'Tampilkan Lebih Sedikit' : '+{{ count($hotelFacilities) - $maxFacilities }} Fasilitas Lainnya'"></span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                @endif
            </div>

            {{-- Container Chips dengan Flex Wrap & Gap Nyaman --}}
            <div class="flex flex-wrap gap-2">
                @foreach($hotelFacilities as $index => $facility)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-[#EFF3EB] text-[#12271E] border border-[#CCD8C7] shadow-2xs hover:bg-[#E0E7DC] hover:border-[#B5C4B0] transition-colors"
                         @if($index >= $maxFacilities) x-show="expanded" x-transition @endif>
                        {!! $getFacilityIcon($facility) !!}
                        <span>{{ $facility }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</div>
