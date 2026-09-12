<x-layouts.admin :title="'Verifikasi Dokumen — Admin PT. Zein Internasional'">

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="animation: fadeSlideUp 0.4s ease both">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">Verifikasi Dokumen & Pendaftaran</h1>
            <p class="text-xs sm:text-sm text-[#526057] mt-1">Periksa kelengkapan dan keabsahan dokumen persyaratan calon jamaah.</p>
        </div>
    </div>

    {{-- Kartu Ringkasan Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6" style="animation: fadeSlideUp 0.4s ease 0.05s both">
        <div class="bg-white rounded-xl border border-[#E0E7DC] p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[11px] text-[#526057] block font-medium">Total Pendaftaran</span>
                <span class="text-xl font-bold text-[#12271E] mt-0.5 block">{{ $totalRegistrations }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-amber-200 p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[11px] text-amber-800 block font-semibold">Verifikasi Tahap Awal</span>
                <span class="text-xl font-bold text-amber-900 mt-0.5 block">{{ $pendingDocsCount }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-purple-200 p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[11px] text-purple-800 block font-semibold">Dokumen Keberangkatan</span>
                <span class="text-xl font-bold text-purple-900 mt-0.5 block">{{ $pendingDepartureDocsCount ?? 0 }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-blue-200 p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[11px] text-blue-800 block font-semibold">Menunggu Bayar DP</span>
                <span class="text-xl font-bold text-blue-900 mt-0.5 block">{{ $pendingPaymentCount }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-emerald-200 p-4 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[11px] text-emerald-800 block font-semibold">Jamaah Lunas / Berangkat</span>
                <span class="text-xl font-bold text-emerald-900 mt-0.5 block">{{ $verifiedJamaahCount }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs mb-6 overflow-hidden" style="animation: fadeSlideUp 0.4s ease 0.1s both">
        {{-- Phase Quick Filter Tabs --}}
        <div class="flex items-center gap-1 p-2 bg-[#F8FAF7] border-b border-[#E0E7DC] overflow-x-auto text-xs font-semibold">
            <a href="{{ route('admin.registrations.index', array_merge(request()->except('phase', 'page'), [])) }}"
               class="px-3.5 py-1.5 rounded-xl transition-all {{ !request('phase') ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:bg-[#EFF3EB]' }}">
                Semua Tahap
            </a>
            <a href="{{ route('admin.registrations.index', array_merge(request()->except('phase', 'page'), ['phase' => 'awal'])) }}"
               class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 {{ request('phase') === 'awal' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:bg-[#EFF3EB]' }}">
                <span>Tahap 1: Verifikasi Awal</span>
                @if($pendingDocsCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('phase') === 'awal' ? 'bg-amber-400 text-amber-950 font-bold' : 'bg-amber-100 text-amber-800' }}">
                        {{ $pendingDocsCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.registrations.index', array_merge(request()->except('phase', 'page'), ['phase' => 'keberangkatan'])) }}"
               class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 {{ request('phase') === 'keberangkatan' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:bg-[#EFF3EB]' }}">
                <span>Tahap 7: Dokumen Keberangkatan (Visa & Vaksin)</span>
                @if(($pendingDepartureDocsCount ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('phase') === 'keberangkatan' ? 'bg-purple-400 text-purple-950 font-bold' : 'bg-purple-100 text-purple-800' }}">
                        {{ $pendingDepartureDocsCount }}
                    </span>
                @endif
            </a>
        </div>

        <div class="p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.registrations.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                @if(request('phase'))
                    <input type="hidden" name="phase" value="{{ request('phase') }}">
                @endif

                {{-- Search --}}
                <div class="sm:col-span-6 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama jamaah, email, nomor HP, atau nama paket..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/50 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all">
                    <svg class="w-4 h-4 text-[#526057] absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>

                {{-- Filter Status --}}
                <div class="sm:col-span-4">
                    <select name="status" 
                            class="w-full px-4 py-2.5 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white">
                        <option value="">Semua Status Pendaftaran</option>
                        <option value="menunggu_verifikasi_dokumen" {{ request('status') === 'menunggu_verifikasi_dokumen' ? 'selected' : '' }}>Menunggu Verifikasi Dokumen</option>
                        <option value="menunggu_pembayaran_dp" {{ request('status') === 'menunggu_pembayaran_dp' ? 'selected' : '' }}>Menunggu Pembayaran DP</option>
                        <option value="menunggu_verifikasi_pembayaran_dp" {{ request('status') === 'menunggu_verifikasi_pembayaran_dp' ? 'selected' : '' }}>Menunggu Verifikasi DP</option>
                        <option value="jamaah" {{ request('status') === 'jamaah' ? 'selected' : '' }}>Sudah Terdaftar Resmi</option>
                        <option value="cicilan_pelunasan" {{ request('status') === 'cicilan_pelunasan' ? 'selected' : '' }}>Proses Pelunasan</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="menunggu_kelengkapan_keberangkatan" {{ request('status') === 'menunggu_kelengkapan_keberangkatan' ? 'selected' : '' }}>Kelengkapan Dokumen Keberangkatan</option>
                        <option value="berangkat" {{ request('status') === 'berangkat' ? 'selected' : '' }}>Siap Berangkat</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                {{-- Submit & Reset Buttons --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors text-center cursor-pointer">
                        Cari
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.registrations.index') }}" 
                            class="p-2.5 rounded-xl border border-[#E0E7DC] text-[#526057] hover:text-[#12271E] hover:bg-[#EFF3EB] transition-colors"
                            title="Reset filter">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto border-t border-[#E0E7DC]">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-[#EFF3EB] text-[#12271E] uppercase text-[10px] sm:text-[11px] font-bold tracking-wider border-b border-[#E0E7DC]">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">No. Pendaftaran</th>
                        <th class="py-3.5 px-4">Nama Jamaah</th>
                        <th class="py-3.5 px-4">Paket Umrah</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Anggota</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Total Biaya</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E7DC]">
                    @forelse($registrations as $reg)
                        <tr class="hover:bg-[#F8FAF7] transition-colors">
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                <span class="font-mono font-bold text-xs bg-[#EFF3EB] text-[#1B3B2B] px-2.5 py-1 rounded-md">
                                    {{ $reg->registration_number }}
                                </span>
                                <span class="text-[10px] text-[#526057] block mt-1">
                                    {{ $reg->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-[#12271E]">{{ $reg->user->name }}</div>
                                <div class="text-[11px] text-[#526057] mt-0.5">{{ $reg->user->phone }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-[#12271E]">{{ $reg->package->name }}</div>
                                <div class="text-[11px] text-[#526057]">Berangkat: {{ $reg->package->departure_date ? $reg->package->departure_date->translatedFormat('d M Y') : '-' }}</div>
                            </td>
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#EFF3EB] text-[#12271E]">
                                    {{ $reg->members->count() }} Orang
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold 
                                    {{ $reg->status === 'selesai' ? 'bg-purple-100 text-purple-800 border border-purple-200' : (in_array($reg->status, ['jamaah', 'cicilan_pelunasan', 'lunas', 'berangkat']) ? 'bg-emerald-100 text-emerald-800' : ($reg->status === 'menunggu_kelengkapan_keberangkatan' ? 'bg-purple-100 text-purple-900 border border-purple-200' : ($reg->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'))) }}">
                                    {{ $reg->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right whitespace-nowrap font-bold text-[#12271E]">
                                Rp {{ number_format($reg->invoice->total_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                <a href="{{ route('admin.registrations.show', $reg) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-2xs">
                                    <span>Periksa Berkas</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#526057]">
                                <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                </div>
                                <p class="font-semibold text-sm text-[#12271E]">Belum Ada Pendaftaran</p>
                                <p class="text-xs mt-1">Tidak ditemukan pendaftaran jamaah yang sesuai filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($registrations->hasPages())
            <div class="p-4 border-t border-[#E0E7DC] bg-[#EFF3EB]/30">
                {{ $registrations->links() }}
            </div>
        @endif

    </div>

</x-layouts.admin>
