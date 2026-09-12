<x-layouts.admin title="Kelola Hotel">
    <div x-data="{
            deleteModalOpen: false,
            itemToDelete: null,
            itemNameToDelete: '',
            openDeleteModal(id, name) {
                this.itemToDelete = id;
                this.itemNameToDelete = name;
                this.deleteModalOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.itemToDelete = null;
                this.itemNameToDelete = '';
                document.body.classList.remove('overflow-hidden');
            }
        }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="mb-8" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.05s;">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#122B1F]">Daftar Hotel</h1>
                    <p class="text-[#526057] text-sm mt-1">Kelola data hotel di Makkah dan Madinah.</p>
                </div>
                <a href="{{ route('admin.hotels.create') }}" class="inline-flex items-center justify-center bg-[#1B3B2B] text-white rounded-xl px-4 py-2 text-xs font-semibold hover:bg-[#12271E] transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Hotel
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden mb-6" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.15s;">
            <div class="p-5 bg-[#F8FAF7]">
                <form action="{{ route('admin.hotels.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama hotel..." class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none">
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="city" class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none" onchange="this.form.submit()">
                            <option value="">Semua Kota</option>
                            <option value="makkah" {{ request('city') == 'makkah' ? 'selected' : '' }}>Makkah</option>
                            <option value="madinah" {{ request('city') == 'madinah' ? 'selected' : '' }}>Madinah</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-[#EFF3EB] text-[#122B1F] border border-[#E0E7DC] rounded-xl px-4 py-2 text-xs font-semibold hover:bg-[#E0E7DC] transition-colors">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Cards List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.2s;">
            @forelse($hotels ?? [] as $hotel)
            @php
                $usageCount = ($hotel->variants_as_makkah_count ?? 0) + ($hotel->variants_as_madinah_count ?? 0);
            @endphp
            <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden flex flex-col hover:border-[#1B3B2B]/30 transition-colors">
                <div class="h-48 bg-gray-100 relative overflow-hidden">
                    @if($hotel->main_photo)
                        <img src="{{ Storage::url($hotel->main_photo) }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-[#526057]">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3 flex gap-2">
                        @if(strtolower($hotel->city) === 'makkah')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-[#EFF3EB] text-[#1B3B2B] shadow-sm border border-[#E0E7DC]/50">Makkah</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 shadow-sm border border-blue-200/50">Madinah</span>
                        @endif
                    </div>
                </div>
                
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-[#122B1F] line-clamp-1" title="{{ $hotel->name }}">{{ $hotel->name }}</h3>
                        <div class="flex items-center text-yellow-400 shrink-0 ml-2">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="ml-1 text-sm font-bold text-[#122B1F]">{{ $hotel->star_rating }}</span>
                        </div>
                    </div>
                    
                    <p class="text-sm text-[#526057] flex-1 line-clamp-2 mb-4">{{ $hotel->address ?? 'Alamat tidak tersedia' }}</p>
                    
                    <div class="pt-4 border-t border-[#E0E7DC] mt-auto">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-medium px-2 py-1 bg-[#F8FAF7] rounded-md text-[#526057] border border-[#E0E7DC]">
                                Digunakan di {{ $usageCount }} sub-paket
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.hotels.edit', $hotel) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors" title="Edit Hotel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @if($usageCount > 0)
                                <button type="button" disabled class="p-2 bg-gray-50 text-gray-400 rounded-xl cursor-not-allowed" title="Tidak dapat dihapus karena sedang digunakan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                @else
                                <button type="button" @click="openDeleteModal('{{ $hotel->id }}', '{{ addslashes($hotel->name) }}')" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition-colors cursor-pointer" title="Hapus Hotel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-[#E0E7DC]">
                <svg class="mx-auto h-12 w-12 text-[#526057] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <p class="text-[#122B1F] font-medium">Belum ada data hotel</p>
                <p class="text-[#526057] text-sm mt-1">Silakan tambah data hotel baru.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if(method_exists($hotels, 'links'))
        <div class="mt-6">
            {{ $hotels->links() }}
        </div>
        @endif

        <!-- Delete Modal (Fixed Viewport Overlay) -->
        <template x-teleport="body">
            <div x-show="deleteModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" 
                 style="display: none;"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-title"
                 @keydown.escape.window="closeDeleteModal()">

                {{-- Backdrop Overlay --}}
                <div x-show="deleteModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#122B1F]/60 backdrop-blur-xs transition-opacity" 
                     @click="closeDeleteModal()"></div>

                {{-- Modal Card --}}
                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative w-full sm:max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-[#E0E7DC] z-10 my-auto"
                     @click.outside="closeDeleteModal()">
                    
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-red-100 text-red-600 sm:mx-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]" id="modal-title">Hapus Hotel</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus hotel <span x-text="'\"' + itemNameToDelete + '\"'" class="font-bold text-[#122B1F]"></span>?
                                    </p>
                                    <p class="text-xs text-red-600 mt-2 bg-red-50 p-2.5 rounded-xl border border-red-100">
                                        Perhatian: Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form :action="'{{ url('admin/hotels') }}/' + itemToDelete" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-xs cursor-pointer">
                                Ya, Hapus Hotel
                            </button>
                        </form>
                        <button type="button" @click="closeDeleteModal()" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-layouts.admin>
