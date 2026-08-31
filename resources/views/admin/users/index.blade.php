<x-layouts.admin :title="'Manajemen Jamaah — PT. Zein Internasional'">

    <div x-data="{ deleteModalOpen: false, selectedUser: { id: null, name: '', phone: '', url: '' } }" class="space-y-6 sm:space-y-8">
        
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                    Manajemen Jamaah
                </h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-1">
                    Kelola dan lihat seluruh jamaah yang telah terdaftar dalam sistem.
                </p>
            </div>
        </div>

        {{-- Bar Pencarian & Filter Server-Side --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-5 sm:p-6 shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                    
                    {{-- Search Box --}}
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#526057]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama jamaah, NIK, No. KK, No. Paspor, WhatsApp..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-xs sm:text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all">
                    </div>

                    {{-- Tombol Cari & Reset --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-98 transition-all shadow-xs cursor-pointer">
                            <span>Cari</span>
                        </button>
                        @if(request()->hasAny(['search', 'status', 'reg_status', 'doc_status']))
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold text-[#526057] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7]">
                                <span>Reset Filter</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Dropdown Filter --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-[#E0E7DC]">
                    
                    {{-- Filter Status Jamaah / Pendaftaran --}}
                    <div>
                        <label for="status" class="block text-[10px] font-bold uppercase tracking-wider text-[#526057] mb-1">
                            Status Jamaah
                        </label>
                        @php
                            $currentStatus = request('status', request('reg_status', ''));
                        @endphp
                        <select id="status" 
                                name="status" 
                                onchange="this.form.submit()" 
                                class="w-full px-3 py-2 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-xs text-[#12271E] font-medium focus:bg-white focus:outline-none">
                            <option value="">Semua Status Jamaah</option>
                            <option value="menunggu" {{ $currentStatus === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi (Dokumen / DP)</option>
                            <option value="terverifikasi" {{ $currentStatus === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi (Jamaah Resmi / Aktif)</option>
                            <option value="pembayaran" {{ $currentStatus === 'pembayaran' ? 'selected' : '' }}>Proses Pembayaran (DP / Cicilan)</option>
                            <option value="dibatalkan" {{ $currentStatus === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="belum_daftar" {{ $currentStatus === 'belum_daftar' ? 'selected' : '' }}>Belum Memilih Paket</option>
                        </select>
                    </div>

                    {{-- Filter Status Dokumen --}}
                    <div>
                        <label for="doc_status" class="block text-[10px] font-bold uppercase tracking-wider text-[#526057] mb-1">
                            Status Dokumen
                        </label>
                        <select id="doc_status" 
                                name="doc_status" 
                                onchange="this.form.submit()" 
                                class="w-full px-3 py-2 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-xs text-[#12271E] font-medium focus:bg-white focus:outline-none">
                            <option value="">Semua Status Dokumen</option>
                            <option value="disetujui" {{ request('doc_status') === 'disetujui' ? 'selected' : '' }}>Disetujui / Lengkap</option>
                            <option value="menunggu_verifikasi" {{ request('doc_status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="ditolak" {{ request('doc_status') === 'ditolak' ? 'selected' : '' }}>Perlu Perbaikan / Ditolak</option>
                        </select>
                    </div>

                </div>

            </form>
        </div>

        {{-- Tabel Data Seluruh Jamaah (Desktop & Tablet) --}}
        <div class="hidden md:block bg-white rounded-3xl border border-[#E0E7DC] overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-[#526057]">
                    <thead class="bg-[#EFF3EB]/60 border-b border-[#E0E7DC] text-[10px] uppercase font-bold tracking-wider text-[#12271E]">
                        <tr>
                            <th scope="col" class="py-4 px-4 w-12 text-center">No</th>
                            <th scope="col" class="py-4 px-4">Nama Jamaah</th>
                            <th scope="col" class="py-4 px-4">NIK & No. KK</th>
                            <th scope="col" class="py-4 px-4">Paket</th>
                            <th scope="col" class="py-4 px-4">Status</th>
                            <th scope="col" class="py-4 px-4">Pembayaran</th>
                            <th scope="col" class="py-4 px-4">Dokumen</th>
                            <th scope="col" class="py-4 px-4">Tanggal Daftar</th>
                            <th scope="col" class="py-4 px-4 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        @forelse($users as $index => $userItem)
                            @php
                                $latestReg = $userItem->registrations->first();
                                $primaryMember = $latestReg ? ($latestReg->members->where('relationship', 'diri_sendiri')->first() ?? $latestReg->members->first()) : null;
                                $isCancelled = $latestReg && ($latestReg->status === 'dibatalkan' || $latestReg->cancellation_status === 'approved');
                            @endphp
                            <tr class="hover:bg-[#F8FAF7] transition-colors">
                                
                                {{-- Nomor Urut Pagination --}}
                                <td class="py-4 px-4 text-center font-medium text-[#526057]/70">
                                    {{ $users->firstItem() + $index }}
                                </td>

                                {{-- Nama Jamaah & Kontak --}}
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden shadow-xs ring-2 ring-[#EFF3EB]">
                                            @if($userItem->avatar_url)
                                                <img src="{{ $userItem->avatar_url }}" alt="{{ $userItem->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ $userItem->initials }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-[#12271E] block text-sm leading-snug">
                                                {{ $userItem->name }}
                                            </span>
                                            <span class="text-[11px] text-[#1B3B2B] font-medium block mt-0.5">
                                                {{ $userItem->phone_formatted }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- NIK & Nomor KK --}}
                                <td class="py-4 px-4">
                                    <div class="space-y-0.5 text-[11px]">
                                        <div class="flex items-center gap-1 font-mono text-[#12271E]">
                                            <span class="text-[#526057] font-sans text-[10px] font-bold uppercase">NIK:</span>
                                            <span>{{ $primaryMember?->nik ?: '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 font-mono text-[#526057]">
                                            <span class="text-[#526057] font-sans text-[10px] font-bold uppercase">KK:</span>
                                            <span>{{ $primaryMember?->no_kk ?: '-' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Paket --}}
                                <td class="py-4 px-4">
                                    @if($latestReg && $latestReg->package)
                                        <div>
                                            <span class="font-bold text-[#12271E] block truncate max-w-[180px]">
                                                {{ $latestReg->package->name }}
                                            </span>
                                            <span class="text-[10px] text-[#526057] block mt-0.5">
                                                {{ $latestReg->members->count() }} Jamaah
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-[#526057]/60 italic">Belum Memilih Paket</span>
                                    @endif
                                </td>

                                {{-- Status Jamaah --}}
                                <td class="py-4 px-4">
                                    @if($isCancelled)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-800 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                            <span>Dibatalkan</span>
                                        </span>
                                    @elseif($latestReg)
                                        @if(in_array($latestReg->status, ['jamaah', 'lunas', 'berangkat']))
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                <span>Terverifikasi</span>
                                            </span>
                                        @elseif(in_array($latestReg->status, ['menunggu_verifikasi_dokumen', 'menunggu_pembayaran_dp', 'menunggu_verifikasi_pembayaran_dp']))
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                <span>Menunggu</span>
                                            </span>
                                        @elseif($latestReg->status === 'cicilan_pelunasan')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                <span>Pembayaran</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-800 border border-zinc-200">
                                                {{ $latestReg->status_label }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center text-[10px] font-medium px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-600">
                                            Belum Daftar
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Pembayaran --}}
                                <td class="py-4 px-4">
                                    @if($isCancelled)
                                        <span class="text-[10px] font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-full border border-red-200">
                                            Dibatalkan
                                        </span>
                                    @elseif($latestReg && $latestReg->invoice)
                                        @if($latestReg->invoice->remaining_balance <= 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                <span>Lunas</span>
                                            </span>
                                        @elseif($latestReg->invoice->total_paid > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                                <span>DP / Cicilan</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                                <span>Belum Bayar</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[11px] text-[#526057]/60">-</span>
                                    @endif
                                </td>

                                {{-- Status Dokumen --}}
                                <td class="py-4 px-4">
                                    @if($primaryMember && $primaryMember->document_status)
                                        <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full {{ $primaryMember->document_status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($primaryMember->document_status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $primaryMember->document_status_label ?? $primaryMember->document_status }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-[#526057]/60 italic">Belum Upload</span>
                                    @endif
                                </td>

                                {{-- Tanggal Terdaftar --}}
                                <td class="py-4 px-4 text-xs text-[#526057]">
                                    {{ $latestReg ? $latestReg->created_at->translatedFormat('d M Y') : $userItem->created_at->translatedFormat('d M Y') }}
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="py-4 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a href="{{ route('admin.users.show', $userItem->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7] shadow-2xs">
                                            <span>Lihat Detail</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </a>
                                        <button type="button" 
                                                @click="selectedUser = { id: {{ $userItem->id }}, name: '{{ addslashes($userItem->name) }}', phone: '{{ $userItem->phone_formatted }}', url: '{{ route('admin.users.destroy', $userItem->id) }}' }; deleteModalOpen = true;"
                                                class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg border border-transparent hover:border-red-200 transition-colors shadow-2xs cursor-pointer"
                                                title="Hapus Jamaah">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 px-4 text-center">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        </div>
                                        <h3 class="text-base font-bold text-[#12271E]">Tidak ada jamaah yang ditemukan</h3>
                                        <p class="text-xs text-[#526057] leading-relaxed">
                                            Coba gunakan kata kunci nama jamaah, NIK, No. KK, No. Paspor, atau reset filter.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Desktop --}}
            @if($users->hasPages())
                <div class="p-4 sm:p-5 border-t border-[#E0E7DC] bg-[#EFF3EB]/20">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Tampilan Kartu Khusus Mobile --}}
        <div class="md:hidden space-y-3.5">
            @forelse($users as $userItem)
                @php
                    $latestReg = $userItem->registrations->first();
                    $primaryMember = $latestReg ? ($latestReg->members->where('relationship', 'diri_sendiri')->first() ?? $latestReg->members->first()) : null;
                    $isCancelled = $latestReg && ($latestReg->status === 'dibatalkan' || $latestReg->cancellation_status === 'approved');
                @endphp
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 shadow-xs space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden shadow-xs ring-2 ring-[#EFF3EB]">
                                @if($userItem->avatar_url)
                                    <img src="{{ $userItem->avatar_url }}" alt="{{ $userItem->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ $userItem->initials }}
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-[#12271E] leading-snug">{{ $userItem->name }}</h3>
                                <p class="text-[11px] text-[#526057]">{{ $userItem->phone_formatted }}</p>
                            </div>
                        </div>

                        {{-- Badge Status Mobile --}}
                        @if($isCancelled)
                            <span class="text-[9px] font-bold uppercase text-red-800 bg-red-100 px-2.5 py-0.5 rounded-full border border-red-200 shrink-0">
                                Dibatalkan
                            </span>
                        @elseif($latestReg && in_array($latestReg->status, ['jamaah', 'lunas', 'berangkat']))
                            <span class="text-[9px] font-bold uppercase text-emerald-800 bg-emerald-100/80 px-2.5 py-0.5 rounded-full border border-emerald-200 shrink-0">
                                Terverifikasi
                            </span>
                        @elseif($latestReg)
                            <span class="text-[9px] font-bold uppercase text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full border border-amber-200 shrink-0">
                                Menunggu
                            </span>
                        @else
                            <span class="text-[9px] font-medium text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded-full shrink-0">
                                Belum Daftar
                            </span>
                        @endif
                    </div>

                    {{-- Data Detail Ringkas Mobile --}}
                    <div class="text-xs border-t border-[#E0E7DC] pt-2.5 space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[#526057]">NIK:</span>
                            <span class="font-mono font-semibold text-[#12271E]">{{ $primaryMember?->nik ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#526057]">No. KK:</span>
                            <span class="font-mono font-semibold text-[#12271E]">{{ $primaryMember?->no_kk ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#526057]">Paket:</span>
                            <span class="font-semibold text-[#1B3B2B] truncate max-w-[200px]">{{ $latestReg?->package?->name ?? 'Belum Memilih Paket' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#526057]">Pembayaran:</span>
                            <span class="font-semibold text-[#12271E]">
                                @if($isCancelled)
                                    Dibatalkan
                                @elseif($latestReg && $latestReg->invoice)
                                    {{ $latestReg->invoice->remaining_balance <= 0 ? 'Lunas' : ($latestReg->invoice->total_paid > 0 ? 'DP / Sebagian' : 'Belum Bayar') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#526057]">Dokumen:</span>
                            <span class="font-semibold text-[#12271E]">{{ $primaryMember?->document_status_label ?? 'Belum Upload' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#526057]">Tanggal Daftar:</span>
                            <span class="text-[#526057]">{{ $latestReg ? $latestReg->created_at->translatedFormat('d M Y') : $userItem->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#E0E7DC] flex items-center gap-2 justify-between">
                        <a href="{{ route('admin.users.show', $userItem->id) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7]">
                            <span>Lihat Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                        <button type="button" 
                                @click="selectedUser = { id: {{ $userItem->id }}, name: '{{ addslashes($userItem->name) }}', phone: '{{ $userItem->phone_formatted }}', url: '{{ route('admin.users.destroy', $userItem->id) }}' }; deleteModalOpen = true;"
                                class="px-3 py-2 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition-colors cursor-pointer text-xs font-bold flex items-center gap-1"
                                title="Hapus Jamaah">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-8 text-center">
                    <p class="text-xs text-[#526057]">Tidak ada data jamaah yang sesuai kriteria pencarian.</p>
                </div>
            @endforelse

            @if($users->hasPages())
                <div class="pt-2">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Konfirmasi Hapus Jamaah --}}
        <div x-show="deleteModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             @keydown.escape.window="deleteModalOpen = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-[#122B1F]/60 backdrop-blur-xs" @click="deleteModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E0E7DC] relative z-10"
                     @click.outside="deleteModalOpen = false">
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]">Hapus Akun Jamaah?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus akun jamaah <span class="font-bold text-[#122B1F]" x-text="selectedUser.name"></span> (<span x-text="selectedUser.phone"></span>)?
                                    </p>
                                    <p class="text-xs text-red-600 mt-2 bg-red-50 p-2.5 rounded-lg border border-red-100">
                                        Perhatian: Seluruh data pendaftaran, berkas dokumen (KTP, KK, Paspor), dan riwayat setoran pembayaran milik akun ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form :action="selectedUser.url" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-sm cursor-pointer">
                                Ya, Hapus Akun Jamaah
                            </button>
                        </form>
                        <button type="button" @click="deleteModalOpen = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layouts.admin>
