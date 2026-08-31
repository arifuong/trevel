<x-layouts.jamaah title="Pendaftaran Paket - PT. Zein Internasional">
    
    <section class="py-8 sm:py-12 bg-[#F3F6F1] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Breadcrumb & Header --}}
            <div>
                <nav class="flex items-center gap-2 text-xs text-[#526057] mb-3">
                    <a href="{{ route('jamaah.dashboard') }}" class="hover:text-[#1B3B2B] transition-colors">Beranda</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-[#12271E] font-medium">Form Pendaftaran Paket</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">Formulir Pendaftaran Umrah & Haji</h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-1">Lengkapi data jamaah dan unggah berkas dokumen persyaratan.</p>
            </div>

            {{-- Alert Error Validation --}}
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-1 shadow-sm">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <span>Terdapat kesalahan pada formulir pendaftaran:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-6 text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main Form with Alpine.js --}}
            <form id="registrationFormElement"
                  method="POST" 
                  action="{{ route('jamaah.registration.store') }}" 
                  enctype="multipart/form-data"
                  x-data="registrationForm({{ Js::from($packages) }}, {{ $selectedPackage->id ?? ($packages->first()->id ?? 1) }}, {{ $selectedVariantId ? (int)$selectedVariantId : 'null' }}, '{{ $selectedRoomType ?? 'quad' }}', '{{ addslashes(auth()->user()->name) }}')"
                  class="space-y-8">
                @csrf

                {{-- 1. Pilihan Paket & Sub-Paket --}}
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs space-y-6" style="animation: fadeSlideUp 0.4s ease 0.1s both">
                    <div class="flex items-center gap-3 pb-4 border-b border-[#E0E7DC]">
                        <div class="w-8 h-8 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center font-bold text-xs">1</div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-[#12271E]">Pilihan Paket & Sub-Paket Perjalanan</h2>
                            <p class="text-[11px] text-[#526057]">Pilih paket ibadah induk, varian layanan, dan tipe kamar yang diinginkan</p>
                        </div>
                    </div>

                    {{-- 1A. Pilih Paket Induk --}}
                    <div>
                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-2">
                            Pilih Paket Ibadah <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="pkg in packages" :key="pkg.id">
                                <label class="relative flex flex-col p-4 rounded-2xl border transition-all cursor-pointer select-none"
                                       :class="selectedPackageId == pkg.id ? 'border-[#1B3B2B] bg-[#EFF3EB]/60 ring-2 ring-[#1B3B2B]/20 shadow-xs' : 'border-[#E0E7DC] bg-white hover:border-[#1B3B2B]/40'">
                                    
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <div>
                                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] uppercase tracking-wider mb-1" x-text="pkg.duration ? pkg.duration + ' Hari' : 'Paket Umrah'"></span>
                                            <h3 class="font-bold text-sm text-[#12271E]" x-text="pkg.name"></h3>
                                        </div>
                                        <input type="radio" name="package_id" :value="pkg.id" x-model="selectedPackageId" @change="onPackageChange()" class="mt-1 text-[#1B3B2B] focus:ring-[#1B3B2B]">
                                    </div>

                                    <div class="mt-auto pt-3 border-t border-[#E0E7DC]/60 flex items-baseline justify-between">
                                        <span class="text-[11px] text-[#526057]">Mulai dari</span>
                                        <span class="font-bold text-sm sm:text-base text-[#1B3B2B]" x-text="getPackageStartingPrice(pkg)"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    {{-- 1B. Pilih Sub-Paket Varian (Jika ada) --}}
                    <div x-show="currentVariants.length > 0" class="pt-4 border-t border-[#E0E7DC] space-y-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                Pilih Sub-Paket (Varian) <span class="text-red-500">*</span>
                            </label>
                            <p class="text-[11px] text-[#526057] mb-2.5">Setiap varian memiliki maskapai, hotel, dan fasilitas tersendiri.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <template x-for="v in currentVariants" :key="v.id">
                                <label class="relative flex flex-col p-4 rounded-xl border transition-all cursor-pointer select-none"
                                       :class="selectedVariantId == v.id ? 'border-[#1B3B2B] bg-[#EFF3EB] ring-2 ring-[#1B3B2B]/20' : 'border-[#E0E7DC] bg-white hover:border-[#CCD8C7]'">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold text-sm text-[#12271E]" x-text="v.name"></span>
                                        <input type="radio" name="package_variant_id" :value="v.id" x-model="selectedVariantId" @change="onVariantChange()" class="text-[#1B3B2B] focus:ring-[#1B3B2B]">
                                    </div>
                                    <div class="text-[11px] text-[#526057] mb-0.5">Mulai dari</div>
                                    <div class="font-bold text-sm text-[#1B3B2B]" x-text="v.lowest_price_formatted || '-'"></div>
                                    <div class="mt-2 pt-2 border-t border-[#E0E7DC]/60 text-[10px] text-[#526057] flex justify-between">
                                        <span x-text="v.hotel_makkah_name || 'Hotel Bintang 4/5'"></span>
                                        <span class="font-bold" x-text="(v.remaining_quota !== undefined ? v.remaining_quota : v.quota) + ' Kursi Tersisa'"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    {{-- 1C. Pilih Tipe Kamar --}}
                    <div x-show="currentVariants.length > 0" class="pt-4 border-t border-[#E0E7DC] space-y-4">
                        <template x-if="currentPrices.length > 0">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                        Pilih Tipe Kamar <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-[11px] text-[#526057] mb-2.5">Harga dihitung otomatis per jamaah sesuai pilihan kamar.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <template x-for="p in currentPrices" :key="p.id">
                                        <label class="relative flex flex-col p-4 rounded-xl border transition-all cursor-pointer select-none"
                                               :class="selectedRoomType == p.room_type ? 'border-[#1B3B2B] bg-[#EFF3EB] ring-2 ring-[#1B3B2B]/20' : 'border-[#E0E7DC] bg-white hover:border-[#CCD8C7]'">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-bold text-sm text-[#12271E]" x-text="p.room_label || ucfirst(p.room_type)"></span>
                                                <input type="radio" name="room_type" :value="p.room_type" x-model="selectedRoomType" class="text-[#1B3B2B] focus:ring-[#1B3B2B]">
                                            </div>
                                            <div class="text-[11px] text-[#526057] mb-0.5">Biaya per Jamaah</div>
                                            <div class="font-serif font-bold text-sm sm:text-base text-[#1B3B2B]" x-text="'Rp ' + (parseFloat(p.promo_price || p.normal_price) || 0).toLocaleString('id-ID')"></div>
                                            <template x-if="p.promo_price">
                                                <span class="text-[10px] text-emerald-700 font-semibold mt-0.5">Hemat Promo</span>
                                            </template>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="currentVariants.length > 0 && currentPrices.length === 0">
                            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
                                Sub-paket ini belum memiliki pilihan tipe kamar aktif. Silakan pilih sub-paket yang lain.
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 2. Data Calon Jamaah & Dokumen --}}
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs" style="animation: fadeSlideUp 0.4s ease 0.15s both">
                    <div class="flex items-center justify-between flex-wrap gap-4 mb-6 pb-4 border-b border-[#E0E7DC]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center font-bold text-xs">2</div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-[#12271E]">Data Jamaah & Berkas Dokumen</h2>
                                <p class="text-[11px] text-[#526057]">Unggah berkas untuk mengisi data secara otomatis dan tinjau foto preview sebelum mengirim.</p>
                            </div>
                        </div>
                        <button type="button" @click="addMember()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all cursor-pointer shadow-xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>+ Tambah Anggota Keluarga</span>
                        </button>
                    </div>

                    {{-- Members Accordion / Repeater --}}
                    <div class="space-y-6">
                        <template x-for="(member, index) in members" :key="member.uid">
                            <div class="rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] p-5 sm:p-6 transition-all space-y-6">
                                
                                {{-- Card Header --}}
                                <div class="flex items-center justify-between pb-3 border-b border-[#E0E7DC]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                                        <span class="font-bold text-sm text-[#12271E]" x-text="index === 0 ? 'Jamaah Utama (' + (member.name || 'Pemesan') + ')' : 'Anggota Keluarga ' + (index + 1)"></span>
                                    </div>
                                    <button type="button" x-show="members.length > 1" @click="removeMember(index)" 
                                            class="text-xs text-red-600 hover:text-red-800 font-semibold flex items-center gap-1 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </div>

                                {{-- ════════════════════════════════════════════════════════════ --}}
                                {{-- BAGIAN 1: FOTO KTP & DATA IDENTITAS JAMAAH SESUAI KTP       --}}
                                {{-- ════════════════════════════════════════════════════════════ --}}
                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-4 shadow-2xs">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                        
                                        {{-- KOLOM KIRI: FOTO KTP --}}
                                        <div class="lg:col-span-5 space-y-3">
                                            <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/></svg>
                                                    <span>Foto KTP</span>
                                                    <span class="text-red-500">*</span>
                                                </label>
                                            </div>

                                            {{-- Hidden File Input --}}
                                            <input type="file" 
                                                   :id="'ktp_input_' + index"
                                                   :name="'members[' + index + '][ktp_file]'" 
                                                   required 
                                                   accept=".jpg,.jpeg,.png,.webp"
                                                   @change="handleKtpFileSelect($event, member)"
                                                   class="hidden">

                                            {{-- Empty State: Upload Trigger Box --}}
                                            <div x-show="!member.ktpPreviewUrl" 
                                                 @click="document.getElementById('ktp_input_' + index).click()"
                                                 class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Foto / Scan KTP</span>
                                                    <span class="text-[10px] text-[#526057]">Format: JPG, JPEG, PNG, WebP (Maks. 10 MB)</span>
                                                </div>
                                            </div>

                                            {{-- Active State: Photo Preview Box --}}
                                            <div x-show="member.ktpPreviewUrl" class="space-y-2.5">
                                                <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                    <img :src="member.ktpPreviewUrl" alt="Preview KTP" class="max-h-[210px] w-full object-contain">
                                                </div>

                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span>✓ KTP terunggah</span>
                                                    </span>

                                                    <div class="flex items-center gap-2">
                                                        <button type="button" 
                                                                @click="document.getElementById('ktp_input_' + index).click()"
                                                                class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                            <span>Ganti Foto</span>
                                                        </button>
                                                        <button type="button" 
                                                                @click="removeKtpFile(member, index)"
                                                                class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status & Warning OCR KTP --}}
                                            <div x-show="member.ocrKtpStatus !== 'idle'" class="text-[11px] pt-1">
                                                <template x-if="member.ocrKtpStatus === 'scanning'">
                                                    <div class="flex items-center gap-2 text-amber-700 font-medium">
                                                        <svg class="animate-spin w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                        <span x-text="member.ocrKtpMessage"></span>
                                                    </div>
                                                </template>
                                                <template x-if="member.ocrKtpStatus === 'success'">
                                                    <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span x-text="member.ocrKtpMessage"></span>
                                                    </div>
                                                </template>
                                                <template x-if="member.ocrKtpStatus === 'warning' || member.ocrKtpStatus === 'quality_warning'">
                                                    <div class="p-2.5 rounded-xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-1">
                                                        <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                                            <span>Pemberitahuan Pembacaan KTP</span>
                                                        </div>
                                                        <p class="text-[10.5px] leading-relaxed" x-text="member.ocrKtpMessage"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- KOLOM KANAN: DATA IDENTITAS JAMAAH SESUAI KTP --}}
                                        <div class="lg:col-span-7 space-y-3.5 lg:border-l lg:border-[#E0E7DC] lg:pl-6">
                                            <div class="pb-1.5 border-b border-[#EFF3EB]">
                                                <h5 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">
                                                    Data Identitas Jamaah Sesuai KTP
                                                </h5>
                                                <p class="text-[11px] text-[#526057] mt-0.5">
                                                    Data harus diisi sesuai dengan informasi yang tercantum pada KTP.
                                                </p>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                                                
                                                {{-- Nama Lengkap Sesuai KTP --}}
                                                <div class="sm:col-span-12">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                        Nama Lengkap <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" :name="'members[' + index + '][name]'" x-model="member.name" required
                                                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                           placeholder="Nama lengkap sesuai KTP">
                                                </div>

                                                {{-- NIK (16 Digit) --}}
                                                <div class="sm:col-span-12">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">
                                                            NIK (16 Digit Angka) <span class="text-red-500">*</span>
                                                        </label>
                                                        <span :class="member.nik && member.nik.length === 16 ? 'text-emerald-700 bg-emerald-50 border-emerald-300' : 'text-amber-700 bg-amber-50 border-amber-300'"
                                                              class="text-[10px] px-2 py-0.5 rounded-full border font-mono font-bold transition-all"
                                                              x-text="(member.nik ? member.nik.length : 0) + '/16 digit ' + (member.nik && member.nik.length === 16 ? '✓' : '⚠')">
                                                        </span>
                                                    </div>
                                                    <input type="text" 
                                                           inputmode="numeric" 
                                                           maxlength="16" 
                                                           :name="'members[' + index + '][nik]'" 
                                                           x-model="member.nik" 
                                                           @input="member.nik = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); $event.target.value = member.nik; checkNikCrossMatch(member)"
                                                           @keydown="if ($event.key.length === 1 && !/[0-9]/.test($event.key) && !$event.ctrlKey && !$event.metaKey) { $event.preventDefault(); }"
                                                           @paste="setTimeout(() => { member.nik = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); $event.target.value = member.nik; checkNikCrossMatch(member); }, 0)"
                                                           pattern="[0-9]{16}"
                                                           title="NIK harus terdiri dari tepat 16 angka (0–9)"
                                                           required 
                                                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] font-mono tracking-wider focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                           placeholder="3201xxxxxxxxxxxx">
                                                    
                                                    {{-- Cross Match Status --}}
                                                    <div class="mt-1">
                                                        <template x-if="member.nikCrossCheckStatus === 'matched'">
                                                            <p class="text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                                                                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                <span>✓ NIK cocok dengan data Kartu Keluarga</span>
                                                            </p>
                                                        </template>
                                                        <template x-if="member.nikCrossCheckStatus === 'different'">
                                                            <p class="text-[10px] text-amber-800 font-semibold flex items-center gap-1">
                                                                <svg class="w-3 h-3 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                                                <span>⚠ NIK pada KTP dan KK berbeda. Silakan periksa kembali.</span>
                                                            </p>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Tempat Lahir Sesuai KTP --}}
                                                <div class="sm:col-span-6">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                        Tempat Lahir <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" :name="'members[' + index + '][birth_place]'" x-model="member.birth_place" required
                                                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                           placeholder="Contoh: BANDUNG">
                                                </div>

                                                {{-- Tanggal Lahir Sesuai KTP --}}
                                                <div class="sm:col-span-6">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                        Tanggal Lahir <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="date" :name="'members[' + index + '][birth_date]'" x-model="member.birth_date" required
                                                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                </div>

                                                {{-- Jenis Kelamin Sesuai KTP --}}
                                                <div class="sm:col-span-6">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                        Jenis Kelamin <span class="text-red-500">*</span>
                                                    </label>
                                                    <select :name="'members[' + index + '][gender]'" x-model="member.gender" required
                                                            class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                                        <option value="laki-laki">Laki-laki</option>
                                                        <option value="perempuan">Perempuan</option>
                                                    </select>
                                                </div>

                                                {{-- Hubungan Keluarga --}}
                                                <div class="sm:col-span-6">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                        Hubungan Keluarga <span class="text-red-500">*</span>
                                                    </label>
                                                    <select :name="'members[' + index + '][relationship]'" x-model="member.relationship" required
                                                            class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                        <option value="diri_sendiri">Diri Sendiri (Pemesan Akun)</option>
                                                        <option value="suami">Suami</option>
                                                        <option value="istri">Istri</option>
                                                        <option value="anak">Anak</option>
                                                        <option value="orang_tua">Orang Tua</option>
                                                        <option value="saudara">Saudara Kandung</option>
                                                    </select>
                                                </div>

                                                {{-- Alamat Tinggal Sesuai KTP --}}
                                                <div class="sm:col-span-12">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                        Alamat Tinggal <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea :name="'members[' + index + '][address]'" x-model="member.address" rows="2" required
                                                              class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                              placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan/Desa, Kecamatan, Kabupaten/Kota, Provinsi"></textarea>
                                                    <p class="text-[10px] text-[#526057] mt-1">Isi persis berdasarkan alamat yang tertera pada KTP.</p>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ════════════════════════════════════════════════════════════ --}}
                                {{-- BAGIAN 2: FOTO KK & NOMOR KK                                --}}
                                {{-- ════════════════════════════════════════════════════════════ --}}
                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-4 shadow-2xs">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                        
                                        {{-- KOLOM KIRI: FOTO KK --}}
                                        <div class="lg:col-span-5 space-y-3">
                                            <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                    <span>Foto KK</span>
                                                    <span class="text-red-500">*</span>
                                                </label>
                                            </div>

                                            {{-- Hidden File Input --}}
                                            <input type="file" 
                                                   :id="'kk_input_' + index"
                                                   :name="'members[' + index + '][kk_file]'" 
                                                   required 
                                                   accept=".jpg,.jpeg,.png,.webp"
                                                   @change="handleKkFileSelect($event, member)"
                                                   class="hidden">

                                            {{-- Empty State: Upload Trigger Box --}}
                                            <div x-show="!member.kkPreviewUrl" 
                                                 @click="document.getElementById('kk_input_' + index).click()"
                                                 class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Foto / Scan Kartu Keluarga</span>
                                                    <span class="text-[10px] text-[#526057]">Format: JPG, JPEG, PNG, WebP (Maks. 10 MB)</span>
                                                </div>
                                            </div>

                                            {{-- Active State: Photo Preview Box --}}
                                            <div x-show="member.kkPreviewUrl" class="space-y-2.5">
                                                <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                    <img :src="member.kkPreviewUrl" alt="Preview Kartu Keluarga" class="max-h-[210px] w-full object-contain">
                                                </div>

                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span>✓ KK terunggah</span>
                                                    </span>

                                                    <div class="flex items-center gap-2">
                                                        <button type="button" 
                                                                @click="document.getElementById('kk_input_' + index).click()"
                                                                class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                            <span>Ganti Foto</span>
                                                        </button>
                                                        <button type="button" 
                                                                @click="removeKkFile(member, index)"
                                                                class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status & Warning OCR KK --}}
                                            <div x-show="member.ocrKkStatus !== 'idle'" class="text-[11px] pt-1">
                                                <template x-if="member.ocrKkStatus === 'scanning'">
                                                    <div class="flex items-center gap-2 text-amber-700 font-medium">
                                                        <svg class="animate-spin w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                        <span x-text="member.ocrKkMessage"></span>
                                                    </div>
                                                </template>
                                                <template x-if="member.ocrKkStatus === 'success'">
                                                    <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span x-text="member.ocrKkMessage"></span>
                                                    </div>
                                                </template>
                                                <template x-if="member.ocrKkStatus === 'warning' || member.ocrKkStatus === 'quality_warning'">
                                                    <div class="p-2.5 rounded-xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-1">
                                                        <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                                            <span>Pemberitahuan Pembacaan KK</span>
                                                        </div>
                                                        <p class="text-[10.5px] leading-relaxed" x-text="member.ocrKkMessage"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- KOLOM KANAN: NOMOR KK --}}
                                        <div class="lg:col-span-7 space-y-3.5 lg:border-l lg:border-[#E0E7DC] lg:pl-6">
                                            <div class="pb-1.5 border-b border-[#EFF3EB]">
                                                <h5 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">
                                                    Nomor KK
                                                </h5>
                                                <p class="text-[11px] text-[#526057] mt-0.5">
                                                    Nomor Kartu Keluarga (KK) yang tertera pada dokumen KK.
                                                </p>
                                            </div>

                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">
                                                        Nomor KK (16 Digit Angka) <span class="text-red-500">*</span>
                                                    </label>
                                                    <span :class="member.no_kk && member.no_kk.length === 16 ? 'text-emerald-700 bg-emerald-50 border-emerald-300' : 'text-amber-700 bg-amber-50 border-amber-300'"
                                                          class="text-[10px] px-2 py-0.5 rounded-full border font-mono font-bold transition-all"
                                                          x-text="(member.no_kk ? member.no_kk.length : 0) + '/16 digit ' + (member.no_kk && member.no_kk.length === 16 ? '✓' : '⚠')">
                                                    </span>
                                                </div>
                                                <input type="text" 
                                                       inputmode="numeric" 
                                                       maxlength="16" 
                                                       :name="'members[' + index + '][no_kk]'" 
                                                       x-model="member.no_kk" 
                                                       @input="member.no_kk = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); $event.target.value = member.no_kk"
                                                       @keydown="if ($event.key.length === 1 && !/[0-9]/.test($event.key) && !$event.ctrlKey && !$event.metaKey) { $event.preventDefault(); }"
                                                       @paste="setTimeout(() => { member.no_kk = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); $event.target.value = member.no_kk; }, 0)"
                                                       pattern="[0-9]{16}"
                                                       title="Nomor Kartu Keluarga harus terdiri dari tepat 16 angka (0–9)"
                                                       required 
                                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] font-mono tracking-wider focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                       placeholder="3201xxxxxxxxxxxx">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ════════════════════════════════════════════════════════════ --}}
                                {{-- BAGIAN 3: FOTO PASPOR & NOMOR PASPOR                        --}}
                                {{-- ════════════════════════════════════════════════════════════ --}}
                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-4 shadow-2xs">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                        
                                        {{-- KOLOM KIRI: FOTO PASPOR --}}
                                        <div class="lg:col-span-5 space-y-3">
                                            <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                                    <span>Foto Paspor</span>
                                                    <span class="text-[10px] font-normal text-[#526057] lowercase">(opsional)</span>
                                                </label>
                                            </div>

                                            <input type="file" 
                                                   :id="'passport_input_' + index"
                                                   :name="'members[' + index + '][passport_file]'" 
                                                   accept=".jpg,.jpeg,.png,.webp"
                                                   @change="handlePassportFileSelect($event, member)"
                                                   class="hidden">

                                            <div x-show="!member.passportPreviewUrl" 
                                                 @click="document.getElementById('passport_input_' + index).click()"
                                                 class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Foto / Scan Paspor</span>
                                                    <span class="text-[10px] text-[#526057]">Format: JPG, JPEG, PNG, WebP (Maks. 10 MB)</span>
                                                </div>
                                            </div>

                                            <div x-show="member.passportPreviewUrl" class="space-y-2.5">
                                                <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                    <img :src="member.passportPreviewUrl" alt="Preview Paspor" class="max-h-[210px] w-full object-contain">
                                                </div>

                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span>✓ Paspor terunggah</span>
                                                    </span>

                                                    <div class="flex items-center gap-2">
                                                        <button type="button" 
                                                                @click="document.getElementById('passport_input_' + index).click()"
                                                                class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                            <span>Ganti</span>
                                                        </button>
                                                        <button type="button" 
                                                                @click="removePassportFile(member, index)"
                                                                class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div x-show="member.ocrPassportStatus !== 'idle'" class="text-[11px] pt-1">
                                                <template x-if="member.ocrPassportStatus === 'scanning'">
                                                    <div class="flex items-center gap-2 text-amber-700 font-medium">
                                                        <svg class="animate-spin w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                        <span x-text="member.ocrPassportMessage"></span>
                                                    </div>
                                                </template>
                                                <template x-if="member.ocrPassportStatus === 'success'">
                                                    <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span x-text="member.ocrPassportMessage"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- KOLOM KANAN: NOMOR PASPOR --}}
                                        <div class="lg:col-span-7 space-y-3.5 lg:border-l lg:border-[#E0E7DC] lg:pl-6">
                                            <div class="pb-1.5 border-b border-[#EFF3EB]">
                                                <h5 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">
                                                    Nomor Paspor
                                                </h5>
                                                <p class="text-[11px] text-[#526057] mt-0.5">
                                                    Nomor paspor bagi calon jamaah yang telah memiliki paspor aktif.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                    Nomor Paspor <span class="text-[10px] font-normal text-[#526057] lowercase">(opsional)</span>
                                                </label>
                                                <input type="text" :name="'members[' + index + '][no_passport]'" x-model="member.no_passport"
                                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                       placeholder="Contoh: A1234567">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ════════════════════════════════════════════════════════════ --}}
                                {{-- BAGIAN 4: DOKUMEN PENDUKUNG (BUKU NIKAH / AKTA KELAHIRAN)     --}}
                                {{-- ════════════════════════════════════════════════════════════ --}}
                                <div class="space-y-4" x-show="member.relationship === 'suami' || member.relationship === 'istri' || member.relationship === 'anak'">
                                    
                                    {{-- Buku Nikah (Jika Suami/Istri) --}}
                                    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-3 shadow-2xs" 
                                         x-show="member.relationship === 'suami' || member.relationship === 'istri'">
                                        <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                            <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                <span>Scan Buku Nikah</span>
                                                <span class="text-red-500">*</span>
                                            </label>
                                        </div>

                                        <input type="file" 
                                               :id="'marriage_input_' + index"
                                               :name="'members[' + index + '][marriage_book_file]'" 
                                               accept=".jpg,.jpeg,.png,.webp"
                                               :required="member.relationship === 'suami' || member.relationship === 'istri'"
                                               @change="handleMarriageBookFileSelect($event, member)"
                                               class="hidden">

                                        <div x-show="!member.marriageBookPreviewUrl" 
                                             @click="document.getElementById('marriage_input_' + index).click()"
                                             class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                            <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                            </div>
                                            <div>
                                                <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Scan Buku Nikah</span>
                                                <span class="text-[10px] text-[#526057]">Wajib bagi pasangan suami/istri (Maks. 10 MB)</span>
                                            </div>
                                        </div>

                                        <div x-show="member.marriageBookPreviewUrl" class="space-y-2.5">
                                            <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                <img :src="member.marriageBookPreviewUrl" alt="Preview Buku Nikah" class="max-h-[210px] w-full object-contain">
                                            </div>

                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">✓ Buku Nikah terunggah</span>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="document.getElementById('marriage_input_' + index).click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] text-[11px] font-semibold">Ganti</button>
                                                    <button type="button" @click="removeMarriageBookFile(member, index)" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-semibold">Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Akta Lahir (Jika Anak) --}}
                                    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-3 shadow-2xs" 
                                         x-show="member.relationship === 'anak'">
                                        <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                            <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                <span>Scan Akta Kelahiran</span>
                                                <span class="text-red-500">*</span>
                                            </label>
                                        </div>

                                        <input type="file" 
                                               :id="'birth_input_' + index"
                                               :name="'members[' + index + '][birth_certificate_file]'" 
                                               accept=".jpg,.jpeg,.png,.webp"
                                               :required="member.relationship === 'anak'"
                                               @change="handleBirthCertificateFileSelect($event, member)"
                                               class="hidden">

                                        <div x-show="!member.birthCertificatePreviewUrl" 
                                             @click="document.getElementById('birth_input_' + index).click()"
                                             class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                            <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                            </div>
                                            <div>
                                                <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Scan Akta Kelahiran</span>
                                                <span class="text-[10px] text-[#526057]">Wajib bagi pendaftaran anak (Maks. 10 MB)</span>
                                            </div>
                                        </div>

                                        <div x-show="member.birthCertificatePreviewUrl" class="space-y-2.5">
                                            <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                <img :src="member.birthCertificatePreviewUrl" alt="Preview Akta Kelahiran" class="max-h-[210px] w-full object-contain">
                                            </div>

                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">✓ Akta Kelahiran terunggah</span>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="document.getElementById('birth_input_' + index).click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] text-[11px] font-semibold">Ganti</button>
                                                    <button type="button" @click="removeBirthCertificateFile(member, index)" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-semibold">Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>                    </div>

                            </div>
                        </template>
                    </div>

                </div>

                {{-- 3. Sticky Bottom Bar: Review & Submit --}}
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-8 shadow-md sticky bottom-4 z-30" style="animation: fadeSlideUp 0.4s ease 0.2s both">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                        
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-[#4D5E54] font-bold block mb-1">Ringkasan Biaya Pendaftaran</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl sm:text-3xl font-bold text-[#1B3B2B]" x-text="calculateTotalFormatted()"></span>
                                <span class="text-xs text-[#526057]" x-text="'(' + members.length + ' Orang Jamaah)'"></span>
                            </div>
                            <p class="text-[11px] text-[#526057] mt-1">
                                Pembayaran Uang Muka (DP) dilakukan setelah seluruh berkas diverifikasi.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="button" 
                                    @click="openReviewModal()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] active:scale-[0.98] transition-all cursor-pointer border border-[#CCD8C7]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Preview Ringkasan</span>
                            </button>

                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                <span>Kirim Pendaftaran</span>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- MODAL PREVIEW & RINGKASAN PENDAFTARAN --}}
                <div x-show="showReviewModal" 
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
                     @keydown.escape.window="showReviewModal = false">
                    
                    <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 space-y-6 shadow-2xl border border-[#E0E7DC]"
                         @click.outside="showReviewModal = false">
                        
                        <div class="flex items-center justify-between pb-4 border-b border-[#E0E7DC]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-[#12271E]">Ringkasan Dokumen & Data Jamaah</h3>
                                    <p class="text-xs text-[#526057]">Pastikan foto dokumen dan data identitas telah cocok sebelum mengirim formulir.</p>
                                </div>
                            </div>
                            <button type="button" @click="showReviewModal = false" class="text-zinc-400 hover:text-zinc-700 text-lg font-bold p-1">&times;</button>
                        </div>

                        {{-- Ringkasan Paket Terpilih --}}
                        <div class="p-4 rounded-2xl bg-[#F8FAF7] border border-[#CCD8C7] flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-[#526057] block">Paket Dipilih</span>
                                <h4 class="font-bold text-sm text-[#1B3B2B]" x-text="getSelectedPackage()?.name || 'Paket Umrah'"></h4>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-[#526057] block">Total Estimasi</span>
                                <span class="font-bold text-base text-[#1B3B2B]" x-text="calculateTotalFormatted()"></span>
                            </div>
                        </div>

                        {{-- List Jamaah Review --}}
                        <div class="space-y-4">
                            <template x-for="(m, idx) in members" :key="m.uid">
                                <div class="p-4 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] space-y-4">
                                    <div class="flex items-center justify-between border-b border-[#E0E7DC] pb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center text-[10px] font-bold" x-text="idx + 1"></span>
                                            <span class="font-bold text-xs sm:text-sm text-[#12271E]" x-text="m.name || ('Jamaah ' + (idx + 1))"></span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#EFF3EB] text-[#1B3B2B] font-semibold uppercase" x-text="m.relationship"></span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {{-- Foto Preview Side --}}
                                        <div class="space-y-2">
                                            <span class="text-[10px] font-bold uppercase text-[#526057]">Preview Berkas Foto</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="rounded-xl border border-[#CCD8C7] overflow-hidden bg-white p-1 text-center">
                                                    <span class="text-[9px] font-bold block text-zinc-500 mb-1">Foto KTP</span>
                                                    <template x-if="m.ktpPreviewUrl">
                                                        <img :src="m.ktpPreviewUrl" class="h-20 w-full object-contain mx-auto">
                                                    </template>
                                                    <template x-if="!m.ktpPreviewUrl">
                                                        <div class="h-20 flex items-center justify-center text-[10px] text-red-500 font-semibold bg-red-50/50">Belum diunggah</div>
                                                    </template>
                                                </div>
                                                <div class="rounded-xl border border-[#CCD8C7] overflow-hidden bg-white p-1 text-center">
                                                    <span class="text-[9px] font-bold block text-zinc-500 mb-1">Foto KK</span>
                                                    <template x-if="m.kkPreviewUrl">
                                                        <img :src="m.kkPreviewUrl" class="h-20 w-full object-contain mx-auto">
                                                    </template>
                                                    <template x-if="!m.kkPreviewUrl">
                                                        <div class="h-20 flex items-center justify-center text-[10px] text-red-500 font-semibold bg-red-50/50">Belum diunggah</div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Data Teks Side --}}
                                        <div class="space-y-1.5 text-xs">
                                            <span class="text-[10px] font-bold uppercase text-[#526057] block">Data Identitas KTP</span>
                                            <div class="p-2.5 rounded-xl bg-white border border-[#E0E7DC] space-y-1">
                                                <div class="flex justify-between">
                                                    <span class="text-zinc-500 text-[11px]">NIK:</span>
                                                    <span class="font-mono font-bold" :class="m.nik && m.nik.length === 16 ? 'text-[#12271E]' : 'text-red-600'" x-text="m.nik || 'Belum diisi'"></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-zinc-500 text-[11px]">Tempat, Tgl Lahir:</span>
                                                    <span class="font-semibold text-[#12271E]" x-text="(m.birth_place || '-') + ', ' + (m.birth_date || '-')"></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-zinc-500 text-[11px]">Jenis Kelamin:</span>
                                                    <span class="font-semibold text-[#12271E]" x-text="m.gender === 'laki-laki' ? 'Laki-laki' : (m.gender === 'perempuan' ? 'Perempuan' : '-')"></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-zinc-500 text-[11px]">No. KK:</span>
                                                    <span class="font-mono font-bold" :class="m.no_kk && m.no_kk.length === 16 ? 'text-[#12271E]' : 'text-red-600'" x-text="m.no_kk || 'Belum diisi'"></span>
                                                </div>
                                                <div class="flex justify-between" x-show="m.address">
                                                    <span class="text-zinc-500 text-[11px]">Alamat:</span>
                                                    <span class="font-medium text-[#12271E] truncate max-w-[180px]" x-text="m.address"></span>
                                                </div>
                                                <div class="flex justify-between" x-show="m.no_passport">
                                                    <span class="text-zinc-500 text-[11px]">Paspor:</span>
                                                    <span class="font-mono font-bold text-[#12271E]" x-text="m.no_passport"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E0E7DC]">
                            <button type="button" 
                                    @click="showReviewModal = false"
                                    class="px-5 py-2.5 rounded-xl border border-[#CCD8C7] bg-[#F8FAF7] text-xs font-bold text-[#12271E] hover:bg-[#EFF3EB] cursor-pointer">
                                ✏️ Koreksi / Edit Data
                            </button>
                            <button type="button" 
                                    @click="submitDirectly()"
                                    class="px-7 py-2.5 rounded-xl bg-[#1B3B2B] text-white text-xs font-bold hover:bg-[#132E22] active:scale-[0.98] shadow-md cursor-pointer">
                                ✓ Konfirmasi & Kirim Sekarang
                            </button>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </section>

    @push('scripts')
    <script>
        function registrationForm(packagesList, initialPackageId, initialVariantId, initialRoomType, userName) {
            return {
                packages: packagesList,
                selectedPackageId: initialPackageId,
                selectedVariantId: initialVariantId,
                selectedRoomType: initialRoomType || 'quad',
                showReviewModal: false,

                init() {
                    const currentPkg = this.getSelectedPackage();
                    if (currentPkg && currentPkg.variants && currentPkg.variants.length > 0) {
                        if (!this.selectedVariantId || !currentPkg.variants.some(v => v.id == this.selectedVariantId)) {
                            this.selectedVariantId = currentPkg.variants[0].id;
                        }
                    }
                    this.ensureActiveRoomType();
                },

                get currentVariants() {
                    const pkg = this.getSelectedPackage();
                    return (pkg && pkg.variants) ? pkg.variants : [];
                },

                get currentVariant() {
                    return this.currentVariants.find(v => v.id == this.selectedVariantId) || null;
                },

                get currentPrices() {
                    const variant = this.currentVariant;
                    return (variant && variant.prices) ? variant.prices.filter(p => p.is_active && (parseFloat(p.normal_price) > 0 || parseFloat(p.promo_price) > 0)) : [];
                },

                ensureActiveRoomType() {
                    const prices = this.currentPrices;
                    if (prices.length > 0) {
                        if (!prices.some(p => p.room_type === this.selectedRoomType)) {
                            this.selectedRoomType = prices[0].room_type;
                        }
                    }
                },

                onPackageChange() {
                    const pkg = this.getSelectedPackage();
                    if (pkg && pkg.variants && pkg.variants.length > 0) {
                        this.selectedVariantId = pkg.variants[0].id;
                        this.ensureActiveRoomType();
                    } else {
                        this.selectedVariantId = null;
                    }
                },

                onVariantChange() {
                    this.ensureActiveRoomType();
                },

                getPackageStartingPrice(pkg) {
                    if (pkg.variants && pkg.variants.length > 0) {
                        const lowest = pkg.variants.reduce((min, v) => {
                            const p = parseFloat(v.lowest_price) || 0;
                            return (p > 0 && (min === 0 || p < min)) ? p : min;
                        }, 0);
                        return lowest > 0 ? 'Rp ' + lowest.toLocaleString('id-ID') : '-';
                    }
                    return 'Rp ' + (parseFloat(pkg.price) || 0).toLocaleString('id-ID');
                },

                getPricePerPerson() {
                    const variant = this.currentVariant;
                    if (variant && variant.prices && variant.prices.length > 0) {
                        const priceObj = variant.prices.find(p => p.room_type === this.selectedRoomType && p.is_active);
                        if (priceObj) {
                            return parseFloat(priceObj.promo_price || priceObj.normal_price) || 0;
                        }
                        return parseFloat(variant.lowest_price) || 0;
                    }
                    const pkg = this.getSelectedPackage();
                    return pkg ? (parseFloat(pkg.price) || 0) : 0;
                },

                members: [
                    {
                        uid: Date.now(),
                        name: userName,
                        relationship: 'diri_sendiri',
                        birth_place: '{{ addslashes(auth()->user()->birth_place ?? '') }}',
                        birth_date: '{{ auth()->user()->birth_date ? auth()->user()->birth_date->format('Y-m-d') : '' }}',
                        gender: '{{ auth()->user()->gender ?? '' }}',
                        address: '{{ addslashes(auth()->user()->address ?? '') }}',
                        nik: '',
                        no_kk: '',
                        no_passport: '',
                        ktpPreviewUrl: null,
                        kkPreviewUrl: null,
                        passportPreviewUrl: null,
                        marriageBookPreviewUrl: null,
                        birthCertificatePreviewUrl: null,
                        ocrKtpStatus: 'idle',
                        ocrKtpMessage: '',
                        ocrKtpProgress: 0,
                        ocrKkStatus: 'idle',
                        ocrKkMessage: '',
                        ocrKkProgress: 0,
                        ocrPassportStatus: 'idle',
                        ocrPassportMessage: '',
                        kkRawText: '',
                        nikCrossCheckStatus: 'none',
                    }
                ],
                addMember() {
                    this.members.push({
                        uid: Date.now() + Math.random(),
                        name: '',
                        relationship: 'istri',
                        birth_place: '',
                        birth_date: '',
                        gender: '',
                        address: '',
                        nik: '',
                        no_kk: '',
                        no_passport: '',
                        ktpPreviewUrl: null,
                        kkPreviewUrl: null,
                        passportPreviewUrl: null,
                        marriageBookPreviewUrl: null,
                        birthCertificatePreviewUrl: null,
                        ocrKtpStatus: 'idle',
                        ocrKtpMessage: '',
                        ocrKtpProgress: 0,
                        ocrKkStatus: 'idle',
                        ocrKkMessage: '',
                        ocrKkProgress: 0,
                        ocrPassportStatus: 'idle',
                        ocrPassportMessage: '',
                        kkRawText: '',
                        nikCrossCheckStatus: 'none',
                    });
                },
                removeMember(index) {
                    if (this.members.length > 1) {
                        this.members.splice(index, 1);
                    }
                },
                getSelectedPackage() {
                    return this.packages.find(p => p.id == this.selectedPackageId);
                },
                calculateTotal() {
                    return this.getPricePerPerson() * this.members.length;
                },
                calculateTotalFormatted() {
                    const total = this.calculateTotal();
                    return 'Rp ' + total.toLocaleString('id-ID');
                },

                openReviewModal() {
                    this.showReviewModal = true;
                },

                submitDirectly() {
                    this.showReviewModal = false;
                    document.getElementById('registrationFormElement').submit();
                },

                // ──────────────────────────────────────────
                // KTP PREVIEW & OCR HANDLER
                // ──────────────────────────────────────────

                async handleKtpFileSelect(event, member) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // 1. Create instant preview URL
                    member.ktpPreviewUrl = URL.createObjectURL(file);

                    member.ocrKtpStatus = 'scanning';
                    member.ocrKtpProgress = 10;
                    member.ocrKtpMessage = 'Sedang memeriksa kualitas & membaca data KTP...';

                    try {
                        if (window.OcrScanner && window.OcrScanner.scanDocument) {
                            const res = await window.OcrScanner.scanDocument(file, 'ktp', (progress) => {
                                member.ocrKtpProgress = progress;
                                member.ocrKtpMessage = `Membaca data KTP... (${progress}%)`;
                            });

                            if (res.success && res.data) {
                                let foundCount = 0;

                                // Auto-fill NIK if extracted exactly
                                if (res.data.nik && /^[0-9]{16}$/.test(res.data.nik)) {
                                    member.nik = res.data.nik;
                                    foundCount++;
                                }

                                // Auto-fill Nama
                                if (res.data.name) {
                                    member.name = res.data.name;
                                    foundCount++;
                                }

                                // Auto-fill Tempat Lahir
                                if (res.data.birth_place) {
                                    member.birth_place = res.data.birth_place;
                                    foundCount++;
                                }

                                // Auto-fill Tanggal Lahir
                                if (res.data.birth_date) {
                                    member.birth_date = res.data.birth_date;
                                    foundCount++;
                                }

                                // Auto-fill Jenis Kelamin
                                if (res.data.gender) {
                                    member.gender = res.data.gender;
                                    foundCount++;
                                }

                                // Auto-fill Alamat
                                if (res.data.address) {
                                    member.address = res.data.address;
                                    foundCount++;
                                }

                                // Quality warning check
                                if (res.quality && !res.quality.isAcceptable && res.quality.warning) {
                                    member.ocrKtpStatus = 'quality_warning';
                                    member.ocrKtpMessage = `⚠ ${res.quality.warning} Mohon cocokkan NIK dan Nama Anda dengan teliti.`;
                                } else if (foundCount > 0) {
                                    member.ocrKtpStatus = 'success';
                                    member.ocrKtpMessage = `✓ Data KTP berhasil dibaca (${foundCount} kolom terisi otomatis). Silakan periksa atau koreksi jika diperlukan.`;
                                } else {
                                    member.ocrKtpStatus = 'warning';
                                    member.ocrKtpMessage = '⚠ NIK atau Nama belum terbaca dengan yakin. Silakan masukkan NIK 16 digit secara manual.';
                                }

                                this.checkNikCrossMatch(member);
                            } else {
                                member.ocrKtpStatus = 'warning';
                                member.ocrKtpMessage = '⚠ Gagal membaca KTP otomatis. Silakan masukkan data secara manual.';
                            }
                        }
                    } catch (err) {
                        console.error('KTP OCR Error:', err);
                        member.ocrKtpStatus = 'warning';
                        member.ocrKtpMessage = '⚠ Gagal memproses OCR KTP. Silakan isi data secara manual.';
                    }
                },

                removeKtpFile(member, index) {
                    member.ktpPreviewUrl = null;
                    member.ocrKtpStatus = 'idle';
                    member.ocrKtpMessage = '';
                    const el = document.getElementById('ktp_input_' + index);
                    if (el) el.value = '';
                },

                // ──────────────────────────────────────────
                // KK PREVIEW & OCR HANDLER
                // ──────────────────────────────────────────

                async handleKkFileSelect(event, member) {
                    const file = event.target.files[0];
                    if (!file) return;

                    member.kkPreviewUrl = URL.createObjectURL(file);

                    member.ocrKkStatus = 'scanning';
                    member.ocrKkProgress = 10;
                    member.ocrKkMessage = 'Sedang membaca dokumen Kartu Keluarga...';

                    try {
                        if (window.OcrScanner && window.OcrScanner.scanDocument) {
                            const res = await window.OcrScanner.scanDocument(file, 'kk', (progress) => {
                                member.ocrKkProgress = progress;
                                member.ocrKkMessage = `Membaca No. KK... (${progress}%)`;
                            });

                            if (res.success && res.data) {
                                if (res.data.no_kk && /^[0-9]{16}$/.test(res.data.no_kk)) {
                                    member.no_kk = res.data.no_kk;
                                    member.ocrKkStatus = 'success';
                                    member.ocrKkMessage = `✓ No. KK berhasil dibaca otomatis (${res.data.no_kk}). Silakan periksa atau koreksi jika diperlukan.`;
                                } else {
                                    member.ocrKkStatus = 'warning';
                                    member.ocrKkMessage = '⚠ No. KK belum terbaca dengan yakin. Silakan masukkan No. KK 16 digit secara manual.';
                                }

                                member.kkRawText = res.data.raw_text || '';
                                this.checkNikCrossMatch(member);
                            }
                        }
                    } catch (err) {
                        console.error('KK OCR Error:', err);
                        member.ocrKkStatus = 'warning';
                        member.ocrKkMessage = '⚠ Gagal memproses OCR KK. Silakan isi No. KK secara manual.';
                    }
                },

                removeKkFile(member, index) {
                    member.kkPreviewUrl = null;
                    member.ocrKkStatus = 'idle';
                    member.ocrKkMessage = '';
                    member.kkRawText = '';
                    member.nikCrossCheckStatus = 'none';
                    const el = document.getElementById('kk_input_' + index);
                    if (el) el.value = '';
                },

                checkNikCrossMatch(member) {
                    if (member.nik && member.nik.length === 16 && member.kkRawText && window.OcrScanner) {
                        const match = window.OcrScanner.crossCheckNik(member.nik, member.kkRawText);
                        member.nikCrossCheckStatus = match;
                    } else {
                        member.nikCrossCheckStatus = 'none';
                    }
                },

                // ──────────────────────────────────────────
                // PASSPORT PREVIEW & OCR HANDLER
                // ──────────────────────────────────────────

                async handlePassportFileSelect(event, member) {
                    const file = event.target.files[0];
                    if (!file) return;

                    member.passportPreviewUrl = URL.createObjectURL(file);

                    member.ocrPassportStatus = 'scanning';
                    member.ocrPassportMessage = 'Sedang membaca dokumen Paspor...';

                    try {
                        if (window.OcrScanner && window.OcrScanner.scanDocument) {
                            const res = await window.OcrScanner.scanDocument(file, 'passport', (progress) => {
                                member.ocrPassportMessage = `Membaca Paspor... (${progress}%)`;
                            });

                            if (res.success && res.data && res.data.no_passport) {
                                member.no_passport = res.data.no_passport;
                                member.ocrPassportStatus = 'success';
                                member.ocrPassportMessage = `✓ No. Paspor berhasil dibaca otomatis (${res.data.no_passport}).`;
                            } else {
                                member.ocrPassportStatus = 'idle';
                            }
                        }
                    } catch (err) {
                        console.error('Passport OCR Error:', err);
                        member.ocrPassportStatus = 'idle';
                    }
                },

                removePassportFile(member, index) {
                    member.passportPreviewUrl = null;
                    member.ocrPassportStatus = 'idle';
                    member.ocrPassportMessage = '';
                    const el = document.getElementById('passport_input_' + index);
                    if (el) el.value = '';
                },

                // ──────────────────────────────────────────
                // BUKU NIKAH & AKTA LAHIR PREVIEWS
                // ──────────────────────────────────────────

                handleMarriageBookFileSelect(event, member) {
                    const file = event.target.files[0];
                    if (!file) return;
                    member.marriageBookPreviewUrl = URL.createObjectURL(file);
                },

                removeMarriageBookFile(member, index) {
                    member.marriageBookPreviewUrl = null;
                    const el = document.getElementById('marriage_input_' + index);
                    if (el) el.value = '';
                },

                handleBirthCertificateFileSelect(event, member) {
                    const file = event.target.files[0];
                    if (!file) return;
                    member.birthCertificatePreviewUrl = URL.createObjectURL(file);
                },

                removeBirthCertificateFile(member, index) {
                    member.birthCertificatePreviewUrl = null;
                    const el = document.getElementById('birth_input_' + index);
                    if (el) el.value = '';
                },
            }
        }
    </script>
    @endpush

</x-layouts.jamaah>
