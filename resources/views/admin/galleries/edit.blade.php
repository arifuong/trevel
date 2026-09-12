<x-layouts.admin :title="'Edit Media Galeri — Admin Panel'">

    <div class="max-w-3xl mx-auto space-y-6"
         x-data="{
             type: '{{ old('type', $gallery->type) }}',
             videoUrl: @js(old('video_url', $gallery->video_url ?? '')),
             imagePreview: null,
             thumbnailPreview: null,
             handleImageChange(e) {
                 const file = e.target.files[0];
                 this.imagePreview = file ? URL.createObjectURL(file) : null;
             },
             handleThumbChange(e) {
                 const file = e.target.files[0];
                 this.thumbnailPreview = file ? URL.createObjectURL(file) : null;
             },
             getEmbedUrl() {
                 if (!this.videoUrl) return '';
                 let url = this.videoUrl.trim();
                 let match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed|v|shorts)\/|youtube\.com\/watch\?.*v=)([a-zA-Z0-9_-]{11})/i);
                 if (match) return 'https://www.youtube.com/embed/' + match[1];
                 if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return 'https://www.youtube.com/embed/' + url;
                 return '';
             }
         }">

        {{-- Breadcrumb & Header --}}
        <div>
            <nav class="flex items-center gap-2 text-xs text-[#526057] mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#1B3B2B] transition-colors">Beranda</a>
                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.galleries.index') }}" class="hover:text-[#1B3B2B] transition-colors">Galeri Media</a>
                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="font-semibold text-[#12271E]">Edit Media</span>
            </nav>

            <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                Edit Media Galeri
            </h1>
            <p class="text-xs sm:text-sm text-[#526057] mt-1">
                Perbarui informasi foto dokumentasi atau video YouTube.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs">
            <form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- 1. PILIHAN JENIS MEDIA (FOTO / VIDEO) --}}
                <div>
                    <label class="block text-xs font-bold text-[#12271E] mb-2">
                        Jenis Item Media <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                               :class="type === 'photo' ? 'border-[#1B3B2B] bg-[#EFF3EB] text-[#1B3B2B] font-bold shadow-xs' : 'border-[#E0E7DC] text-[#526057] hover:bg-zinc-50'">
                            <input type="radio" name="type" value="photo" x-model="type" class="sr-only">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 :class="type === 'photo' ? 'bg-[#1B3B2B] text-white' : 'bg-zinc-100 text-zinc-500'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs block">Foto / Gambar</span>
                                <span class="text-[11px] font-normal text-[#526057]">File JPG, PNG, WEBP</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                               :class="type === 'video' ? 'border-red-600 bg-red-50 text-red-900 font-bold shadow-xs' : 'border-[#E0E7DC] text-[#526057] hover:bg-zinc-50'">
                            <input type="radio" name="type" value="video" x-model="type" class="sr-only">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 :class="type === 'video' ? 'bg-red-600 text-white' : 'bg-zinc-100 text-zinc-500'">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs block">Video YouTube</span>
                                <span class="text-[11px] font-normal text-[#526057]">Tautan video sematan</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- 2. PENANDA MEDIA HERO PROFIL PERUSAHAAN --}}
                <div class="p-4 rounded-2xl bg-[#EFF3EB] border border-[#CCD8C7]">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               name="is_profile_hero" 
                               value="1" 
                               {{ old('is_profile_hero', $gallery->is_profile_hero) ? 'checked' : '' }}
                               class="mt-0.5 w-4 h-4 rounded border-zinc-300 text-[#1B3B2B] focus:ring-[#1B3B2B]">
                        <div>
                            <span class="text-xs font-bold text-[#12271E] block">
                                Jadikan media ini sebagai Foto / Video Hero di halaman Profil Perusahaan (/profil)
                            </span>
                            <span class="text-[11px] text-[#526057] block mt-1 leading-relaxed">
                                Jika diaktifkan, media ini akan otomatis dijadikan sebagai <strong>Foto Hero Utama</strong> (jika tipe Foto) atau <strong>Video Profil Utama</strong> (jika tipe Video) di halaman publik <a href="{{ route('profil') }}" target="_blank" class="underline text-[#1B3B2B] font-semibold">/profil</a>. Sistem otomatis memastikan hanya 1 Foto dan 1 Video Hero aktif pada saat bersamaan.
                            </span>
                        </div>
                    </label>
                </div>

                {{-- 3. INPUT KHUSUS FOTO --}}
                <div x-show="type === 'photo'" class="space-y-3">
                    <label class="block text-xs font-bold text-[#12271E] mb-1.5">
                        Foto Saat Ini / Pratinjau
                    </label>
                    <div class="aspect-[4/3] max-w-sm rounded-xl overflow-hidden border border-[#E0E7DC] bg-[#EFF3EB]">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="w-full h-full object-cover" alt="Pratinjau Foto Baru">
                        </template>
                        <template x-if="!imagePreview">
                            <img src="{{ $gallery->thumbnail_url }}" class="w-full h-full object-cover" alt="Foto Saat Ini">
                        </template>
                    </div>

                    <div>
                        <label for="image" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Ganti File Foto <span class="text-xs font-normal text-[#526057]">(Kosongkan jika tidak ingin mengubah)</span>
                        </label>
                        <input type="file" 
                               name="image" 
                               id="image" 
                               accept="image/jpeg,image/png,image/webp,image/jpg"
                               @change="handleImageChange($event)"
                               class="w-full text-xs text-[#526057] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EFF3EB] file:text-[#1B3B2B] hover:file:bg-[#E0E7DC] file:cursor-pointer cursor-pointer border border-[#E0E7DC] rounded-xl p-1.5 focus:outline-none">
                        @error('image')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 4. INPUT KHUSUS VIDEO --}}
                <div x-show="type === 'video'" class="space-y-4" style="display: none;">
                    <div>
                        <label for="video_url" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Tautan Video YouTube <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-red-600">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </div>
                            <input type="text" 
                                   name="video_url" 
                                   id="video_url" 
                                   x-model="videoUrl"
                                   placeholder="Contoh: https://youtu.be/QrYcpXEC0RU"
                                   class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-sm text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                        </div>
                        @error('video_url')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Live Preview Embed Video --}}
                    <template x-if="getEmbedUrl()">
                        <div class="space-y-1.5">
                            <span class="block text-xs font-semibold text-[#12271E]">Pratinjau Sematan Video</span>
                            <div class="aspect-video max-w-md rounded-xl overflow-hidden shadow-md bg-black border border-[#E0E7DC]">
                                <iframe :src="getEmbedUrl()"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        class="w-full h-full">
                                </iframe>
                            </div>
                        </div>
                    </template>

                    {{-- Opsional: Upload Kustom Thumbnail Video --}}
                    <div>
                        <label for="video_thumbnail" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Thumbnail Video Kustom <span class="text-xs font-normal text-[#526057]">(Opsional)</span>
                        </label>
                        <input type="file" 
                               name="video_thumbnail" 
                               id="video_thumbnail" 
                               accept="image/jpeg,image/png,image/webp,image/jpg"
                               @change="handleThumbChange($event)"
                               class="w-full text-xs text-[#526057] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EFF3EB] file:text-[#1B3B2B] hover:file:bg-[#E0E7DC] file:cursor-pointer cursor-pointer border border-[#E0E7DC] rounded-xl p-1.5 focus:outline-none">
                        
                        @if($gallery->type === 'video' && $gallery->image_path)
                            <div class="pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-red-600 hover:text-red-700">
                                    <input type="checkbox" name="remove_thumbnail" value="1" class="rounded border-zinc-300 text-red-600 focus:ring-red-500">
                                    <span>Hapus thumbnail kustom (gunakan thumbnail YouTube otomatis)</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 5. INFORMASI TEKS (JUDUL & KETERANGAN) --}}
                <div class="space-y-4 pt-2 border-t border-[#E0E7DC]">
                    <div>
                        <label for="title" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Judul Media Dokumentasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title', $gallery->title) }}"
                               required
                               placeholder="Contoh: Momen Thawaf Jamaah Umrah Ramadhan di Depan Ka'bah"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-sm text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                        @error('title')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="caption" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Keterangan / Caption <span class="text-xs font-normal text-[#526057]">(Opsional)</span>
                        </label>
                        <textarea name="caption" 
                                  id="caption" 
                                  rows="3"
                                  placeholder="Tuliskan cerita singkat atau keterangan momen kegiatan ini..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-sm text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">{{ old('caption', $gallery->caption) }}</textarea>
                        @error('caption')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sort_order" class="block text-xs font-bold text-[#12271E] mb-1.5">
                                Urutan Prioritas Tampil
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order" 
                                   value="{{ old('sort_order', $gallery->sort_order) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-sm text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gallery->is_active) ? 'checked' : '' }} class="rounded border-zinc-300 text-[#1B3B2B] focus:ring-[#1B3B2B]">
                                <span class="text-xs font-bold text-[#12271E]">Aktifkan di Galeri Publik</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-[#E0E7DC] flex items-center justify-end gap-3">
                    <a href="{{ route('admin.galleries.index') }}" 
                       class="px-5 py-2.5 rounded-xl border border-[#E0E7DC] text-xs font-semibold text-[#526057] hover:bg-[#EFF3EB] transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-[#1B3B2B] hover:bg-[#132E22] text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                        Perbarui Media
                    </button>
                </div>

            </form>
        </div>

    </div>

</x-layouts.admin>
