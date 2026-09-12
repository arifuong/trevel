<x-layouts.admin title="Tambah Maskapai">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-6" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.05s;">
            <a href="{{ route('admin.airlines.index') }}" class="text-[#526057] hover:text-[#122B1F] text-sm flex items-center mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kelola Maskapai &gt; Tambah Maskapai Baru
            </a>
            <h1 class="text-2xl font-bold text-[#122B1F]">Tambah Maskapai Baru</h1>
            <p class="text-[#526057] text-sm mt-1">Tambahkan maskapai penerbangan baru untuk paket perjalanan.</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.1s;">
            <div class="flex">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="font-medium">Terdapat kesalahan pada input Anda:</div>
            </div>
            <ul class="list-disc list-inside mt-2 ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.15s;" x-data="{
            photoPreview: null,
            updatePreview(event) {
                const file = event.target.files[0];
                if(file) {
                    this.photoPreview = URL.createObjectURL(file);
                }
            }
        }">
            <form action="{{ route('admin.airlines.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="p-6 space-y-6">
                    <!-- Basic Info -->
                    <div>
                        <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-4 border-b border-[#E0E7DC] pb-2">Informasi Maskapai</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2 md:col-span-1">
                                <label for="name" class="block text-sm font-medium text-[#122B1F] mb-1">Nama Maskapai <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2.5 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none transition-colors"
                                    placeholder="Contoh: Garuda Indonesia">
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label for="code" class="block text-sm font-medium text-[#122B1F] mb-1">Kode IATA</label>
                                <input type="text" id="code" name="code" value="{{ old('code') }}"
                                    class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2.5 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none transition-colors uppercase"
                                    placeholder="Contoh: GA">
                                <p class="text-xs text-[#526057] mt-1">Opsional.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Media Foto -->
                    <div>
                        <h2 class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-4 border-b border-[#E0E7DC] pb-2">Logo Maskapai</h2>
                        <div class="grid grid-cols-1 gap-6">
                            
                            <!-- Foto Utama -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-[#122B1F] mb-2">Logo</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[#E0E7DC] border-dashed rounded-xl relative overflow-hidden bg-[#F8FAF7]" :class="photoPreview ? 'p-2 border-solid' : ''">
                                    
                                    <!-- Preview Image -->
                                    <template x-if="photoPreview">
                                        <div class="relative w-full max-w-sm mx-auto group flex items-center justify-center min-h-[140px]">
                                            <img :src="photoPreview" class="w-full max-h-40 object-contain rounded-xl" alt="Preview" />
                                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                                                <button type="button" @click="photoPreview = null; $refs.photoInput.value = ''" class="bg-white text-red-600 rounded-lg px-3 py-1.5 text-xs font-semibold shadow-sm hover:bg-gray-50">
                                                    Ganti / Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Empty State -->
                                    <div class="space-y-1 text-center" x-show="!photoPreview">
                                        <svg class="mx-auto h-12 w-12 text-[#526057]" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-[#526057] justify-center">
                                            <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-[#1B3B2B] hover:text-[#122B1F] focus-within:outline-none">
                                                <span>Upload Foto</span>
                                                <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp,image/jpg" class="sr-only" x-ref="photoInput" @change="updatePreview">
                                            </label>
                                        </div>
                                        <p class="text-xs text-[#526057]">PNG, JPG, WEBP maks 2MB</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-[#F8FAF7] border-t border-[#E0E7DC] flex justify-end gap-3">
                    <a href="{{ route('admin.airlines.index') }}" class="px-4 py-2 bg-white border border-[#E0E7DC] text-[#122B1F] rounded-xl text-sm font-medium hover:bg-[#F8FAF7] transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#1B3B2B] text-white rounded-xl text-sm font-medium hover:bg-[#12271E] transition-colors">
                        Simpan Maskapai
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>
