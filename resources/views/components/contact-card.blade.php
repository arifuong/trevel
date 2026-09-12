@props(['company' => null])

@php
    $phone = $company['phone'] ?? '+62821 2148 3337';
    $phoneCall = $company['phone_call'] ?? '+6282121483337';
    $whatsapp = $company['whatsapp'] ?? '6281222222562';
    $whatsappFormatted = $company['whatsapp_formatted'] ?? '+62812 2222 2562';
    $address = $company['address'] ?? 'Jl. Cihanjuang Kp. Karangsari No.15, Parongpong Bandung Barat 40559';
    $mapsUrl = $company['maps_url'] ?? 'https://maps.app.goo.gl/mbTWxdHMtDLWKu9k7';
    $officeHours = $company['office_hours'] ?? 'Senin - Sabtu: 08.30 - 17.00 WIB';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
    
    <!-- Info List (5 Cols) -->
    <div data-reveal class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-[#E5E7EB] shadow-[0_4px_24px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-between space-y-6">
        <div>
            <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB] mb-6">
                <h3 class="font-serif text-lg sm:text-xl font-bold text-[#12271E] tracking-tight">
                    INFORMASI DAN PENDAFTARAN
                </h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>

            <!-- Address -->
            <div class="flex items-start gap-3.5 mb-6">
                <div class="w-10 h-10 rounded-2xl bg-[#F4F7F2] text-[#1B3B2B] border border-[#E5E7EB] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-5 h-5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280] block">Alamat Kantor</span>
                    <p class="text-xs sm:text-sm font-medium text-[#12271E] leading-relaxed">
                        Jl. Cihanjuang Kp. Karangsari No.15<br>
                        Parongpong Bandung Barat 40559
                    </p>
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:text-emerald-700 hover:underline pt-0.5">
                        <span>Buka di Google Maps &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- WhatsApp & Phone -->
            <div class="flex items-start gap-3.5 pt-6 border-t border-[#E5E7EB] mb-6">
                <div class="w-10 h-10 rounded-2xl bg-[#F4F7F2] text-[#1B3B2B] border border-[#E5E7EB] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <svg class="w-5 h-5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </div>
                <div class="space-y-2 flex-1">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280] block">HUBUNGI KAMI</span>
                    <div class="text-xs sm:text-sm text-[#12271E] space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-[#6B7280] text-xs w-10">Call :</span>
                            <a href="tel:{{ $phoneCall }}" class="font-bold text-[#12271E] hover:text-[#1B3B2B] hover:underline">{{ $phone }}</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[#6B7280] text-xs w-10">WA :</span>
                            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="font-bold text-[#12271E] hover:text-[#1B3B2B] hover:underline">{{ $whatsappFormatted }}</a>
                        </div>
                    </div>
                    
                    {{-- Primary CTA Button WhatsApp --}}
                    <div class="pt-2">
                        <a href="https://wa.me/{{ $whatsapp }}?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20Umrah" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-xs sm:text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer">
                            <svg class="w-4 h-4 fill-current text-emerald-300" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.274.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                            <span>Chat Konsultan WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hours -->
        <div class="flex items-start gap-3.5 pt-6 border-t border-[#E5E7EB]">
            <div class="w-10 h-10 rounded-2xl bg-[#F4F7F2] text-[#1B3B2B] border border-[#E5E7EB] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                <svg class="w-5 h-5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280] block">Jam Operasional</span>
                <p class="text-xs sm:text-sm font-medium text-[#12271E]">{{ $officeHours }}</p>
                <p class="text-[11px] text-[#6B7280]">Konsultasi tatap muka & pendaftaran langsung</p>
            </div>
        </div>
    </div>

    <!-- Map Column (7 Cols) -->
    <div data-reveal class="lg:col-span-7 bg-white rounded-2xl sm:rounded-3xl border border-[#E5E7EB] overflow-hidden shadow-[0_4px_24px_-4px_rgba(0,0,0,0.05)] flex flex-col">
        <div class="p-4 sm:p-5 bg-white border-b border-[#E5E7EB] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#F4F7F2] text-[#1B3B2B] border border-[#E5E7EB] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                </div>
                <div>
                    <span class="font-serif text-sm font-bold text-[#12271E] block">Peta Lokasi Kantor</span>
                    <span class="text-[11px] text-[#6B7280] line-clamp-1">{{ $address }}</span>
                </div>
            </div>
            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-[#1B3B2B] hover:text-emerald-700 hover:underline shrink-0 ml-2 flex items-center gap-1">
                <span>Buka di Google Maps</span>
                <span>&rarr;</span>
            </a>
        </div>
        <div class="relative min-h-[300px] sm:min-h-[380px] w-full bg-zinc-100 flex-1">
            <iframe 
                class="w-full h-full border-0"
                width="600"
                height="380"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.464674681664!2d107.5670732!3d-6.8347714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e3f462bb1e29%3A0xe9ea0c354784a0ec!2sJl.%20Cihanjuang%20No.15%2C%20Cihanjuang%20Rahayu%2C%20Kec.%20Parongpong%2C%20Kabupaten%20Bandung%20Barat%2C%20Jawa%20Barat%2040559!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Peta Lokasi Kantor PT. Zein Internasional">
            </iframe>
        </div>
    </div>

</div>
