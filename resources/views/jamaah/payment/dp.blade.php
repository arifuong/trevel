<x-layouts.jamaah :title="'Pembayaran Uang Muka (DP) — PT. Zein Internasional'">

    <section class="py-6 sm:py-10 min-h-[calc(100vh-140px)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

            {{-- Breadcrumb / Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-[#E0E7DC]/60" style="animation: fadeSlideUp 0.4s ease both">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">Pembayaran Uang Muka (DP)</h1>
                    <p class="text-xs sm:text-sm text-[#526057] mt-1">Lakukan transfer ke rekening resmi PT. Zein Internasional dan unggah bukti transfer Anda.</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('jamaah.my-registration') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-[#12271E] bg-white border border-[#E0E7DC] hover:border-[#1B3B2B] hover:bg-[#EFF3EB]/50 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Pendaftaran Saya</span>
                    </a>
                </div>
            </div>

            {{-- Flash Errors --}}
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-800 shadow-2xs">
                    <div class="font-bold mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <span>Terdapat kendala pada formulir:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
                
                {{-- Kolom Kiri: Form Upload Bukti (7 Kolom) --}}
                <div class="lg:col-span-7 space-y-6 min-w-0">

                    {{-- Card Alasan Penolakan Jika Ada --}}
                    @if(isset($lastRejectedDp) && $lastRejectedDp)
                        <div class="p-5 rounded-3xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                            <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <span>⚠ Pembayaran DP Sebelumnya Ditolak</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-white border border-red-200 space-y-1 shadow-2xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan dari Admin:</span>
                                <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $lastRejectedDp->rejection_reason }}</p>
                                @if($lastRejectedDp->rejected_at)
                                    <span class="text-[10px] text-red-600/80 block mt-0.5">{{ $lastRejectedDp->rejected_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                @endif
                            </div>
                            <p class="text-red-800 font-medium leading-relaxed">
                                Silakan perbaiki pembayaran Anda dan unggah bukti transfer yang baru dan valid di bawah ini.
                            </p>
                        </div>
                    @endif

                    <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs" style="animation: fadeSlideUp 0.4s ease 0.1s both">
                        <h2 class="text-base sm:text-lg font-bold text-[#12271E] mb-4 pb-3 border-b border-[#E0E7DC]">
                            Unggah Bukti Pembayaran DP
                        </h2>

                        <form method="POST" action="{{ route('jamaah.payment.dp.store') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            {{-- Nominal Pembayaran --}}
                            <div>
                                <label for="amount" class="block text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                                    Nominal Pembayaran DP (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-[#1B3B2B]">Rp</span>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount', $recommendedDp) }}" required min="1000000" step="10000"
                                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-[#E0E7DC] text-sm font-bold text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                                </div>
                                <p class="text-[11px] text-[#526057] mt-1.5 leading-relaxed">
                                    Nominal DP yang dianjurkan: <strong class="text-[#1B3B2B]">Rp {{ number_format($recommendedDp, 0, ',', '.') }}</strong> (Rp 5.000.000 &times; {{ $registration->members->count() }} Jamaah)
                                </p>
                            </div>

                            {{-- Upload Bukti Transfer --}}
                            <div>
                                <label for="proof_file" class="block text-[11px] font-bold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                                    Unggah Bukti Transfer Bank <span class="text-red-500">*</span>
                                </label>
                                <input type="file" id="proof_file" name="proof_file" required accept=".jpg,.jpeg,.png,.pdf"
                                       class="w-full text-xs text-[#526057] file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#1B3B2B] file:text-white hover:file:bg-[#132E22] file:cursor-pointer border border-[#E0E7DC] bg-[#F8FAF7] rounded-xl p-1.5">
                                <p class="text-[10px] text-[#526057] mt-1">Format: JPG, PNG, atau PDF (Maksimal 3MB). Pastikan tanggal transfer, nomor rekening tujuan, dan nominal terlihat jelas.</p>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-3 border-t border-[#E0E7DC]">
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md shadow-[#1B3B2B]/15 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                    <span>Kirim Bukti Pembayaran DP</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Kolom Kanan: Rekening Tujuan & Ringkasan (5 Kolom) --}}
                <div class="lg:col-span-5 space-y-6 min-w-0">
                    
                    {{-- Rekening Resmi --}}
                    <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs" style="animation: fadeSlideUp 0.4s ease 0.15s both">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">Rekening Resmi Pembayaran</span>
                        <h3 class="text-base font-bold text-[#12271E] mb-4 pb-2 border-b border-[#E0E7DC]">
                            PT. Zein Internasional Travel
                        </h3>

                        <div class="space-y-3.5">
                            
                            {{-- Bank Syariah Indonesia (BSI) --}}
                            <div class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/50">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-emerald-950">Bank Syariah Indonesia (BSI)</span>
                                    <span class="text-[10px] font-semibold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full border border-emerald-200">Syariah</span>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-emerald-200/60">
                                    <span class="font-mono text-sm sm:text-base font-bold text-[#12271E]">7123 4567 89</span>
                                    <span class="text-[11px] text-[#526057]">a.n. PT Zein Internasional</span>
                                </div>
                            </div>

                            {{-- Bank Mandiri --}}
                            <div class="p-4 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7]">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-[#12271E]">Bank Mandiri</span>
                                    <span class="text-[10px] font-semibold bg-[#EFF3EB] text-[#526057] px-2.5 py-0.5 rounded-full border border-[#CCD8C7]">Operasional</span>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#E0E7DC]">
                                    <span class="font-mono text-sm sm:text-base font-bold text-[#12271E]">1300 0123 4567 8</span>
                                    <span class="text-[11px] text-[#526057]">a.n. PT Zein Internasional</span>
                                </div>
                            </div>

                        </div>

                        <div class="mt-4 p-3.5 rounded-2xl bg-[#EFF3EB] border border-[#CCD8C7] text-xs text-[#526057] space-y-1">
                            <p class="font-bold text-[#12271E]">💡 Petunjuk Pembayaran:</p>
                            <p class="leading-relaxed text-[11.5px]">Tuliskan nama pemesan akun pada berita transfer untuk mempercepat proses verifikasi oleh tim kami.</p>
                        </div>
                    </div>

                    {{-- Bantuan WhatsApp --}}
                    <div class="bg-white rounded-3xl border border-[#E0E7DC] p-5 sm:p-6 shadow-xs flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-[#12271E] block">Mengalami Kendala Transfer?</span>
                            <span class="text-[11px] text-[#526057]">Konsultasikan bersama tim kami</span>
                        </div>
                        <a href="https://wa.me/6281222222562?text=Assalamu%27alaikum,%20saya%20butuh%20panduan%20transfer%20DP" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors shrink-0 shadow-2xs">
                            Chat CS
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </section>

</x-layouts.jamaah>
