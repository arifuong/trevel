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

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Info List (5 Cols) -->
    <div data-reveal class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-[#E1E8DC] shadow-xs space-y-6">
        <h3 class="font-serif text-xl font-bold text-[#11231D] pb-4 border-b border-[#E5E7EB]">
            INFORMASI DAN PENDAFTARAN
        </h3>

        <!-- Address -->
        <div class="space-y-1.5">
            <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280]">Alamat Kantor</span>
            <p class="text-xs sm:text-sm font-medium text-[#11231D] leading-relaxed">
                Jl. Cihanjuang Kp. Karangsari No.15<br>
                Parongpong Bandung Barat 40559
            </p>
            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline pt-1">
                <span>Buka di Google Maps &rarr;</span>
            </a>
        </div>

        <!-- WhatsApp & Phone -->
        <div class="space-y-1.5 pt-3 border-t border-[#E5E7EB]">
            <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280]">HUBUNGI KAMI</span>
            <div class="text-xs sm:text-sm text-[#11231D] space-y-1">
                <div>
                    <span class="text-[#6B7280]">Call : </span>
                    <a href="tel:{{ $phoneCall }}" class="font-bold text-[#17382E] hover:underline">{{ $phone }}</a>
                </div>
                <div>
                    <span class="text-[#6B7280]">WA : </span>
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="font-bold text-[#17382E] hover:underline">{{ $whatsappFormatted }}</a>
                </div>
            </div>
            <a href="https://wa.me/{{ $whatsapp }}?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20Umrah" 
               target="_blank" 
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#17382E] hover:underline pt-1.5">
                <span>Chat Konsultan WhatsApp &rarr;</span>
            </a>
        </div>

        <!-- Hours -->
        <div class="space-y-1.5 pt-3 border-t border-[#E5E7EB]">
            <span class="text-[10px] uppercase font-bold tracking-wider text-[#6B7280]">Jam Operasional</span>
            <p class="text-xs sm:text-sm text-[#11231D]">{{ $officeHours }}</p>
        </div>
    </div>

    <!-- Map Column (7 Cols) -->
    <div data-reveal class="lg:col-span-7 bg-white rounded-2xl sm:rounded-3xl border border-[#E1E8DC] overflow-hidden shadow-xs">
        <div class="p-4 sm:p-5 bg-white border-b border-[#E5E7EB] flex items-center justify-between">
            <div>
                <span class="font-serif text-sm font-bold text-[#11231D] block">Peta Lokasi Kantor</span>
                <span class="text-[11px] text-[#6B7280]">{{ $address }}</span>
            </div>
            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-[#17382E] hover:underline shrink-0 ml-2">
                Buka di Google Maps &rarr;
            </a>
        </div>
        <div class="relative h-72 sm:h-84 w-full bg-zinc-100">
            <iframe 
                class="w-full h-full border-0"
                width="600"
                height="350"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.464674681664!2d107.5670732!3d-6.8347714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e3f462bb1e29%3A0xe9ea0c354784a0ec!2sJl.%20Cihanjuang%20No.15%2C%20Cihanjuang%20Rahayu%2C%20Kec.%20Parongpong%2C%20Kabupaten%20Bandung%20Barat%2C%20Jawa%20Barat%2040559!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Peta Lokasi Kantor PT. Zein Internasional">
            </iframe>
        </div>
    </div>

</div>
