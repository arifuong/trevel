<x-layouts.main :title="'Hubungi Kami & Lokasi Kantor — PT. Zein Internasional'" :company="$company">

    @php
        $phone = $company['phone'] ?? '+62821 2148 3337';
        $phoneCall = $company['phone_call'] ?? '+6282121483337';
        $whatsapp = $company['whatsapp'] ?? '6281222222562';
        $whatsappFormatted = $company['whatsapp_formatted'] ?? '+62812 2222 2562';
        $address = $company['address'] ?? 'Jl. Cihanjuang Kp. Karangsari No.15, Parongpong Bandung Barat 40559';
        $mapsUrl = $company['maps_url'] ?? 'https://maps.app.goo.gl/mbTWxdHMtDLWKu9k7';
        $officeHours = $company['office_hours'] ?? 'Senin - Sabtu: 08.30 - 17.00 WIB';
    @endphp

    {{-- ═══════════════════════════════════════════════════════════════
         1. PAGE HEADER (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-[#E0E7DC] pt-8 pb-14 sm:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['title' => 'Kontak & Lokasi Kantor']]" />

            <div data-reveal class="max-w-3xl mt-6 space-y-4">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block">
                    CONTACT & ASSISTANCE
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight leading-[1.2]">
                    Kami Siap Membantu Rencana Ibadah Anda
                </h1>
                <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                    Silakan hubungi tim konsultan kami atau kunjungi kantor pusat PT. Zein Internasional untuk konsultasi gratis mengenai jadwal keberangkatan, rincian biaya, dan bimbingan manasik.
                </p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. KONTAK & FORM KONSULTASI (bg-[#EFF3EB])
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-16 sm:py-24 bg-[#EFF3EB] border-b border-[#E0E7DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                {{-- Left: Informasi Kontak (5 cols) --}}
                <div data-reveal class="lg:col-span-5 space-y-5">
                    
                    {{-- Card Alamat --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E0E7DC] shadow-xs">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">INFORMASI DAN PENDAFTARAN</span>
                                <p class="text-xs sm:text-sm font-medium text-[#12271E] leading-relaxed">
                                    Jl. Cihanjuang Kp. Karangsari No.15<br>
                                    Parongpong Bandung Barat 40559
                                </p>
                                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline mt-2">
                                    <span>Buka di Google Maps &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card Telepon & WhatsApp --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E0E7DC] shadow-xs">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">HUBUNGI KAMI</span>
                                <div class="text-xs sm:text-sm text-[#12271E]">
                                    <span class="text-[#4D5E54]">Call : </span>
                                    <a href="tel:{{ $phoneCall }}" class="font-bold text-[#1B3B2B] hover:underline">{{ $phone }}</a>
                                </div>
                                <div class="text-xs sm:text-sm text-[#12271E]">
                                    <span class="text-[#4D5E54]">WA : </span>
                                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="font-bold text-[#1B3B2B] hover:underline">{{ $whatsappFormatted }}</a>
                                </div>
                                <a href="https://wa.me/{{ $whatsapp }}?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20Umrah" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline mt-2">
                                    <span>Buka WhatsApp Langsung &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card Jam Operasional --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E0E7DC] shadow-xs">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-[#EAF1E8] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">Jam Layanan Kantor</span>
                                <p class="text-xs sm:text-sm font-medium text-[#12271E] leading-relaxed">{{ $officeHours }}</p>
                                <p class="text-[11px] text-[#4D5E54] mt-1">Konsultasi tatap muka & pendaftaran langsung</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right: WhatsApp Form (7 cols) --}}
                <div data-reveal class="lg:col-span-7">
                    <div class="bg-white rounded-3xl p-7 sm:p-9 border border-[#E0E7DC] shadow-sm" x-data="{
                        name: '',
                        phone: '',
                        packageType: 'Umrah Reguler',
                        notes: '',
                        sendWA() {
                            let text = `Assalamu'alaikum PT. Zein Internasional,%0A%0A`;
                            text += `*Nama:* ${this.name || '-' }%0A`;
                            text += `*No. HP / WA:* ${this.phone || '-' }%0A`;
                            text += `*Rencana Paket:* ${this.packageType}%0A`;
                            if(this.notes) { text += `*Catatan / Pertanyaan:* ${encodeURIComponent(this.notes)}%0A`; }
                            text += `%0AMohon informasi ketersediaan jadwal & rincian biaya. Terima kasih.`;
                            window.open(`https://wa.me/{{ $whatsapp }}?text=${text}`, '_blank');
                        }
                    }">
                        <h3 class="font-serif text-xl font-bold text-[#12271E] mb-1">Kirim Pesan Konsultasi</h3>
                        <p class="text-xs text-[#4D5E54] mb-6">Isi formulir singkat di bawah ini untuk tersambung otomatis dengan WhatsApp konsultan kami.</p>

                        <form @submit.prevent="sendWA" class="space-y-4">
                            <div>
                                <label for="contact_name" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                                <input id="contact_name" type="text" x-model="name" required placeholder="Contoh: H. Ahmad Fauzi" 
                                       class="w-full px-4 py-3 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] focus:border-[#1B3B2B] focus:outline-none bg-[#F9FAF8]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="contact_phone" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">Nomor WhatsApp</label>
                                    <input id="contact_phone" type="tel" x-model="phone" required placeholder="081234567890" 
                                           class="w-full px-4 py-3 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] focus:border-[#1B3B2B] focus:outline-none bg-[#F9FAF8]">
                                </div>
                                <div>
                                    <label for="contact_package" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">Pilihan Ibadah</label>
                                    <select id="contact_package" x-model="packageType" class="w-full px-4 py-3 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] focus:border-[#1B3B2B] focus:outline-none bg-[#F9FAF8]">
                                        <option value="Umrah Reguler">Umrah Reguler</option>
                                        <option value="Umrah VIP / Ramadhan">Umrah VIP / Ramadhan</option>
                                        <option value="Haji Khusus / Furoda">Haji Khusus / Furoda</option>
                                        <option value="Umrah Plus Wisata">Umrah Plus Wisata Halal</option>
                                        <option value="Konsultasi Umum">Konsultasi Umum</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="contact_notes" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">Pertanyaan / Rencana Jumlah Jamaah</label>
                                <textarea id="contact_notes" x-model="notes" rows="3" placeholder="Contoh: Rencana berangkat bersama keluarga 4 orang di bulan Syawal..." 
                                          class="w-full px-4 py-3 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] focus:border-[#1B3B2B] focus:outline-none bg-[#F9FAF8]"></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-sm cursor-pointer">
                                <span>Kirim via WhatsApp</span>
                                <svg class="w-4 h-4 fill-current text-emerald-300" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.99.54 1.761.819 2.796.819 3.18 0 5.767-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm3.39 8.175c-.144.405-.837.774-1.17.824-.312.045-.634.072-1.025-.054-.374-.122-.857-.282-1.488-.564-1.391-.621-2.296-2.032-2.366-2.126-.07-.093-.564-.75-.564-1.429 0-.678.354-1.011.48-1.144.126-.134.275-.168.367-.168.092 0 .184.001.265.006.085.005.199-.033.31.235.115.277.392.955.426 1.025.034.07.057.151.011.242-.045.092-.068.149-.136.228-.068.079-.143.176-.205.236-.068.067-.14.14-.06.277.08.138.356.587.764.951.526.468.97.613 1.108.682.138.069.219.058.3-.035.08-.093.344-.402.436-.54.092-.138.184-.115.31-.069.126.046.804.379.942.448.138.069.23.103.264.161.034.057.034.333-.11.738z"/></svg>
                            </button>
                            <p class="text-[11px] text-[#4D5E54] text-center mt-2">Pesan akan langsung diteruskan ke WhatsApp konsultan resmi kami tanpa biaya.</p>
                        </form>
                    </div>
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════════════════
                 3. PETA LOKASI KANTOR (Full Width)
            ═══════════════════════════════════════════════════════════ --}}
            <div data-reveal class="mt-14 bg-white rounded-3xl border border-[#E0E7DC] overflow-hidden shadow-xs">
                <div class="p-4 sm:p-5 bg-white border-b border-[#E0E7DC] flex items-center justify-between">
                    <div>
                        <span class="font-serif text-sm font-bold text-[#12271E] block">Peta Kantor Pusat PT. Zein Internasional</span>
                        <span class="text-[11px] text-[#4D5E54]">{{ $address }}</span>
                    </div>
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-[#1B3B2B] hover:underline">
                        Buka di Google Maps &rarr;
                    </a>
                </div>
                <div class="h-80 sm:h-96 w-full bg-zinc-100">
                    <iframe 
                        class="w-full h-full border-0"
                        width="600"
                        height="380"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.052601930777!2d106.9715!3d-6.2568!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTUnMjQuNSJTIDEwNsKwNTgnMTcuNCJF!5e0!3m2!1sid!2sid!4v1620000000000!5m2!1sid!2sid" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta Lokasi Kantor PT. Zein Internasional">
                    </iframe>
                </div>
            </div>

        </div>
    </section>

</x-layouts.main>
