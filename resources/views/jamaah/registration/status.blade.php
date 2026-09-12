<x-layouts.jamaah :title="'Pendaftaran Saya — PT. Zein Internasional'">

    <section class="py-6 sm:py-10 min-h-[calc(100vh-140px)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

            {{-- Header Halaman --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-[#E0E7DC]/60" style="animation: fadeSlideUp 0.4s ease both">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">Pendaftaran Saya</h1>
                    <p class="text-xs sm:text-sm text-[#526057] mt-1">Pantau perkembangan tahapan persiapan perjalanan ibadah Umrah & Haji Anda</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('jamaah.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-[#12271E] bg-white border border-[#E0E7DC] hover:border-[#1B3B2B] hover:bg-[#EFF3EB]/50 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

            {{-- Pesan Notifikasi --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs sm:text-sm text-amber-800 flex items-start gap-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <div class="leading-relaxed">{{ session('warning') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-800 flex items-start gap-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c-.866 1.5-.217 3.374-1.948 3.374H4.236c-1.73 0-2.813-1.874-1.948-3.374L10.051 3.378c.866-1.5 3.032-1.5 3.898 0l8.354 14.748zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <div class="leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            @if($registration)

                @php
                    $currentStep = $registration->step_number;
                    $isCancelled = $registration->status === 'dibatalkan';

                    $steps = [
                        1 => ['title' => 'Verifikasi Berkas', 'desc' => 'Pemeriksaan KTP, KK, Paspor & Dokumen'],
                        2 => ['title' => 'Pembayaran DP', 'desc' => 'Batas waktu 7 hari sejak disetujui'],
                        3 => ['title' => 'Verifikasi Pembayaran', 'desc' => 'Pengecekan bukti setoran oleh tim keuangan'],
                        4 => ['title' => 'Calon Jamaah Resmi', 'desc' => 'Tercatat dalam manifes keberangkatan'],
                        5 => ['title' => 'Pelunasan Bertahap', 'desc' => 'Setor tabungan umrah sesuai kemampuan'],
                        6 => ['title' => 'Lunas', 'desc' => 'Seluruh biaya perjalanan telah selesai'],
                        7 => ['title' => 'Kelengkapan Dokumen Keberangkatan', 'desc' => 'Unggah Visa Umrah, Vaksin Meningitis & Foto Visa'],
                        8 => ['title' => 'Siap Berangkat', 'desc' => 'Manasik final dan penerbangan ke Tanah Suci'],
                        9 => ['title' => 'Selesai', 'desc' => 'Rangkaian ibadah tuntas & kepulangan mabrur'],
                    ];
                @endphp

                {{-- Status Alert Banner --}}
                <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs overflow-hidden relative" style="animation: fadeSlideUp 0.4s ease 0.1s both">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm {{ $isCancelled ? 'bg-red-100 text-red-700' : 'bg-[#EFF3EB] text-[#1B3B2B] border border-[#CCD8C7]' }}">
                                @if($isCancelled)
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block">Status Pendaftaran Terkini</span>
                                <h2 class="text-xl sm:text-2xl font-bold tracking-tight mt-0.5 {{ $isCancelled ? 'text-red-700' : 'text-[#12271E]' }}">
                                    {{ $registration->status_label }}
                                </h2>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                            <span class="text-xs text-[#526057] font-medium hidden sm:inline">No. Registrasi:</span>
                            <span class="font-mono font-bold text-xs bg-[#EFF3EB] text-[#1B3B2B] px-3 py-1.5 rounded-xl border border-[#CCD8C7]">
                                {{ $registration->registration_number }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Banner Status Pengajuan Pembatalan (Jika Ada) --}}
                @php
                    $latestCancel = $registration->latestCancellation;
                @endphp

                @if($registration->hasPendingCancellation() || ($latestCancel && $latestCancel->isPending()))
                    <div class="p-5 sm:p-6 rounded-3xl bg-amber-50/90 border border-amber-200 text-xs text-amber-950 space-y-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.12s both">
                        <div class="flex items-center gap-2 text-amber-800 font-bold text-sm">
                            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>⏳ Pengajuan Pembatalan Sedang Menunggu Validasi Admin</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white border border-amber-200 space-y-1.5 shadow-2xs">
                            <div class="flex justify-between items-center text-[10px] text-[#526057]">
                                <span class="font-bold uppercase tracking-wider text-amber-800">Alasan Pembatalan yang Diajukan:</span>
                                <span>{{ $latestCancel?->requested_at ? $latestCancel->requested_at->translatedFormat('d F Y, H:i') : '' }} WIB</span>
                            </div>
                            <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $latestCancel?->reason }}</p>
                        </div>
                        <p class="text-amber-900 leading-relaxed font-medium">
                            Tim administrasi PT. Zein Internasional sedang meninjau permohonan pembatalan Anda. Status pendaftaran akan diperbarui setelah validasi selesai.
                        </p>
                    </div>
                @elseif($registration->hasApprovedCancellation() || $registration->status === 'dibatalkan')
                    @php
                        $approvedPaymentsTotal = (float) $registration->payments->where('status', 'disetujui')->sum('amount');
                        $invoicePaidTotal = (float) ($registration->invoice->total_paid ?? 0);
                        $hasApprovedPayments = $approvedPaymentsTotal > 0 || $invoicePaidTotal > 0;
                        $hasPendingPayments = $registration->payments->where('status', 'menunggu_verifikasi')->isNotEmpty();
                        
                        $adminWa = config('services.whatsapp.admin_number', '6281222222562');
                        $waRefundText = "Halo Admin, saya ingin mengajukan proses pengembalian dana karena pendaftaran saya telah dibatalkan.\n" .
                                        "Nama: " . ($user->name ?? 'Jamaah') . "\n" .
                                        "Paket: " . ($registration->package->name ?? 'Paket Umrah') . "\n" .
                                        "Terima kasih.";
                        $waRefundUrl = "https://wa.me/{$adminWa}?text=" . rawurlencode($waRefundText);
                    @endphp

                    <div class="p-5 sm:p-6 rounded-3xl bg-emerald-50/90 border border-emerald-200 text-xs text-emerald-950 space-y-4 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.12s both">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>✓ Pembatalan Disetujui</span>
                        </div>
                        
                        <p class="text-emerald-900 leading-relaxed font-medium">
                            Pendaftaran Anda telah dibatalkan.
                        </p>

                        @if($hasApprovedPayments)
                            {{-- Section Pengembalian Dana --}}
                            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-emerald-200/90 space-y-3.5 shadow-2xs">
                                <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm border-b border-emerald-100 pb-2">
                                    <span class="text-base">💰</span>
                                    <span>Pengembalian Dana</span>
                                </div>

                                <div class="space-y-1.5 text-xs text-[#526057]">
                                    <p class="font-medium text-[#12271E]">
                                        Pembayaran Anda telah tercatat sebesar <strong class="text-emerald-700 font-bold">Rp {{ number_format(max($approvedPaymentsTotal, $invoicePaidTotal), 0, ',', '.') }}</strong>.
                                    </p>
                                    <p class="leading-relaxed">
                                        Karena Anda sudah melakukan pembayaran, untuk proses pengembalian dana silakan hubungi Admin melalui WhatsApp.
                                    </p>
                                </div>

                                @if($latestCancel && ($latestCancel->fee_amount > 0 || $latestCancel->refund_amount > 0))
                                    <div class="p-3 rounded-xl bg-[#EFF3EB]/60 border border-[#CCD8C7] space-y-1 text-[11px]">
                                        <div class="flex justify-between">
                                            <span>Kategori:</span>
                                            <strong class="text-[#12271E]">{{ $latestCancel->category }}</strong>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Potongan Biaya Batal:</span>
                                            <strong class="text-red-700">- {{ $latestCancel->fee_amount_formatted }}</strong>
                                        </div>
                                        <div class="flex justify-between text-xs font-bold pt-1 border-t border-[#CCD8C7]">
                                            <span class="text-[#1B3B2B]">Estimasi Pengembalian:</span>
                                            <span class="text-emerald-800">{{ $latestCancel->refund_amount_formatted }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Tombol WhatsApp Refund --}}
                                <div class="pt-1">
                                    <a href="{{ $waRefundUrl }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#25D366] hover:bg-[#20ba5a] active:scale-[0.98] transition-all shadow-xs">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                                        <span>Hubungi Admin via WhatsApp</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($hasPendingPayments)
                            <div class="p-3.5 rounded-2xl bg-white border border-amber-200 space-y-1 shadow-2xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">Status Pembayaran:</span>
                                <p class="text-xs text-[#12271E] font-medium leading-relaxed">
                                    Bukti pembayaran Anda sebelumnya sedang dalam proses peninjauan oleh tim admin. Silakan hubungi Admin jika memerlukan informasi lebih lanjut.
                                </p>
                            </div>
                        @endif
                    </div>
                @elseif($registration->hasRejectedCancellation() || ($latestCancel && $latestCancel->isRejected()))
                    <div class="p-5 sm:p-6 rounded-3xl bg-red-50/90 border border-red-200 text-xs text-red-900 space-y-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.12s both">
                        <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            <span>⚠ Pembatalan Ditolak</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white border border-red-200 space-y-1 shadow-2xs">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan dari Admin:</span>
                            <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $latestCancel?->rejection_reason }}</p>
                            @if($latestCancel?->rejected_at)
                                <span class="text-[10px] text-red-600/80 block mt-0.5">{{ $latestCancel->rejected_at->translatedFormat('d F Y, H:i') }} WIB</span>
                            @endif
                        </div>
                        <p class="text-red-800 font-medium leading-relaxed">
                            Pengajuan pembatalan Anda tidak dapat diproses. Pendaftaran dan jadwal ibadah Anda tetap aktif.
                        </p>
                    </div>
                @endif

                {{-- Banner Selebrasi Selesai Ibadah (Tahap 9) --}}
                @if($registration->status === 'selesai' || $currentStep === 9)
                    <div class="bg-gradient-to-r from-[#0F261B] via-[#1B3B2B] to-[#12271E] rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden border border-emerald-800/40" style="animation: fadeSlideUp 0.4s ease 0.12s both">
                        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -left-12 -top-12 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div class="space-y-3 max-w-2xl">
                                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                    <span>🎉 Alhamdulillah &bull; Perjalanan Ibadah Telah Selesai</span>
                                </div>
                                <h3 class="text-2xl sm:text-3xl font-serif font-bold tracking-tight text-white">
                                    Semoga Menjadi Ibadah yang Mabrur
                                </h3>
                                <p class="text-emerald-100/90 text-xs sm:text-sm leading-relaxed font-light">
                                    Keluarga besar <strong>PT. Zein Internasional</strong> mengucapkan terima kasih yang sebesar-besarnya atas kepercayaan Bapak/Ibu <strong>{{ $user->name }}</strong> dan keluarga telah beribadah ke Tanah Suci bersama kami. Semoga seluruh amal ibadah, tawaf, sa'i, dan doa yang dipanjatkan diijabah oleh Allah SWT serta membawa keberkahan abadi bagi keluarga.
                                </p>
                                <div class="flex flex-wrap items-center gap-3 pt-1 text-xs text-emerald-200/80">
                                    <span class="flex items-center gap-1.5 font-medium">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Status: Selesai & Terarsip
                                    </span>
                                    <span>&bull;</span>
                                    <span>Paket: {{ $registration->package->name }}</span>
                                </div>
                            </div>
                            <div class="shrink-0 flex flex-wrap items-center justify-center sm:justify-end gap-2 w-full md:w-auto">
                                <a href="{{ route('documents.invoice.pdf', $registration) }}" 
                                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-md active:scale-98">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    <span>Unduh PDF Invoice</span>
                                </a>
                                <a href="{{ route('documents.invoice', $registration) }}" target="_blank"
                                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold text-[#12271E] bg-white hover:bg-emerald-50 transition-all shadow-md active:scale-98">
                                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    <span>Lihat Invoice</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Indikator Tahapan Perjalanan --}}
                @if(!$isCancelled)
                    <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs" style="animation: fadeSlideUp 0.4s ease 0.15s both">
                        <div class="flex items-center justify-between mb-6 pb-3 border-b border-[#E0E7DC]/70">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#4D5E54]">Tahapan Persiapan Ibadah Anda</span>
                            <span class="text-xs font-semibold text-[#1B3B2B]">Langkah {{ $currentStep }} dari {{ \App\Models\Registration::TOTAL_STEPS }}</span>
                        </div>
                        
                        {{-- Desktop Horizontal Stepper --}}
                        <div class="hidden lg:grid grid-cols-9 gap-2 relative">
                            @foreach($steps as $stepIdx => $stepInfo)
                                @php
                                    $isPassed = $currentStep > $stepIdx;
                                    $isCurrent = $currentStep === $stepIdx;
                                    $isUpcoming = $currentStep < $stepIdx;
                                    $isDone = $isPassed || ($stepIdx === 9 && $currentStep === 9);
                                @endphp
                                <div class="flex flex-col items-center text-center relative z-10 min-w-0">
                                    {{-- Lingkaran Angka / Checkmark --}}
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all mb-2.5 shrink-0
                                                {{ $isDone ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-200' : ($isCurrent ? 'bg-[#1B3B2B] text-white ring-4 ring-[#1B3B2B]/20 shadow-md' : 'bg-[#EFF3EB] text-[#526057] border border-[#CCD8C7]') }}">
                                        @if($isDone)
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @else
                                            {{ $stepIdx }}
                                        @endif
                                    </div>
                                    <span class="text-[11px] font-bold leading-tight line-clamp-2 px-1 {{ $isDone ? 'text-emerald-800' : ($isCurrent ? 'text-[#1B3B2B]' : 'text-[#526057]/70') }}">
                                        {{ $stepInfo['title'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Mobile Vertical Stepper --}}
                        <div class="lg:hidden space-y-3.5 pt-1">
                            @foreach($steps as $stepIdx => $stepInfo)
                                @php
                                    $isPassed = $currentStep > $stepIdx;
                                    $isCurrent = $currentStep === $stepIdx;
                                    $isUpcoming = $currentStep < $stepIdx;
                                    $isDone = $isPassed || ($stepIdx === 9 && $currentStep === 9);
                                @endphp
                                <div class="flex items-start gap-3.5 p-2 rounded-xl {{ $isCurrent && !$isDone ? 'bg-[#EFF3EB]/50 border border-[#CCD8C7]' : ($isDone && $stepIdx === 9 ? 'bg-emerald-50/70 border border-emerald-200' : '') }}">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5
                                                {{ $isDone ? 'bg-emerald-600 text-white' : ($isCurrent ? 'bg-[#1B3B2B] text-white ring-2 ring-[#1B3B2B]/20' : 'bg-[#EFF3EB] text-[#526057]') }}">
                                        @if($isDone)
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @else
                                            {{ $stepIdx }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold {{ $isDone ? 'text-emerald-800' : ($isCurrent ? 'text-[#1B3B2B]' : 'text-[#526057]') }}">
                                            {{ $stepInfo['title'] }}
                                        </p>
                                        <p class="text-[11px] text-[#526057] mt-0.5 leading-snug">{{ $stepInfo['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif

                {{-- Detail 2 Kolom (Proporsional 8 : 4) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start" style="animation: fadeSlideUp 0.4s ease 0.2s both">
                    
                    {{-- Kolom Kiri: Detail Paket & Anggota Keluarga (8 Kolom) --}}
                    <div class="lg:col-span-8 space-y-6 min-w-0">

                        {{-- Paket Dipilih --}}
                        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs">
                            <h3 class="text-base font-bold text-[#12271E] mb-4 pb-3 border-b border-[#E0E7DC] flex items-center justify-between">
                                <span>Paket yang Didaftarkan</span>
                                <div class="flex items-center gap-2">
                                    @if($registration->packageVariant)
                                        <span class="text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] px-2.5 py-0.5 rounded-full border border-[#CCD8C7]">
                                            Varian: {{ $registration->packageVariant->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs font-semibold text-emerald-800 bg-emerald-100/70 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        {{ $registration->package->status === 'aktif' ? 'Program Aktif' : 'Terjadwal' }}
                                    </span>
                                </div>
                            </h3>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <h4 class="text-lg font-bold text-[#1B3B2B] tracking-tight">
                                        {{ $registration->package->name }}
                                        @if($registration->packageVariant)
                                            <span class="text-[#526057] font-normal text-base">— {{ $registration->packageVariant->name }}</span>
                                        @endif
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-[#526057] mt-2">
                                        <span class="inline-flex items-center gap-1 bg-[#EFF3EB] px-2.5 py-1 rounded-lg font-semibold text-[#12271E] border border-[#CCD8C7]">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ $registration->package->duration }} Hari</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 bg-[#EFF3EB] px-2.5 py-1 rounded-lg font-semibold text-[#12271E] border border-[#CCD8C7]">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                                            <span>{{ $registration->package->departure_date ? $registration->package->departure_date->translatedFormat('d F Y') : 'Jadwal Menyesuaikan' }}</span>
                                        </span>
                                        @if($registration->room_type)
                                            <span class="inline-flex items-center gap-1 bg-[#EFF3EB] px-2.5 py-1 rounded-lg font-semibold text-[#12271E] border border-[#CCD8C7]">
                                                <span>Kamar: {{ ucfirst($registration->room_type) }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-left sm:text-right shrink-0 bg-[#EFF3EB]/50 sm:bg-transparent p-3 sm:p-0 rounded-xl border sm:border-0 border-[#CCD8C7]">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block">Harga per Orang</span>
                                    <span class="text-lg font-bold text-[#1B3B2B] block mt-0.5">
                                        @if($registration->invoice && $registration->members->count() > 0)
                                            Rp {{ number_format((float) ($registration->invoice->total_price / $registration->members->count()), 0, ',', '.') }}
                                        @else
                                            {{ $registration->package->price_formatted }}
                                        @endif
                                    </span>
                                </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════════════════════════ --}}
                        {{-- TAHAP 7: KELENGKAPAN DOKUMEN KEBERANGKATAN (VISA, VAKSIN, FOTO VISA)        --}}
                        {{-- ══════════════════════════════════════════════════════════════════════════ --}}
                        @if($currentStep >= 6 && isset($departureSummary))
                            <div class="bg-white rounded-3xl border {{ $currentStep === 7 ? 'border-amber-300 ring-4 ring-amber-100/60' : 'border-[#E0E7DC]' }} p-6 sm:p-7 shadow-xs space-y-6" id="departure-documents-section">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E0E7DC]">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $currentStep === 7 ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($currentStep > 7 ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-[#EFF3EB] text-[#4D5E54]') }}">
                                                Tahap 7 &bull; Persiapan Keberangkatan
                                            </span>
                                            @if($departureSummary['is_all_valid'])
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    <span>Seluruh Dokumen Lengkap & Valid</span>
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-lg font-bold text-[#12271E] flex items-center gap-2">
                                            <span>Kelengkapan Dokumen Keberangkatan</span>
                                        </h3>
                                        <p class="text-xs text-[#526057]">
                                            Unggah Visa Umrah, Sertifikat Vaksin Meningitis, dan Pas Foto Visa untuk setiap anggota keluarga sebelum penerbangan.
                                        </p>
                                    </div>

                                    {{-- Ringkasan Progress Dokumen Keberangkatan --}}
                                    <div class="flex items-center gap-2 shrink-0 bg-[#F8FAF7] p-2.5 rounded-2xl border border-[#E0E7DC]">
                                        <div class="text-center px-3 py-1">
                                            <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Valid</span>
                                            <span class="text-sm font-bold text-emerald-700">{{ $departureSummary['total_valid'] }} / {{ $departureSummary['total_required'] }}</span>
                                        </div>
                                        @if($departureSummary['total_pending'] > 0)
                                            <div class="text-center px-3 py-1 border-l border-[#E0E7DC]">
                                                <span class="text-[10px] uppercase font-bold text-amber-700 block">Menunggu</span>
                                                <span class="text-sm font-bold text-amber-800">{{ $departureSummary['total_pending'] }}</span>
                                            </div>
                                        @endif
                                        @if($departureSummary['total_missing'] > 0)
                                            <div class="text-center px-3 py-1 border-l border-[#E0E7DC]">
                                                <span class="text-[10px] uppercase font-bold text-red-700 block">Belum</span>
                                                <span class="text-sm font-bold text-red-700">{{ $departureSummary['total_missing'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Info Box Ketentuan --}}
                                @if($currentStep === 7)
                                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950 space-y-1.5 leading-relaxed">
                                        <div class="font-bold flex items-center gap-1.5 text-amber-900">
                                            <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                            <span>Ketentuan Dokumen Keberangkatan (Pra-Keberangkatan):</span>
                                        </div>
                                        <ul class="list-disc list-inside space-y-1 text-[#4D5E54] pl-1">
                                            <li><strong>Visa Umrah:</strong> File visa elektronik (e-Visa) resmi Kerajaan Arab Saudi dalam format PDF atau Gambar (maks 10 MB).</li>
                                            <li><strong>Sertifikat Vaksin Meningitis:</strong> Buku kuning/sertifikat vaksin meningitis resmi dari faskes terakreditasi Kemenkes.</li>
                                            <li><strong>Foto Visa:</strong> Pas foto khusus visa ukuran 4x6 latar belakang <em>putih/biru</em>, fokus wajah 80%, pakaian kontras, tanpa kacamata.</li>
                                        </ul>
                                    </div>
                                @endif

                                {{-- Daftar Jamaah & Checklist Dokumen --}}
                                <div class="space-y-6">
                                    @foreach($departureSummary['members_data'] as $mIndex => $item)
                                        @php
                                            $mem = $item['member'];
                                            $docs = $item['docs'];
                                        @endphp
                                        <div class="p-5 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] space-y-4" x-data="{ expanded: true }">
                                            
                                            {{-- Header Anggota --}}
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#E0E7DC]">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <span class="w-7 h-7 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                                        {{ $mIndex + 1 }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        <h4 class="text-sm font-bold text-[#12271E] truncate">{{ $mem->name }}</h4>
                                                        <span class="text-xs text-[#526057] block truncate">
                                                            {{ $mem->relationship_label }} &bull; NIK: <span class="font-mono font-medium text-[#12271E]">{{ $mem->nik }}</span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <button type="button" @click="expanded = !expanded" class="text-xs font-semibold text-[#1B3B2B] hover:underline flex items-center gap-1 self-start sm:self-auto cursor-pointer">
                                                    <span x-text="expanded ? 'Sembunyikan Berkas' : 'Tampilkan Berkas'"></span>
                                                    <svg class="w-3.5 h-3.5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                                </button>
                                            </div>

                                            {{-- Grid 3 Dokumen Keberangkatan --}}
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="expanded" x-collapse>
                                                @foreach($docs as $docItem)
                                                    @php
                                                        $docType = $docItem['doc_type'];
                                                        $doc = $docItem['document'];
                                                        $status = $docItem['status'];
                                                    @endphp
                                                    <div class="bg-white rounded-2xl border p-4 flex flex-col justify-between space-y-3.5 transition-all shadow-2xs {{ $status === 'valid' ? 'border-emerald-200 ring-1 ring-emerald-100' : ($status === 'ditolak' ? 'border-red-200 bg-red-50/20' : ($status === 'menunggu_verifikasi' ? 'border-amber-200 bg-amber-50/20' : 'border-[#E0E7DC]')) }}">
                                                        
                                                        {{-- Header Dokumen --}}
                                                        <div class="space-y-1.5">
                                                            <div class="flex items-start justify-between gap-2">
                                                                <h5 class="text-xs font-bold text-[#12271E] leading-snug">
                                                                    {{ $docType->name }}
                                                                </h5>
                                                                @if($docType->is_required)
                                                                    <span class="text-[10px] font-bold text-red-500 shrink-0">*Wajib</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[11px] text-[#526057] leading-relaxed line-clamp-2">
                                                                {{ $docType->description }}
                                                            </p>
                                                        </div>

                                                        {{-- Status Badge --}}
                                                        <div>
                                                            @if($status === 'valid')
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                    <span>Terverifikasi Valid</span>
                                                                </span>
                                                            @elseif($status === 'ditolak')
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    <span>Perlu Perbaikan (Ditolak)</span>
                                                                </span>
                                                            @elseif($status === 'menunggu_verifikasi')
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                    <span>Menunggu Verifikasi Admin</span>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-gray-100 text-[#526057] border border-gray-200">
                                                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                                    <span>Belum Diunggah</span>
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Alasan Penolakan Jika Ditolak --}}
                                                        @if($status === 'ditolak' && $doc?->rejection_reason)
                                                            <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-1">
                                                                <div class="flex items-center gap-1.5 font-bold text-red-800">
                                                                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z"/></svg>
                                                                    <span>Catatan Penolakan dari Admin:</span>
                                                                </div>
                                                                <p class="leading-relaxed pl-5 font-medium">{{ $doc->rejection_reason }}</p>
                                                            </div>
                                                        @endif

                                                        {{-- Tombol Aksi / Form Unggah Langsung --}}
                                                        <div class="pt-2 border-t border-[#E0E7DC]/70 space-y-2.5">
                                                            @if($doc && $doc->file_url)
                                                                <a href="{{ $doc->file_url }}" target="_blank"
                                                                   class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-all">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                    <span>{{ $status === 'ditolak' ? 'Lihat Berkas Sebelumnya' : 'Lihat Berkas' }}</span>
                                                                </a>
                                                            @endif

                                                            {{-- KONDISI FORM UPLOAD: HANYA tampil jika belum diunggah ATAU status = 'ditolak', dan pendaftaran BELUM selesai --}}
                                                            @if((!$doc || $status === 'ditolak') && $registration->status !== \App\Models\Registration::STATUS_SELESAI)
                                                                <div class="space-y-2">
                                                                    @if($status === 'ditolak')
                                                                        <div class="flex items-center gap-1 text-[11px] font-bold text-red-700">
                                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                                            <span>Formulir Unggah Ulang Dokumen:</span>
                                                                        </div>
                                                                    @endif

                                                                    <form method="POST" action="{{ route('jamaah.members.departure-documents.upload', [$mem, $docType]) }}" enctype="multipart/form-data" class="space-y-2">
                                                                        @csrf
                                                                        <div class="space-y-1">
                                                                            <input type="file" name="file" required
                                                                                   accept="{{ $docType->code === 'FOTO_VISA' ? '.jpg,.jpeg,.png,.webp' : '.jpg,.jpeg,.png,.webp,.pdf' }}"
                                                                                   class="block w-full text-[11px] text-[#526057] file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold {{ $status === 'ditolak' ? 'file:bg-red-100 file:text-red-800 hover:file:bg-red-200 border-red-300' : 'file:bg-[#EFF3EB] file:text-[#1B3B2B] hover:file:bg-[#E0E7DC] border-[#CCD8C7]' }} cursor-pointer border rounded-xl p-1 bg-white">
                                                                            <span class="text-[10px] text-[#526057]/80 block">
                                                                                {{ $docType->code === 'FOTO_VISA' ? 'Format JPG/PNG, maks 10MB' : 'Format PDF/JPG/PNG, maks 10MB' }}
                                                                            </span>
                                                                        </div>
                                                                        <button type="submit"
                                                                                class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-bold text-white {{ $status === 'ditolak' ? 'bg-red-700 hover:bg-red-800' : 'bg-emerald-700 hover:bg-emerald-800' }} transition-all cursor-pointer shadow-2xs">
                                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                                            <span>{{ $status === 'ditolak' ? 'Unggah Ulang Dokumen' : 'Unggah ' . $docType->name }}</span>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @elseif($registration->status === \App\Models\Registration::STATUS_SELESAI && (!$doc || $status === 'ditolak'))
                                                                <div class="p-2 rounded-xl bg-gray-50 border border-gray-200 text-[11px] text-[#526057] text-center">
                                                                    Dokumen diarsipkan.
                                                                </div>
                                                            @elseif($status === 'menunggu_verifikasi')
                                                                <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 flex items-start gap-1.5">
                                                                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                    <span class="leading-relaxed">Dokumen telah dikirim dan sedang dalam proses peninjauan oleh tim admin.</span>
                                                                </div>
                                                            @elseif($status === 'valid')
                                                                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-800 flex items-center gap-1.5">
                                                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                    <span class="font-medium">Dokumen telah diverifikasi & valid.</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Anggota Keluarga & Status Dokumen --}}
                        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs space-y-5">
                            <div class="flex items-center justify-between pb-3 border-b border-[#E0E7DC]">
                                <div>
                                    <h3 class="text-base font-bold text-[#12271E]">
                                        Daftar Jamaah & Status Dokumen
                                    </h3>
                                    <p class="text-xs text-[#526057] mt-0.5">Verifikasi berkas persyaratan untuk setiap calon jamaah</p>
                                </div>
                                <span class="text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] px-3 py-1 rounded-full border border-[#CCD8C7] shrink-0">
                                    {{ $registration->members->count() }} Orang
                                </span>
                            </div>

                            <div class="space-y-5">
                                @foreach($registration->members as $idx => $member)
                                    <div class="p-5 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] space-y-4 shadow-2xs">
                                        
                                        {{-- Header Member --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#E0E7DC]">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span class="w-7 h-7 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <div class="min-w-0">
                                                    <h5 class="text-sm font-bold text-[#12271E] truncate">{{ $member->name }}</h5>
                                                    <span class="text-xs text-[#526057] block truncate mt-0.5">
                                                        {{ $member->relationship_label }} &bull; NIK: <strong class="font-mono text-[#12271E]">{{ $member->nik }}</strong>
                                                        @if($member->birth_place && $member->birth_date)
                                                            &bull; {{ $member->birth_place }}, {{ $member->birth_date->translatedFormat('d M Y') }}
                                                        @endif
                                                        @if($member->gender)
                                                            &bull; {{ $member->gender_label }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Badge Status Dokumen --}}
                                            <div class="shrink-0 self-start sm:self-auto">
                                                @if($member->document_status === 'disetujui')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        <span>Terverifikasi</span>
                                                    </span>
                                                @elseif($member->document_status === 'ditolak')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        <span>❌ Ditolak</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>Menunggu Verifikasi</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Status Detail Messages --}}
                                        @if($member->document_status === 'disetujui')
                                            <div class="p-4 rounded-xl bg-emerald-50/80 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                                                <p class="font-bold flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Dokumen Telah Terverifikasi</span>
                                                </p>
                                                <p class="text-emerald-800 leading-relaxed">
                                                    Dokumen jamaah telah diverifikasi oleh tim admin dan tidak memerlukan tindakan lebih lanjut.
                                                </p>
                                            </div>
                                        @elseif($member->document_status === 'ditolak')
                                            <div class="p-4 sm:p-5 rounded-2xl bg-red-50/90 border border-red-200 text-xs text-red-900 space-y-3 shadow-2xs">
                                                <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                                                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z"/></svg>
                                                    <span>Pengajuan Ditolak — Mode Revisi Total</span>
                                                </div>

                                                @if($member->rejection_reason)
                                                    <div class="p-3.5 rounded-xl bg-white border border-red-200 space-y-1 shadow-2xs">
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 block">Alasan Penolakan dari Admin:</span>
                                                        <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $member->rejection_reason }}</p>
                                                    </div>
                                                @endif

                                                <p class="text-red-800 font-semibold leading-relaxed">
                                                    Sesuai ketentuan, silakan lakukan <strong>Pengajuan Ulang Penuh</strong> dengan mengisi kembali seluruh data identitas (Nama, NIK, Tempat/Tgl Lahir, Jenis Kelamin, Alamat, No. KK) dan mengunggah kembali seluruh berkas persyaratan di bawah ini.
                                                </p>
                                            </div>

                                            {{-- Full Resubmission Form (Identitas + Dokumen) --}}
                                            <div class="p-5 sm:p-6 rounded-2xl border border-red-200 bg-white space-y-6 shadow-2xs"
                                                 x-data="resubmitForm('{{ addslashes(old('name', '')) }}', '{{ old('nik', '') }}', '{{ old('no_kk', '') }}', '{{ old('no_passport', '') }}', '{{ old('relationship', $member->relationship) }}', '{{ addslashes(old('birth_place', '')) }}', '{{ old('birth_date', '') }}', '{{ old('gender', '') }}', '{{ addslashes(old('address', '')) }}')">
                                                <div class="border-b border-[#E0E7DC] pb-3">
                                                    <h4 class="text-sm font-bold text-[#12271E] flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                        <span>Formulir Pengajuan Ulang (Revisi Total Data & Dokumen)</span>
                                                    </h4>
                                                    <p class="text-[11px] text-[#526057] mt-0.5">Unggah ulang seluruh berkas dokumen persyaratan dan lengkapi data identitas.</p>
                                                </div>

                                                <form method="POST" action="{{ route('jamaah.members.documents.update', $member) }}" enctype="multipart/form-data" class="space-y-6">
                                                    @csrf
                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    {{-- BAGIAN 1: FOTO KTP & DATA IDENTITAS JAMAAH SESUAI KTP       --}}
                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-4 shadow-2xs">
                                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                                            
                                                            {{-- KOLOM KIRI: FOTO KTP --}}
                                                            <div class="lg:col-span-5 space-y-3">
                                                                <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                                    <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/></svg>
                                                                        <span>Foto KTP</span>
                                                                        <span class="text-red-500">*</span>
                                                                    </label>
                                                                </div>

                                                                <input type="file" 
                                                                       id="resubmit_ktp_input"
                                                                       name="ktp_file" 
                                                                       accept=".jpg,.jpeg,.png,.webp" 
                                                                       required
                                                                       @change="handleKtpFileSelect($event)"
                                                                       class="hidden">

                                                                <div x-show="!ktpPreviewUrl" 
                                                                     @click="document.getElementById('resubmit_ktp_input').click()"
                                                                     class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                                    <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Foto KTP Baru</span>
                                                                        <span class="text-[10px] text-[#526057]">Format: JPG, JPEG, PNG, WebP (Maks. 10 MB)</span>
                                                                    </div>
                                                                </div>

                                                                <div x-show="ktpPreviewUrl" class="space-y-2.5">
                                                                    <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                                        <img :src="ktpPreviewUrl" alt="Preview KTP" class="max-h-[210px] w-full object-contain">
                                                                    </div>
                                                                    <div class="flex items-center justify-between gap-2 pt-1">
                                                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                            <span>✓ KTP terunggah</span>
                                                                        </span>
                                                                        <div class="flex items-center gap-2">
                                                                            <button type="button" @click="document.getElementById('resubmit_ktp_input').click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                                                <span>Ganti Foto</span>
                                                                            </button>
                                                                            <button type="button" @click="removeKtpFile()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                                                <span>Hapus</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                {{-- Status OCR KTP --}}
                                                                <div x-show="ocrKtpStatus !== 'idle'" class="text-[11px] pt-1">
                                                                    <template x-if="ocrKtpStatus === 'scanning'">
                                                                        <div class="flex items-center gap-2 text-amber-700 font-medium">
                                                                            <svg class="animate-spin w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                                            <span x-text="ocrKtpMessage"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="ocrKtpStatus === 'success'">
                                                                        <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                            <span x-text="ocrKtpMessage"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="ocrKtpStatus === 'warning' || ocrKtpStatus === 'quality_warning'">
                                                                        <div class="p-2.5 rounded-xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-1">
                                                                            <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                                                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                                                                <span>Pemberitahuan Pembacaan KTP</span>
                                                                            </div>
                                                                            <p class="text-[10.5px] leading-relaxed" x-text="ocrKtpMessage"></p>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            {{-- KOLOM KANAN: DATA IDENTITAS JAMAAH SESUAI KTP --}}
                                                            <div class="lg:col-span-7 space-y-3.5 lg:border-l lg:border-[#E0E7DC] lg:pl-6">
                                                                <div class="pb-1.5 border-b border-[#EFF3EB]">
                                                                    <h5 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">
                                                                        Data Identitas Jamaah Sesuai KTP
                                                                    </h5>
                                                                    <p class="text-[11px] text-[#526057] mt-0.5">
                                                                        Data harus diisi sesuai dengan informasi yang tercantum pada KTP.
                                                                    </p>
                                                                </div>

                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                                                    
                                                                    {{-- Nama Lengkap --}}
                                                                    <div class="sm:col-span-2">
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                            Nama Lengkap Sesuai KTP <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <input type="text" name="name" x-model="form.name" required placeholder="Masukkan nama lengkap sesuai KTP"
                                                                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                    </div>

                                                                    {{-- NIK (16 Digit) + Realtime Counter --}}
                                                                    <div class="sm:col-span-2">
                                                                        <div class="flex items-center justify-between mb-1">
                                                                            <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">
                                                                                NIK (16 Digit Angka) <span class="text-red-500">*</span>
                                                                            </label>
                                                                            <span :class="form.nik && form.nik.length === 16 ? 'text-emerald-700 bg-emerald-50 border-emerald-300' : 'text-amber-700 bg-amber-50 border-amber-300'"
                                                                                  class="text-[10px] px-2 py-0.5 rounded-full border font-mono font-bold"
                                                                                  x-text="(form.nik ? form.nik.length : 0) + '/16 digit ' + (form.nik && form.nik.length === 16 ? '✓' : '⚠')">
                                                                            </span>
                                                                        </div>
                                                                        <input type="text" name="nik" inputmode="numeric" maxlength="16" pattern="[0-9]{16}" required
                                                                               x-model="form.nik"
                                                                               placeholder="16 digit angka (0–9)"
                                                                               @keydown="if(!/^[0-9]$/.test($event.key) && !['Backspace','Delete','ArrowLeft','ArrowRight','Tab'].includes($event.key)) $event.preventDefault()"
                                                                               @input="form.nik = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); checkNikCrossMatch()"
                                                                               @paste="setTimeout(() => { form.nik = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); checkNikCrossMatch(); }, 0)"
                                                                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] font-mono text-xs sm:text-sm text-[#12271E] tracking-wider focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                        
                                                                        {{-- Cross Match Status --}}
                                                                        <div class="mt-1">
                                                                            <template x-if="nikCrossCheckStatus === 'matched'">
                                                                                <p class="text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                                                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                                    <span>✓ NIK cocok dengan Kartu Keluarga</span>
                                                                                </p>
                                                                            </template>
                                                                            <template x-if="nikCrossCheckStatus === 'different'">
                                                                                <p class="text-[10px] text-amber-800 font-semibold flex items-center gap-1">
                                                                                    <svg class="w-3 h-3 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                                                                    <span>⚠ NIK pada KTP dan KK berbeda.</span>
                                                                                </p>
                                                                            </template>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Tempat Lahir Sesuai KTP --}}
                                                                    <div>
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                            Tempat Lahir <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <input type="text" name="birth_place" x-model="form.birth_place" required placeholder="Contoh: BANDUNG"
                                                                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                    </div>

                                                                    {{-- Tanggal Lahir Sesuai KTP --}}
                                                                    <div>
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                            Tanggal Lahir <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <input type="date" name="birth_date" x-model="form.birth_date" required
                                                                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                    </div>

                                                                    {{-- Jenis Kelamin Sesuai KTP --}}
                                                                    <div>
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                            Jenis Kelamin <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <select name="gender" x-model="form.gender" required
                                                                                class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                                                            <option value="laki-laki">Laki-laki</option>
                                                                            <option value="perempuan">Perempuan</option>
                                                                        </select>
                                                                    </div>

                                                                    {{-- Hubungan Keluarga --}}
                                                                    <div>
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                            Hubungan Keluarga <span class="text-red-500">*</span>
                                                                        </label>
                                                                        @if($member->relationship === 'diri_sendiri')
                                                                            <input type="hidden" name="relationship" value="diri_sendiri">
                                                                            <input type="text" value="Diri Sendiri (Pemesan Utama)" disabled
                                                                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#EFF3EB] text-xs sm:text-sm font-semibold text-[#12271E]">
                                                                        @else
                                                                            <select name="relationship" x-model="form.relationship" required
                                                                                    class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                                <option value="">-- Pilih Hubungan --</option>
                                                                                <option value="suami">Suami</option>
                                                                                <option value="istri">Istri</option>
                                                                                <option value="anak">Anak</option>
                                                                                <option value="orang_tua">Orang Tua</option>
                                                                                <option value="saudara">Saudara Kandung</option>
                                                                            </select>
                                                                        @endif
                                                                    </div>

                                                                    {{-- Alamat Tinggal Sesuai KTP --}}
                                                                    <div class="sm:col-span-2">
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                            Alamat Tinggal Sesuai KTP <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <textarea name="address" x-model="form.address" rows="2" required
                                                                                  class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                                                                  placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan/Desa, Kecamatan, Kabupaten/Kota, Provinsi"></textarea>
                                                                        <p class="text-[10px] text-[#526057] mt-0.5">Isi persis berdasarkan alamat yang tertera pada KTP.</p>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    {{-- BAGIAN 2: FOTO KK & NOMOR KK                                --}}
                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-4 shadow-2xs">
                                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                                            
                                                            {{-- KOLOM KIRI: FOTO KK --}}
                                                            <div class="lg:col-span-5 space-y-3">
                                                                <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                                    <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                                        <span>Foto KK</span>
                                                                        <span class="text-red-500">*</span>
                                                                    </label>
                                                                </div>

                                                                <input type="file" 
                                                                       id="resubmit_kk_input"
                                                                       name="kk_file" 
                                                                       accept=".jpg,.jpeg,.png,.webp" 
                                                                       required
                                                                       @change="handleKkFileSelect($event)"
                                                                       class="hidden">

                                                                <div x-show="!kkPreviewUrl" 
                                                                     @click="document.getElementById('resubmit_kk_input').click()"
                                                                     class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                                    <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Foto KK Baru</span>
                                                                        <span class="text-[10px] text-[#526057]">Format: JPG, JPEG, PNG, WebP (Maks. 10 MB)</span>
                                                                    </div>
                                                                </div>

                                                                <div x-show="kkPreviewUrl" class="space-y-2.5">
                                                                    <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                                        <img :src="kkPreviewUrl" alt="Preview KK" class="max-h-[210px] w-full object-contain">
                                                                    </div>
                                                                    <div class="flex items-center justify-between gap-2 pt-1">
                                                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                            <span>✓ KK terunggah</span>
                                                                        </span>
                                                                        <div class="flex items-center gap-2">
                                                                            <button type="button" @click="document.getElementById('resubmit_kk_input').click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                                                <span>Ganti Foto</span>
                                                                            </button>
                                                                            <button type="button" @click="removeKkFile()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                                                <span>Hapus</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                {{-- Status OCR KK --}}
                                                                <div x-show="ocrKkStatus !== 'idle'" class="text-[11px] pt-1">
                                                                    <template x-if="ocrKkStatus === 'scanning'">
                                                                        <div class="flex items-center gap-2 text-amber-700 font-medium">
                                                                            <svg class="animate-spin w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                                            <span x-text="ocrKkMessage"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="ocrKkStatus === 'success'">
                                                                        <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                            <span x-text="ocrKkMessage"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="ocrKkStatus === 'warning' || ocrKkStatus === 'quality_warning'">
                                                                        <div class="p-2.5 rounded-xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-1">
                                                                            <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                                                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                                                                <span>Pemberitahuan Pembacaan KK</span>
                                                                            </div>
                                                                            <p class="text-[10.5px] leading-relaxed" x-text="ocrKkMessage"></p>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            {{-- KOLOM KANAN: NOMOR KK --}}
                                                            <div class="lg:col-span-7 space-y-3.5 lg:border-l lg:border-[#E0E7DC] lg:pl-6">
                                                                <div class="pb-1.5 border-b border-[#EFF3EB]">
                                                                    <h5 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">
                                                                        Nomor KK
                                                                    </h5>
                                                                    <p class="text-[11px] text-[#526057] mt-0.5">
                                                                        Nomor Kartu Keluarga (KK) yang tertera pada dokumen KK.
                                                                    </p>
                                                                </div>

                                                                <div>
                                                                    <div class="flex items-center justify-between mb-1">
                                                                        <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider">
                                                                            Nomor KK (16 Digit Angka) <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <span :class="form.no_kk && form.no_kk.length === 16 ? 'text-emerald-700 bg-emerald-50 border-emerald-300' : 'text-amber-700 bg-amber-50 border-amber-300'"
                                                                              class="text-[10px] px-2 py-0.5 rounded-full border font-mono font-bold"
                                                                              x-text="(form.no_kk ? form.no_kk.length : 0) + '/16 digit ' + (form.no_kk && form.no_kk.length === 16 ? '✓' : '⚠')">
                                                                        </span>
                                                                    </div>
                                                                    <input type="text" name="no_kk" inputmode="numeric" maxlength="16" pattern="[0-9]{16}" required
                                                                           x-model="form.no_kk"
                                                                           placeholder="16 digit angka (0–9)"
                                                                           @keydown="if(!/^[0-9]$/.test($event.key) && !['Backspace','Delete','ArrowLeft','ArrowRight','Tab'].includes($event.key)) $event.preventDefault()"
                                                                           @input="form.no_kk = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                                                           @paste="setTimeout(() => { form.no_kk = $event.target.value.replace(/[^0-9]/g, '').slice(0, 16); }, 0)"
                                                                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] font-mono text-xs sm:text-sm text-[#12271E] tracking-wider focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    {{-- BAGIAN 3: FOTO PASPOR & NOMOR PASPOR                        --}}
                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-4 shadow-2xs">
                                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                                            
                                                            {{-- KOLOM KIRI: FOTO PASPOR --}}
                                                            <div class="lg:col-span-5 space-y-3">
                                                                <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                                    <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                                                        <span>Foto Paspor</span>
                                                                        <span class="text-[10px] font-normal text-[#526057] lowercase">(opsional)</span>
                                                                    </label>
                                                                </div>

                                                                <input type="file" 
                                                                       id="resubmit_passport_input"
                                                                       name="passport_file" 
                                                                       accept=".jpg,.jpeg,.png,.webp" 
                                                                       @change="handlePassportFileSelect($event)"
                                                                       class="hidden">

                                                                <div x-show="!passportPreviewUrl" 
                                                                     @click="document.getElementById('resubmit_passport_input').click()"
                                                                     class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                                    <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Foto Paspor Baru</span>
                                                                        <span class="text-[10px] text-[#526057]">Format: JPG, JPEG, PNG, WebP (Maks. 10 MB)</span>
                                                                    </div>
                                                                </div>

                                                                <div x-show="passportPreviewUrl" class="space-y-2.5">
                                                                    <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                                        <img :src="passportPreviewUrl" alt="Preview Paspor" class="max-h-[210px] w-full object-contain">
                                                                    </div>
                                                                    <div class="flex items-center justify-between gap-2 pt-1">
                                                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                            <span>✓ Paspor terunggah</span>
                                                                        </span>
                                                                        <div class="flex items-center gap-2">
                                                                            <button type="button" @click="document.getElementById('resubmit_passport_input').click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                                                                <span>Ganti</span>
                                                                            </button>
                                                                            <button type="button" @click="removePassportFile()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-semibold transition-colors cursor-pointer flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                                                <span>Hapus</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div x-show="ocrPassportStatus !== 'idle'" class="text-[11px] pt-1">
                                                                    <template x-if="ocrPassportStatus === 'scanning'">
                                                                        <div class="flex items-center gap-2 text-amber-700 font-medium">
                                                                            <svg class="animate-spin w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                                            <span x-text="ocrPassportMessage"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="ocrPassportStatus === 'success'">
                                                                        <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                            <span x-text="ocrPassportMessage"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            {{-- KOLOM KANAN: NOMOR PASPOR --}}
                                                            <div class="lg:col-span-7 space-y-3.5 lg:border-l lg:border-[#E0E7DC] lg:pl-6">
                                                                <div class="pb-1.5 border-b border-[#EFF3EB]">
                                                                    <h5 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">
                                                                        Nomor Paspor
                                                                    </h5>
                                                                    <p class="text-[11px] text-[#526057] mt-0.5">
                                                                        Nomor paspor bagi calon jamaah yang telah memiliki paspor aktif.
                                                                    </p>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1">
                                                                        Nomor Paspor <span class="text-[10px] font-normal text-[#526057] lowercase">(opsional)</span>
                                                                    </label>
                                                                    <input type="text" name="no_passport" x-model="form.no_passport" placeholder="Contoh: B1234567 (opsional)"
                                                                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] font-mono text-xs sm:text-sm text-[#12271E] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    {{-- BAGIAN 4: DOKUMEN PENDUKUNG (BUKU NIKAH / AKTA KELAHIRAN)     --}}
                                                    {{-- ════════════════════════════════════════════════════════════ --}}
                                                    @if(in_array($member->relationship, ['suami', 'istri', 'anak']))
                                                        <div class="space-y-4">
                                                            
                                                            {{-- Buku Nikah (Jika Suami/Istri) --}}
                                                            @if(in_array($member->relationship, ['suami', 'istri']))
                                                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-3 shadow-2xs">
                                                                    <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                                        <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                                            <span>Scan Buku Nikah</span>
                                                                            <span class="text-red-500">*</span>
                                                                        </label>
                                                                    </div>

                                                                    <input type="file" 
                                                                           id="resubmit_marriage_input"
                                                                           name="marriage_book_file" 
                                                                           accept=".jpg,.jpeg,.png,.webp" 
                                                                           required
                                                                           @change="handleMarriageBookFileSelect($event)"
                                                                           class="hidden">

                                                                    <div x-show="!marriageBookPreviewUrl" 
                                                                         @click="document.getElementById('resubmit_marriage_input').click()"
                                                                         class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                                        <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                                                        </div>
                                                                        <div>
                                                                            <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Buku Nikah Baru</span>
                                                                            <span class="text-[10px] text-[#526057]">Wajib bagi pasangan suami/istri (Maks. 10 MB)</span>
                                                                        </div>
                                                                    </div>

                                                                    <div x-show="marriageBookPreviewUrl" class="space-y-2.5">
                                                                        <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                                            <img :src="marriageBookPreviewUrl" alt="Preview Buku Nikah" class="max-h-[210px] w-full object-contain">
                                                                        </div>
                                                                        <div class="flex items-center justify-between gap-2 pt-1">
                                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">✓ Buku Nikah terunggah</span>
                                                                            <div class="flex items-center gap-2">
                                                                                <button type="button" @click="document.getElementById('resubmit_marriage_input').click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] text-[11px] font-semibold">Ganti</button>
                                                                                <button type="button" @click="removeMarriageBookFile()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-semibold">Hapus</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Akta Lahir (Jika Anak) --}}
                                                            @if($member->relationship === 'anak')
                                                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E0E7DC] space-y-3 shadow-2xs">
                                                                    <div class="flex items-center justify-between pb-1.5 border-b border-[#EFF3EB]">
                                                                        <label class="block text-xs font-bold uppercase tracking-wider text-[#12271E] flex items-center gap-1.5">
                                                                            <span>Scan Akta Kelahiran</span>
                                                                            <span class="text-red-500">*</span>
                                                                        </label>
                                                                    </div>

                                                                    <input type="file" 
                                                                           id="resubmit_birth_input"
                                                                           name="birth_certificate_file" 
                                                                           accept=".jpg,.jpeg,.png,.webp" 
                                                                           required
                                                                           @change="handleBirthCertificateFileSelect($event)"
                                                                           class="hidden">

                                                                    <div x-show="!birthCertificatePreviewUrl" 
                                                                         @click="document.getElementById('resubmit_birth_input').click()"
                                                                         class="border-2 border-dashed border-[#CCD8C7] hover:border-[#1B3B2B] bg-[#F8FAF7] hover:bg-[#EFF3EB]/40 rounded-xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2">
                                                                        <div class="w-10 h-10 rounded-full bg-[#1B3B2B]/10 text-[#1B3B2B] flex items-center justify-center">
                                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                                        </div>
                                                                        <div>
                                                                            <span class="text-xs font-bold text-[#1B3B2B] block">Pilih Scan Akta Kelahiran</span>
                                                                            <span class="text-[10px] text-[#526057]">Wajib bagi anak (Maks. 10 MB)</span>
                                                                        </div>
                                                                    </div>

                                                                    <div x-show="birthCertificatePreviewUrl" class="space-y-2.5">
                                                                        <div class="relative rounded-xl border border-[#CCD8C7] overflow-hidden bg-zinc-950/5 flex items-center justify-center min-h-[140px] max-h-[220px]">
                                                                            <img :src="birthCertificatePreviewUrl" alt="Preview Akta" class="max-h-[210px] w-full object-contain">
                                                                        </div>
                                                                        <div class="flex items-center justify-between gap-2 pt-1">
                                                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">✓ Akta Kelahiran terunggah</span>
                                                                            <div class="flex items-center gap-2">
                                                                                <button type="button" @click="document.getElementById('resubmit_birth_input').click()" class="px-2.5 py-1 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] text-[11px] font-semibold">Ganti</button>
                                                                                <button type="button" @click="removeBirthCertificateFile()" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[11px] font-semibold">Hapus</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                        </div>
                                                    @endif

                                                    <div class="pt-3 border-t border-[#E0E7DC]">
                                                        <button type="submit" 
                                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 py-3 px-7 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all cursor-pointer shadow-md">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                                            <span>Kirim Ulang Seluruh Data & Dokumen Pengajuan</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @elseif($member->document_status === 'menunggu_verifikasi')
                                            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-1">
                                                <p class="font-bold flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Sedang Diperiksa oleh Admin</span>
                                                </p>
                                                <p class="text-amber-800 leading-relaxed">
                                                    Dokumen sedang dalam antrean pemeriksaan. Anda akan mendapatkan notifikasi status setelah verifikasi selesai.
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Cek Kelengkapan Berkas (Grid 4 Kolom Proporsional) --}}
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-3 border-t border-[#E0E7DC]">
                                            
                                            {{-- KTP --}}
                                            <div class="p-2.5 rounded-xl bg-white border border-[#E0E7DC] min-w-0">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block truncate">1. KTP</span>
                                                <div class="mt-1 flex items-center gap-1">
                                                    @if($member->ktp_file)
                                                        <span class="text-xs font-bold text-emerald-700 truncate">Sudah Ada ✓</span>
                                                    @else
                                                        <span class="text-xs font-bold text-red-500 truncate">Belum Ada ✗</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- KK --}}
                                            <div class="p-2.5 rounded-xl bg-white border border-[#E0E7DC] min-w-0">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block truncate">2. Kartu Keluarga</span>
                                                <div class="mt-1 flex items-center gap-1">
                                                    @if($member->kk_file)
                                                        <span class="text-xs font-bold text-emerald-700 truncate">Sudah Ada ✓</span>
                                                    @else
                                                        <span class="text-xs font-bold text-red-500 truncate">Belum Ada ✗</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Paspor --}}
                                            <div class="p-2.5 rounded-xl bg-white border border-[#E0E7DC] min-w-0">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block truncate">3. Paspor</span>
                                                <div class="mt-1 flex items-center gap-1">
                                                    @if($member->passport_file)
                                                        <span class="text-xs font-bold text-emerald-700 truncate">Sudah Ada ✓</span>
                                                    @else
                                                        <span class="text-xs font-semibold text-[#526057] truncate">Menyusul</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Dokumen Pelengkap --}}
                                            <div class="p-2.5 rounded-xl bg-white border border-[#E0E7DC] min-w-0">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block truncate">
                                                    4. {{ $member->relationship === 'anak' ? 'Akta Lahir' : ($member->relationship === 'suami' || $member->relationship === 'istri' ? 'Buku Nikah' : 'Pelengkap') }}
                                                </span>
                                                <div class="mt-1 flex items-center gap-1">
                                                    @if($member->marriage_book_file || $member->birth_certificate_file)
                                                        <span class="text-xs font-bold text-emerald-700 truncate">Sudah Ada ✓</span>
                                                    @else
                                                        <span class="text-xs font-semibold text-[#526057] truncate">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- Kolom Kanan: Tagihan & Bantuan (4 Kolom) --}}
                    <div class="lg:col-span-4 space-y-6 min-w-0">

                        {{-- Kartu Rincian Pembayaran --}}
                        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs space-y-5">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">Rincian Pembayaran</span>
                                <h3 class="text-base font-bold text-[#12271E] pb-3 border-b border-[#E0E7DC]">
                                    Total Biaya Pendaftaran
                                </h3>
                            </div>

                            @if($registration->invoice)
                                @php
                                    $totalPrice = (float) $registration->invoice->total_price;
                                    $totalPaid = (float) $registration->invoice->total_paid;
                                    $remainingBalance = (float) $registration->invoice->remaining_balance;
                                    $percentPaid = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
                                @endphp

                                <div class="space-y-3.5 text-xs text-[#526057]">
                                    @if(!$isCancelled)
                                        {{-- Progress Pelunasan --}}
                                        <div class="space-y-1.5 pb-2">
                                            <div class="flex justify-between text-[11px] font-semibold">
                                                <span class="text-[#4D5E54]">Progres Pelunasan:</span>
                                                <span class="text-[#1B3B2B] font-bold">{{ $percentPaid }}%</span>
                                            </div>
                                            <div class="w-full bg-[#EFF3EB] h-2.5 rounded-full overflow-hidden border border-[#CCD8C7]">
                                                <div class="bg-[#1B3B2B] h-full rounded-full transition-all duration-300" style="width: {{ $percentPaid }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-2.5 rounded-xl bg-red-50/80 border border-red-200 text-red-800 text-[11px] font-semibold text-center">
                                            Pendaftaran Dibatalkan
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <span>Total Biaya ({{ $registration->members->count() }} Jamaah)</span>
                                        <strong class="text-sm font-bold text-[#12271E]">Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>Sudah Dibayar</span>
                                        <strong class="text-sm font-bold text-emerald-700">Rp {{ number_format($totalPaid, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-[#E0E7DC]">
                                        <span>Sisa Pembayaran</span>
                                        <strong class="text-base font-bold {{ $isCancelled ? 'text-[#526057]' : 'text-[#1B3B2B]' }}">
                                            {{ $isCancelled ? '-' : 'Rp ' . number_format($remainingBalance, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                    @if($registration->status === 'menunggu_pembayaran_dp' && !$isCancelled)
                                        <div class="flex items-center justify-between pt-2 border-t border-[#E0E7DC] text-[11px]">
                                            <span>Batas Waktu DP:</span>
                                            <strong class="text-red-700 font-bold">{{ $registration->invoice->due_date ? \Carbon\Carbon::parse($registration->invoice->due_date)->translatedFormat('d M Y') : '-' }}</strong>
                                        </div>
                                    @endif

                                    {{-- Tombol Lihat & Unduh Invoice --}}
                                    <div class="pt-2 flex items-center gap-2">
                                        <a href="{{ route('documents.invoice.pdf', $registration) }}" 
                                           class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-2xs">
                                            <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            <span>Unduh PDF</span>
                                        </a>
                                        <a href="{{ route('documents.invoice', $registration) }}" 
                                           target="_blank"
                                           class="inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] active:scale-[0.98] transition-all border border-[#CCD8C7] shadow-2xs">
                                            <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                            <span>Lihat</span>
                                        </a>
                                        <a href="{{ route('documents.invoice.download', $registration) }}" 
                                           title="Unduh File Excel Invoice Resmi"
                                           class="inline-flex items-center justify-center p-2.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-all shadow-2xs">
                                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            {{-- Aksi Pembayaran Sesuai Status --}}
                            @php
                                $lastDp = $registration->payments->where('type', 'dp')->last();
                                $lastPelunasan = $registration->payments->where('type', 'pelunasan')->last();
                                $hasPendingPayment = $registration->payments->where('status', 'menunggu_verifikasi')->isNotEmpty();
                                $hasApprovedPayment = $registration->payments->where('status', 'disetujui')->isNotEmpty();
                            @endphp

                            @if($isCancelled)
                                <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 text-xs text-zinc-700 space-y-2">
                                    <div class="flex items-center gap-2 font-bold text-[#12271E]">
                                        <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        <span>Pendaftaran Dibatalkan</span>
                                    </div>
                                    <p class="leading-relaxed text-[11.5px] text-[#526057]">
                                        Pendaftaran ini telah resmi dibatalkan. Tidak ada tagihan atau pembayaran aktif.
                                    </p>
                                </div>
                            @elseif($registration->status === 'menunggu_pembayaran_dp')
                                @if($lastDp && $lastDp->status === 'ditolak')
                                    {{-- Card Pembayaran DP Ditolak --}}
                                    <div class="p-5 rounded-2xl bg-red-50/90 border border-red-200 text-xs text-red-900 space-y-3 shadow-2xs">
                                        <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                            <span>⚠ Pembayaran DP Ditolak</span>
                                        </div>
                                        <div class="p-3.5 rounded-xl bg-white border border-red-200 space-y-1 shadow-2xs">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan:</span>
                                            <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $lastDp->rejection_reason ?? 'Nominal atau bukti transfer tidak valid.' }}</p>
                                            @if($lastDp->rejected_at)
                                                <span class="text-[10px] text-red-600/80 block mt-0.5">{{ $lastDp->rejected_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                            @endif
                                        </div>
                                        <p class="text-red-800 font-medium leading-relaxed">
                                            Silakan perbaiki pembayaran dan upload bukti pembayaran kembali.
                                        </p>
                                        <a href="{{ route('jamaah.payment.dp') }}" 
                                           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 active:scale-[0.98] transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                            <span>Upload Bukti Pembayaran DP</span>
                                        </a>
                                    </div>
                                @else
                                    <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200 text-xs text-blue-950 space-y-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                                            <p class="font-bold text-sm">Dokumen Terverifikasi!</p>
                                        </div>
                                        <p class="text-blue-900 leading-relaxed text-[11.5px]">
                                            Silakan transfer uang muka (DP) dan unggah bukti transfer sebelum batas waktu agar pendaftaran Anda dapat diproses.
                                        </p>
                                        <a href="{{ route('jamaah.payment.dp') }}" 
                                           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                                            <span>Bayar DP Sekarang &rarr;</span>
                                        </a>
                                    </div>
                                @endif
                            @elseif($registration->status === 'menunggu_verifikasi_pembayaran_dp')
                                <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950 space-y-2">
                                    <div class="flex items-center gap-2 text-amber-800 font-bold">
                                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Pembayaran sedang menunggu verifikasi Admin.</span>
                                    </div>
                                    <p class="leading-relaxed text-[11.5px] text-amber-900">
                                        Tim keuangan kami sedang memeriksa bukti setoran DP Anda. Status akan otomatis diperbarui begitu verifikasi selesai.
                                    </p>
                                </div>
                            @elseif(in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                                {{-- Jika ada setoran pelunasan terakhir yang ditolak dan tidak ada pending setelahnya --}}
                                @if($lastPelunasan && $lastPelunasan->status === 'ditolak' && !$hasPendingPayment)
                                    <div class="p-5 rounded-2xl bg-red-50/90 border border-red-200 text-xs text-red-900 space-y-3 shadow-2xs">
                                        <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                            <span>⚠ Pembayaran Ditolak</span>
                                        </div>
                                        <div class="p-3.5 rounded-xl bg-white border border-red-200 space-y-1 shadow-2xs">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan:</span>
                                            <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $lastPelunasan->rejection_reason ?? 'Nominal atau bukti transfer pelunasan tidak valid.' }}</p>
                                            @if($lastPelunasan->rejected_at)
                                                <span class="text-[10px] text-red-600/80 block mt-0.5">{{ $lastPelunasan->rejected_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                            @endif
                                        </div>
                                        <p class="text-red-800 font-medium leading-relaxed">
                                            Silakan perbaiki pembayaran dan upload bukti pembayaran kembali.
                                        </p>
                                        <a href="{{ route('jamaah.payment.pelunasan') }}" 
                                           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 active:scale-[0.98] transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                            <span>Upload Bukti Pembayaran Pelunasan</span>
                                        </a>
                                    </div>
                                @elseif($hasPendingPayment)
                                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950 space-y-2">
                                        <div class="flex items-center gap-2 text-amber-800 font-bold">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Pembayaran sedang menunggu verifikasi Admin.</span>
                                        </div>
                                        <p class="leading-relaxed text-[11.5px] text-amber-900">
                                            Setoran pelunasan terbaru Anda sedang diverifikasi. Total saldo akan bertambah setelah disetujui.
                                        </p>
                                    </div>
                                @else
                                    <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs text-emerald-950 space-y-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="font-bold text-sm">✓ Pembayaran telah diverifikasi.</p>
                                        </div>
                                        <p class="leading-relaxed text-[11.5px] text-emerald-900">
                                            Status Anda resmi terdaftar. Anda dapat menyetor pelunasan secara bertahap kapan saja dengan nominal bebas sesuai kemampuan tabungan Anda.
                                        </p>

                                        @if(($registration->invoice->remaining_balance ?? 0) > 0)
                                            <a href="{{ route('jamaah.payment.pelunasan') }}" 
                                               class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Setor Pelunasan &rarr;</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @elseif(in_array($registration->status, ['lunas', 'berangkat']))
                                <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs text-emerald-950 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="font-bold text-sm">✓ Pembayaran telah diverifikasi.</p>
                                    </div>
                                    <p class="leading-relaxed text-[11.5px] text-emerald-900">
                                        Alhamdulillah, seluruh administrasi pembayaran paket Anda telah selesai dan LUNAS. Tim kami akan segera berkoordinasi mengenai manasik dan keberangkatan.
                                    </p>
                                </div>
                            @elseif($registration->status === 'menunggu_verifikasi_dokumen')
                                <div class="p-4 rounded-2xl bg-[#EFF3EB] border border-[#CCD8C7] text-xs text-[#12271E] space-y-1">
                                    <p class="font-bold">📋 Dokumen Sedang Diperiksa</p>
                                    <p class="text-[#526057] leading-relaxed text-[11.5px]">
                                        Setelah dokumen diverifikasi oleh tim admin, tombol pembayaran DP akan aktif secara otomatis.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Riwayat Pembayaran Jamaah --}}
                        @if($registration->payments->isNotEmpty())
                            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-[#E0E7DC]">
                                    <h4 class="text-sm font-bold text-[#12271E] flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Riwayat Pembayaran</span>
                                    </h4>
                                    <span class="text-[11px] font-semibold text-[#526057]">
                                        {{ $registration->payments->count() }} Transaksi
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    @foreach($registration->payments->sortByDesc('id') as $p)
                                        <div class="p-3.5 rounded-2xl border {{ $p->status === 'disetujui' ? 'border-emerald-200 bg-emerald-50/40' : ($p->status === 'ditolak' ? 'border-red-200 bg-red-50/40' : 'border-[#E0E7DC] bg-[#F8FAF7]') }} text-xs space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <div>
                                                    <span class="font-bold text-[#12271E]">{{ $p->type_label }}</span>
                                                    <span class="text-[10px] text-[#526057] block">{{ $p->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-bold text-sm text-[#1B3B2B] block">{{ $p->amount_formatted }}</span>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-[#E0E7DC]/60">
                                                <div class="flex items-center gap-2">
                                                    @if($p->status === 'disetujui')
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md">
                                                            ✓ Disetujui
                                                        </span>
                                                    @elseif($p->status === 'ditolak')
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-800 bg-red-100 px-2 py-0.5 rounded-md">
                                                            ✕ Ditolak
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md">
                                                            ⏳ Menunggu Verifikasi
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    @if($p->status === 'disetujui')
                                                        <a href="{{ route('documents.receipt', $p) }}" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-all shadow-2xs">
                                                            <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span>Lihat Kwitansi</span>
                                                        </a>
                                                        <a href="{{ route('documents.receipt.download', $p) }}" 
                                                           title="Unduh File Excel Kwitansi Resmi"
                                                           class="inline-flex items-center justify-center p-1.5 rounded-lg text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-all shadow-2xs">
                                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                        </a>
                                                    @endif

                                                    @if($p->proof_url)
                                                        <a href="{{ $p->proof_url }}" target="_blank" class="text-[10.5px] text-[#526057] hover:text-[#12271E] hover:underline font-semibold flex items-center gap-1">
                                                            <span>Bukti</span>
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($p->status === 'ditolak' && $p->rejection_reason)
                                                <div class="p-2 rounded-xl bg-white border border-red-200 text-[11px] text-red-900">
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan:</span>
                                                    <p class="leading-snug mt-0.5">{{ $p->rejection_reason }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Bantuan WhatsApp --}}
                        <div class="bg-[#1B3B2B] text-white rounded-3xl p-6 sm:p-7 shadow-md shadow-[#1B3B2B]/15 space-y-4">
                            <div>
                                <h4 class="text-base font-bold">Butuh Bantuan?</h4>
                                <p class="text-xs text-white/80 leading-relaxed mt-1">
                                    Tim pelayanan jamaah Zein Tour siap membantu Anda terkait berkas paspor, manasik, dan jadwal perjalanan.
                                </p>
                            </div>
                            <a href="https://wa.me/6281222222562?text=Assalamu%27alaikum,%20saya%20ingin%20konsultasi%20status%20pendaftaran%20{{ $registration->registration_number }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-[#1B3B2B] bg-white hover:bg-zinc-100 active:scale-[0.98] transition-all shadow-sm">
                                <svg class="w-4 h-4 text-emerald-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                                <span>Hubungi CS via WhatsApp</span>
                            </a>
                        </div>

                        {{-- Tautan Pembatalan --}}
                        @if(!in_array($registration->status, ['berangkat', \App\Models\Registration::STATUS_SELESAI, 'dibatalkan']))
                            <div class="text-center pt-1">
                                <a href="{{ route('jamaah.registration.cancel') }}" class="text-[11.5px] font-semibold text-red-600 hover:text-red-700 hover:underline inline-flex items-center gap-1">
                                    <span>Ingin membatalkan pendaftaran? Cek simulasi biaya</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                </a>
                            </div>
                        @endif

                    </div>

                </div>

            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-3xl border border-[#E0E7DC] p-8 sm:p-12 text-center shadow-xs max-w-2xl mx-auto" style="animation: fadeSlideUp 0.4s ease 0.1s both">
                    <div class="w-18 h-18 rounded-3xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mx-auto mb-5 shadow-xs border border-[#CCD8C7]">
                        <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#12271E] mb-2">Belum Ada Pendaftaran</h2>
                    <p class="text-xs sm:text-sm text-[#526057] max-w-md mx-auto mb-7 leading-relaxed">
                        Anda belum memiliki paket perjalanan ibadah aktif. Pilih paket umrah atau haji khusus untuk memulai proses pendaftaran bersama PT. Zein Internasional.
                    </p>
                    <a href="{{ route('jamaah.registration.create') }}" 
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md shadow-[#1B3B2B]/15">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span>Daftar Paket Sekarang</span>
                    </a>
                </div>
            @endif

        </div>
    </section>

    @push('scripts')
    @vite(['resources/js/ocr.js'])
    <script>
        function resubmitForm(initialName, initialNik, initialNoKk, initialPassport, initialRel, initialBirthPlace, initialBirthDate, initialGender, initialAddress) {
            return {
                form: {
                    name: initialName || '',
                    nik: initialNik || '',
                    no_kk: initialNoKk || '',
                    no_passport: initialPassport || '',
                    relationship: initialRel || '',
                    birth_place: initialBirthPlace || '',
                    birth_date: initialBirthDate || '',
                    gender: initialGender || '',
                    address: initialAddress || '',
                },
                ktpPreviewUrl: null,
                kkPreviewUrl: null,
                passportPreviewUrl: null,
                marriageBookPreviewUrl: null,
                birthCertificatePreviewUrl: null,
                ocrKtpStatus: 'idle',
                ocrKtpMessage: '',
                ocrKkStatus: 'idle',
                ocrKkMessage: '',
                ocrPassportStatus: 'idle',
                ocrPassportMessage: '',
                kkRawText: '',
                nikCrossCheckStatus: 'none',

                async handleKtpFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.ktpPreviewUrl = URL.createObjectURL(file);
                    this.ocrKtpStatus = 'scanning';
                    this.ocrKtpMessage = 'Sedang membaca dokumen KTP...';

                    try {
                        if (window.OcrScanner && window.OcrScanner.scanDocument) {
                            const res = await window.OcrScanner.scanDocument(file, 'ktp', (progress) => {
                                this.ocrKtpMessage = `Membaca data KTP... (${progress}%)`;
                            });

                            if (res.success && res.data) {
                                let count = 0;
                                if (res.data.nik && /^[0-9]{16}$/.test(res.data.nik)) {
                                    this.form.nik = res.data.nik;
                                    count++;
                                }
                                if (res.data.name) {
                                    this.form.name = res.data.name;
                                    count++;
                                }
                                if (res.data.birth_place) {
                                    this.form.birth_place = res.data.birth_place;
                                    count++;
                                }
                                if (res.data.birth_date) {
                                    this.form.birth_date = res.data.birth_date;
                                    count++;
                                }
                                if (res.data.gender) {
                                    this.form.gender = res.data.gender;
                                    count++;
                                }
                                if (res.data.address) {
                                    this.form.address = res.data.address;
                                    count++;
                                }

                                if (res.quality && !res.quality.isAcceptable && res.quality.warning) {
                                    this.ocrKtpStatus = 'quality_warning';
                                    this.ocrKtpMessage = `⚠ ${res.quality.warning} Mohon cocokkan NIK & Nama Anda secara teliti.`;
                                } else if (count > 0) {
                                    this.ocrKtpStatus = 'success';
                                    this.ocrKtpMessage = `✓ Data KTP berhasil dibaca (${count} kolom terisi otomatis). Silakan periksa atau edit jika diperlukan.`;
                                } else {
                                    this.ocrKtpStatus = 'warning';
                                    this.ocrKtpMessage = '⚠ NIK atau Nama belum terbaca dengan yakin. Silakan masukkan data secara manual.';
                                }

                                this.checkNikCrossMatch();
                            } else {
                                this.ocrKtpStatus = 'warning';
                                this.ocrKtpMessage = '⚠ Gagal membaca KTP otomatis. Silakan masukkan secara manual.';
                            }
                        }
                    } catch (err) {
                        console.error('KTP OCR Error:', err);
                        this.ocrKtpStatus = 'warning';
                        this.ocrKtpMessage = '⚠ Gagal memproses OCR KTP. Silakan masukkan secara manual.';
                    }
                },

                removeKtpFile() {
                    this.ktpPreviewUrl = null;
                    this.ocrKtpStatus = 'idle';
                    this.ocrKtpMessage = '';
                    const el = document.getElementById('resubmit_ktp_input');
                    if (el) el.value = '';
                },

                async handleKkFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.kkPreviewUrl = URL.createObjectURL(file);
                    this.ocrKkStatus = 'scanning';
                    this.ocrKkMessage = 'Sedang membaca dokumen KK...';

                    try {
                        if (window.OcrScanner && window.OcrScanner.scanDocument) {
                            const res = await window.OcrScanner.scanDocument(file, 'kk', (progress) => {
                                this.ocrKkMessage = `Membaca No. KK... (${progress}%)`;
                            });

                            if (res.success && res.data) {
                                if (res.data.no_kk && /^[0-9]{16}$/.test(res.data.no_kk)) {
                                    this.form.no_kk = res.data.no_kk;
                                    this.ocrKkStatus = 'success';
                                    this.ocrKkMessage = `✓ No. KK berhasil dibaca otomatis (${res.data.no_kk}).`;
                                } else {
                                    this.ocrKkStatus = 'warning';
                                    this.ocrKkMessage = '⚠ No. KK belum terbaca dengan yakin. Silakan masukkan secara manual.';
                                }

                                this.kkRawText = res.data.raw_text || '';
                                this.checkNikCrossMatch();
                            }
                        }
                    } catch (err) {
                        console.error('KK OCR Error:', err);
                        this.ocrKkStatus = 'warning';
                        this.ocrKkMessage = '⚠ Gagal memproses OCR KK. Silakan masukkan secara manual.';
                    }
                },

                removeKkFile() {
                    this.kkPreviewUrl = null;
                    this.ocrKkStatus = 'idle';
                    this.ocrKkMessage = '';
                    this.kkRawText = '';
                    this.nikCrossCheckStatus = 'none';
                    const el = document.getElementById('resubmit_kk_input');
                    if (el) el.value = '';
                },

                checkNikCrossMatch() {
                    if (this.form.nik && this.form.nik.length === 16 && this.kkRawText && window.OcrScanner) {
                        this.nikCrossCheckStatus = window.OcrScanner.crossCheckNik(this.form.nik, this.kkRawText);
                    } else {
                        this.nikCrossCheckStatus = 'none';
                    }
                },

                async handlePassportFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.passportPreviewUrl = URL.createObjectURL(file);
                    this.ocrPassportStatus = 'scanning';
                    this.ocrPassportMessage = 'Sedang membaca dokumen Paspor...';

                    try {
                        if (window.OcrScanner && window.OcrScanner.scanDocument) {
                            const res = await window.OcrScanner.scanDocument(file, 'passport', (progress) => {
                                this.ocrPassportMessage = `Membaca Paspor... (${progress}%)`;
                            });

                            if (res.success && res.data && res.data.no_passport) {
                                this.form.no_passport = res.data.no_passport;
                                this.ocrPassportStatus = 'success';
                                this.ocrPassportMessage = `✓ No. Paspor berhasil dibaca otomatis (${res.data.no_passport}).`;
                            } else {
                                this.ocrPassportStatus = 'idle';
                            }
                        }
                    } catch (err) {
                        console.error('Passport OCR Error:', err);
                        this.ocrPassportStatus = 'idle';
                    }
                },

                removePassportFile() {
                    this.passportPreviewUrl = null;
                    this.ocrPassportStatus = 'idle';
                    this.ocrPassportMessage = '';
                    const el = document.getElementById('resubmit_passport_input');
                    if (el) el.value = '';
                },

                handleMarriageBookFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.marriageBookPreviewUrl = URL.createObjectURL(file);
                },

                handleBirthCertificateFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.birthCertificatePreviewUrl = URL.createObjectURL(file);
                },
            }
        }
    </script>
    @endpush

</x-layouts.jamaah>
