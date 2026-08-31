<x-layouts.admin :title="'Beranda Operasional — Admin PT. Zein Internasional'">

    <div class="space-y-6 sm:space-y-8">

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
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5">
                
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider">Total Jamaah</span>
                        <div class="w-7 h-7 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl sm:text-3xl font-bold text-[#12271E] block">{{ $totalJamaah }}</span>
                    <span class="text-[11px] text-[#526057] mt-1 block">Akun Terverifikasi</span>
                </div>

                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider">Total Pendaftaran</span>
                        <div class="w-7 h-7 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                        </div>
                    </div>
                    <span class="text-2xl sm:text-3xl font-bold text-[#12271E] block">{{ $totalPendaftaran }}</span>
                    <span class="text-[11px] text-[#526057] mt-1 block">{{ $totalPaketAktif }} Paket Tersedia</span>
                </div>

                <div class="bg-white rounded-2xl border border-emerald-200/80 p-4 sm:p-5 shadow-2xs bg-gradient-to-b from-emerald-50/30 to-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Dana Masuk</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                            Rp
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-bold text-[#12271E] block truncate">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-emerald-700 mt-1 block font-medium">Sudah diverifikasi</span>
                </div>

                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] sm:text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider">Sisa Pembayaran</span>
                        <div class="w-7 h-7 rounded-lg bg-[#EFF3EB] text-[#526057] flex items-center justify-center font-bold text-xs">
                            Rp
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-bold text-[#12271E] block truncate">
                        Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-[#526057] mt-1 block">Menunggu pelunasan</span>
                </div>

            </div>
        </div>

        {{-- 4. Tabel Ringkasan Pendaftar per Paket Umrah --}}
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
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Penuh / Habis</span>
                                        @elseif($pkg->status === 'aktif')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-600">{{ ucfirst($pkg->status) }}</span>
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

        {{-- 5. Tabel Peringatan Batas Pembayaran & Pendaftaran Terbaru --}}
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
                                    <div class="text-right shrink-0">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#EFF3EB] text-[#1B3B2B] border border-[#E0E7DC]">
                                            {{ $reg->status_label }}
                                        </span>
                                        <span class="text-[10px] text-[#526057] block mt-1">
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

</x-layouts.admin>
