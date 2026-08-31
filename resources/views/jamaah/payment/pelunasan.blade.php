<x-layouts.jamaah :title="'Setor Pelunasan / Tabungan Umrah — PT. Zein Internasional'">

    <section class="py-6 sm:py-10 min-h-[calc(100vh-140px)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb / Header --}}
            <div class="mb-8" style="animation: fadeSlideUp 0.4s ease both">
                <a href="{{ route('jamaah.my-registration') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1B3B2B] hover:underline mb-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>Kembali ke Pendaftaran Saya</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">Setor Pelunasan / Tabungan Umrah</h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-1">Setor cicilan pelunasan secara bertahap dengan nominal bebas kapan saja sesuai kemampuan Anda.</p>
            </div>

            {{-- Flash Messages --}}
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-800 shadow-xs">
                    <div class="font-bold mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        Terdapat kendala pada formulir:
                    </div>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] sm:text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $totalPrice = (float) ($registration->invoice->total_price ?? 0);
                $totalPaid = (float) ($registration->invoice->total_paid ?? 0);
                $remainingBalance = (float) ($registration->invoice->remaining_balance ?? 0);
                $percentage = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
            @endphp

            {{-- Progress Card --}}
            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs mb-8" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block">Progres Pembayaran Paket</span>
                        <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">{{ $registration->package->name }}</h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block">Sisa Pembayaran</span>
                        <span class="text-xl sm:text-2xl font-bold text-[#1B3B2B]">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-2">
                    <div class="w-full bg-[#EFF3EB] h-3 rounded-full overflow-hidden">
                        <div class="bg-[#1B3B2B] h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-[#526057]">
                        <span>Sudah Dibayar: <strong class="text-emerald-700">Rp {{ number_format($totalPaid, 0, ',', '.') }} ({{ $percentage }}%)</strong></span>
                        <span>Total Biaya: <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" x-data="{ amount: '' }">
                
                {{-- Kolom Kiri: Form Setor Pelunasan (7 Kolom) --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Card Alasan Penolakan Jika Ada --}}
                    @if(isset($lastRejectedPelunasan) && $lastRejectedPelunasan)
                        <div class="p-5 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-3 shadow-2xs" style="animation: fadeSlideUp 0.4s ease 0.05s both">
                            <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <span>⚠ Setoran Pelunasan Sebelumnya Ditolak</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white border border-red-200 space-y-1 shadow-2xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan dari Admin:</span>
                                <p class="text-xs text-[#12271E] font-medium leading-relaxed">{{ $lastRejectedPelunasan->rejection_reason }}</p>
                                @if($lastRejectedPelunasan->rejected_at)
                                    <span class="text-[10px] text-red-600/80 block mt-0.5">{{ $lastRejectedPelunasan->rejected_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                @endif
                            </div>
                            <p class="text-red-800 font-medium leading-relaxed">
                                Silakan perbaiki nominal atau bukti transfer dan kirimkan kembali di bawah ini.
                            </p>
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs" style="animation: fadeSlideUp 0.4s ease 0.1s both">
                        <h3 class="text-base sm:text-lg font-bold text-[#12271E] mb-4 pb-3 border-b border-[#E0E7DC]">
                            Unggah Bukti Setoran Pelunasan
                        </h3>

                        <form method="POST" action="{{ route('jamaah.payment.pelunasan.store') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            {{-- Nominal Transfer --}}
                            <div>
                                <label for="amount" class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                                    Nominal Transfer (Rp) <span class="text-red-500">* (Nominal Bebas)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-[#1B3B2B]">Rp</span>
                                    <input type="number" id="amount" name="amount" x-model="amount" required min="100000" max="{{ $remainingBalance }}" step="10000"
                                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-[#E0E7DC] text-sm font-bold text-[#12271E] focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]"
                                           placeholder="Contoh: 5000000">
                                </div>
                                <p class="text-[11px] text-[#526057] mt-1">
                                    Minimal setoran Rp 100.000 &bull; Maksimal Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                                </p>

                                {{-- Pilihan Cepat Nominal --}}
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <button type="button" @click="amount = 2000000" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] transition-colors cursor-pointer">
                                        + Rp 2 Jt
                                    </button>
                                    <button type="button" @click="amount = 5000000" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] transition-colors cursor-pointer">
                                        + Rp 5 Jt
                                    </button>
                                    <button type="button" @click="amount = 10000000" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] transition-colors cursor-pointer">
                                        + Rp 10 Jt
                                    </button>
                                    <button type="button" @click="amount = {{ $remainingBalance }}" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors cursor-pointer">
                                        Lunasi Semua
                                    </button>
                                </div>
                            </div>

                            {{-- Upload Bukti Transfer --}}
                            <div>
                                <label for="proof_file" class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                                    Unggah Bukti Transfer Bank <span class="text-red-500">*</span>
                                </label>
                                <input type="file" id="proof_file" name="proof_file" required accept=".jpg,.jpeg,.png,.pdf"
                                       class="w-full text-xs text-[#526057] file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#1B3B2B] file:text-white hover:file:bg-[#132E22] file:cursor-pointer border border-[#E0E7DC] bg-[#F8FAF7] rounded-xl p-2">
                                <p class="text-[10px] text-[#526057] mt-1">Format: JPG, PNG, atau PDF (Maksimal 3MB). Pastikan struk atau bukti transfer terbaca jelas.</p>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-4 border-t border-[#E0E7DC]">
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Kirim Bukti Pembayaran</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Kolom Kanan: Rekening Tujuan & Info (5 Kolom) --}}
                <div class="lg:col-span-5 space-y-6">
                    
                    {{-- Rekening Resmi --}}
                    <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 shadow-xs" style="animation: fadeSlideUp 0.4s ease 0.15s both">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block mb-1">Rekening Resmi Pembayaran</span>
                        <h3 class="text-base font-bold text-[#12271E] mb-4 pb-2 border-b border-[#E0E7DC]">
                            PT. Zein Internasional Travel
                        </h3>

                        <div class="space-y-3.5">
                            
                            {{-- Bank Syariah Indonesia (BSI) --}}
                            <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-emerald-950">Bank Syariah Indonesia (BSI)</span>
                                    <span class="text-[10px] font-semibold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">Syariah</span>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-mono text-sm sm:text-base font-bold text-[#12271E]">7123 4567 89</span>
                                    <span class="text-[11px] text-[#526057]">a.n. PT Zein Internasional</span>
                                </div>
                            </div>

                            {{-- Bank Mandiri --}}
                            <div class="p-3.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7]">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-[#12271E]">Bank Mandiri</span>
                                    <span class="text-[10px] font-semibold bg-[#EFF3EB] text-[#526057] px-2 py-0.5 rounded-full">Operasional</span>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-mono text-sm sm:text-base font-bold text-[#12271E]">1300 0123 4567 8</span>
                                    <span class="text-[11px] text-[#526057]">a.n. PT Zein Internasional</span>
                                </div>
                            </div>

                        </div>

                        <div class="mt-4 p-3 rounded-xl bg-[#EFF3EB] border border-[#E0E7DC] text-[11px] text-[#526057] space-y-1">
                            <p class="font-bold text-[#12271E]">💡 Fitur Tabungan Umrah Bebas:</p>
                            <p>Anda dapat melakukan setoran berulang kali tanpa batas frekuensi hingga saldo mencapai lunas sebelum batas waktu keberangkatan.</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

</x-layouts.jamaah>
