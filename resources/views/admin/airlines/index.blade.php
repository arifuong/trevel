<x-layouts.admin title="Kelola Maskapai">
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
                    <h1 class="text-2xl font-bold text-[#122B1F]">Daftar Maskapai</h1>
                    <p class="text-[#526057] text-sm mt-1">Kelola data maskapai penerbangan.</p>
                </div>
                <a href="{{ route('admin.airlines.create') }}" class="inline-flex items-center justify-center bg-[#1B3B2B] text-white rounded-xl px-4 py-2 text-xs font-semibold hover:bg-[#12271E] transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Maskapai
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.15s;">
            <!-- Filters -->
            <div class="p-5 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <form action="{{ route('admin.airlines.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama maskapai atau kode..." class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none">
                    </div>
                    <button type="submit" class="bg-[#EFF3EB] text-[#122B1F] border border-[#E0E7DC] rounded-xl px-4 py-2 text-xs font-semibold hover:bg-[#E0E7DC] transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F8FAF7] border-b border-[#E0E7DC]">
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider w-16">Logo</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">Maskapai</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider text-center">Kode IATA</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider text-center">Penggunaan</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        @forelse($airlines ?? [] as $airline)
                        <tr class="hover:bg-[#F8FAF7] transition-colors">
                            <td class="px-5 py-4">
                                @if($airline->logo)
                                    <img src="{{ Storage::url($airline->logo) }}" alt="{{ $airline->name }}" class="w-10 h-10 object-contain rounded bg-white border border-[#E0E7DC]">
                                @else
                                    <div class="w-10 h-10 rounded bg-[#EFF3EB] border border-[#E0E7DC] flex items-center justify-center text-[#526057]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-[#122B1F]">{{ $airline->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-medium text-[#526057]">
                                {{ $airline->code ?: '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-[#EFF3EB] text-[#1B3B2B] border border-[#E0E7DC]">
                                    {{ $airline->variants_count ?? 0 }} Paket
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.airlines.edit', $airline) }}" 
                                       class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors inline-flex items-center gap-1 text-xs font-semibold" 
                                       title="Edit Maskapai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span class="hidden md:inline">Edit</span>
                                    </a>
                                    @if(($airline->variants_count ?? 0) > 0)
                                        <button type="button" 
                                                disabled
                                                class="p-2 bg-gray-50 text-gray-400 rounded-xl inline-flex items-center gap-1 text-xs font-semibold cursor-not-allowed" 
                                                title="Tidak dapat dihapus karena sedang digunakan dalam paket">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span class="hidden md:inline">Hapus</span>
                                        </button>
                                    @else
                                        <button type="button" 
                                                @click="openDeleteModal('{{ $airline->id }}', '{{ addslashes($airline->name) }}')" 
                                                class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition-colors inline-flex items-center gap-1 text-xs font-semibold cursor-pointer" 
                                                title="Hapus Maskapai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span class="hidden md:inline">Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-[#526057]">Belum ada data maskapai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($airlines ?? [], 'links'))
            <div class="p-4 border-t border-[#E0E7DC] bg-[#F8FAF7]">
                {{ $airlines->links() }}
            </div>
            @endif
        </div>

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
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]" id="modal-title">Hapus Maskapai</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus maskapai <span x-text="'\"' + itemNameToDelete + '\"'" class="font-bold text-[#122B1F]"></span>? Data tidak dapat dikembalikan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form :action="'{{ url('admin/airlines') }}/' + itemToDelete" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-xs cursor-pointer">
                                Ya, Hapus Maskapai
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
