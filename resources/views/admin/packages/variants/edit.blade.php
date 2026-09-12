<x-layouts.admin>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-[#526057] mb-1">
                <a href="{{ route('admin.packages.index') }}" class="hover:text-[#1B3B2B] transition-colors">Daftar Paket</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('admin.packages.show', $package) }}" class="hover:text-[#1B3B2B] transition-colors">{{ $package->name }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-[#12271E] font-medium">Edit {{ $variant->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-[#12271E]">Edit Sub-Paket: {{ $variant->name }}</h1>
            <p class="text-sm text-[#526057] mt-1">Kelola harga, maskapai, hotel, dan fasilitas sub-paket pada paket <strong>{{ $package->name }}</strong>.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
        <div class="flex items-center mb-1">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-semibold">Mohon periksa kembali input Anda:</span>
        </div>
        <ul class="list-disc list-inside ml-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $existingPrices = $variant->prices->keyBy('room_type');
        $quadPrice = $existingPrices->get('quad');
        $triplePrice = $existingPrices->get('triple');
        $doublePrice = $existingPrices->get('double');
    @endphp

    <form action="{{ route('admin.packages.variants.update', [$package, $variant]) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
          x-data="{
              mainPhotoPreview: '{{ $variant->main_photo ? Storage::url($variant->main_photo) : '' }}',
              previewFile(e, prop) {
                  const file = e.target.files[0];
                  if(file) this[prop] = URL.createObjectURL(file);
              }
          }">
        @csrf
        @method('PUT')

        <!-- Section 1: Informasi Sub-Paket -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">1. Informasi Sub-Paket</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="name">Nama Sub-Paket <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $variant->name) }}" placeholder="Contoh: VIP, Bisnis, Ekonomi" required
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="description">Deskripsi (Opsional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Penjelasan singkat mengenai kelas atau fasilitas sub-paket ini..."
                              class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">{{ old('description', $variant->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="quota">Kuota Kursi <span class="text-red-500">*</span></label>
                    <input type="number" id="quota" name="quota" value="{{ old('quota', $variant->quota) }}" placeholder="0" min="0" required
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="status">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors bg-white">
                        <option value="aktif" {{ old('status', $variant->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $variant->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="sold_out" {{ old('status', $variant->status) == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="sort_order">Urutan Tampil (Opsional)</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $variant->sort_order) }}" placeholder="1"
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>

                <!-- Foto Utama Sub-Paket -->
                <div class="md:col-span-2 pt-2">
                    <label class="block text-xs font-semibold text-[#12271E] mb-2">Foto Utama Sub-Paket</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-24 shrink-0 rounded-xl bg-[#EFF3EB] border border-[#E0E7DC] overflow-hidden flex items-center justify-center p-1">
                            <template x-if="mainPhotoPreview">
                                <img :src="mainPhotoPreview" class="max-w-full max-h-full object-contain">
                            </template>
                            <template x-if="!mainPhotoPreview">
                                <svg class="w-8 h-8 text-[#526057]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="main_photo" id="main_photo" accept="image/*" @change="previewFile($event, 'mainPhotoPreview')"
                                   class="w-full text-sm text-[#526057] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EFF3EB] file:text-[#1B3B2B] hover:file:bg-[#E0E7DC] transition-colors">
                            <p class="text-[11px] text-[#526057] mt-1">PNG, JPG, WEBP maks 5MB. Kosongkan bila tidak diubah.</p>
                            <p class="text-[11px] text-amber-600 mt-1 flex items-start gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <span>Gunakan foto bersih (landmark/hotel) <strong>tanpa</strong> teks tanggal/harga/ribbon tercetak di gambar — info tanggal & durasi ditampilkan otomatis oleh sistem.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Pricing Matrix -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden" 
             x-data="{
                 prices: [
                     { 
                         room_type: 'quad', 
                         label: 'Kamar Kuad (Isi 4)', 
                         normal_price: '{{ old('prices.0.normal_price', $quadPrice ? (float)$quadPrice->normal_price : '') }}', 
                         promo_price: '{{ old('prices.0.promo_price', $quadPrice ? (float)$quadPrice->promo_price : '') }}', 
                         is_active: {{ old('prices.0.is_active', $quadPrice ? ($quadPrice->is_active ? '1' : '0') : '1') == '1' ? 'true' : 'false' }}, 
                         has_promo: {{ old('prices.0.has_promo', ($quadPrice && $quadPrice->promo_price > 0) ? '1' : '0') == '1' ? 'true' : 'false' }} 
                     },
                     { 
                         room_type: 'triple', 
                         label: 'Kamar Triple (Isi 3)', 
                         normal_price: '{{ old('prices.1.normal_price', $triplePrice ? (float)$triplePrice->normal_price : '') }}', 
                         promo_price: '{{ old('prices.1.promo_price', $triplePrice ? (float)$triplePrice->promo_price : '') }}', 
                         is_active: {{ old('prices.1.is_active', $triplePrice ? ($triplePrice->is_active ? '1' : '0') : '1') == '1' ? 'true' : 'false' }}, 
                         has_promo: {{ old('prices.1.has_promo', ($triplePrice && $triplePrice->promo_price > 0) ? '1' : '0') == '1' ? 'true' : 'false' }} 
                     },
                     { 
                         room_type: 'double', 
                         label: 'Kamar Double (Isi 2)', 
                         normal_price: '{{ old('prices.2.normal_price', $doublePrice ? (float)$doublePrice->normal_price : '') }}', 
                         promo_price: '{{ old('prices.2.promo_price', $doublePrice ? (float)$doublePrice->promo_price : '') }}', 
                         is_active: {{ old('prices.2.is_active', $doublePrice ? ($doublePrice->is_active ? '1' : '0') : '1') == '1' ? 'true' : 'false' }}, 
                         has_promo: {{ old('prices.2.has_promo', ($doublePrice && $doublePrice->promo_price > 0) ? '1' : '0') == '1' ? 'true' : 'false' }} 
                     }
                 ]
             }">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7] flex justify-between items-center">
                <div>
                    <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">2. Pricing Matrix (Harga Kamar & Promo)</h2>
                    <p class="text-xs text-[#526057] mt-0.5">Tipe kamar bersifat opsional. Nonaktifkan tipe kamar yang tidak tersedia untuk sub-paket ini.</p>
                </div>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs text-[#526057] border-b border-[#E0E7DC] bg-[#F8FAF7]">
                            <th class="px-4 py-3 font-semibold">Tipe Kamar</th>
                            <th class="px-4 py-3 font-semibold">Harga</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        <template x-for="(price, index) in prices" :key="index">
                            <tr class="transition-colors hover:bg-[#F8FAF7]/50" :class="!price.is_active ? 'opacity-60 bg-[#F8FAF7]/40' : ''">
                                <td class="px-4 py-4 font-medium text-[#12271E] align-top">
                                    <input type="hidden" :name="`prices[${index}][room_type]`" :value="price.room_type">
                                    <span x-text="price.label" class="font-bold"></span>
                                    <span x-show="!price.is_active" class="block text-[10px] text-gray-500 font-normal mt-0.5">Nonaktif (Tidak Tersedia)</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#526057] uppercase tracking-wider mb-1">Harga Normal</label>
                                            <input type="number" :name="`prices[${index}][normal_price]`" x-model="price.normal_price" 
                                                :placeholder="price.is_active ? 'Contoh: 35000000' : 'Kosong (Nonaktif)'" 
                                                :required="price.is_active" 
                                                :class="!price.is_active ? 'bg-gray-100/80 text-gray-500 border-dashed' : 'bg-white'"
                                                class="w-full rounded-xl border border-[#E0E7DC] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                                        </div>
                                        
                                        <div x-show="price.is_active" class="flex items-center gap-2">
                                            <input type="hidden" :name="`prices[${index}][has_promo]`" value="0">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" :name="`prices[${index}][has_promo]`" value="1" x-model="price.has_promo" class="sr-only peer">
                                                <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1B3B2B]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1B3B2B]"></div>
                                                <span class="ms-2 text-xs font-medium text-[#526057]">Aktifkan Harga Promo</span>
                                            </label>
                                        </div>

                                        <div x-show="price.is_active && price.has_promo" x-transition>
                                            <label class="block text-[10px] font-semibold text-[#1B3B2B] uppercase tracking-wider mb-1">Harga Promo</label>
                                            <input type="number" :name="`prices[${index}][promo_price]`" x-model="price.promo_price" 
                                                :placeholder="price.is_active ? 'Contoh: 33000000' : '-'" 
                                                :class="!price.is_active ? 'bg-gray-100/80 text-gray-500 border-dashed' : 'bg-white'"
                                                class="w-full rounded-xl border border-[#1B3B2B]/30 bg-[#EFF3EB]/30 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center align-top">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="hidden" :name="`prices[${index}][is_active]`" value="0">
                                        <input type="checkbox" :name="`prices[${index}][is_active]`" value="1" x-model="price.is_active" class="w-4 h-4 text-[#1B3B2B] rounded border-gray-300 focus:ring-[#1B3B2B]">
                                        <span class="text-xs font-semibold" :class="price.is_active ? 'text-[#1B3B2B]' : 'text-gray-400'" x-text="price.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                    </label>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Maskapai Penerbangan -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden"
             x-data="{
                 airlines: {{ isset($airlines) ? Js::from($airlines) : '[]' }},
                 selectedAirlines: {{ json_encode(old('airline_ids', $selectedAirlineIds ?? [])) }},
                 get selectedNames() {
                     if (this.selectedAirlines.length === 0) return 'Belum ada maskapai yang dipilih';
                     return this.selectedAirlines.map(id => {
                         let a = this.airlines.find(x => x.id == id);
                         return a ? a.name : '';
                     }).filter(n => n !== '').join(' / ');
                 }
             }">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">3. Maskapai Penerbangan</h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-xs font-semibold text-[#12271E] mb-2">Pilih Maskapai</p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="airline in airlines" :key="airline.id">
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="airline_ids[]" :value="airline.id" x-model="selectedAirlines" class="peer sr-only">
                                <div class="flex items-center gap-2 p-2 pr-4 border border-[#E0E7DC] rounded-xl bg-white hover:bg-[#F8FAF7] peer-checked:border-[#1B3B2B] peer-checked:bg-[#EFF3EB] peer-checked:ring-1 peer-checked:ring-[#1B3B2B] transition-all">
                                    <div class="w-8 h-8 rounded bg-[#F8FAF7] border border-[#E0E7DC] overflow-hidden flex items-center justify-center">
                                        <img x-show="airline.logo" :src="airline.logo ? (airline.logo.startsWith('/') || airline.logo.startsWith('http') ? airline.logo : '/storage/' + airline.logo) : ''" class="w-full h-full object-contain p-1">
                                        <svg x-show="!airline.logo" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                                    </div>
                                    <span class="text-sm font-medium text-[#12271E]" x-text="airline.name"></span>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
                <div class="bg-[#F8FAF7] border border-[#E0E7DC] rounded-xl p-3 flex items-start gap-2">
                    <svg class="w-5 h-5 text-[#526057] mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-[11px] font-semibold text-[#526057] uppercase tracking-wider">Preview Tampilan Maskapai</p>
                        <p class="text-sm font-medium text-[#1B3B2B] mt-0.5" x-text="selectedNames"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Hotel Makkah -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden"
             x-data="{
                 hotelId: '{{ old('hotel_makkah_id', $variant->hotel_makkah_id ?? '') }}',
                 hotelDetails: null,
                 loading: false,
                 fetchHotel() {
                     if(!this.hotelId) { this.hotelDetails = null; return; }
                     this.loading = true;
                     fetch(`/admin/api/hotels/search?city=makkah`)
                        .then(res => res.json())
                        .then(data => {
                            this.hotelDetails = data.find(h => h.id == this.hotelId) || null;
                            this.loading = false;
                        })
                        .catch(() => { this.loading = false; });
                 }
             }"
             x-init="if(hotelId) fetchHotel()">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">4. Hotel Makkah</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-[#12271E]" for="hotel_makkah_id">Pilih Hotel Makkah</label>
                    <a href="{{ route('admin.hotels.create') }}" target="_blank" class="text-xs text-[#1B3B2B] hover:underline font-medium">Tambah hotel Makkah baru &rarr;</a>
                </div>
                <select id="hotel_makkah_id" name="hotel_makkah_id" x-model="hotelId" @change="fetchHotel"
                        class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors bg-white">
                    <option value="">-- Pilih Hotel Makkah --</option>
                    @if(isset($hotelsMakkah))
                        @foreach($hotelsMakkah as $h)
                            <option value="{{ $h->id }}" {{ old('hotel_makkah_id', $variant->hotel_makkah_id) == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                        @endforeach
                    @endif
                </select>

                <div x-show="loading" class="mt-4 text-xs text-[#526057] flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memuat detail hotel...
                </div>

                <div x-show="hotelDetails" x-transition class="mt-4 border border-[#E0E7DC] rounded-2xl bg-[#F8FAF7] overflow-hidden flex flex-col md:flex-row shadow-2xs">
                    <div class="w-full md:w-2/5 shrink-0 bg-[#EFF3EB] relative min-h-[140px] flex items-center justify-center border-b md:border-b-0 md:border-r border-[#E0E7DC]">
                        <template x-if="hotelDetails && hotelDetails.main_photo">
                            <img :src="hotelDetails.main_photo" class="w-full h-full object-cover">
                        </template>
                        <template x-if="hotelDetails && !hotelDetails.main_photo && hotelDetails.photos && hotelDetails.photos.length > 0">
                            <img :src="hotelDetails.photos[0].url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!hotelDetails || (!hotelDetails.main_photo && (!hotelDetails.photos || hotelDetails.photos.length === 0))">
                            <div class="p-6 text-center text-xs text-[#526057]">
                                <svg class="w-8 h-8 text-[#CCD8C7] mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                <span>Belum ada foto</span>
                            </div>
                        </template>
                    </div>

                    <div class="p-5 flex-1 space-y-3">
                        {{-- Header Badges: Star Rating (Gold) & Distance (Ruby Red) --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                                <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span x-text="'Bintang ' + (hotelDetails?.star_rating || '5')"></span>
                            </span>

                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200"
                                  x-show="hotelDetails?.distance_to_haram">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                <span x-text="(hotelDetails?.distance_to_haram || '0') + 'm ke Masjidil Haram'"></span>
                            </span>
                        </div>

                        <div>
                            <h4 class="font-serif font-bold text-base text-[#12271E]" x-text="hotelDetails?.name"></h4>
                            <p class="text-xs text-[#526057] mt-1 line-clamp-2" x-text="hotelDetails?.description || 'Tidak ada deskripsi.'"></p>
                        </div>

                        {{-- Fasilitas Chip Tags --}}
                        <template x-if="hotelDetails && hotelDetails.facilities && hotelDetails.facilities.length > 0">
                            <div class="pt-2 border-t border-[#E0E7DC]">
                                <span class="text-[10px] uppercase font-bold text-[#4D5E54] tracking-wider block mb-1.5">Fasilitas Hotel:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="fac in hotelDetails.facilities" :key="fac.id">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#CCD8C7] rounded-lg text-xs font-medium text-[#12271E] shadow-2xs">
                                            <svg class="w-3 h-3 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="fac.name"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Hotel Madinah -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden"
             x-data="{
                 hotelId: '{{ old('hotel_madinah_id', $variant->hotel_madinah_id ?? '') }}',
                 hotelDetails: null,
                 loading: false,
                 fetchHotel() {
                     if(!this.hotelId) { this.hotelDetails = null; return; }
                     this.loading = true;
                     fetch(`/admin/api/hotels/search?city=madinah`)
                        .then(res => res.json())
                        .then(data => {
                            this.hotelDetails = data.find(h => h.id == this.hotelId) || null;
                            this.loading = false;
                        })
                        .catch(() => { this.loading = false; });
                 }
             }"
             x-init="if(hotelId) fetchHotel()">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">5. Hotel Madinah</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-[#12271E]" for="hotel_madinah_id">Pilih Hotel Madinah</label>
                    <a href="{{ route('admin.hotels.create') }}" target="_blank" class="text-xs text-[#1B3B2B] hover:underline font-medium">Tambah hotel Madinah baru &rarr;</a>
                </div>
                <select id="hotel_madinah_id" name="hotel_madinah_id" x-model="hotelId" @change="fetchHotel"
                        class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors bg-white">
                    <option value="">-- Pilih Hotel Madinah --</option>
                    @if(isset($hotelsMadinah))
                        @foreach($hotelsMadinah as $h)
                            <option value="{{ $h->id }}" {{ old('hotel_madinah_id', $variant->hotel_madinah_id) == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                        @endforeach
                    @endif
                </select>

                <div x-show="loading" class="mt-4 text-xs text-[#526057] flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memuat detail hotel...
                </div>

                <div x-show="hotelDetails" x-transition class="mt-4 border border-[#E0E7DC] rounded-2xl bg-[#F8FAF7] overflow-hidden flex flex-col md:flex-row shadow-2xs">
                    <div class="w-full md:w-2/5 shrink-0 bg-[#EFF3EB] relative min-h-[140px] flex items-center justify-center border-b md:border-b-0 md:border-r border-[#E0E7DC]">
                        <template x-if="hotelDetails && hotelDetails.main_photo">
                            <img :src="hotelDetails.main_photo" class="w-full h-full object-cover">
                        </template>
                        <template x-if="hotelDetails && !hotelDetails.main_photo && hotelDetails.photos && hotelDetails.photos.length > 0">
                            <img :src="hotelDetails.photos[0].url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!hotelDetails || (!hotelDetails.main_photo && (!hotelDetails.photos || hotelDetails.photos.length === 0))">
                            <div class="p-6 text-center text-xs text-[#526057]">
                                <svg class="w-8 h-8 text-[#CCD8C7] mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                <span>Belum ada foto</span>
                            </div>
                        </template>
                    </div>

                    <div class="p-5 flex-1 space-y-3">
                        {{-- Header Badges: Star Rating (Gold) & Distance (Ruby Red) --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                                <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span x-text="'Bintang ' + (hotelDetails?.star_rating || '4')"></span>
                            </span>

                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200"
                                  x-show="hotelDetails?.distance_to_haram">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                <span x-text="(hotelDetails?.distance_to_haram || '0') + 'm ke Masjid Nabawi'"></span>
                            </span>
                        </div>

                        <div>
                            <h4 class="font-serif font-bold text-base text-[#12271E]" x-text="hotelDetails?.name"></h4>
                            <p class="text-xs text-[#526057] mt-1 line-clamp-2" x-text="hotelDetails?.description || 'Tidak ada deskripsi.'"></p>
                        </div>

                        {{-- Fasilitas Chip Tags --}}
                        <template x-if="hotelDetails && hotelDetails.facilities && hotelDetails.facilities.length > 0">
                            <div class="pt-2 border-t border-[#E0E7DC]">
                                <span class="text-[10px] uppercase font-bold text-[#4D5E54] tracking-wider block mb-1.5">Fasilitas Hotel:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="fac in hotelDetails.facilities" :key="fac.id">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#CCD8C7] rounded-lg text-xs font-medium text-[#12271E] shadow-2xs">
                                            <svg class="w-3 h-3 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="fac.name"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Override Include/Exclude -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">6. Fasilitas Include / Exclude</h2>
                <p class="text-xs text-[#526057] mt-0.5">Secara default, sub-paket mewarisi fasilitas dari paket utama. Aktifkan override jika fasilitas berbeda.</p>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Biaya Termasuk (Include) -->
                <div x-data="{
                        hasOverride: {{ old('has_include_override', $variant->has_include_override) ? 'true' : 'false' }},
                        includes: {{ isset($package->includes) ? Js::from($package->includes->pluck('item')->toArray()) : '[]' }},
                        overrideIncludes: {{ json_encode(old('override_includes', $variant->overrideIncludes->pluck('item')->toArray() ?: [])) }}
                     }" class="border border-[#E0E7DC] rounded-xl overflow-hidden bg-[#F8FAF7]">
                    <div class="p-4 border-b border-[#E0E7DC] flex items-center justify-between bg-white">
                        <span class="font-semibold text-sm text-[#12271E]">Biaya Termasuk (Include)</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="has_include_override" value="0">
                            <input type="checkbox" name="has_include_override" value="1" x-model="hasOverride" class="sr-only peer">
                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1B3B2B]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1B3B2B]"></div>
                            <span class="ms-2 text-xs font-medium text-[#526057]">Override</span>
                        </label>
                    </div>
                    
                    <!-- Read-only Default Includes -->
                    <div x-show="!hasOverride" class="p-4 space-y-2">
                        <template x-for="(item, i) in includes" :key="i">
                            <div class="flex items-center gap-2 text-sm text-[#526057]">
                                <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="item"></span>
                            </div>
                        </template>
                        <div x-show="includes.length === 0" class="text-xs text-gray-400 italic">Belum ada data dari paket utama.</div>
                    </div>

                    <!-- Override Inputs -->
                    <div x-show="hasOverride" class="p-4 bg-white" x-transition>
                        <div class="space-y-3">
                            <template x-for="(item, index) in overrideIncludes" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="override_includes[]" x-model="overrideIncludes[index]" placeholder="Contoh: Tiket Pesawat PP"
                                           class="flex-1 rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                                    <button type="button" @click="overrideIncludes.splice(index, 1)" class="p-2 text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="overrideIncludes.push('')" class="mt-3 text-xs text-[#1B3B2B] hover:text-[#122B1F] font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Item
                        </button>
                    </div>
                </div>

                <!-- Biaya Tidak Termasuk (Exclude) -->
                <div x-data="{
                        hasOverride: {{ old('has_exclude_override', $variant->has_exclude_override) ? 'true' : 'false' }},
                        excludes: {{ isset($package->excludes) ? Js::from($package->excludes->pluck('item')->toArray()) : '[]' }},
                        overrideExcludes: {{ json_encode(old('override_excludes', $variant->overrideExcludes->pluck('item')->toArray() ?: [])) }}
                     }" class="border border-[#E0E7DC] rounded-xl overflow-hidden bg-[#F8FAF7]">
                    <div class="p-4 border-b border-[#E0E7DC] flex items-center justify-between bg-white">
                        <span class="font-semibold text-sm text-[#12271E]">Biaya Tidak Termasuk (Exclude)</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="has_exclude_override" value="0">
                            <input type="checkbox" name="has_exclude_override" value="1" x-model="hasOverride" class="sr-only peer">
                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1B3B2B]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1B3B2B]"></div>
                            <span class="ms-2 text-xs font-medium text-[#526057]">Override</span>
                        </label>
                    </div>
                    
                    <!-- Read-only Default Excludes -->
                    <div x-show="!hasOverride" class="p-4 space-y-2">
                        <template x-for="(item, i) in excludes" :key="i">
                            <div class="flex items-center gap-2 text-sm text-[#526057]">
                                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span x-text="item"></span>
                            </div>
                        </template>
                        <div x-show="excludes.length === 0" class="text-xs text-gray-400 italic">Belum ada data dari paket utama.</div>
                    </div>

                    <!-- Override Inputs -->
                    <div x-show="hasOverride" class="p-4 bg-white" x-transition>
                        <div class="space-y-3">
                            <template x-for="(item, index) in overrideExcludes" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="override_excludes[]" x-model="overrideExcludes[index]" placeholder="Contoh: Paspor"
                                           class="flex-1 rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                                    <button type="button" @click="overrideExcludes.splice(index, 1)" class="p-2 text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="overrideExcludes.push('')" class="mt-3 text-xs text-[#1B3B2B] hover:text-[#122B1F] font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.packages.show', $package) }}" class="px-5 py-2.5 bg-white border border-[#E0E7DC] text-[#12271E] rounded-xl text-sm font-medium hover:bg-[#F8FAF7] transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#1B3B2B] text-white rounded-xl text-sm font-medium hover:bg-[#12271E] transition-colors">
                Perbarui Sub-Paket
            </button>
        </div>
    </form>
</x-layouts.admin>
