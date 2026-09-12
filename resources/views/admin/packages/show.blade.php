<x-layouts.admin title="Kelola Paket: {{ $package->name }}">
    <div x-data="{
            deleteModalOpen: false,
            variantToDelete: null,
            variantNameToDelete: '',
            deletePackageModalOpen: false,
            completeBulkModalOpen: false,
            openDeleteModal(id, name) {
                this.variantToDelete = id;
                this.variantNameToDelete = name || '';
                this.deleteModalOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.variantToDelete = null;
                this.variantNameToDelete = '';
                document.body.classList.remove('overflow-hidden');
            },
            openDeletePackageModal() {
                this.deletePackageModalOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closeDeletePackageModal() {
                this.deletePackageModalOpen = false;
                document.body.classList.remove('overflow-hidden');
            }
        }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.05s;">
            <a href="{{ route('admin.packages.index') }}" class="text-[#526057] hover:text-[#122B1F] text-sm flex items-center mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Paket
            </a>
        </div>

        <!-- Package Header Card -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs p-6 mb-8 flex flex-col md:flex-row gap-6 items-start" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.15s;">
            @if($package->main_photo)
            <div class="w-full md:w-64 aspect-[16/10] bg-[#F8FAF7] border border-[#E0E7DC] rounded-xl overflow-hidden flex items-center justify-center p-1 shrink-0">
                <img src="{{ Storage::url($package->main_photo) }}" alt="{{ $package->name }}" class="w-full h-full object-contain">
            </div>
            @else
            <div class="w-full md:w-64 aspect-[16/10] bg-[#F8FAF7] border border-[#E0E7DC] rounded-xl flex items-center justify-center text-[#526057] shrink-0">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            @endif

            <div class="flex-1 w-full">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-2">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h1 class="text-2xl font-bold text-[#122B1F]">{{ $package->name }}</h1>
                            @if($package->status === 'aktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-[#EFF3EB] text-[#1B3B2B] border border-[#E0E7DC]">Aktif</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-200">Nonaktif</span>
                            @endif
                        </div>
                        <p class="text-sm text-[#526057]">{{ $package->slug }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if(isset($readyToDepartCount) && $readyToDepartCount > 0)
                        <button type="button" @click="completeBulkModalOpen = true" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white border border-emerald-600 rounded-xl text-xs font-bold transition-colors flex items-center shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tandai Semua Peserta Selesai ({{ $readyToDepartCount }})
                        </button>
                        @endif
                        <a href="{{ route('paket.detail', $package->slug) }}" target="_blank" class="px-3 py-1.5 bg-white border border-[#E0E7DC] text-[#122B1F] rounded-xl text-xs font-medium hover:bg-[#F8FAF7] transition-colors flex items-center shadow-2xs">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Halaman Publik
                        </a>
                        <a href="{{ route('admin.packages.edit', $package) }}" class="px-3 py-1.5 bg-[#EFF3EB] text-[#1B3B2B] border border-[#E0E7DC] rounded-xl text-xs font-semibold hover:bg-[#E0E7DC] transition-colors flex items-center shadow-2xs">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Paket
                        </a>
                        <button type="button" @click="openDeletePackageModal()" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-xl text-xs font-semibold hover:bg-red-100 transition-colors flex items-center shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Paket
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">Durasi</p>
                        <p class="text-sm font-medium text-[#122B1F]">{{ $package->duration }} Hari</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">Keberangkatan</p>
                        <p class="text-sm font-medium text-[#122B1F]">{{ $package->departure_date ? \Carbon\Carbon::parse($package->departure_date)->translatedFormat('d M Y') : '-' }}</p>
                    </div>
                </div>

                @if($package->description)
                <div class="mt-4 pt-4 border-t border-[#E0E7DC]">
                    <p class="text-sm text-[#526057] line-clamp-2">{{ $package->description }}</p>
                </div>
                @endif

                <!-- Includes / Excludes Default -->
                @if($package->includes->isNotEmpty() || $package->excludes->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-[#E0E7DC] grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($package->includes->isNotEmpty())
                    <div>
                        <p class="text-[10px] font-semibold text-[#1B3B2B] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Biaya Termasuk (Default Seluruh Varian)
                        </p>
                        <ul class="space-y-1">
                            @foreach($package->includes as $inc)
                            <li class="text-xs text-[#526057] flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B3B2B]"></span>
                                {{ $inc->item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($package->excludes->isNotEmpty())
                    <div>
                        <p class="text-[10px] font-semibold text-red-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Biaya Tidak Termasuk (Default Seluruh Varian)
                        </p>
                        <ul class="space-y-1">
                            @foreach($package->excludes as $exc)
                            <li class="text-xs text-[#526057] flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                {{ $exc->item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif
        <!-- Departure & Participants Status Card -->
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs p-6 mb-8" style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.18s;">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-[#E0E7DC]">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 inline-block mb-1">
                        Manajemen Keberangkatan
                    </span>
                    <h2 class="text-lg font-bold text-[#122B1F]">Peserta & Status Keberangkatan</h2>
                    <p class="text-xs text-[#526057]">Kelola penyelesaian perjalanan seluruh jamaah pada paket ini.</p>
                </div>
                @if(isset($readyToDepartCount) && $readyToDepartCount > 0)
                <button type="button" @click="completeBulkModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 border border-emerald-600 transition-all shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Tandai Semua Peserta Selesai ({{ $readyToDepartCount }} Siap)</span>
                </button>
                @endif
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 mb-4">
                <div class="bg-[#F8FAF7] p-3.5 rounded-xl border border-[#E0E7DC]">
                    <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Total Booking</span>
                    <span class="text-xl font-bold text-[#12271E]">{{ $package->registrations->count() }}</span>
                    <span class="text-[10px] text-[#526057] block mt-0.5">{{ $package->registrations->sum(fn($r) => $r->members->count()) }} Jamaah</span>
                </div>
                <div class="bg-amber-50/70 p-3.5 rounded-xl border border-amber-200">
                    <span class="text-[10px] uppercase font-bold text-amber-800 block">Menunggu Dokumen (Tahap 7)</span>
                    <span class="text-xl font-bold text-amber-900">{{ $package->registrations->where('status', \App\Models\Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN)->count() }}</span>
                    <span class="text-[10px] text-amber-700 block mt-0.5">Booking</span>
                </div>
                <div class="bg-emerald-50/70 p-3.5 rounded-xl border border-emerald-200">
                    <span class="text-[10px] uppercase font-bold text-emerald-800 block">Siap Berangkat (Tahap 8)</span>
                    <span class="text-xl font-bold text-emerald-900">{{ $readyToDepartCount ?? 0 }}</span>
                    <span class="text-[10px] text-emerald-700 block mt-0.5">Dapat ditandai selesai</span>
                </div>
                <div class="bg-purple-50/70 p-3.5 rounded-xl border border-purple-200">
                    <span class="text-[10px] uppercase font-bold text-purple-800 block">Selesai Ibadah (Tahap 9)</span>
                    <span class="text-xl font-bold text-purple-900">{{ $completedCount ?? 0 }}</span>
                    <span class="text-[10px] text-purple-700 block mt-0.5">Tuntas</span>
                </div>
            </div>

            {{-- Ringkasan Peserta --}}
            @if($package->registrations->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#E0E7DC] text-[#4D5E54] bg-[#F8FAF7]">
                            <th class="py-2.5 px-3 font-semibold">No. Registrasi</th>
                            <th class="py-2.5 px-3 font-semibold">Pemesan</th>
                            <th class="py-2.5 px-3 font-semibold">Jumlah Jamaah</th>
                            <th class="py-2.5 px-3 font-semibold">Status Saat Ini</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFF3EB]">
                        @foreach($package->registrations->take(8) as $reg)
                        <tr class="hover:bg-[#F8FAF7]/60 transition-colors">
                            <td class="py-2.5 px-3 font-mono font-medium text-[#12271E]">{{ $reg->registration_number }}</td>
                            <td class="py-2.5 px-3 text-[#12271E] font-medium">{{ $reg->user->name ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-[#526057]">{{ $reg->members->count() }} Orang</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $reg->status === 'selesai' ? 'bg-purple-100 text-purple-800 border border-purple-200' : ($reg->status === 'berangkat' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $reg->status_label }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <a href="{{ route('admin.registrations.show', $reg) }}" class="text-[#1B3B2B] hover:underline font-semibold text-xs">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Variants Section -->
        <div style="animation: fadeSlideUp 0.4s ease both; animation-delay: 0.2s;">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-[#122B1F]">Sub-Paket / Varian</h2>
                    <p class="text-[#526057] text-sm mt-1">Kelola pilihan varian beserta harga kamar, kuota, maskapai, dan hotel.</p>
                </div>
                <a href="{{ route('admin.packages.variants.create', $package) }}" class="inline-flex items-center justify-center bg-[#1B3B2B] text-white rounded-xl px-4 py-2 text-xs font-semibold hover:bg-[#12271E] transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Sub-Paket
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($package->variants ?? [] as $variant)
                <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs overflow-hidden flex flex-col hover:border-[#1B3B2B] transition-colors group">
                    @if($variant->main_photo)
                    <div class="aspect-[16/10] w-full overflow-hidden bg-[#F8FAF7] border-b border-[#E0E7DC] flex items-center justify-center p-1">
                        <img src="{{ Storage::url($variant->main_photo) }}" alt="{{ $variant->name }}" class="w-full h-full object-contain">
                    </div>
                    @endif

                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-bold text-[#12271E]">{{ $variant->name }}</h3>
                            @if($variant->status === 'nonaktif')
                            <span class="px-2 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-lg text-[10px] font-semibold">Nonaktif</span>
                            @elseif($variant->is_sold_out)
                            <span class="px-2 py-1 bg-red-50 text-red-700 border border-red-200 rounded-lg text-[10px] font-semibold">Sold Out</span>
                            @elseif($variant->remaining_quota <= 5)
                            <span class="px-2 py-1 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg text-[10px] font-semibold">Hampir Penuh</span>
                            @else
                            <span class="px-2 py-1 bg-[#EFF3EB] text-[#1B3B2B] border border-[#E0E7DC] rounded-lg text-[10px] font-semibold">Aktif</span>
                            @endif
                        </div>

                        <!-- Harga Mulai Dari -->
                        <div class="mb-4">
                            <p class="text-[10px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-0.5">Mulai dari</p>
                            <p class="text-xl font-bold text-[#1B3B2B]">
                                {{ $variant->lowest_price_formatted }}
                            </p>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-[#526057] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="text-[#122B1F] font-medium">{{ $variant->remaining_quota }} Kursi</span>
                                <span class="text-[#526057] ml-1">tersedia (dari {{ $variant->quota }})</span>
                            </div>
                            
                            @if($variant->airlines_display)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-[#526057] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-[#526057] truncate text-xs">{{ $variant->airlines_display }}</span>
                            </div>
                            @endif

                            @if($variant->hotelMakkah)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-[#526057] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-[#526057] truncate text-xs">Makkah: {{ $variant->hotelMakkah->name }}</span>
                            </div>
                            @endif

                            @if($variant->hotelMadinah)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-[#526057] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-[#526057] truncate text-xs">Madinah: {{ $variant->hotelMadinah->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-4 bg-[#F8FAF7] border-t border-[#E0E7DC] flex gap-2">
                        <a href="{{ route('admin.packages.variants.edit', [$package, $variant]) }}" class="flex-1 text-center bg-white border border-[#E0E7DC] text-[#122B1F] rounded-xl px-3 py-2 text-xs font-semibold hover:bg-[#E0E7DC] transition-colors">
                            Kelola Sub-Paket
                        </a>
                        <button type="button" @click="openDeleteModal('{{ $variant->id }}', '{{ addslashes($variant->name) }}')" class="px-3 py-2 bg-white border border-red-200 text-red-600 rounded-xl text-xs font-semibold hover:bg-red-50 transition-colors cursor-pointer" title="Hapus Sub-Paket">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-dashed border-[#E0E7DC] p-8 text-center">
                    <div class="w-16 h-16 bg-[#F8FAF7] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#526057]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#122B1F] mb-1">Belum ada sub-paket</h3>
                    <p class="text-[#526057] text-sm mb-6 max-w-md mx-auto">Tambahkan sub-paket untuk melengkapi paket ini dengan detail harga, kuota, dan fasilitas kamar.</p>
                    <a href="{{ route('admin.packages.variants.create', $package) }}" class="inline-flex items-center justify-center bg-[#1B3B2B] text-white rounded-xl px-5 py-2.5 text-sm font-semibold hover:bg-[#12271E] transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Sub-Paket Pertama
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Delete Variant Modal (Fixed Viewport Overlay) -->
        <template x-teleport="body">
            <div x-show="deleteModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" 
                 style="display: none;"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-variant-title"
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
                                <h3 class="text-lg font-bold text-[#122B1F]" id="modal-variant-title">Hapus Sub-Paket</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus sub-paket <span x-text="'\"' + variantNameToDelete + '\"'" class="font-bold text-[#122B1F]"></span>? Data sub-paket, harga kamar, dan foto terkait akan terhapus.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form :action="'{{ url('admin/packages/' . $package->id . '/variants') }}/' + variantToDelete" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-xs cursor-pointer">
                                Ya, Hapus Sub-Paket
                            </button>
                        </form>
                        <button type="button" @click="closeDeleteModal()" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Delete Parent Package Modal (Fixed Viewport Overlay) -->
        <template x-teleport="body">
            <div x-show="deletePackageModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" 
                 style="display: none;"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-package-title"
                 @keydown.escape.window="closeDeletePackageModal()">

                {{-- Backdrop Overlay --}}
                <div x-show="deletePackageModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#122B1F]/60 backdrop-blur-xs transition-opacity" 
                     @click="closeDeletePackageModal()"></div>

                {{-- Modal Card --}}
                <div x-show="deletePackageModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative w-full sm:max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-[#E0E7DC] z-10 my-auto"
                     @click.outside="closeDeletePackageModal()">
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-red-100 text-red-600 sm:mx-0">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]" id="modal-package-title">Hapus Paket Induk</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus paket <span class="font-bold text-[#122B1F]">"{{ $package->name }}"</span>?
                                    </p>
                                    <p class="text-xs text-red-600 mt-2 bg-red-50 p-2.5 rounded-xl border border-red-100">
                                        Perhatian: Seluruh sub-paket (VIP/Bisnis/Ekonomi), harga, fasilitas, dan foto terkait akan ikut terhapus secara permanen.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-xs cursor-pointer">
                                Ya, Hapus Paket Ini
                            </button>
                        </form>
                        <button type="button" @click="closeDeletePackageModal()" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Konfirmasi Tandai Semua Selesai --}}
        <template x-teleport="body">
            <div x-show="completeBulkModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto"
                 role="dialog" 
                 aria-modal="true">
                
                {{-- Backdrop --}}
                <div x-show="completeBulkModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#122B1F]/60 backdrop-blur-xs transition-opacity" 
                     @click="completeBulkModalOpen = false"></div>

                {{-- Modal Card --}}
                <div x-show="completeBulkModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative w-full sm:max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-[#E0E7DC] z-10 my-auto"
                     @click.outside="completeBulkModalOpen = false">
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-700 sm:mx-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]">Tandai Semua Jamaah Selesai Ibadah</h3>
                                <div class="mt-2 space-y-2">
                                    <p class="text-sm text-[#526057]">
                                        Tindakan ini akan mengubah status seluruh booking yang saat ini berstatus <span class="font-bold text-emerald-800">"Siap Berangkat"</span> ({{ $readyToDepartCount ?? 0 }} booking) pada keberangkatan <span class="font-bold text-[#122B1F]">"{{ $package->name }}"</span> menjadi <span class="font-bold text-purple-700">"Selesai"</span> (Tahap 9).
                                    </p>
                                    <p class="text-xs text-[#526057] bg-emerald-50/70 p-3 rounded-xl border border-emerald-200">
                                        Setelah ditandai selesai, status jamaah akan diarsipkan sebagai alumni tuntas dan tidak lagi dihitung sebagai jamaah aktif. Riwayat akan dicatat di audit log.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form action="{{ route('admin.departures.complete', $package) }}" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-emerald-700 text-xs font-bold text-white hover:bg-emerald-800 focus:outline-none transition-colors shadow-xs cursor-pointer">
                                Ya, Tandai Selesai
                            </button>
                        </form>
                        <button type="button" @click="completeBulkModalOpen = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-layouts.admin>
