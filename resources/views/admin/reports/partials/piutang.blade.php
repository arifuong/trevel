{{-- 4 Summary Cards di Atas Tabel --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 mb-5" style="animation: fadeSlideUp 0.3s ease both">
    <div class="bg-white rounded-2xl border border-[#CCD8C7] p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-[#1B3B2B] uppercase tracking-wider block font-bold">Total Nilai Paket</span>
            <span class="text-xl sm:text-2xl font-extrabold text-[#12271E] mt-0.5 block leading-tight">
                Rp {{ number_format($summary['totalContract'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Keberangkatan periode ini</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-emerald-200 p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-emerald-800 uppercase tracking-wider block font-bold">Sudah Diterima</span>
            <span class="text-xl sm:text-2xl font-extrabold text-emerald-950 mt-0.5 block leading-tight">
                Rp {{ number_format($summary['totalReceived'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Dana masuk tervalidasi</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-amber-300 p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-amber-900 uppercase tracking-wider block font-bold">Sisa Piutang Berjalan</span>
            <span class="text-xl sm:text-2xl font-extrabold text-amber-950 mt-0.5 block leading-tight">
                Rp {{ number_format($summary['totalOutstanding'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-amber-800/80 block mt-0.5">Wajib dilunasi</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-[#526057] uppercase tracking-wider block font-bold">Booking Belum Lunas</span>
            <span class="text-2xl font-extrabold text-[#12271E] mt-0.5 block leading-tight">{{ $summary['unpaidCount'] }}</span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Memiliki sisa tagihan</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
        </div>
    </div>
</div>

{{-- Filter Tambahan & Tabel --}}
<div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-2xs overflow-hidden">
    {{-- Search Bar (Single Inline with Debounce, Tanpa Tombol Filter) --}}
    <div class="p-3.5 sm:p-4 border-b border-[#E0E7DC] bg-[#F8FAF7]">
        <div class="relative w-full">
            <input type="text" 
                   x-model="search"
                   @input.debounce.350ms="applyFilters()"
                   placeholder="Cari nama jamaah, nomor pendaftaran, atau paket umrah..."
                   class="w-full pl-9 pr-8 py-2 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/50 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium">
            <svg class="w-4 h-4 text-[#526057] absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <button type="button" 
                    x-show="search.length > 0" 
                    @click="search = ''; applyFilters()" 
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-[#EFF3EB] text-[#12271E] uppercase text-[10px] sm:text-[11px] font-bold tracking-wider border-b border-[#E0E7DC]">
                <tr>
                    <th class="py-3 px-4 sm:px-6">No. Pendaftaran</th>
                    <th class="py-3 px-4">Nama Jamaah</th>
                    <th class="py-3 px-4">Paket & Tgl Berangkat</th>
                    <th class="py-3 px-4 text-center">Jatuh Tempo</th>
                    <th class="py-3 px-4 text-right">Total Tagihan</th>
                    <th class="py-3 px-4 text-right">Sudah Bayar</th>
                    <th class="py-3 px-4 text-right">Sisa Piutang</th>
                    <th class="py-3 px-4 sm:px-6 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E0E7DC]">
                @forelse($registrations as $reg)
                    @php
                        $inv = $reg->invoice;
                        $rem = $inv ? (float) $inv->remaining_balance : 0;
                        $isLunas = $rem <= 0;
                    @endphp
                    <tr class="hover:bg-[#F8FAF7] transition-colors">
                        <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                            <span class="font-mono font-bold text-xs bg-[#EFF3EB] text-[#1B3B2B] px-2 py-0.5 rounded-md">
                                {{ $reg->registration_number }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-[#12271E]">{{ $reg->user->name ?? '-' }}</div>
                            <div class="text-[11px] text-[#526057]">{{ $reg->user->phone ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-[#12271E]">{{ $reg->package->name ?? '-' }}</div>
                            <div class="text-[11px] text-emerald-800 font-bold mt-0.5">
                                ✈ {{ $reg->package?->departure_date ? $reg->package->departure_date->translatedFormat('d M Y') : 'TBA' }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="text-xs {{ $inv?->due_date && $inv->due_date->isPast() && !$isLunas ? 'text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-md' : 'text-[#526057]' }}">
                                {{ $inv?->due_date ? $inv->due_date->translatedFormat('d M Y') : '-' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-[#12271E]">
                            Rp {{ number_format($inv->total_price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap text-emerald-700 font-semibold">
                            Rp {{ number_format($inv->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-extrabold {{ $isLunas ? 'text-emerald-700' : 'text-amber-800' }}">
                            Rp {{ number_format($rem, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                            @if($isLunas)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Lunas ✓
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                    Belum Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-[#526057]">
                            <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-semibold text-sm text-[#12271E]">Tidak Ada Tagihan & Piutang</p>
                            <p class="text-xs mt-1">Tidak ada jadwal keberangkatan atau booking pada periode {{ $periodData['label'] }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($registrations->hasPages())
        <div class="p-3.5 border-t border-[#E0E7DC] bg-[#EFF3EB]/30">
            {{ $registrations->links() }}
        </div>
    @endif
</div>