@props([
    'filterData',
    'action' => null,
    'exportCsvUrl' => null,
    'exportPdfUrl' => null,
])

@php
    $actionUrl = $action ?? request()->url();
    $currentPeriod = $filterData['period'] ?? 'month';
    $selectedMonth = (int) ($filterData['selectedMonth'] ?? now()->month);
    $selectedYear = (int) ($filterData['selectedYear'] ?? now()->year);
    $weekDate = $filterData['weekDate'] ?? now()->format('Y-m-d');
    $prevWeekDate = $filterData['prevWeekDate'] ?? now()->subWeek()->format('Y-m-d');
    $nextWeekDate = $filterData['nextWeekDate'] ?? now()->addWeek()->format('Y-m-d');
    $customStartDate = $filterData['customStartDate'] ?? now()->startOfMonth()->format('Y-m-d');
    $customEndDate = $filterData['customEndDate'] ?? now()->endOfMonth()->format('Y-m-d');
    $activeLabel = $filterData['label'] ?? 'Bulan Ini';

    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $years = range(now()->year + 2, 2024);
    if (!in_array($selectedYear, $years)) {
        $years[] = $selectedYear;
        sort($years);
    }
@endphp

<div x-data="{ activeTab: '{{ $currentPeriod }}' }" 
     class="bg-white rounded-2xl border border-[#E0E7DC] shadow-2xs p-3.5 sm:p-4 mb-6 transition-all"
     style="animation: fadeSlideUp 0.35s ease both">
    
    {{-- Header Baris Atas: Badge Periode (Kiri) & Grup Tombol Ekspor (Kanan) --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-3 border-b border-[#E0E7DC]">
        {{-- Badge Periode Aktif --}}
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#EFF3EB] border border-[#CCD8C7] text-xs font-bold text-[#1B3B2B] w-fit">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[#526057] font-medium">Periode:</span>
            <span class="text-[#12271E]">{{ $activeLabel }}</span>
        </div>

        {{-- Grup Tombol Ekspor (Terpisah Rapi di Kanan) --}}
        @if($exportCsvUrl || $exportPdfUrl)
            <div class="inline-flex items-center gap-2">
                @if($exportCsvUrl)
                    <a href="{{ $exportCsvUrl }}" 
                       title="Unduh data dalam format CSV/Excel"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#1B3B2B] bg-white hover:bg-[#EFF3EB] border border-[#CCD8C7] transition-all shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Export Excel/CSV</span>
                    </a>
                @endif

                @if($exportPdfUrl)
                    <a href="{{ $exportPdfUrl }}" 
                       target="_blank"
                       title="Buka atau unduh versi PDF / Cetak"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-800 bg-rose-50/70 hover:bg-rose-100 border border-rose-200 transition-all shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        <span>Cetak / PDF</span>
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Kontrol Filter Periode 1 Baris: Tab Mode (Kiri) + Dynamic Control Auto-Apply (Sebelah Kanan) --}}
    <form method="GET" action="{{ $actionUrl }}" x-ref="periodFilterForm" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        {{-- Query params tersembunyi (tab, search, status, dsb) --}}
        @foreach(request()->except(['period', 'month', 'year', 'week_date', 'start_date', 'end_date', 'page']) as $key => $value)
            @if(is_array($value))
                @foreach($value as $subValue)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <input type="hidden" name="period" x-ref="periodInput" :value="activeTab">

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tab Pilihan Mode di Kiri --}}
            <div class="inline-flex p-1 bg-[#F4F6F2] rounded-xl border border-[#E0E7DC] overflow-x-auto text-xs font-semibold">
                <button type="button" 
                        @click="if (activeTab !== 'week') { activeTab = 'week'; $refs.periodInput.value = 'week'; $nextTick(() => $refs.periodFilterForm.submit()); }"
                        :class="activeTab === 'week' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/60'"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer whitespace-nowrap">
                    Per Minggu
                </button>

                <button type="button" 
                        @click="if (activeTab !== 'month') { activeTab = 'month'; $refs.periodInput.value = 'month'; $nextTick(() => $refs.periodFilterForm.submit()); }"
                        :class="activeTab === 'month' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/60'"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer whitespace-nowrap">
                    Per Bulan
                </button>

                <button type="button" 
                        @click="if (activeTab !== 'year') { activeTab = 'year'; $refs.periodInput.value = 'year'; $nextTick(() => $refs.periodFilterForm.submit()); }"
                        :class="activeTab === 'year' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/60'"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer whitespace-nowrap">
                    Per Tahun
                </button>

                <button type="button" 
                        @click="activeTab = 'custom'; $refs.periodInput.value = 'custom'"
                        :class="activeTab === 'custom' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/60'"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer whitespace-nowrap">
                    Rentang Kustom
                </button>
            </div>

            {{-- Dynamic Date-Picker / Dropdown (Langsung di Sebelah Tab) --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                {{-- 1. Mode Minggu: Arrow < + Datepicker + Arrow > (Auto Apply on Change) --}}
                <div x-show="activeTab === 'week'" class="flex items-center gap-1.5" x-cloak>
                    <a href="{{ $actionUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'week_date', 'period']), ['period' => 'week', 'week_date' => $prevWeekDate])) }}"
                       class="p-2 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] hover:bg-[#EFF3EB] text-[#526057] hover:text-[#12271E] transition-colors"
                       title="Minggu Sebelumnya">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </a>

                    <input type="date" 
                           name="week_date" 
                           value="{{ $weekDate }}" 
                           @change="$refs.periodFilterForm.submit()"
                           class="px-3 py-1.5 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium">

                    <a href="{{ $actionUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'week_date', 'period']), ['period' => 'week', 'week_date' => $nextWeekDate])) }}"
                       class="p-2 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] hover:bg-[#EFF3EB] text-[#526057] hover:text-[#12271E] transition-colors"
                       title="Minggu Berikutnya">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </a>
                </div>

                {{-- 2. Mode Bulan: Dropdown Bulan & Tahun (Auto Apply on Change) --}}
                <div x-show="activeTab === 'month'" class="flex items-center gap-1.5">
                    <select name="month" 
                            @change="$refs.periodFilterForm.submit()"
                            class="px-3 py-1.5 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium cursor-pointer">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $selectedMonth === $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" 
                            @change="$refs.periodFilterForm.submit()"
                            class="px-3 py-1.5 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium cursor-pointer">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Mode Tahun: Dropdown Tahun (Auto Apply on Change) --}}
                <div x-show="activeTab === 'year'" class="flex items-center gap-1.5" x-cloak>
                    <select name="year" 
                            @change="$refs.periodFilterForm.submit()"
                            class="px-3 py-1.5 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium cursor-pointer">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>
                                Tahun {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Mode Rentang Kustom: Dari Tanggal + s/d + Sampai Tanggal + Tombol Terapkan --}}
                <div x-show="activeTab === 'custom'" class="flex items-center gap-1.5 flex-wrap" x-cloak>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $customStartDate }}" 
                           class="px-2.5 py-1.5 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium">

                    <span class="text-xs text-[#526057]">s/d</span>

                    <input type="date" 
                           name="end_date" 
                           value="{{ $customEndDate }}" 
                           class="px-2.5 py-1.5 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all bg-white font-medium">

                    <button type="submit" 
                            class="py-1.5 px-3 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shadow-2xs cursor-pointer">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        {{-- Reset Cepat (Teks Halus, Hanya Muncul Jika Bukan Bulan Ini) --}}
        @if($currentPeriod !== 'month' || $selectedMonth !== (int) now()->month || $selectedYear !== (int) now()->year)
            <a href="{{ $actionUrl }}" 
               class="text-[11px] font-semibold text-[#526057] hover:text-[#1B3B2B] hover:underline self-end sm:self-center">
                Reset ke Bulan Ini
            </a>
        @endif
    </form>
</div>
