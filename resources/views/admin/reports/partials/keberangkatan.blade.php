{{-- 4 Summary Cards di Atas Tabel --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 mb-5" style="animation: fadeSlideUp 0.3s ease both">
    <div class="bg-white rounded-2xl border border-[#CCD8C7] p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-[#1B3B2B] uppercase tracking-wider block font-bold">Total Keberangkatan</span>
            <span class="text-2xl font-extrabold text-[#12271E] mt-0.5 block leading-tight">{{ $summary['totalDepartures'] }}</span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Kloter terjadwal</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-[#526057] uppercase tracking-wider block font-bold">Total Kuota Kursi</span>
            <span class="text-2xl font-extrabold text-[#12271E] mt-0.5 block leading-tight">{{ $summary['totalCapacity'] }}</span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Kapasitas seluruh paket</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-emerald-200 p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-emerald-800 uppercase tracking-wider block font-bold">Kursi Terisi (Pax)</span>
            <span class="text-2xl font-extrabold text-emerald-950 mt-0.5 block leading-tight">{{ $summary['totalRegisteredPax'] }}</span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Jamaah terdaftar aktif</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-blue-200 p-4 flex items-center justify-between shadow-2xs">
        <div>
            <span class="text-[10px] sm:text-[11px] text-blue-800 uppercase tracking-wider block font-bold">Sisa Kursi Tersedia</span>
            <span class="text-2xl font-extrabold text-blue-950 mt-0.5 block leading-tight">{{ $summary['totalRemainingSeats'] }}</span>
            <span class="text-[10px] text-[#526057]/70 block mt-0.5">Dapat dipesan jamaah</span>
        </div>
        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
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
                   placeholder="Cari nama paket umrah atau haji khusus..."
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
                    <th class="py-3 px-4 sm:px-6">Nama Paket</th>
                    <th class="py-3 px-4">Tgl Keberangkatan</th>
                    <th class="py-3 px-4">Maskapai & Durasi</th>
                    <th class="py-3 px-4">Hotel Makkah / Madinah</th>
                    <th class="py-3 px-4 text-center">Kuota Kursi</th>
                    <th class="py-3 px-4 text-center">Terisi</th>
                    <th class="py-3 px-4 text-center">Sisa</th>
                    <th class="py-3 px-4 sm:px-6 text-right">Keterisian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E0E7DC]">
                @forelse($packages as $pkg)
                    <tr class="hover:bg-[#F8FAF7] transition-colors">
                        <td class="py-3.5 px-4 sm:px-6">
                            <div class="font-bold text-[#12271E]">{{ $pkg->name }}</div>
                            <div class="text-[11px] text-[#526057] mt-0.5">
                                Mulai Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-bold text-emerald-800">
                                {{ $pkg->departure_date ? $pkg->departure_date->translatedFormat('d M Y') : 'TBA' }}
                            </div>
                            <span class="text-[10px] text-[#526057] block mt-0.5">
                                {{ $pkg->departure_date ? $pkg->departure_date->diffForHumans() : '-' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-semibold text-[#12271E]">{{ $pkg->variants->first()?->airlines->first()?->name ?? 'Maskapai Reguler' }}</div>
                            <div class="text-[11px] text-[#526057]">{{ $pkg->duration ?? 9 }} Hari Program</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="text-xs text-[#12271E] font-medium truncate max-w-xs">
                                🕋 {{ $pkg->variants->first()?->hotelMakkah?->name ?? 'Hotel Makkah' }}
                            </div>
                            <div class="text-[11px] text-[#526057] truncate max-w-xs mt-0.5">
                                🕌 {{ $pkg->variants->first()?->hotelMadinah?->name ?? 'Hotel Madinah' }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap font-bold text-[#12271E]">
                            {{ $pkg->quota }} Kursi
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                {{ $pkg->filled_pax }} Pax
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $pkg->remaining_quota <= 5 && $pkg->remaining_quota > 0 ? 'bg-amber-100 text-amber-900' : ($pkg->remaining_quota === 0 ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $pkg->remaining_quota }} Kursi
                            </span>
                        </td>
                        <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                            <div class="inline-flex flex-col items-end">
                                <span class="text-xs font-extrabold {{ $pkg->fill_percentage >= 100 ? 'text-emerald-700' : 'text-[#12271E]' }}">
                                    {{ $pkg->fill_percentage }}%
                                </span>
                                <div class="w-16 bg-[#EFF3EB] rounded-full h-1.5 mt-1 overflow-hidden">
                                    <div class="bg-[#1B3B2B] h-full rounded-full" style="width: {{ $pkg->fill_percentage }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-[#526057]">
                            <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            </div>
                            <p class="font-semibold text-sm text-[#12271E]">Tidak Ada Jadwal Keberangkatan</p>
                            <p class="text-xs mt-1">Tidak ada paket yang berangkat pada periode {{ $periodData['label'] }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($packages->hasPages())
        <div class="p-3.5 border-t border-[#E0E7DC] bg-[#EFF3EB]/30">
            {{ $packages->links() }}
        </div>
    @endif
</div>