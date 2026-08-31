<x-layouts.main :title="'Katalog Paket Umrah & Haji Khusus — PT. Zein Internasional'" :company="$company">

    @php
        $whatsapp = $company['whatsapp'] ?? '6281222222562';
        $totalPackages = count($packages);
        $umrahCount = collect($packages)->where('type', 'umrah')->count();
        $hajiCount = collect($packages)->where('type', 'haji')->count();
    @endphp

    {{-- ═══════════════════════════════════════════════════════════════
         1. HEADER HALAMAN (bg-white)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-[#E0E7DC]" x-data="{ filter: '{{ $type ?? 'all' }}' }">

        <div class="pt-8 pb-14 sm:pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumb :items="[['title' => 'Katalog Paket']]" />

                <div data-reveal class="mt-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="max-w-2xl">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.25em] text-[#1B3B2B] block mb-2">
                            PAKET PILIHAN
                        </span>
                        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12271E] tracking-tight">
                            Jelajahi Paket Pilihan Kami
                        </h1>
                        <p class="text-xs sm:text-sm text-[#526057] mt-3 leading-relaxed max-w-xl">
                            Pilihlah jadwal dan fasilitas paket yang paling sesuai dengan kebutuhan ibadah Anda dan keluarga.
                        </p>
                    </div>

                    {{-- Tab Filter Kategori --}}
                    <div class="flex items-center gap-1.5 bg-[#EFF3EB] border border-[#E0E7DC] rounded-full p-1.5 shrink-0">
                        <button @click="filter = 'all'"
                                :class="filter === 'all' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'bg-transparent text-[#526057] hover:text-[#12271E] font-semibold'"
                                class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer">
                            Semua
                            <span class="ml-1 opacity-75">({{ $totalPackages }})</span>
                        </button>
                        <button @click="filter = 'umrah'"
                                :class="filter === 'umrah' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'bg-transparent text-[#526057] hover:text-[#12271E] font-semibold'"
                                class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer">
                            Umrah
                            <span class="ml-1 opacity-75">({{ $umrahCount }})</span>
                        </button>
                        <button @click="filter = 'haji'"
                                :class="filter === 'haji' ? 'bg-[#1B3B2B] text-white shadow-xs font-bold' : 'bg-transparent text-[#526057] hover:text-[#12271E] font-semibold'"
                                class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer">
                            Haji Khusus
                            <span class="ml-1 opacity-75">({{ $hajiCount }})</span>
                        </button>
                    </div>
                </div>

                {{-- Baris Statistik --}}
                <div data-reveal class="mt-8 pt-4 border-t border-[#E0E7DC] flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#4D5E54]">
                    <span class="flex items-center gap-1.5 font-bold text-[#12271E]">
                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span>{{ $totalPackages }} Paket Tersedia</span>
                    </span>
                    <span class="text-zinc-300">•</span>
                    <span class="text-zinc-300">•</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Izin Resmi Kemenag RI</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. GRID PAKET (bg-white)
        ═══════════════════════════════════════════════════════════ --}}
        <div class="py-14 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                @if(count($packages) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                        @foreach($packages as $package)
                            <div x-show="filter === 'all' || filter === '{{ $package['type'] }}'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <x-package-card :package="$package" :company="$company" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-[#EFF3EB] rounded-3xl border border-[#E0E7DC]">
                        <p class="text-sm font-semibold text-[#12271E]">Belum ada paket yang tersedia untuk kategori ini.</p>
                        <a href="{{ route('paket') }}" class="mt-4 inline-block text-xs font-bold text-[#1B3B2B] hover:underline">
                            Lihat Semua Paket &rarr;
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>

</x-layouts.main>
