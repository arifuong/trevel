@props(['company' => null])

@php
    $phone = $company['whatsapp'] ?? '6281222222562';
@endphp

<!-- ═══════════════════════════════════════════════════════════════
     HERO SECTION (Foto Penuh & Konten Rata Tengah)
═══════════════════════════════════════════════════════════════ -->
<section id="hero" class="relative bg-[#122B1F] text-white overflow-hidden">
    
    <!-- Gambar Latar dengan Lapisan Gelap Merata -->
    <div class="absolute inset-0 z-0">
        <picture class="w-full h-full">
            <source type="image/webp" 
                    srcset="{{ asset('images/hero-480.webp') }} 480w, {{ asset('images/hero-640.webp') }} 640w, {{ asset('images/hero-1200.webp') }} 1200w, {{ asset('images/hero-1920.webp') }} 1920w" 
                    sizes="100vw">
            <img src="{{ asset('images/hero-480.webp') }}" 
                 alt="Masjidil Haram Makkah" 
                 width="1920" 
                 height="1080" 
                 fetchpriority="high" 
                 decoding="async" 
                 class="w-full h-full object-cover object-center filter brightness-90">
        </picture>
        <!-- Lapisan Gelap Solid -->
        <div class="absolute inset-0 bg-[#0F261F]/65 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-black/30"></div>
    </div>

    <!-- Konten Hero Rata Tengah -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-32 pb-24 sm:pb-32 text-center">
        
        <div class="space-y-6">
            
            <!-- Label Atas Huruf Kapital -->
            <div>
                <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.25em] text-[#A3D9B1]">
                    PERJALANAN IBADAH PENUH KEKHUSYUKAN
                </span>
            </div>

            <!-- Headline Serif Dua Baris -->
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white leading-[1.15] max-w-3xl mx-auto">
                Pengalaman Ibadah Umrah<br class="hidden sm:inline"> yang Khusyuk & Berkesan
            </h1>

            <!-- Sub-headline 2-3 Baris -->
            <p class="text-xs sm:text-sm lg:text-base text-zinc-200 leading-relaxed max-w-xl mx-auto font-normal">
                Penyelenggara Perjalanan Ibadah Umrah (PPIU) & Haji Khusus resmi Kementerian Agama RI dengan bimbingan murni sesuai Sunnah, dan penerbangan langsung tanpa transit.
            </p>

            <!-- Bukti Sosial: Avatar Bertumpuk + Teks Kepercayaan -->
            <div class="pt-2 flex items-center justify-center gap-3">
                <div class="flex -space-x-2 overflow-hidden"></div>
            </div>

            <!-- Dua Tombol Berdampingan -->
            <div class="pt-3 flex flex-row items-center justify-center gap-3">
                
                <!-- Tombol Kiri: Hijau Tua dengan Ikon Panah -->
                <a href="#paket" 
                   class="inline-flex items-center justify-center gap-2 px-6 sm:px-7 py-3 rounded-full text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-sm cursor-pointer border border-white/10">
                    <span>Paket Kami</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>

                <!-- Tombol Kanan: Hitam Polos -->
                <a href="{{ route('profil') }}" 
                   class="inline-flex items-center justify-center px-6 sm:px-7 py-3 rounded-full text-xs font-semibold text-white bg-[#18221E] hover:bg-black transition-colors shadow-sm cursor-pointer border border-white/10">
                    <span>Pelajari Selengkapnya</span>
                </a>

            </div>

        </div>

    </div>

</section>

<!-- ═══════════════════════════════════════════════════════════════
     3 KOLOM FITUR SINGKAT (Langsung di Bawah Hero Tanpa Jarak)
═══════════════════════════════════════════════════════════════ -->
<section class="relative z-20 w-full">
    <div data-reveal class="grid grid-cols-1 md:grid-cols-3 gap-0 w-full">
        
        <!-- Kolom 1: Hijau Tua (Layanan Pendaftaran Mudah) -->
        <div class="bg-[#1B3B2B] text-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between">
            <div class="space-y-3 mb-8">
                <h2 class="font-serif text-lg sm:text-xl font-bold tracking-tight text-white">
                    Layanan Pendaftaran Mudah
                </h2>
                <p class="text-xs text-white/80 leading-relaxed font-normal">
                    Pendaftaran mudah dan transparan didampingi konsultan resmi dari awal hingga paspor dan visa terbit tanpa kendala.
                </p>
            </div>
            <a href="#skema-pendaftaran" class="inline-flex items-center gap-2.5 text-xs font-semibold text-white hover:text-emerald-200 transition-colors group">
                <div class="w-7 h-7 rounded-full border border-white/80 group-hover:border-white group-hover:bg-white/10 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
                <span>Selengkapnya</span>
            </a>
        </div>

        <!-- Kolom 2: Hijau Tua (Kepuasan & Kepastian 100%) -->
        <div class="bg-[#1B3B2B] text-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between border-t md:border-t-0 md:border-l md:border-r border-white/10">
            <div class="space-y-3 mb-8">
                <h2 class="font-serif text-lg sm:text-xl font-bold tracking-tight text-white">
                    Kepuasan & Kepastian 100%
                </h2>
                <p class="text-xs text-white/80 leading-relaxed font-normal">
                    Izin resmi PPIU Kemenag RI, kepastian jadwal penerbangan langsung, serta hotel pelataran ring 1 yang terkonfirmasi.
                </p>
            </div>
            <a href="#legalitas" class="inline-flex items-center gap-2.5 text-xs font-semibold text-white hover:text-emerald-200 transition-colors group">
                <div class="w-7 h-7 rounded-full border border-white/80 group-hover:border-white group-hover:bg-white/10 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
                <span>Selengkapnya</span>
            </a>
        </div>

        <!-- Kolom 3: Sage Krem (Layanan Bintang Lima Jamaah) -->
        <div class="bg-[#EFF3EB] text-[#12271E] p-8 sm:p-10 lg:p-12 flex flex-col justify-between border-t md:border-t-0 border-[#E0E7DC]">
            <div class="space-y-3 mb-8">
                <h2 class="font-serif text-lg sm:text-xl font-bold tracking-tight text-[#12271E]">
                    Layanan Bintang Lima Jamaah
                </h2>
                <p class="text-xs text-[#526057] leading-relaxed font-normal">
                    Akomodasi hotel bintang 4 & 5 ring 1 Masjidil Haram dan Nabawi serta santapan prasmanan menu Nusantara 3x sehari.
                </p>
            </div>
            <a href="#layanan-saudi" class="inline-flex items-center gap-2.5 text-xs font-semibold text-[#1B3B2B] hover:text-[#132E22] transition-colors group">
                <div class="w-7 h-7 rounded-full bg-[#1B3B2B] text-white group-hover:bg-[#132E22] flex items-center justify-center transition-colors shrink-0 shadow-xs">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
                <span>Selengkapnya</span>
            </a>
        </div>

    </div>
</section>
