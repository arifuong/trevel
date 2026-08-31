<x-layouts.jamaah :title="'Pengajuan Pembatalan — PT. Zein Internasional'">

    <section class="py-6 sm:py-10 min-h-[calc(100vh-140px)]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb / Header --}}
            <div class="mb-8" style="animation: fadeSlideUp 0.4s ease both">
                <a href="{{ route('jamaah.my-registration') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline mb-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>Kembali ke Pendaftaran Saya</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-red-950 tracking-tight">Pengajuan Pembatalan Pendaftaran</h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-1">Perhitungan biaya potongan dan estimasi pengembalian dana sesuai ketentuan resmi Zein Tour.</p>
            </div>

            {{-- Flash Errors --}}
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-800 shadow-xs">
                    <div class="font-bold mb-1">Mohon lengkapi formulir pembatalan:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Summary Card & Fee Breakdown --}}
            <div class="bg-white rounded-2xl border border-red-200/80 p-6 sm:p-7 shadow-xs mb-6 space-y-6" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                
                {{-- Package Info --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E0E7DC] gap-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block">Paket yang Dibatalkan</span>
                        <h2 class="text-lg font-bold text-[#12271E]">{{ $registration->package->name }}</h2>
                        <span class="text-xs text-[#526057]">Nomor Pendaftaran: {{ $registration->registration_number }} &bull; {{ $registration->members->count() }} Jamaah</span>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block">Jadwal Keberangkatan</span>
                        <span class="text-xs font-semibold text-[#12271E] block">
                            {{ $registration->package->departure_date ? \Carbon\Carbon::parse($registration->package->departure_date)->translatedFormat('d F Y') : 'Menyesuaikan' }}
                        </span>
                        @if($fee['days_to_departure'] !== null)
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fee['days_to_departure'] <= 6 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $fee['days_to_departure'] > 0 ? "H-{$fee['days_to_departure']} Keberangkatan" : 'Hari Keberangkatan' }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Fee Calculation Box --}}
                <div class="p-4 sm:p-5 rounded-xl {{ $fee['has_paid'] ? 'bg-amber-50/70 border border-amber-200' : 'bg-emerald-50/70 border border-emerald-200' }}">
                    <h3 class="text-xs font-bold uppercase tracking-wider {{ $fee['has_paid'] ? 'text-amber-950' : 'text-emerald-950' }} mb-3">
                        Rincian Perhitungan Biaya Pembatalan
                    </h3>

                    <div class="space-y-2.5 text-xs text-[#526057]">
                        <div class="flex justify-between">
                            <span>Status Pembayaran:</span>
                            <strong class="text-[#12271E]">{{ $fee['has_paid'] ? 'Sudah Membayar DP / Pelunasan' : 'Belum Membayar DP (Bebas Biaya)' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Kategori Pembatalan:</span>
                            <strong class="text-[#12271E]">{{ $fee['category'] }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Sudah Dibayar:</span>
                            <strong class="text-[#12271E]">{{ $fee['total_paid_formatted'] }}</strong>
                        </div>
                        <div class="flex justify-between text-red-700 pt-2 border-t border-zinc-200">
                            <span>Potongan Biaya Pembatalan:</span>
                            <strong class="font-bold">- {{ $fee['fee_formatted'] }}</strong>
                        </div>
                        <div class="flex justify-between text-sm sm:text-base font-bold text-[#1B3B2B] pt-2 border-t border-zinc-200">
                            <span>Estimasi Pengembalian Dana:</span>
                            <span class="text-emerald-800">{{ $fee['refund_formatted'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Policy Table Reference --}}
                <div class="space-y-2">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block">Ketentuan Biaya Pembatalan Resmi:</span>
                    <div class="overflow-x-auto rounded-xl border border-[#E0E7DC] text-[11px]">
                        <table class="w-full text-left">
                            <thead class="bg-[#EFF3EB] text-[#4D5E54] font-bold">
                                <tr>
                                    <th class="p-2.5">Kondisi Waktu Pembatalan</th>
                                    <th class="p-2.5 text-right">Persentase Potongan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E0E7DC] text-[#12271E]">
                                <tr class="{{ !$fee['has_paid'] ? 'bg-emerald-50 font-bold' : '' }}">
                                    <td class="p-2.5">Belum bayar DP sama sekali</td>
                                    <td class="p-2.5 text-right text-emerald-700">0% (Bebas Biaya)</td>
                                </tr>
                                <tr class="{{ $fee['has_paid'] && $fee['fee_percentage'] == 2 ? 'bg-amber-50 font-bold' : '' }}">
                                    <td class="p-2.5">Setelah menjadi pendaftar (> 25 hari sebelum berangkat)</td>
                                    <td class="p-2.5 text-right">2% dari harga paket</td>
                                </tr>
                                <tr class="{{ $fee['has_paid'] && $fee['fee_percentage'] == 25 ? 'bg-amber-50 font-bold' : '' }}">
                                    <td class="p-2.5">25 hari sebelum keberangkatan (H-25 s/d H-16)</td>
                                    <td class="p-2.5 text-right">25% dari harga paket</td>
                                </tr>
                                <tr class="{{ $fee['has_paid'] && $fee['fee_percentage'] == 65 ? 'bg-amber-50 font-bold' : '' }}">
                                    <td class="p-2.5">15 hari sebelum keberangkatan (H-15 s/d H-7)</td>
                                    <td class="p-2.5 text-right">65% dari harga paket</td>
                                </tr>
                                <tr class="{{ $fee['has_paid'] && $fee['fee_percentage'] == 85 ? 'bg-amber-50 font-bold' : '' }}">
                                    <td class="p-2.5">6 hari sebelum keberangkatan (H-6 s/d hari H)</td>
                                    <td class="p-2.5 text-right">85% dari harga paket</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Alert Penolakan Pembatalan Sebelumnya --}}
            @if(isset($latestCancellation) && $latestCancellation && $latestCancellation->isRejected())
                <div class="mb-6 p-5 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-2 shadow-2xs">
                    <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <span>Pengajuan Pembatalan Sebelumnya Ditolak</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-red-200 space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan dari Admin:</span>
                        <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $latestCancellation->rejection_reason }}</p>
                    </div>
                    <p class="text-red-800 font-medium">
                        Anda dapat mengajukan permohonan pembatalan kembali dengan alasan yang lebih lengkap di bawah ini.
                    </p>
                </div>
            @endif

            @if(isset($latestCancellation) && $latestCancellation && $latestCancellation->isPending())
                {{-- State Sedang Menunggu Validasi Admin --}}
                <div class="bg-white rounded-2xl border border-amber-300 p-6 sm:p-8 shadow-xs space-y-4 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-[#12271E]">Pengajuan Pembatalan Sedang Diproses</h3>
                        <p class="text-xs sm:text-sm text-[#526057] mt-1 max-w-md mx-auto">
                            Anda telah mengajukan permohonan pembatalan pendaftaran ini pada {{ $latestCancellation->requested_at ? $latestCancellation->requested_at->translatedFormat('d F Y, H:i') : '' }} WIB.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F8FAF7] border border-[#E0E7DC] text-left max-w-lg mx-auto space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block">Alasan Pembatalan:</span>
                        <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $latestCancellation->reason }}</p>
                    </div>

                    <div class="pt-3">
                        <a href="{{ route('jamaah.my-registration') }}" 
                           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors">
                            <span>Kembali ke Status Pendaftaran</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>
            @elseif($registration->hasApprovedCancellation() || $registration->status === 'dibatalkan')
                @php
                    $approvedPaymentsTotal = (float) $registration->payments->where('status', 'disetujui')->sum('amount');
                    $invoicePaidTotal = (float) ($registration->invoice->total_paid ?? 0);
                    $hasApprovedPayments = $approvedPaymentsTotal > 0 || $invoicePaidTotal > 0;
                    
                    $adminWa = config('services.whatsapp.admin_number', '6281222222562');
                    $waRefundText = "Halo Admin, saya ingin mengajukan proses pengembalian dana karena pendaftaran saya telah dibatalkan.\n" .
                                    "Nama: " . (auth()->user()->name ?? 'Jamaah') . "\n" .
                                    "Paket: " . ($registration->package->name ?? 'Paket Umrah') . "\n" .
                                    "Terima kasih.";
                    $waRefundUrl = "https://wa.me/{$adminWa}?text=" . rawurlencode($waRefundText);
                @endphp

                <div class="bg-white rounded-2xl border border-emerald-300 p-6 sm:p-8 shadow-xs space-y-5 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-[#12271E]">Pembatalan Disetujui</h3>
                        <p class="text-xs sm:text-sm text-[#526057] mt-1 max-w-md mx-auto">
                            Pendaftaran Anda telah dibatalkan.
                        </p>
                    </div>

                    @if($hasApprovedPayments)
                        <div class="p-5 rounded-2xl bg-[#EFF3EB]/80 border border-emerald-200 text-left max-w-lg mx-auto space-y-3">
                            <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm border-b border-emerald-200/60 pb-2">
                                <span>💰 Pengembalian Dana</span>
                            </div>
                            <p class="text-xs text-[#12271E] font-medium leading-relaxed">
                                Pembayaran Anda telah tercatat sebesar <strong class="text-emerald-700 font-bold">Rp {{ number_format(max($approvedPaymentsTotal, $invoicePaidTotal), 0, ',', '.') }}</strong>.
                            </p>
                            <p class="text-xs text-[#526057] leading-relaxed">
                                Karena Anda sudah melakukan pembayaran, untuk proses pengembalian dana silakan hubungi Admin melalui WhatsApp.
                            </p>
                            <div class="pt-2">
                                <a href="{{ $waRefundUrl }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#25D366] hover:bg-[#20ba5a] active:scale-[0.98] transition-all shadow-xs">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                                    <span>Hubungi Admin via WhatsApp</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('jamaah.my-registration') }}" 
                           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors">
                            <span>Kembali ke Status Pendaftaran</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>
            @else
                {{-- Form Pembatalan --}}
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs">
                    <form method="POST" action="{{ route('jamaah.registration.cancel.process') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="cancellation_reason" class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                                Alasan Pembatalan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="cancellation_reason" name="cancellation_reason" rows="4" required minlength="5" maxlength="1000"
                                      class="w-full p-3 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 placeholder-[#526057]/50"
                                      placeholder="Mohon sampaikan alasan pembatalan pendaftaran Anda... (minimal 5 karakter)">{{ old('cancellation_reason') }}</textarea>
                        </div>

                        {{-- Persetujuan Syarat --}}
                        <div class="p-3.5 rounded-xl bg-[#EFF3EB] border border-[#E0E7DC]">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="agree_terms" required value="1"
                                       class="mt-0.5 rounded border-[#E0E7DC] text-red-700 focus:ring-red-500">
                                <span class="text-xs text-[#12271E] leading-relaxed">
                                    Saya memahami bahwa pengajuan pembatalan ini akan diverifikasi oleh Admin. Setelah disetujui, pendaftaran dibatalkan permanen dan estimasi pengembalian dana (jika ada) akan ditransfer manual ke rekening saya.
                                </span>
                            </label>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                            <a href="{{ route('jamaah.my-registration') }}" 
                               class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-semibold text-[#526057] hover:text-[#12271E] text-center">
                                Batal & Kembali
                            </a>

                            <button type="submit" 
                                    onclick="return confirm('Apakah Anda yakin ingin mengajukan pembatalan pendaftaran ibadah ini?')"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 active:scale-[0.98] transition-all shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Kirim Pengajuan Pembatalan</span>
                            </button>
                        </div>

                    </form>
                </div>
            @endif

        </div>
    </section>

</x-layouts.jamaah>
