<x-layouts.admin title="Kelola Paket">
    <div x-data="{
            deleteModalOpen: false,
            itemToDelete: null,
            itemNameToDelete: '',
            openDeleteModal(id, name) {
                this.itemToDelete = id;
                this.itemNameToDelete = name;
                this.deleteModalOpen = true;
            },
            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.itemToDelete = null;
                this.itemNameToDelete = '';
            }
        }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header & Stats -->
        <div class="mb-8" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.05s;">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#122B1F]">Daftar Paket</h1>
                    <p class="text-[#526057] text-sm mt-1">Kelola data paket umroh induk.</p>
                </div>
                <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center justify-center bg-[#1B3B2B] text-white rounded-xl px-4 py-2 text-xs font-semibold hover:bg-[#12271E] transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Paket
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs p-5">
                    <p class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">Total Paket</p>
                    <p class="text-2xl font-bold text-[#122B1F]">{{ $totalPackages ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs p-5">
                    <p class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">Paket Aktif</p>
                    <p class="text-2xl font-bold text-[#122B1F]">{{ $activePackages ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs p-5">
                    <p class="text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">Paket Nonaktif</p>
                    <p class="text-2xl font-bold text-[#122B1F]">{{ $nonactivePackages ?? 0 }}</p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-[#EFF3EB] border border-[#E0E7DC] rounded-xl text-[#1B3B2B] text-sm flex items-center" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.1s;">
            <svg class="w-5 h-5 mr-2 text-[#1B3B2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.1s;">
            <svg class="w-5 h-5 mr-2 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
        @endif

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.15s;">
            <!-- Filters -->
            <div class="p-5 border-b border-[#E0E7DC] bg-[#F8FAF7]">
                <form action="{{ route('admin.packages.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama paket..." class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none">
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="status" class="w-full rounded-xl border border-[#E0E7DC] text-sm px-4 py-2 focus:ring-[#1B3B2B] focus:border-[#1B3B2B] outline-none" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
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
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">Paket</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">Durasi</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">Keberangkatan</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider text-center">Sub-Paket</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider text-center">Status</th>
                            <th class="px-5 py-4 text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        @forelse($packages ?? [] as $package)
                        <tr class="hover:bg-[#F8FAF7] transition-colors">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-[#122B1F]">{{ $package->name }}</p>
                                <p class="text-xs text-[#526057] mt-0.5">{{ $package->slug }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-[#12271E]">{{ $package->duration }} Hari</td>
                            <td class="px-5 py-4 text-sm text-[#12271E]">
                                {{ $package->departure_date ? \Carbon\Carbon::parse($package->departure_date)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-semibold text-[#1B3B2B]">
                                {{ $package->variants_count ?? 0 }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($package->status === 'aktif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#EFF3EB] text-[#1B3B2B] border border-[#E0E7DC]">Aktif</span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.packages.show', $package) }}" 
                                       class="p-2 bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] rounded-xl transition-colors inline-flex items-center gap-1 text-xs font-semibold" 
                                       title="Kelola Sub-Paket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span class="hidden md:inline">Detail</span>
                                    </a>
                                    <a href="{{ route('admin.packages.edit', $package) }}" 
                                       class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors inline-flex items-center gap-1 text-xs font-semibold" 
                                       title="Edit Paket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span class="hidden md:inline">Edit</span>
                                    </a>
                                    <button type="button" 
                                            @click="openDeleteModal('{{ $package->id }}', '{{ addslashes($package->name) }}')" 
                                            class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition-colors inline-flex items-center gap-1 text-xs font-semibold cursor-pointer" 
                                            title="Hapus Paket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span class="hidden md:inline">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-[#526057]">Belum ada data paket.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($packages, 'links'))
            <div class="p-4 border-t border-[#E0E7DC] bg-[#F8FAF7]">
                {{ $packages->links() }}
            </div>
            @endif
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             @keydown.escape.window="closeDeleteModal()">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-[#122B1F]/60 backdrop-blur-xs" @click="closeDeleteModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E0E7DC] relative z-10"
                     @click.outside="closeDeleteModal()">
                    
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]" id="modal-title">Hapus Paket Induk</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus paket <span x-text="'\"' + itemNameToDelete + '\"'" class="font-bold text-[#122B1F]"></span>?
                                    </p>
                                    <p class="text-xs text-red-600 mt-2 bg-red-50 p-2.5 rounded-lg border border-red-100">
                                        Perhatian: Semua data sub-paket (VIP/Bisnis/Ekonomi), harga, fasilitas, dan foto terkait akan ikut terhapus secara permanen.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form :action="'{{ url('admin/packages') }}/' + itemToDelete" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-sm cursor-pointer">
                                Ya, Hapus Paket Ini
                            </button>
                        </form>
                        <button type="button" @click="closeDeleteModal()" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
