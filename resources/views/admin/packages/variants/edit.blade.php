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
                <span class="text-[#12271E] font-medium">Kelola {{ $variant->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-[#12271E]">Kelola Sub-Paket: {{ $variant->name }}</h1>
            <p class="text-sm text-[#526057] mt-1">Ubah informasi harga, maskapai, hotel, dan foto untuk sub-paket ini.</p>
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

    <form action="{{ route('admin.packages.variants.update', [$package, $variant]) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
          x-data="{
              mainPhotoPreview: '{{ $variant->main_photo ? Storage::url($variant->main_photo) : '' }}',
              outboundLogoPreview: '{{ $variant->airline_departure_logo ? Storage::url($variant->airline_departure_logo) : '' }}',
              inboundLogoPreview: '{{ $variant->airline_return_logo ? Storage::url($variant->airline_return_logo) : '' }}',
              deletedPhotos: [],
              removePhoto(id) {
                  if (!this.deletedPhotos.includes(id)) {
                      this.deletedPhotos.push(id);
                  }
              },
              isDeleted(id) {
                  return this.deletedPhotos.includes(id);
              },
              previewFile(e, prop) {
                  const file = e.target.files[0];
                  if(file) this[prop] = URL.createObjectURL(file);
              },
              makkahPreviews: { main: null, building: null, room: null, dining: null, facility: null, gallery: [] },
              madinahPreviews: { main: null, building: null, room: null, dining: null, facility: null, gallery: [] },
              previewHotelSingle(e, hotel, cat) {
                  const file = e.target.files[0];
                  if(file) this[hotel + 'Previews'][cat] = URL.createObjectURL(file);
              },
              previewHotelMultiple(e, hotel) {
                  const files = e.target.files;
                  if(files && files.length > 0) {
                      this[hotel + 'Previews'].gallery = Array.from(files).map(f => URL.createObjectURL(f));
                  }
              }
          }">
        @csrf
        @method('PUT')

        {{-- Dynamic hidden inputs for deferred photo deletions --}}
        <template x-for="id in deletedPhotos" :key="id">
            <input type="hidden" name="deleted_hotel_photo_ids[]" :value="id">
        </template>

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
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
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
                                <p class="text-[11px] text-[#526057] mt-1">Kosongkan jika tidak ingin mengubah (Maks 5MB).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Pricing Matrix -->
        @php
            $roomLabels = [
                'quad' => 'Kamar Kuad (Isi 4)',
                'triple' => 'Kamar Triple (Isi 3)',
                'double' => 'Kamar Double (Isi 2)',
                'single' => 'Kamar Single (Isi 1)'
            ];
        @endphp
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden"
             x-data="{
                 prices: [
                     @foreach(['quad', 'triple', 'double'] as $idx => $rType)
                         @php
                             $priceRow = $variant->prices->firstWhere('room_type', $rType);
                             $isActiveDefault = $priceRow ? ($priceRow->is_active ? 'true' : 'false') : 'true';
                             $normalVal = old('prices.'.$idx.'.normal_price', $priceRow ? (int) $priceRow->normal_price : '');
                             $promoVal = old('prices.'.$idx.'.promo_price', ($priceRow && $priceRow->promo_price) ? (int) $priceRow->promo_price : '');
                             $activeVal = old('prices.'.$idx.'.is_active') !== null ? (old('prices.'.$idx.'.is_active') == '1' ? 'true' : 'false') : $isActiveDefault;
                         @endphp
                         { 
                             id: {{ $priceRow ? $priceRow->id : 'null' }}, 
                             room_type: '{{ $rType }}', 
                             label: '{{ $roomLabels[$rType] }}', 
                             normal_price: '{{ $normalVal }}', 
                             promo_price: '{{ $promoVal }}', 
                             is_active: {{ $activeVal }} 
                         },
                     @endforeach
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
                            <th class="px-4 py-3 font-semibold">Harga Normal (Rp)</th>
                            <th class="px-4 py-3 font-semibold">Harga Promo / Diskon (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        <template x-for="(price, index) in prices" :key="index">
                            <tr class="transition-colors hover:bg-[#F8FAF7]/50" :class="!price.is_active ? 'opacity-60 bg-[#F8FAF7]/40' : ''">
                                <td class="px-4 py-4 font-medium text-[#12271E]">
                                    <input type="hidden" :name="`prices[${index}][id]`" :value="price.id">
                                    <input type="hidden" :name="`prices[${index}][room_type]`" :value="price.room_type">
                                    <span x-text="price.label" class="font-bold"></span>
                                    <span x-show="!price.is_active" class="block text-[10px] text-gray-500 font-normal mt-0.5">Nonaktif (Tidak Tersedia)</span>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" :name="`prices[${index}][normal_price]`" x-model="price.normal_price" 
                                           :placeholder="price.is_active ? 'Contoh: 35000000' : 'Kosong (Nonaktif)'" 
                                           :required="price.is_active" 
                                           :class="!price.is_active ? 'bg-gray-100/80 text-gray-500 border-dashed' : 'bg-white'"
                                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" :name="`prices[${index}][promo_price]`" x-model="price.promo_price" 
                                           :placeholder="price.is_active ? 'Opsional (Contoh: 33000000)' : '-'" 
                                           :class="!price.is_active ? 'bg-gray-100/80 text-gray-500 border-dashed' : 'bg-white'"
                                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                                </td>
                                <td class="px-4 py-4 text-center">
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

        <!-- Section 3: Maskapai & Penerbangan -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">3. Penerbangan & Maskapai</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Keberangkatan -->
                <div class="space-y-4">
                    <h3 class="font-medium text-[#12271E] text-sm border-b border-[#E0E7DC] pb-2">Penerbangan Berangkat</h3>
                    <div>
                        <label class="block text-xs font-semibold text-[#12271E] mb-2" for="airline_departure">Nama Maskapai Berangkat</label>
                        <input type="text" id="airline_departure" name="airline_departure" value="{{ old('airline_departure', $variant->airline_departure) }}" placeholder="Contoh: Saudia Airlines (Direct)"
                               class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#12271E] mb-2">Logo Maskapai Berangkat</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 shrink-0 rounded-xl bg-[#EFF3EB] border border-[#E0E7DC] overflow-hidden flex items-center justify-center p-1">
                                <template x-if="outboundLogoPreview">
                                    <img :src="outboundLogoPreview" class="w-full h-full object-contain">
                                </template>
                                <template x-if="!outboundLogoPreview">
                                    @if($variant->airline_departure_logo)
                                    <img src="{{ Storage::url($variant->airline_departure_logo) }}" class="w-full h-full object-contain">
                                    @else
                                    <span class="text-xs text-[#526057]">Logo</span>
                                    @endif
                                </template>
                            </div>
                            <input type="file" name="airline_departure_logo" accept="image/*" @change="previewFile($event, 'outboundLogoPreview')"
                                   class="w-full text-sm text-[#526057] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EFF3EB] file:text-[#1B3B2B] hover:file:bg-[#E0E7DC] transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Kepulangan -->
                <div class="space-y-4">
                    <h3 class="font-medium text-[#12271E] text-sm border-b border-[#E0E7DC] pb-2">Penerbangan Pulang</h3>
                    <div>
                        <label class="block text-xs font-semibold text-[#12271E] mb-2" for="airline_return">Nama Maskapai Pulang</label>
                        <input type="text" id="airline_return" name="airline_return" value="{{ old('airline_return', $variant->airline_return) }}" placeholder="Contoh: Saudia Airlines (Direct)"
                               class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#12271E] mb-2">Logo Maskapai Pulang</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 shrink-0 rounded-xl bg-[#EFF3EB] border border-[#E0E7DC] overflow-hidden flex items-center justify-center p-1">
                                <template x-if="inboundLogoPreview">
                                    <img :src="inboundLogoPreview" class="w-full h-full object-contain">
                                </template>
                                <template x-if="!inboundLogoPreview">
                                    @if($variant->airline_return_logo)
                                    <img src="{{ Storage::url($variant->airline_return_logo) }}" class="w-full h-full object-contain">
                                    @else
                                    <span class="text-xs text-[#526057]">Logo</span>
                                    @endif
                                </template>
                            </div>
                            <input type="file" name="airline_return_logo" accept="image/*" @change="previewFile($event, 'inboundLogoPreview')"
                                   class="w-full text-sm text-[#526057] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EFF3EB] file:text-[#1B3B2B] hover:file:bg-[#E0E7DC] transition-colors">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Hotel Makkah -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">4. Hotel Makkah</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="hotel_makkah_name">Nama Hotel Makkah</label>
                    <input type="text" id="hotel_makkah_name" name="hotel_makkah_name" value="{{ old('hotel_makkah_name', $variant->hotel_makkah_name) }}" placeholder="Contoh: Pullman Zamzam Makkah"
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="hotel_makkah_star">Bintang / Kategori</label>
                    <input type="text" id="hotel_makkah_star" name="hotel_makkah_star" value="{{ old('hotel_makkah_star', $variant->hotel_makkah_star) }}" placeholder="Contoh: Bintang 5"
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="hotel_makkah_description">Deskripsi Hotel Makkah</label>
                    <textarea id="hotel_makkah_description" name="hotel_makkah_description" rows="2" placeholder="Lokasi hotel, jarak ke Masjidil Haram..."
                              class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">{{ old('hotel_makkah_description', $variant->hotel_makkah_description) }}</textarea>
                </div>

                <!-- Foto Hotel Makkah Tersimpan & Upload -->
                <div class="md:col-span-2 border-t border-[#E0E7DC] pt-4">
                    <p class="text-xs font-semibold text-[#12271E] mb-3">Foto Hotel Makkah</p>
                    
                    @php
                        $makkahHotelPhotos = $variant->hotelPhotos->where('hotel_type', 'makkah');
                    @endphp

                    @if($makkahHotelPhotos->count() > 0)
                    <div class="mb-4">
                        <p class="text-[11px] text-[#526057] mb-2 font-medium">Foto Hotel Makkah Tersimpan:</p>
                        <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-6 gap-3 space-y-3">
                            @foreach($makkahHotelPhotos as $hPhoto)
                            <div x-show="!isDeleted({{ $hPhoto->id }})" class="break-inside-avoid relative group rounded-xl overflow-hidden border border-[#E0E7DC] bg-white shadow-2xs">
                                <img src="{{ Storage::url($hPhoto->photo_path) }}" class="block w-full h-auto" alt="{{ $hPhoto->category }}">
                                <span class="absolute bottom-1 left-1 bg-black/70 text-white text-[9px] px-1.5 py-0.5 rounded backdrop-blur-xs">{{ ucfirst($hPhoto->category) }}</span>
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" 
                                            @click="if(confirm('Hapus foto ini?')) removePhoto({{ $hPhoto->id }})"
                                            class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors shadow cursor-pointer"
                                            title="Hapus foto ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 p-3 bg-[#F8FAF7] rounded-xl border border-[#E0E7DC]">
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Tambah Foto Utama</label>
                            <input type="file" name="hotel_makkah_main_photo" accept="image/*" @change="previewHotelSingle($event, 'makkah', 'main')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="makkahPreviews.main">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="makkahPreviews.main" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Tambah Foto Gedung</label>
                            <input type="file" name="hotel_makkah_building_photo" accept="image/*" @change="previewHotelSingle($event, 'makkah', 'building')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="makkahPreviews.building">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="makkahPreviews.building" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Tambah Foto Kamar</label>
                            <input type="file" name="hotel_makkah_room_photo" accept="image/*" @change="previewHotelSingle($event, 'makkah', 'room')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="makkahPreviews.room">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="makkahPreviews.room" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Tambah Foto Dining</label>
                            <input type="file" name="hotel_makkah_dining_photo" accept="image/*" @change="previewHotelSingle($event, 'makkah', 'dining')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="makkahPreviews.dining">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="makkahPreviews.dining" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Tambah Foto Fasilitas</label>
                            <input type="file" name="hotel_makkah_facility_photo" accept="image/*" @change="previewHotelSingle($event, 'makkah', 'facility')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="makkahPreviews.facility">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="makkahPreviews.facility" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Foto Gallery Tambahan</label>
                            <input type="file" name="makkah_photos[]" multiple accept="image/*" @change="previewHotelMultiple($event, 'makkah')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="makkahPreviews.gallery.length > 0">
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <template x-for="(imgUrl, gIdx) in makkahPreviews.gallery" :key="gIdx">
                                        <div class="rounded border border-[#E0E7DC] overflow-hidden p-0.5 bg-white">
                                            <img :src="imgUrl" class="h-12 w-auto object-contain rounded">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Hotel Makkah -->
                <div class="md:col-span-2 border-t border-[#E0E7DC] pt-4">
                    <label class="block text-xs font-semibold text-[#12271E] mb-3">Checklist Fasilitas Hotel Makkah</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($hotelFacilities as $facility)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="makkah_facilities[]" value="{{ $facility->id }}" {{ in_array($facility->id, old('makkah_facilities', $selectedMakkahFacilities)) ? 'checked' : '' }} class="w-4 h-4 text-[#1B3B2B] border-gray-300 rounded focus:ring-[#1B3B2B]">
                            <span class="text-sm text-[#526057]">{{ $facility->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Hotel Madinah -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">5. Hotel Madinah</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="hotel_madinah_name">Nama Hotel Madinah</label>
                    <input type="text" id="hotel_madinah_name" name="hotel_madinah_name" value="{{ old('hotel_madinah_name', $variant->hotel_madinah_name) }}" placeholder="Contoh: Millennium Al Aqiq"
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="hotel_madinah_star">Bintang / Kategori</label>
                    <input type="text" id="hotel_madinah_star" name="hotel_madinah_star" value="{{ old('hotel_madinah_star', $variant->hotel_madinah_star) }}" placeholder="Contoh: Bintang 5"
                           class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-[#12271E] mb-2" for="hotel_madinah_description">Deskripsi Hotel Madinah</label>
                    <textarea id="hotel_madinah_description" name="hotel_madinah_description" rows="2" placeholder="Lokasi hotel, jarak ke Masjid Nabawi..."
                              class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">{{ old('hotel_madinah_description', $variant->hotel_madinah_description) }}</textarea>
                </div>

                <!-- Foto Hotel Madinah Tersimpan & Upload -->
                <div class="md:col-span-2 border-t border-[#E0E7DC] pt-4">
                    <p class="text-xs font-semibold text-[#12271E] mb-3">Foto Hotel Madinah</p>
                    
                    @php
                        $madinahHotelPhotos = $variant->hotelPhotos->where('hotel_type', 'madinah');
                    @endphp

                    @if($madinahHotelPhotos->count() > 0)
                    <div class="mb-4">
                        <p class="text-[11px] text-[#526057] mb-2 font-medium">Foto Hotel Madinah Tersimpan:</p>
                        <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-6 gap-3 space-y-3">
                            @foreach($madinahHotelPhotos as $hPhoto)
                            <div x-show="!isDeleted({{ $hPhoto->id }})" class="break-inside-avoid relative group rounded-xl overflow-hidden border border-[#E0E7DC] bg-white shadow-2xs">
                                <img src="{{ Storage::url($hPhoto->photo_path) }}" class="block w-full h-auto" alt="{{ $hPhoto->category }}">
                                <span class="absolute bottom-1 left-1 bg-black/70 text-white text-[9px] px-1.5 py-0.5 rounded backdrop-blur-xs">{{ ucfirst($hPhoto->category) }}</span>
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" 
                                            @click="if(confirm('Hapus foto ini?')) removePhoto({{ $hPhoto->id }})"
                                            class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors shadow cursor-pointer"
                                            title="Hapus foto ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 p-3 bg-[#F8FAF7] rounded-xl border border-[#E0E7DC]">
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Ganti/Tambah Foto Utama</label>
                            <input type="file" name="hotel_madinah_main_photo" accept="image/*" @change="previewHotelSingle($event, 'madinah', 'main')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="madinahPreviews.main">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="madinahPreviews.main" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Ganti/Tambah Foto Gedung</label>
                            <input type="file" name="hotel_madinah_building_photo" accept="image/*" @change="previewHotelSingle($event, 'madinah', 'building')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="madinahPreviews.building">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="madinahPreviews.building" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Ganti/Tambah Foto Kamar</label>
                            <input type="file" name="hotel_madinah_room_photo" accept="image/*" @change="previewHotelSingle($event, 'madinah', 'room')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="madinahPreviews.room">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="madinahPreviews.room" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Ganti/Tambah Foto Dining</label>
                            <input type="file" name="hotel_madinah_dining_photo" accept="image/*" @change="previewHotelSingle($event, 'madinah', 'dining')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="madinahPreviews.dining">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="madinahPreviews.dining" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Ganti/Tambah Foto Fasilitas</label>
                            <input type="file" name="hotel_madinah_facility_photo" accept="image/*" @change="previewHotelSingle($event, 'madinah', 'facility')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="madinahPreviews.facility">
                                <div class="mt-2 rounded-lg border border-[#E0E7DC] overflow-hidden p-1 bg-white inline-block">
                                    <img :src="madinahPreviews.facility" class="h-16 w-auto object-contain rounded">
                                </div>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[#526057] mb-1">Foto Gallery Tambahan</label>
                            <input type="file" name="madinah_photos[]" multiple accept="image/*" @change="previewHotelMultiple($event, 'madinah')" class="w-full text-xs text-[#526057] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF3EB] file:text-[#1B3B2B]">
                            <template x-if="madinahPreviews.gallery.length > 0">
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <template x-for="(imgUrl, gIdx) in madinahPreviews.gallery" :key="gIdx">
                                        <div class="rounded border border-[#E0E7DC] overflow-hidden p-0.5 bg-white">
                                            <img :src="imgUrl" class="h-12 w-auto object-contain rounded">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Hotel Madinah -->
                <div class="md:col-span-2 border-t border-[#E0E7DC] pt-4">
                    <label class="block text-xs font-semibold text-[#12271E] mb-3">Checklist Fasilitas Hotel Madinah</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($hotelFacilities as $facility)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="madinah_facilities[]" value="{{ $facility->id }}" {{ in_array($facility->id, old('madinah_facilities', $selectedMadinahFacilities)) ? 'checked' : '' }} class="w-4 h-4 text-[#1B3B2B] border-gray-300 rounded focus:ring-[#1B3B2B]">
                            <span class="text-sm text-[#526057]">{{ $facility->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6 & 7: Include & Exclude -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Biaya Termasuk -->
            <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden" 
                 x-data="{ includes: [
                     @if($variant->includes->count() > 0)
                         @foreach($variant->includes as $inc)
                             '{{ addslashes($inc->item) }}',
                         @endforeach
                     @else
                         ''
                     @endif
                 ] }">
                <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7] flex justify-between items-center">
                    <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">6. Biaya Termasuk (Include)</h2>
                    <button type="button" @click="includes.push('')" class="text-xs text-[#1B3B2B] hover:text-[#122B1F] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Item
                    </button>
                </div>
                <div class="p-6 space-y-3">
                    <template x-for="(item, index) in includes" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" name="includes[]" x-model="includes[index]" placeholder="Contoh: Tiket Pesawat PP" required
                                   class="flex-1 rounded-xl border border-[#E0E7DC] text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                            <button type="button" @click="includes.splice(index, 1)" class="p-2 text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Biaya Tidak Termasuk -->
            <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden" 
                 x-data="{ excludes: [
                     @if($variant->excludes->count() > 0)
                         @foreach($variant->excludes as $exc)
                             '{{ addslashes($exc->item) }}',
                         @endforeach
                     @else
                         ''
                     @endif
                 ] }">
                <div class="px-6 py-4 border-b border-[#E0E7DC] bg-[#F8FAF7] flex justify-between items-center">
                    <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">7. Biaya Tidak Termasuk (Exclude)</h2>
                    <button type="button" @click="excludes.push('')" class="text-xs text-[#1B3B2B] hover:text-[#122B1F] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Item
                    </button>
                </div>
                <div class="p-6 space-y-3">
                    <template x-for="(item, index) in excludes" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" name="excludes[]" x-model="excludes[index]" placeholder="Contoh: Pembuatan Paspor" required
                                   class="flex-1 rounded-xl border border-[#E0E7DC] text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-colors">
                            <button type="button" @click="excludes.splice(index, 1)" class="p-2 text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.packages.show', $package) }}" class="px-5 py-2.5 bg-white border border-[#E0E7DC] text-[#12271E] rounded-xl text-sm font-medium hover:bg-[#F8FAF7] transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#1B3B2B] text-white rounded-xl text-sm font-medium hover:bg-[#12271E] transition-colors">
                Update Sub-Paket
            </button>
        </div>
    </form>
</x-layouts.admin>
