<x-layouts.admin :title="'Galeri Media — Admin Panel'">

    <div class="space-y-6">

        {{-- Breadcrumb & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-[#526057] mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#1B3B2B] transition-colors">Beranda</a>
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-[#12271E]">Galeri Media</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                    Pusat Galeri Foto & Video
                </h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-1">
                    Kelola seluruh media visual travel: foto dokumentasi ibadah, video YouTube, serta media hero Profil Perusahaan.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.galleries.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1B3B2B] hover:bg-[#132E22] text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Tambah Media Baru</span>
                </a>
            </div>
        </div>

        {{-- Info Banner Penanda Hero Profil --}}
        <div class="p-4 rounded-2xl bg-[#EFF3EB] border border-[#CCD8C7] flex items-start gap-3.5">
            <div class="w-8 h-8 rounded-xl bg-[#1B3B2B] text-white flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-[#C2A264]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="flex-grow text-xs text-[#12271E] leading-relaxed">
                <span class="font-bold">Penanda Hero Profil Perusahaan:</span>
                Media yang ditandai <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-400 text-[#12271E] font-bold text-[10px]">★ Hero Profil</span> akan otomatis tampil sebagai <strong>Foto Hero</strong> dan <strong>Video Sematan</strong> di halaman publik <a href="{{ route('profil') }}" target="_blank" class="underline font-semibold text-[#1B3B2B]">/profil</a>. Sistem secara otomatis menjaga maksimal 1 Foto Hero dan 1 Video Hero yang aktif.
            </div>
        </div>

        {{-- Filter & Pencarian Bar --}}
        <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 shadow-xs">
            <form action="{{ route('admin.galleries.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                
                {{-- Search Input --}}
                <div class="sm:col-span-6 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Cari judul dokumentasi atau keterangan..." 
                           class="w-full pl-10 pr-3.5 py-2 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                </div>

                {{-- Filter Tipe (Foto / Video) --}}
                <div class="sm:col-span-3">
                    <select name="type" 
                            onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-[#E0E7DC] bg-white text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                        <option value="">Semua Tipe</option>
                        <option value="photo" {{ request('type') === 'photo' ? 'selected' : '' }}>Foto Saja</option>
                        <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video Saja</option>
                    </select>
                </div>

                {{-- Filter Hero Profil --}}
                <div class="sm:col-span-3 flex items-center gap-2">
                    <select name="hero" 
                            onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded-xl border border-[#E0E7DC] bg-white text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                        <option value="">Semua Media</option>
                        <option value="1" {{ request('hero') === '1' ? 'selected' : '' }}>★ Hanya Hero Profil</option>
                    </select>
                    @if(request()->hasAny(['q', 'hero', 'type']))
                        <a href="{{ route('admin.galleries.index') }}" 
                           class="px-2.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold shrink-0" 
                           title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- Grid Daftar Media Galeri --}}
        @if($galleries->isEmpty())
            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-12 text-center space-y-3">
                <div class="w-12 h-12 mx-auto rounded-full bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#12271E]">Tidak ada media galeri ditemukan</h3>
                <p class="text-xs text-[#526057] max-w-sm mx-auto">
                    Coba sesuaikan filter pencarian atau tambahkan item foto dan video baru ke galeri.
                </p>
                <div class="pt-2">
                    <a href="{{ route('admin.galleries.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#1B3B2B] text-white text-xs font-bold">
                        Tambah Media
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($galleries as $gallery)
                    <div class="bg-white rounded-2xl border border-[#E0E7DC] overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col justify-between group">
                        
                        {{-- Image / Thumbnail Box --}}
                        <div class="aspect-[4/3] w-full bg-[#12271E] relative overflow-hidden">
                            <img src="{{ $gallery->thumbnail_url }}" 
                                 alt="{{ $gallery->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            {{-- Badges --}}
                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1 pointer-events-none">
                                {{-- Hero Profile Badge --}}
                                @if($gallery->is_profile_hero)
                                    <span class="px-2 py-0.5 rounded-lg bg-amber-400 text-[#12271E] font-bold text-[10px] tracking-wide shadow-xs border border-amber-300">
                                        ★ Hero Profil
                                    </span>
                                @else
                                    <span></span>
                                @endif

                                {{-- Type Badge (Foto vs Video) --}}
                                @if($gallery->type === 'video')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-red-600 text-white font-bold text-[10px] shadow-xs">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        <span>Video</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-[#1B3B2B] text-white font-bold text-[10px] shadow-xs">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Foto</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Play Icon Overlay for Videos --}}
                            @if($gallery->type === 'video')
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-10 h-10 rounded-full bg-black/60 text-white flex items-center justify-center backdrop-blur-xs border border-white/40 shadow-lg">
                                        <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Card Content --}}
                        <div class="p-3.5 flex-grow flex flex-col justify-between space-y-2.5">
                            <div>
                                <h3 class="text-xs font-bold text-[#12271E] leading-snug line-clamp-2">
                                    {{ $gallery->title }}
                                </h3>
                                @if($gallery->caption)
                                    <p class="text-[11px] text-[#526057] line-clamp-1 mt-1">
                                        {{ $gallery->caption }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-[#E0E7DC] flex items-center justify-between text-[11px]">
                                <span class="flex items-center gap-1.5 {{ $gallery->is_active ? 'text-emerald-700' : 'text-zinc-400' }} font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $gallery->is_active ? 'bg-emerald-500' : 'bg-zinc-300' }}"></span>
                                    <span>{{ $gallery->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </span>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" 
                                       class="p-1.5 rounded-lg text-[#526057] hover:text-[#1B3B2B] hover:bg-[#EFF3EB] transition-colors"
                                       title="Edit Item">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </a>
                                    <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" onsubmit="return confirm('Hapus item galeri ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors cursor-pointer"
                                                title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $galleries->links() }}
            </div>
        @endif

    </div>

</x-layouts.admin>
