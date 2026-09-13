<x-layouts.admin :title="'Laporan & Rekapitulasi — Admin PT. Zein Internasional'">

    {{-- Header --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="animation: fadeSlideUp 0.35s ease both">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-[#526057] hover:text-[#1B3B2B] transition-colors">
                    Dashboard
                </a>
                <span class="text-xs text-[#526057]/50">&bull;</span>
                <span class="text-xs font-semibold text-[#1B3B2B]">Laporan & Rekap</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                Laporan & Rekapitulasi
            </h1>
            <p class="text-xs sm:text-sm text-[#526057] mt-0.5">
                Monitoring pendaftaran jamaah, mutasi arus kas, saldo piutang berjalan, dan kapasitas kuota paket.
            </p>
        </div>
    </div>

    {{-- Filter Periode BERSAMA (Di Luar Tab, Persisten Saat Ganti Jenis Laporan) --}}
    <x-period-filter :filterData="$periodData" :exportCsvUrl="$exportCsvUrl" :exportPdfUrl="$exportPdfUrl" />

    {{-- Container Tab Laporan (Alpine.js Responsive Tab Switcher Tanpa Reload Penuh) --}}
    <div x-data="reportApp()" class="relative">
        
        {{-- Navigasi Tab Laporan --}}
        <div class="flex items-center gap-1.5 p-1 bg-[#F4F6F2] rounded-2xl border border-[#E0E7DC] overflow-x-auto text-xs font-semibold mb-5 shadow-2xs">
            {{-- Tab 1: Jamaah --}}
            <button type="button" 
                    @click="switchTab('jamaah')"
                    :class="activeTab === 'jamaah' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/70'"
                    class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                <span>Laporan Jamaah</span>
            </button>

            {{-- Tab 2: Pembayaran --}}
            <button type="button" 
                    @click="switchTab('pembayaran')"
                    :class="activeTab === 'pembayaran' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/70'"
                    class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m-19.5 0a.75.75 0 00.75.75h.75m0 0v.75m0-1.5h16.5m0 0v.75m-16.5-.75V6m16.5 0v10.5m0 0a.75.75 0 01-.75.75h-.75" /></svg>
                <span>Laporan Pembayaran</span>
            </button>

            {{-- Tab 3: Piutang --}}
            <button type="button" 
                    @click="switchTab('piutang')"
                    :class="activeTab === 'piutang' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/70'"
                    class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Laporan Piutang</span>
            </button>

            {{-- Tab 4: Keberangkatan --}}
            <button type="button" 
                    @click="switchTab('keberangkatan')"
                    :class="activeTab === 'keberangkatan' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'text-[#526057] hover:text-[#12271E] hover:bg-white/70'"
                    class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                <span>Laporan Keberangkatan</span>
            </button>
        </div>

        {{-- Loading Spinner Overlay Saat Fetch AJAX --}}
        <div x-show="loading" 
             x-cloak 
             class="absolute inset-0 bg-white/70 backdrop-blur-2xs flex flex-col items-center justify-center rounded-2xl z-20 transition-all min-h-[300px]">
            <div class="w-8 h-8 rounded-full border-3 border-[#E0E7DC] border-t-[#1B3B2B] animate-spin mb-2"></div>
            <span class="text-xs font-bold text-[#1B3B2B]">Memuat data laporan...</span>
        </div>

        {{-- Area Konten Tab Dinamis --}}
        <div id="report-tab-container">
            @include('admin.reports.partials.' . $tab)
        </div>

    </div>

    @push('scripts')
    <script>
        function reportApp() {
            return {
                activeTab: '{{ $tab }}',
                search: '{{ request('search') }}',
                status: '{{ request('status', $tab === 'pembayaran' ? ($currentStatus ?? 'disetujui') : '') }}',
                loading: false,

                switchTab(tabName) {
                    if (this.activeTab === tabName) return;
                    this.activeTab = tabName;
                    this.search = '';
                    this.status = tabName === 'pembayaran' ? 'disetujui' : '';
                    this.fetchData();
                },

                applyFilters() {
                    this.fetchData();
                },

                fetchData() {
                    this.loading = true;
                    const params = new URLSearchParams(window.location.search);
                    params.set('tab', this.activeTab);

                    if (this.search && this.search.trim().length > 0) {
                        params.set('search', this.search.trim());
                    } else {
                        params.delete('search');
                    }

                    if (this.status && this.status.trim().length > 0) {
                        params.set('status', this.status.trim());
                    } else {
                        params.delete('status');
                    }

                    params.delete('page');

                    const newUrl = `${window.location.pathname}?${params.toString()}`;
                    window.history.pushState(null, '', newUrl);

                    fetch(newUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.text();
                    })
                    .then(html => {
                        const container = document.getElementById('report-tab-container');
                        if (container) {
                            container.innerHTML = html;
                        }
                        this.loading = false;
                    })
                    .catch(err => {
                        // Fallback jika AJAX gagal: reload halaman normal
                        window.location.href = newUrl;
                    });
                }
            }
        }
    </script>
    @endpush

</x-layouts.admin>