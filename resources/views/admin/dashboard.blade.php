<x-layouts.admin :title="'Beranda Operasional — Admin PT. Zein Internasional'">

    {{-- ═══════════════════════════════════════════════════════════════
         1. KHUSUS TAMPILAN MOBILE (< 1024px) — MOBILE APP DASHBOARD
         Struktur: Header -> Ringkasan Statistik -> Informasi -> Menu
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="lg:hidden space-y-4 max-w-lg mx-auto pb-4">

        {{-- Mobile Header: Greeting & Quick Action --}}
        <div class="flex items-center justify-between gap-3 pt-1">
            <div class="min-w-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#1B3B2B] bg-[#EFF3EB] px-2.5 py-0.5 rounded-full border border-[#CCD8C7] inline-flex items-center gap-1 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ now()->translatedFormat('l, d M Y') }}
                </span>
                <h1 class="text-lg sm:text-xl font-extrabold text-[#12271E] tracking-tight truncate">
                    Halo, {{ explode(' ', auth()->user()->name)[0] }}
                </h1>
                <p class="text-[11px] text-[#526057] truncate">Pusat Kendali Operasional</p>
            </div>

            <a href="{{ route('admin.packages.create') }}" 
               class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-95 transition-all shadow-xs border border-[#2D5A43]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Paket</span>
            </a>
        </div>

        {{-- Mobile Card Utama: Ringkasan Statistik Sistem --}}
        <div class="bg-[#1B3B2B] text-white rounded-3xl p-4 sm:p-5 shadow-lg relative overflow-hidden border border-[#2D5A43]">
            {{-- Background decorative elements --}}
            <div class="absolute -right-8 -top-8 w-36 h-36 bg-[#C2A264]/15 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-8 -bottom-8 w-36 h-36 bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-3.5">
                {{-- Card Header --}}
                <div class="flex items-center justify-between gap-2 border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-[#C2A264]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200/80 block">Ringkasan Sistem</span>
                            <span class="text-xs font-bold text-white block">Ikhtisar Keuangan & Data</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-300 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                        Live Data
                    </span>
                </div>

                {{-- 2x2 Financial & Operational Stat Grid --}}
                <div class="grid grid-cols-2 gap-3 pt-1">
                    {{-- Dana Masuk --}}
                    <div class="bg-white/5 rounded-2xl p-3 border border-white/10 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-200/70 uppercase tracking-wider block mb-0.5">Dana Masuk</span>
                        <div class="text-sm sm:text-base font-extrabold text-emerald-300 truncate">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </div>
                        <span class="text-[9px] text-white/50 block mt-0.5">Diverifikasi</span>
                    </div>

                    {{-- Sisa Piutang --}}
                    <div class="bg-white/5 rounded-2xl p-3 border border-white/10 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-200/70 uppercase tracking-wider block mb-0.5">Sisa Tagihan</span>
                        <div class="text-sm sm:text-base font-extrabold text-[#C2A264] truncate">
                            Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                        </div>
                        <span class="text-[9px] text-white/50 block mt-0.5">Menunggu Pelunasan</span>
                    </div>

                    {{-- Total Jamaah --}}
                    <div class="bg-white/5 rounded-2xl p-3 border border-white/10 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-200/70 uppercase tracking-wider block mb-0.5">Total Jamaah</span>
                        <div class="text-lg sm:text-xl font-extrabold text-white truncate">
                            {{ $totalJamaah }}
                        </div>
                        <span class="text-[9px] text-white/50 block mt-0.5">Akun Terdaftar</span>
                    </div>

                    {{-- Total Pendaftaran Aktif --}}
                    <div class="bg-white/5 rounded-2xl p-3 border border-white/10 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-200/70 uppercase tracking-wider block mb-0.5">Booking Aktif</span>
                        <div class="text-lg sm:text-xl font-extrabold text-white truncate">
                            {{ $totalPendaftaran }}
                        </div>
                        <span class="text-[9px] text-white/50 block mt-0.5">{{ $totalPaketAktif }} Paket Aktif</span>
                    </div>

                    {{-- Selesai Berangkat --}}
                    <div class="col-span-2 bg-[#12271E]/60 rounded-2xl p-3 border border-[#C2A264]/30 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-[#E5C88F] uppercase tracking-wider block">Selesai Berangkat (Tahap 9)</span>
                            <span class="text-[9px] text-emerald-100/70 block">Jamaah Tuntas Ibadah</span>
                        </div>
                        <div class="text-base sm:text-lg font-extrabold text-white flex items-center gap-1.5">
                            <span class="text-[#E5C88F] font-bold text-xs">★</span>
                            <span>{{ $totalSelesai ?? 0 }} Jamaah</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Perlu Tindakan (Action Required Queues) --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ ($pendingDocsCount + $pendingPaymentsCount + $pendingCancellationsCount) > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                    <span>Perlu Tindakan</span>
                </h2>
                <span class="text-[10px] font-semibold text-[#526057]">
                    {{ $pendingDocsCount + $pendingPaymentsCount + $pendingCancellationsCount }} Antrean
                </span>
            </div>

            <div class="grid grid-cols-1 gap-2">
                {{-- Verifikasi Dokumen --}}
                <a href="{{ route('admin.registrations.index') }}" 
                   class="bg-white rounded-2xl border p-3 shadow-2xs flex items-center justify-between gap-3 active:scale-[0.99] transition-all min-h-[48px] {{ $pendingDocsCount > 0 ? 'border-amber-200 bg-amber-50/40' : 'border-[#E0E7DC]' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $pendingDocsCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-[#EFF3EB] text-[#1B3B2B]' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#12271E] truncate">Verifikasi Berkas Jamaah</h3>
                            <p class="text-[11px] text-[#526057] truncate">Paspor, KTP, KK, & Buku Nikah</p>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pendingDocsCount > 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-[#EFF3EB] text-[#526057]' }}">
                            {{ $pendingDocsCount }} Menunggu
                        </span>
                        <svg class="w-3.5 h-3.5 text-[#526057]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>

                {{-- Verifikasi Pembayaran --}}
                <a href="{{ route('admin.payments.index') }}" 
                   class="bg-white rounded-2xl border p-3 shadow-2xs flex items-center justify-between gap-3 active:scale-[0.99] transition-all min-h-[48px] {{ $pendingPaymentsCount > 0 ? 'border-blue-200 bg-blue-50/40' : 'border-[#E0E7DC]' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $pendingPaymentsCount > 0 ? 'bg-blue-100 text-blue-800' : 'bg-[#EFF3EB] text-[#1B3B2B]' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#12271E] truncate">Validasi Setoran Pembayaran</h3>
                            <p class="text-[11px] text-[#526057] truncate">Bukti transfer DP & Pelunasan</p>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pendingPaymentsCount > 0 ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-[#EFF3EB] text-[#526057]' }}">
                            {{ $pendingPaymentsCount }} Setoran
                        </span>
                        <svg class="w-3.5 h-3.5 text-[#526057]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>

                {{-- Permohonan Batal --}}
                <a href="{{ route('admin.cancellations.index') }}" 
                   class="bg-white rounded-2xl border p-3 shadow-2xs flex items-center justify-between gap-3 active:scale-[0.99] transition-all min-h-[48px] {{ $pendingCancellationsCount > 0 ? 'border-amber-200 bg-amber-50/40' : 'border-[#E0E7DC]' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $pendingCancellationsCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-[#EFF3EB] text-[#1B3B2B]' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#12271E] truncate">Validasi Pembatalan</h3>
                            <p class="text-[11px] text-[#526057] truncate">Pengajuan refund & pembatalan</p>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pendingCancellationsCount > 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-[#EFF3EB] text-[#526057]' }}">
                            {{ $pendingCancellationsCount }} Pengajuan
                        </span>
                        <svg class="w-3.5 h-3.5 text-[#526057]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>
            </div>
        </div>

        {{-- Mobile Menu Cepat (Quick Shortcuts Grid) --}}
        <div class="space-y-2">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Menu Cepat Admin</h2>
            <div class="grid grid-cols-4 gap-2">
                {{-- Paket --}}
                <a href="{{ route('admin.packages.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Paket</span>
                </a>

                {{-- Jamaah --}}
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Jamaah</span>
                </a>

                {{-- Pendaftaran --}}
                <a href="{{ route('admin.registrations.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Daftar</span>
                </a>

                {{-- Pembayaran --}}
                <a href="{{ route('admin.payments.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Bayar</span>
                </a>

                {{-- Hotel --}}
                <a href="{{ route('admin.hotels.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Hotel</span>
                </a>

                {{-- Maskapai --}}
                <a href="{{ route('admin.airlines.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Maskapai</span>
                </a>

                {{-- Galeri --}}
                <a href="{{ route('admin.galleries.index') }}" 
                   class="bg-white rounded-2xl border border-[#E0E7DC] p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#12271E] truncate w-full">Galeri</span>
                </a>

                {{-- Tambah Paket --}}
                <a href="{{ route('admin.packages.create') }}" 
                   class="bg-emerald-50 rounded-2xl border border-emerald-200 p-2.5 flex flex-col items-center text-center shadow-2xs active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#1B3B2B] text-white flex items-center justify-center mb-1.5 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#1B3B2B] truncate w-full">+ Paket</span>
                </a>
            </div>
        </div>

        {{-- Mobile Peringatan Jatuh Tempo (Hanya jika ada) --}}
        @if($upcomingDueInvoices->count() > 0)
            <div class="bg-white rounded-2xl border border-amber-200 shadow-2xs overflow-hidden">
                <div class="p-3 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                        <h3 class="text-xs font-bold text-amber-900">Batas Pelunasan Dekat</h3>
                    </div>
                    <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">
                        {{ $upcomingDueInvoices->count() }} Jamaah
                    </span>
                </div>
                <div class="divide-y divide-amber-100/60 p-1">
                    @foreach($upcomingDueInvoices->take(3) as $inv)
                        <div class="p-2.5 flex items-center justify-between gap-2 text-xs">
                            <div class="min-w-0">
                                <span class="font-bold text-[#12271E] block truncate text-xs">{{ $inv->registration->user->name ?? 'Jamaah' }}</span>
                                <span class="text-[10px] text-[#526057] block truncate">{{ $inv->registration->package->name ?? '-' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-red-600 block text-xs">Rp {{ number_format($inv->remaining_balance, 0, ',', '.') }}</span>
                                <span class="text-[9px] text-amber-800 block">Jatuh: {{ \Carbon\Carbon::parse($inv->due_date)->translatedFormat('d M') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Mobile Pendaftaran Terbaru --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Pendaftaran Terbaru</h2>
                <a href="{{ route('admin.registrations.index') }}" class="text-[11px] font-bold text-[#1B3B2B] hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($recentRegistrations->count() > 0)
                <div class="space-y-2">
                    @foreach($recentRegistrations->take(4) as $reg)
                        <a href="{{ route('admin.registrations.show', $reg->id) }}" 
                           class="bg-white rounded-2xl border border-[#E0E7DC] p-3 shadow-2xs flex items-center justify-between gap-3 active:scale-[0.99] transition-all">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center font-bold text-xs shrink-0 border border-[#CCD8C7]">
                                    {{ strtoupper(substr($reg->user->name ?? 'J', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-xs text-[#12271E] truncate">{{ $reg->user->name ?? 'Jamaah' }}</span>
                                    </div>
                                    <p class="text-[11px] text-[#526057] truncate mt-0.5">
                                        {{ $reg->package->name ?? '-' }} &bull; {{ $reg->members->count() }} Jamaah
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex flex-col items-end gap-1">
                                <x-status-badge :status="$reg->status" size="xs" />
                                <span class="text-[9px] text-[#526057]">
                                    {{ $reg->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 text-center shadow-2xs">
                    <p class="text-xs text-[#526057]">Belum ada pendaftaran jamaah baru.</p>
                </div>
            @endif
        </div>

        {{-- Mobile Ringkasan Kuota Paket --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Status Kuota Paket</h2>
                <a href="{{ route('admin.packages.index') }}" class="text-[11px] font-bold text-[#1B3B2B] hover:underline">
                    Semua Paket &rarr;
                </a>
            </div>

            @if($packagesSummary->count() > 0)
                <div class="bg-white rounded-2xl border border-[#E0E7DC] divide-y divide-[#EFF3EB] shadow-2xs overflow-hidden">
                    @foreach($packagesSummary->take(3) as $pkg)
                        <div class="p-3 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-xs text-[#12271E] truncate">{{ $pkg->name }}</span>
                                <span class="text-[10px] font-bold shrink-0 {{ $pkg->pax_count >= $pkg->quota ? 'text-red-600' : 'text-[#1B3B2B]' }}">
                                    {{ $pkg->pax_count }} / {{ $pkg->quota }} Kursi
                                </span>
                            </div>
                            @php
                                $fillPercentage = $pkg->quota > 0 ? min(100, round(($pkg->pax_count / $pkg->quota) * 100)) : 0;
                            @endphp
                            <div class="w-full bg-[#EFF3EB] rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#1B3B2B] h-full rounded-full transition-all" style="width: {{ $fillPercentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-[#526057] pt-0.5">
                                <span>{{ $pkg->departure_date ? \Carbon\Carbon::parse($pkg->departure_date)->translatedFormat('d M Y') : 'TBA' }}</span>
                                <span class="font-semibold text-emerald-700">Rp {{ number_format($pkg->total_paid, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Mobile Analitik & Grafik Tren --}}
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Grafik Tren &amp; Analisis</h2>
                <span class="text-[10px] font-bold text-[#1B3B2B] bg-[#EFF3EB] px-2 py-0.5 rounded-full border border-[#CCD8C7]">{{ $periodData['label'] }}</span>
            </div>

            <x-period-filter :filterData="$periodData" />

            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 shadow-2xs space-y-2">
                <div class="flex items-center justify-between border-b border-[#E0E7DC] pb-2">
                    <div>
                        <span class="text-xs font-bold text-[#12271E] block">Tren Pendaftaran</span>
                        <span class="text-[10px] text-[#526057]">Interval: {{ $periodData['granularity_label'] ?? 'Interval' }}</span>
                    </div>
                    <span class="text-xs font-extrabold text-[#1B3B2B]">{{ number_format($totalPeriodRegistrations) }} Jamaah</span>
                </div>
                <div class="relative h-48 w-full">
                    <canvas id="registrationChartMobile"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 shadow-2xs space-y-2">
                <div class="flex items-center justify-between border-b border-[#E0E7DC] pb-2">
                    <div>
                        <span class="text-xs font-bold text-[#12271E] block">Arus Kas Masuk</span>
                        <span class="text-[10px] text-[#526057]">Interval: {{ $periodData['granularity_label'] ?? 'Interval' }}</span>
                    </div>
                    <span class="text-xs font-extrabold text-[#C2A264]">Rp {{ number_format($totalPeriodIncome, 0, ',', '.') }}</span>
                </div>
                <div class="relative h-48 w-full">
                    <canvas id="paymentChartMobile"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. KHUSUS TAMPILAN DESKTOP & TABLET LEBAR (>= 1024px)
         Mempertahankan Desain Desktop yang Sudah Ada Tanpa Perubahan
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="hidden lg:block space-y-6 sm:space-y-8">

        {{-- 1. Header & Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#1B3B2B] bg-[#EFF3EB] px-2.5 py-1 rounded-full border border-[#E0E7DC] inline-block mb-1.5">
                    Pusat Kendali
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                    Ringkasan Hari Ini
                </h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-0.5">
                    Pantau pendaftaran jamaah, verifikasi berkas, dan validasi pembayaran.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.packages.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Tambah Paket Baru</span>
                </a>
            </div>
        </div>

        {{-- 2. Bagian Perlu Tindakan (Daftar Antrean) --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#4D5E54] mb-3">
                Perlu Tindakan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
                
                {{-- Verifikasi Dokumen --}}
                <a href="{{ route('admin.registrations.index') }}" 
                   class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex items-center justify-between shadow-2xs group {{ $pendingDocsCount > 0 ? 'bg-amber-50/70 border-amber-200 hover:border-amber-300' : 'bg-white border-[#E0E7DC] hover:border-[#1B3B2B]/30' }}">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold {{ $pendingDocsCount > 0 ? 'bg-amber-100 text-amber-800 ring-2 ring-amber-300/40' : 'bg-[#EFF3EB] text-[#526057]' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-[#4D5E54] uppercase tracking-wider block">Verifikasi Dokumen</span>
                            <span class="text-sm sm:text-base font-bold text-[#12271E] block mt-0.5">
                                {{ $pendingDocsCount }} Berkas Menunggu
                            </span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-[#526057] group-hover:text-[#1B3B2B] shadow-2xs border border-[#E0E7DC]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>

                {{-- Verifikasi Pembayaran --}}
                <a href="{{ route('admin.payments.index') }}" 
                   class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex items-center justify-between shadow-2xs group {{ $pendingPaymentsCount > 0 ? 'bg-blue-50/70 border-blue-200 hover:border-blue-300' : 'bg-white border-[#E0E7DC] hover:border-[#1B3B2B]/30' }}">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold {{ $pendingPaymentsCount > 0 ? 'bg-blue-100 text-blue-800 ring-2 ring-blue-300/40' : 'bg-[#EFF3EB] text-[#526057]' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-[#4D5E54] uppercase tracking-wider block">Verifikasi Pembayaran</span>
                            <span class="text-sm sm:text-base font-bold text-[#12271E] block mt-0.5">
                                {{ $pendingPaymentsCount }} Setoran Menunggu
                            </span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-[#526057] group-hover:text-[#1B3B2B] shadow-2xs border border-[#E0E7DC]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>

                {{-- Validasi Pembatalan --}}
                <a href="{{ route('admin.cancellations.index') }}" 
                   class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex items-center justify-between shadow-2xs group {{ $pendingCancellationsCount > 0 ? 'bg-amber-50/70 border-amber-200 hover:border-amber-300' : 'bg-white border-[#E0E7DC] hover:border-[#1B3B2B]/30' }}">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold {{ $pendingCancellationsCount > 0 ? 'bg-amber-100 text-amber-800 ring-2 ring-amber-300/40' : 'bg-[#EFF3EB] text-[#526057]' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-[#4D5E54] uppercase tracking-wider block">Validasi Pembatalan</span>
                            <span class="text-sm sm:text-base font-bold text-[#12271E] block mt-0.5">
                                {{ $pendingCancellationsCount }} Permohonan
                            </span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-[#526057] group-hover:text-[#1B3B2B] shadow-2xs border border-[#E0E7DC]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </a>

            </div>
        </div>

        {{-- 3. Kartu Ringkasan Data --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#4D5E54] mb-3">
                Ringkasan Data
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
                
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 sm:p-5 shadow-xs hover:border-[#1B3B2B]/30 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider">Total Jamaah</span>
                        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center border border-[#CCD8C7]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#12271E] block">{{ $totalJamaah }}</span>
                    <span class="text-[11px] text-[#526057] mt-1 block">Akun Terdaftar</span>
                </div>

                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 sm:p-5 shadow-xs hover:border-[#1B3B2B]/30 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider">Booking Aktif</span>
                        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center border border-[#CCD8C7]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#12271E] block">{{ $totalPendaftaran }}</span>
                    <span class="text-[11px] text-[#526057] mt-1 block">{{ $totalPaketAktif }} Paket Aktif</span>
                </div>

                <div class="bg-white rounded-2xl border border-[#C2A264]/40 p-4 sm:p-5 shadow-xs bg-gradient-to-b from-[#F7F5F0] to-white hover:border-[#C2A264] transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#1B3B2B] uppercase tracking-wider">Selesai Ibadah</span>
                        <div class="w-8 h-8 rounded-xl bg-[#1B3B2B] text-[#E5C88F] flex items-center justify-center border border-[#C2A264]/30 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#12271E] block">{{ $totalSelesai ?? 0 }}</span>
                    <span class="text-[11px] text-[#C2A264] mt-1 block font-bold">Tahap 9 &bull; Tuntas</span>
                </div>

                <div class="bg-white rounded-2xl border border-emerald-200 p-4 sm:p-5 shadow-xs bg-gradient-to-b from-emerald-50/40 to-white hover:border-emerald-300 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Dana Masuk</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs border border-emerald-200">
                            Rp
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-extrabold text-[#12271E] block truncate">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-emerald-700 mt-1 block font-medium">Sudah diverifikasi</span>
                </div>

                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 sm:p-5 shadow-xs hover:border-[#1B3B2B]/30 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider">Sisa Pembayaran</span>
                        <div class="w-8 h-8 rounded-xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center font-bold text-xs border border-[#CCD8C7]">
                            Rp
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-extrabold text-[#C2A264] block truncate">
                        Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-[#526057] mt-1 block">Menunggu pelunasan</span>
                </div>

            </div>
        </div>

        {{-- 4. Grafik & Analitik Kinerja Berdasarkan Periode (PRD Bagian 15) --}}
        <div class="space-y-4">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#4D5E54]">
                    Tren &amp; Analitik Kinerja
                </h2>
                <p class="text-xs text-[#526057] mt-0.5">
                    Visualisasi pendaftaran jamaah dan arus kas pembayaran untuk periode: <span class="font-bold text-[#1B3B2B]">{{ $periodData['label'] }}</span>
                </p>
            </div>

            {{-- Komponen Filter Periode --}}
            <x-period-filter :filterData="$periodData" />

            {{-- 2 Grid Grafik --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Grafik Pendaftaran --}}
                <div class="bg-white rounded-3xl border border-[#E0E7DC] p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 pb-4 border-b border-[#E0E7DC]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center border border-[#CCD8C7]">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#12271E]">Grafik Tren Pendaftaran</h3>
                                    <p class="text-[11px] text-[#526057]">Jumlah pendaftaran baru per interval</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-[#526057] block">Total Periode</span>
                                <span class="text-base font-extrabold text-[#1B3B2B]">{{ number_format($totalPeriodRegistrations) }} Jamaah</span>
                            </div>
                        </div>

                        <div class="mt-4 relative h-64 sm:h-72 w-full">
                            <canvas id="registrationChartDesktop"></canvas>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-[#E0E7DC]/60 flex items-center justify-between text-[11px] text-[#526057]">
                        <span>Sumbu X: {{ $periodData['granularity_label'] ?? 'Interval Periode' }}</span>
                        <a href="{{ route('admin.reports.jamaah', request()->query()) }}" class="font-bold text-[#1B3B2B] hover:underline">
                            Buka Laporan Jamaah &rarr;
                        </a>
                    </div>
                </div>

                {{-- Grafik Pembayaran --}}
                <div class="bg-white rounded-3xl border border-[#E0E7DC] p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 pb-4 border-b border-[#E0E7DC]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center border border-amber-200">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#12271E]">Grafik Arus Kas Masuk</h3>
                                    <p class="text-[11px] text-[#526057]">Nominal pembayaran tervalidasi per interval</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-[#526057] block">Total Periode</span>
                                <span class="text-base font-extrabold text-[#C2A264]">Rp {{ number_format($totalPeriodIncome, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-4 relative h-64 sm:h-72 w-full">
                            <canvas id="paymentChartDesktop"></canvas>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-[#E0E7DC]/60 flex items-center justify-between text-[11px] text-[#526057]">
                        <span>Sumbu X: {{ $periodData['granularity_label'] ?? 'Interval Periode' }}</span>
                        <a href="{{ route('admin.reports.payments', request()->query()) }}" class="font-bold text-[#1B3B2B] hover:underline">
                            Buka Laporan Pembayaran &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- 5. Tabel Ringkasan Pendaftar per Paket Umrah --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-[#E0E7DC] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#EFF3EB]/30">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#12271E]">
                        Ringkasan Pendaftar per Paket Umrah
                    </h2>
                    <p class="text-xs text-[#526057] mt-0.5">Pantau jumlah pendaftar, sisa kuota kursi, dan dana masuk untuk setiap jadwal keberangkatan.</p>
                </div>
                <a href="{{ route('admin.packages.index') }}" class="text-xs font-bold text-[#1B3B2B] hover:underline self-start sm:self-auto">
                    Lihat Semua Paket &rarr;
                </a>
            </div>

            @if($packagesSummary->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-[#EFF3EB]/50 border-b border-[#E0E7DC] text-[#4D5E54] uppercase text-[10px] font-bold tracking-wider">
                                <th class="py-3.5 px-6">Nama Paket</th>
                                <th class="py-3.5 px-4 text-center">Tanggal Berangkat</th>
                                <th class="py-3.5 px-4 text-center">Kuota & Terisi</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Dana Terkumpul</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E0E7DC]">
                            @foreach($packagesSummary as $pkg)
                                <tr class="hover:bg-[#EFF3EB]/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-[#12271E] block text-sm">{{ $pkg->name }}</span>
                                        <span class="text-[11px] text-[#526057]">{{ $pkg->registrations_count }} Pendaftaran ({{ $pkg->pax_count }} Jamaah)</span>
                                    </td>
                                    <td class="py-4 px-4 text-center text-[#526057]">
                                        {{ $pkg->departure_date ? \Carbon\Carbon::parse($pkg->departure_date)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 font-bold {{ $pkg->pax_count >= $pkg->quota ? 'text-red-600' : 'text-[#12271E]' }}">
                                            <span>{{ $pkg->pax_count }}</span>
                                            <span class="text-zinc-300">/</span>
                                            <span>{{ $pkg->quota }} Kursi</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($pkg->status === 'sold_out' || $pkg->pax_count >= $pkg->quota)
                                            <x-status-badge status="sold_out" size="xs" label="Penuh / Habis" />
                                        @elseif($pkg->status === 'aktif')
                                            <x-status-badge status="aktif" size="xs" label="Tersedia" />
                                        @else
                                            <x-status-badge :status="$pkg->status" size="xs" />
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right font-bold text-[#12271E]">
                                        Rp {{ number_format($pkg->total_paid, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-xs text-[#526057]">
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                    </div>
                    <p class="font-semibold text-sm text-[#12271E]">Belum Ada Paket Umrah</p>
                    <p class="mt-1">Silakan tambahkan paket umrah atau haji pertama Anda.</p>
                </div>
            @endif
        </div>

        {{-- 6. Tabel Peringatan Batas Pembayaran & Pendaftaran Terbaru --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">

            {{-- Peringatan Batas Pembayaran --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-6 py-5 border-b border-[#E0E7DC] bg-[#EFF3EB]/30">
                        <h2 class="text-base font-bold text-[#12271E] flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Peringatan Batas Waktu Pembayaran</span>
                        </h2>
                        <p class="text-xs text-[#526057] mt-0.5">Daftar jamaah dengan sisa pembayaran yang mendekati batas waktu pelunasan.</p>
                    </div>

                    @if($upcomingDueInvoices->count() > 0)
                        <div class="divide-y divide-[#E0E7DC]">
                            @foreach($upcomingDueInvoices as $inv)
                                <div class="p-4 sm:px-6 hover:bg-[#EFF3EB]/30 transition-colors flex items-center justify-between gap-4">
                                    <div class="truncate">
                                        <span class="font-bold text-[#12271E] block text-xs truncate">
                                            {{ $inv->registration->user->name ?? 'Jamaah' }}
                                        </span>
                                        <span class="text-[11px] text-[#526057] block truncate">
                                            {{ $inv->registration->package->name ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-bold text-red-600 block text-xs">
                                            Rp {{ number_format($inv->remaining_balance, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] text-[#526057] bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md font-semibold inline-block mt-0.5">
                                            Batas: {{ \Carbon\Carbon::parse($inv->due_date)->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-xs text-[#526057]">
                            <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-semibold text-[#12271E]">Semua Pembayaran Tertib</p>
                            <p class="text-[11px] mt-0.5">Tidak ada jamaah yang melewati batas waktu pembayaran.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pendaftaran Terbaru --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-6 py-5 border-b border-[#E0E7DC] flex items-center justify-between bg-[#EFF3EB]/30">
                        <div>
                            <h2 class="text-base font-bold text-[#12271E]">
                                Pendaftaran Terbaru
                            </h2>
                            <p class="text-xs text-[#526057] mt-0.5">Daftar calon jamaah yang baru mendaftar.</p>
                        </div>
                        <a href="{{ route('admin.registrations.index') }}" class="text-xs font-bold text-[#1B3B2B] hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    @if($recentRegistrations->count() > 0)
                        <div class="divide-y divide-[#E0E7DC]">
                            @foreach($recentRegistrations as $reg)
                                <a href="{{ route('admin.registrations.show', $reg->id) }}" class="p-4 sm:px-6 hover:bg-[#EFF3EB]/30 transition-colors flex items-center justify-between gap-4 block group">
                                    <div class="truncate">
                                        <span class="font-bold text-[#12271E] block text-xs group-hover:text-[#1B3B2B] transition-colors truncate">
                                            {{ $reg->user->name ?? 'Jamaah' }}
                                        </span>
                                        <span class="text-[11px] text-[#526057] block truncate">
                                            {{ $reg->package->name ?? '-' }} &bull; {{ $reg->members->count() }} Anggota Keluarga
                                        </span>
                                    </div>
                                    <div class="text-right shrink-0 flex flex-col items-end gap-1">
                                        <x-status-badge :status="$reg->status" size="xs" />
                                        <span class="text-[10px] text-[#526057]">
                                            {{ $reg->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-xs text-[#526057]">
                            <svg class="w-8 h-8 text-[#526057]/40 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <p class="font-semibold text-[#12271E]">Belum Ada Pendaftaran Baru</p>
                            <p class="text-[11px] mt-0.5">Pendaftaran calon jamaah akan langsung muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($chartLabels);
        const regData = @json($registrationChartData);
        const payData = @json($paymentChartData);

        const formatRupiah = (val) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        };

        function initChart(id, config) {
            const el = document.getElementById(id);
            if (el) {
                return new Chart(el.getContext('2d'), config);
            }
            return null;
        }

        // Config for Registration Chart (Line Chart with smooth tension)
        const regChartConfig = {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pendaftaran',
                    data: regData,
                    borderColor: '#1B3B2B',
                    backgroundColor: 'rgba(27, 59, 43, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1B3B2B',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#12271E',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.parsed.y} Jamaah Terdaftar`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#526057',
                            font: { size: 11, weight: '500' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: '#526057',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(224, 231, 220, 0.6)'
                        }
                    }
                }
            }
        };

        // Config for Payment Chart (Bar Chart with rounded bars)
        const payChartConfig = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pemasukan Kas',
                    data: payData,
                    backgroundColor: '#C2A264',
                    hoverBackgroundColor: '#A88748',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#12271E',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ` Total: ${formatRupiah(ctx.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#526057',
                            font: { size: 11, weight: '500' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#526057',
                            font: { size: 11 },
                            callback: function(value) {
                                if (value >= 1000000000) return (value / 1000000000).toFixed(1) + 'M';
                                if (value >= 1000000) return (value / 1000000).toFixed(0) + 'Jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'Rb';
                                return value;
                            }
                        },
                        grid: {
                            color: 'rgba(224, 231, 220, 0.6)'
                        }
                    }
                }
            }
        };

        initChart('registrationChartDesktop', regChartConfig);
        initChart('paymentChartDesktop', payChartConfig);
        initChart('registrationChartMobile', JSON.parse(JSON.stringify(regChartConfig)));
        initChart('paymentChartMobile', JSON.parse(JSON.stringify(payChartConfig)));
    });
    </script>
    @endpush

</x-layouts.admin>
