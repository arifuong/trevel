@props(['company' => null])

@php
    $phone = $company['whatsapp'] ?? '6281222222562';
    $phoneCall = $company['phone_call'] ?? '+6282121483337';
    $displayPhone = $company['phone'] ?? '+62821 2148 3337';
    $displayWa = $company['whatsapp_formatted'] ?? '+62812 2222 2562';
    $address = $company['address'] ?? 'Jl. Cihanjuang Kp. Karangsari No.15, Parongpong Bandung Barat 40559';
    $mapsUrl = $company['maps_url'] ?? 'https://maps.app.goo.gl/mbTWxdHMtDLWKu9k7';
    $fbUrl = $company['social_media']['facebook'] ?? 'https://id-id.facebook.com/PTZeinInternasional/';
    $ytUrl = $company['social_media']['youtube'] ?? 'https://www.youtube.com/@ZEINTV7';
    $igUrl = $company['social_media']['instagram'] ?? 'https://www.instagram.com/zeintour_official/';
@endphp

<footer class="bg-[#122B1F] text-white">
    
    <!-- Konten Footer Utama -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-12">
            
            <!-- Kolom 1: Profil Brand (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <img src="{{ asset('images/logo-zein-white.webp') }}" 
                     alt="PT. Zein Internasional" 
                     width="160" 
                     height="66" 
                     loading="lazy" 
                     decoding="async" 
                     class="h-10 sm:h-11 w-auto object-contain">
                <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed max-w-sm font-normal">
                    Penyelenggara Perjalanan Ibadah Umrah (PPIU) & Haji Khusus resmi Kementerian Agama RI. Bimbingan Sunnah murni, fasilitas hotel pelataran, dan komitmen 100% kepastian keberangkatan.
                </p>
                <div class="pt-2 space-y-2.5 text-xs text-zinc-300">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        <div>
                            <span class="text-white/60 block text-[10px] uppercase tracking-wider font-semibold">INFORMASI DAN PENDAFTARAN</span>
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-300 transition-colors leading-relaxed block">
                                {{ $address }}
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <span>Call:</span>
                        <a href="tel:{{ $phoneCall }}" class="hover:text-white transition-colors font-medium">
                            {{ $displayPhone }}
                        </a>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.99.54 1.761.819 2.796.819 3.18 0 5.767-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm3.39 8.175c-.144.405-.837.774-1.17.824-.312.045-.634.072-1.025-.054-.374-.122-.857-.282-1.488-.564-1.391-.621-2.296-2.032-2.366-2.126-.07-.093-.564-.75-.564-1.429 0-.678.354-1.011.48-1.144.126-.134.275-.168.367-.168.092 0 .184.001.265.006.085.005.199-.033.31.235.115.277.392.955.426 1.025.034.07.057.151.011.242-.045.092-.068.149-.136.228-.068.079-.143.176-.205.236-.068.067-.14.14-.06.277.08.138.356.587.764.951.526.468.97.613 1.108.682.138.069.219.058.3-.035.08-.093.344-.402.436-.54.092-.138.184-.115.31-.069.126.046.804.379.942.448.138.069.23.103.264.161.034.057.034.333-.11.738z"/></svg>
                        <span>WA:</span>
                        <a href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-300 transition-colors font-medium">
                            {{ $displayWa }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Navigasi (2.5 cols) -->
            <div class="lg:col-span-2">
                <h3 class="font-serif text-sm font-bold tracking-wider text-emerald-300 mb-4">Navigasi</h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li><a href="{{ route('home') }}" class="text-zinc-300 hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="{{ route('profil') }}" class="text-zinc-300 hover:text-white transition-colors">Profil Kami</a></li>
                    <li><a href="{{ route('legalitas') }}" class="text-zinc-300 hover:text-white transition-colors">Legalitas Resmi</a></li>
                    <li><a href="{{ route('paket') }}" class="text-zinc-300 hover:text-white transition-colors">Katalog Paket</a></li>
                    <li><a href="{{ route('galeri') }}" class="text-zinc-300 hover:text-white transition-colors">Galeri Foto</a></li>
                    <li><a href="{{ route('kontak') }}" class="text-zinc-300 hover:text-white transition-colors">Kontak Kami</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Layanan & Program (2.5 cols) -->
            <div class="lg:col-span-2">
                <h3 class="font-serif text-sm font-bold tracking-wider text-emerald-300 mb-4">Program</h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li><a href="{{ route('paket') }}" class="text-zinc-300 hover:text-white transition-colors">Umrah Reguler 9 Hari</a></li>
                    <li><a href="{{ route('paket') }}" class="text-zinc-300 hover:text-white transition-colors">Umrah VIP Ramadhan</a></li>
                    <li><a href="{{ route('paket') }}" class="text-zinc-300 hover:text-white transition-colors">Haji Khusus / Furoda</a></li>
                    <li><a href="{{ route('paket') }}" class="text-zinc-300 hover:text-white transition-colors">Umrah Plus Wisata</a></li>
                    <li><a href="#skema-pendaftaran" class="text-zinc-300 hover:text-white transition-colors">Alur Registrasi</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Izin Resmi Kemenag (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <h3 class="font-serif text-sm font-bold tracking-wider text-emerald-300 mb-4">Legalitas Kemenag RI</h3>
                <div class="space-y-3 text-xs text-zinc-300">
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                        <span class="text-emerald-300 font-semibold block mb-0.5">PPIU Kemenag RI</span>
                        <span class="text-white font-mono">NOMOR U.255 TAHUN 2020</span>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                        <span class="text-emerald-300 font-semibold block mb-0.5">PIHK Kemenag RI</span>
                        <span class="text-white font-mono">NOMOR 599 TAHUN 2021</span>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3 flex items-center justify-between">
                        <span class="text-emerald-300 font-semibold">Akreditasi</span>
                        <span class="text-white font-bold bg-emerald-800/80 px-2.5 py-0.5 rounded text-[11px]">Akreditasi A (IMS-SPPIU)</span>
                    </div>
                </div>

                <!-- Ikon Sosial Media -->
                <div class="pt-3">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-zinc-400 block mb-2.5">Ikuti Media Sosial Kami</span>
                    <div class="flex gap-2.5">
                        <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center text-zinc-300 hover:text-white" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="{{ $ytUrl }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center text-zinc-300 hover:text-white" aria-label="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center text-zinc-300 hover:text-white" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Baris Hak Cipta & Ketentuan -->
    <div class="border-t border-white/10 bg-black/25">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-400">
            
            <p>
                &copy; {{ date('Y') }} PT. Zein Internasional.
                <span class="mx-2 text-zinc-600">|</span>
                Built by
                <a 
                    href="https://mohamadarifinhasbi.vercel.app/" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="text-zinc-300 hover:text-white transition-colors font-medium"
                >
                    Mohamad Arifin Hasbi
                </a>
            </p>

            <div class="flex items-center gap-4">
                <a href="{{ route('legalitas') }}" class="hover:text-zinc-200 transition-colors">Kebijakan Privasi</a>
                <a href="{{ route('legalitas') }}" class="hover:text-zinc-200 transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
