{{-- 4 Summary Cards di Atas Tabel --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 mb-5" style="animation: fadeSlideUp 0.3s ease both">
    <div class="bg-white rounded-2xl border border-emerald-200 p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-emerald-800 uppercase tracking-wider block font-bold">Total Dana Masuk</span>
            <span class="text-xl sm:text-2xl font-extrabold text-emerald-950 mt-0.5 block leading-tight">
                Rp {{ number_format($summary['totalVerifiedAmount'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Diverifikasi sah</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m-19.5 0a.75.75 0 00.75.75h.75m0 0v.75m0-1.5h16.5m0 0v.75m-16.5-.75V6m16.5 0v10.5m0 0a.75.75 0 01-.75.75h-.75" /></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-[#526057] uppercase tracking-wider block font-bold">Jumlah Transaksi</span>
            <span class="text-2xl font-extrabold text-[#12271E] mt-0.5 block leading-tight">{{ $summary['totalTransactions'] }}</span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Setoran disetujui</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#CCD8C7] p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-[#1B3B2B] uppercase tracking-wider block font-bold">Total Uang Muka (DP)</span>
            <span class="text-lg sm:text-xl font-extrabold text-[#1B3B2B] mt-0.5 block leading-tight">
                Rp {{ number_format($summary['totalDpAmount'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Booking awal</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-purple-200 p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-purple-800 uppercase tracking-wider block font-bold">Total Pelunasan</span>
            <span class="text-lg sm:text-xl font-extrabold text-purple-950 mt-0.5 block leading-tight">
                Rp {{ number_format($summary['totalPelunasanAmount'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Cicilan & pelunasan akhir</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>
</div>

{{-- Filter Tambahan & Tabel --}}
<div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-2xs overflow-hidden">
    {{-- Search & Status Filter (Satu Baris Sederhana, Auto-Apply Tanpa Tombol Filter) --}}
    <div class="p-3.5 sm:p-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            {{-- Search Bar dengan Debounce --}}
            <div class="relative flex-1">
                <input type="text" 
                       x-model="search"
                       @input.debounce.350ms="applyFilters()"
                       placeholder="Cari nomor kwitansi, nama jamaah, atau nomor pendaftaran..."
                       class="w-full pl-9 pr-8 py-2 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/50 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium">
                <svg class="w-4 h-4 text-[#526057] absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <button type="button" 
                        x-show="search.length > 0" 
                        @click="search = ''; applyFilters()" 
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Filter Status Verifikasi Pembayaran (Auto-Apply on Change) --}}
            <div class="flex items-center gap-1.5 sm:w-64">
                <span class="text-[11px] font-bold text-[#526057] shrink-0 hidden md:inline">Status:</span>
                <select x-model="status" 
                        @change="applyFilters()"
                        class="w-full px-3 py-2 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium cursor-pointer">
                    <option value="disetujui">Disetujui (Kas Masuk Sah)</option>
                    <option value="all">Semua Status (Histori)</option>
                    <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-[#EFF3EB] text-[#12271E] uppercase text-[10px] sm:text-[11px] font-bold tracking-wider border-b border-[#E0E7DC]">
                <tr>
                    <th class="py-3 px-4 sm:px-6">No. Kwitansi</th>
                    <th class="py-3 px-4">Tanggal Verifikasi</th>
                    <th class="py-3 px-4">Nama Jamaah & Booking</th>
                    <th class="py-3 px-4">Paket Umrah</th>
                    <th class="py-3 px-4 text-center">Jenis Setoran</th>
                    <th class="py-3 px-4 text-right">Nominal</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 sm:px-6 text-right">Kwitansi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E0E7DC]">
                @forelse($payments as $p)
                    <tr class="hover:bg-[#F8FAF7] transition-colors">
                        <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                            <span class="font-mono font-bold text-xs bg-[#EFF3EB] text-[#1B3B2B] px-2 py-0.5 rounded-md">
                                {{ $p->receipt_number ?? '-' }}
                            </span>
                            <span class="text-[10px] text-[#526057] block mt-0.5">
                                Upload: {{ $p->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-semibold text-[#12271E] block">
                                {{ $p->verified_at ? $p->verified_at->translatedFormat('d M Y') : '-' }}
                            </span>
                            <span class="text-[10px] text-[#526057] block mt-0.5">
                                {{ $p->verified_at ? $p->verified_at->format('H:i') . ' WIB' : 'Belum verif' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-[#12271E]">{{ $p->registration->user->name ?? '-' }}</div>
                            <div class="text-[11px] font-mono text-[#526057]">{{ $p->registration->registration_number ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-[#12271E]">{{ $p->registration->package->name ?? '-' }}</div>
                            <div class="text-[11px] text-[#526057]">
                                Berangkat: {{ $p->registration->package?->departure_date ? $p->registration->package->departure_date->translatedFormat('d M Y') : '-' }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->type === 'dp' ? 'bg-[#C2A264]/20 text-[#1B3B2B] border border-[#C2A264]/40' : 'bg-purple-100 text-purple-900 border border-purple-200' }}">
                                {{ $p->type_label }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-extrabold text-emerald-800 text-sm">
                            Rp {{ number_format($p->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <x-status-badge :status="$p->status" size="xs" />
                        </td>
                        <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                            @if($p->status === 'disetujui')
                                <a href="{{ route('documents.receipt.pdf', $p) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#CCD8C7] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    <span>PDF</span>
                                </a>
                            @else
                                <span class="text-[11px] text-[#526057]/60">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-[#526057]">
                            <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m-19.5 0a.75.75 0 00.75.75h.75m0 0v.75m0-1.5h16.5m0 0v.75m-16.5-.75V6m16.5 0v10.5m0 0a.75.75 0 01-.75.75h-.75" /></svg>
                            </div>
                            <p class="font-semibold text-sm text-[#12271E]">Tidak Ada Transaksi Pembayaran</p>
                            <p class="text-xs mt-1">Tidak ditemukan transaksi pada periode {{ $periodData['label'] }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
        <div class="p-3.5 border-t border-[#E0E7DC] bg-[#EFF3EB]/30">
            {{ $payments->links() }}
        </div>
    @endif
</div>