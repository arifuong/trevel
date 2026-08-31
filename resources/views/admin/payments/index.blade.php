<x-layouts.admin :title="'Verifikasi Pembayaran — Admin PT. Zein Internasional'">

    <div x-data="{
        previewModal: false,
        previewUrl: '',
        previewTitle: '',
        isPdf: false,
        rejectModal: false,
        rejectUrl: '',
        paymentDesc: '',
        openPreview(url, title) {
            this.previewUrl = url;
            this.previewTitle = title;
            this.isPdf = url.toLowerCase().endsWith('.pdf');
            this.previewModal = true;
        },
        openReject(url, desc) {
            this.rejectUrl = url;
            this.paymentDesc = desc;
            this.rejectModal = true;
        }
    }">

        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="animation: fadeSlideUp 0.4s ease both">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">Verifikasi Pembayaran</h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-1">Periksa dan verifikasi bukti transfer pembayaran uang muka (DP) dan pelunasan jamaah.</p>
            </div>
        </div>

        {{-- Ringkasan Metrik --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" style="animation: fadeSlideUp 0.4s ease 0.05s both">
            <div class="bg-white rounded-xl border border-[#E0E7DC] p-4 flex items-center justify-between shadow-2xs">
                <div>
                    <span class="text-[11px] text-[#526057] block font-medium">Total Pembayaran Masuk</span>
                    <span class="text-xl font-bold text-[#12271E] mt-0.5 block">{{ $totalPayments }}</span>
                </div>
                <div class="w-9 h-9 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-amber-200 p-4 flex items-center justify-between shadow-2xs">
                <div>
                    <span class="text-[11px] text-amber-800 block font-semibold">Menunggu Verifikasi</span>
                    <span class="text-xl font-bold text-amber-900 mt-0.5 block">{{ $pendingPaymentsCount }}</span>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-emerald-200 p-4 flex items-center justify-between shadow-2xs">
                <div>
                    <span class="text-[11px] text-emerald-800 block font-semibold">Sudah Diverifikasi</span>
                    <span class="text-xl font-bold text-emerald-900 mt-0.5 block">{{ $approvedPaymentsCount }}</span>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-2xl border border-[#E0E7DC] shadow-xs mb-6 overflow-hidden" style="animation: fadeSlideUp 0.4s ease 0.1s both">
            <div class="p-4 sm:p-5">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-center gap-3">
                    
                    <div class="w-full sm:w-48">
                        <select name="status" class="w-full px-3.5 py-2 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                            <option value="">Semua Status</option>
                            <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-44">
                        <select name="type" class="w-full px-3.5 py-2 rounded-xl border border-[#E0E7DC] text-xs text-[#12271E] bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20">
                            <option value="">Semua Jenis</option>
                            <option value="dp" {{ request('type') === 'dp' ? 'selected' : '' }}>Uang Muka (DP)</option>
                            <option value="pelunasan" {{ request('type') === 'pelunasan' ? 'selected' : '' }}>Pelunasan / Tabungan</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="py-2 px-4 rounded-xl text-xs font-semibold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-colors cursor-pointer">
                            Cari
                        </button>
                        @if(request('status') || request('type'))
                            <a href="{{ route('admin.payments.index') }}" class="p-2 rounded-xl border border-[#E0E7DC] text-xs text-[#526057] hover:bg-[#EFF3EB]">
                                Reset
                            </a>
                        @endif
                    </div>

                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto border-t border-[#E0E7DC]">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-[#EFF3EB] text-[#12271E] uppercase text-[10px] sm:text-[11px] font-bold tracking-wider border-b border-[#E0E7DC]">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">No. Pendaftaran & Jamaah</th>
                            <th class="py-3.5 px-4">Paket & Jenis</th>
                            <th class="py-3.5 px-4">Nominal</th>
                            <th class="py-3.5 px-4">Bukti Transfer</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        @forelse($payments as $payment)
                            @php
                                $reg = $payment->registration;
                            @endphp
                            <tr class="hover:bg-[#F8FAF7] transition-colors">
                                <td class="py-4 px-4 sm:px-6">
                                    <span class="font-mono font-bold text-[#12271E]">{{ $reg->registration_number ?? '-' }}</span>
                                    <div class="font-semibold text-[#12271E] mt-0.5">{{ $reg->user->name ?? 'Jamaah' }}</div>
                                    <div class="text-[11px] text-[#526057] font-mono">{{ $reg->user->phone ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-[#12271E]">{{ $reg->package->name ?? '-' }}</div>
                                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-[#EFF3EB] text-[#1B3B2B] mt-1">
                                        {{ $payment->type_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-bold text-sm text-[#1B3B2B]">{{ $payment->amount_formatted }}</span>
                                    <div class="text-[10px] text-[#526057] mt-0.5">
                                        {{ $payment->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($payment->proof_url)
                                        <button type="button" @click="openPreview('{{ $payment->proof_url }}', 'Bukti {{ $payment->type_label }} — {{ addslashes($reg->user->name ?? '') }}')"
                                                class="inline-flex items-center gap-1 py-1.5 px-2.5 rounded-lg text-xs font-semibold text-[#1B3B2B] bg-white border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-colors shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Lihat Struk</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-red-500 font-semibold">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($payment->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            Sudah Diverifikasi
                                        </span>
                                        @if($payment->verified_at)
                                            <div class="text-[10px] text-[#526057] mt-0.5">
                                                {{ $payment->verified_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    @elseif($payment->status === 'ditolak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Ditolak
                                        </span>
                                        @if($payment->rejection_reason)
                                            <div class="mt-1.5 p-2 rounded-lg bg-red-50 border border-red-200 text-left max-w-xs mx-auto">
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-red-700 block">Alasan:</span>
                                                <p class="text-[11px] text-red-900 leading-snug font-medium">{{ $payment->rejection_reason }}</p>
                                                @if($payment->rejected_at)
                                                    <span class="text-[9px] text-red-600/80 block mt-0.5">{{ $payment->rejected_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    @if($payment->status === 'menunggu_verifikasi')
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Approve Button --}}
                                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini? Status pendaftaran akan diperbarui.')"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 active:scale-[0.98] transition-all shadow-xs cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            {{-- Reject Button --}}
                                            <button type="button" @click="openReject('{{ route('admin.payments.verify', $payment) }}', 'Pembayaran {{ $payment->amount_formatted }} an. {{ addslashes($reg->user->name ?? '') }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 active:scale-[0.98] transition-all cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Tolak</span>
                                            </button>
                                        </div>
                                    @elseif($payment->status === 'disetujui')
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('documents.receipt', $payment) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-all shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Lihat Kwitansi</span>
                                            </a>
                                            <a href="{{ route('documents.receipt.download', $payment) }}" 
                                               title="Unduh File Excel Kwitansi Resmi"
                                               class="inline-flex items-center justify-center p-1.5 rounded-xl text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-all shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-xs text-red-600 font-medium italic">Pembayaran Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-[#526057]">
                                    <p class="font-semibold text-sm text-[#12271E]">Tidak Ada Data Pembayaran</p>
                                    <p class="text-xs text-[#526057] mt-1">Belum ada bukti transfer yang dikirim oleh jamaah.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="p-4 border-t border-[#E0E7DC]">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

        {{-- Preview Modal --}}
        <div x-show="previewModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden"
                 @click.away="previewModal = false">
                
                <div class="px-6 py-4 bg-[#1B3B2B] text-white flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-bold truncate" x-text="previewTitle"></h3>
                    <div class="flex items-center gap-3">
                        <a :href="previewUrl" target="_blank" download class="text-xs text-white/80 hover:text-white underline">
                            Buka File Asli
                        </a>
                        <button type="button" @click="previewModal = false" class="text-white/80 hover:text-white p-1 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="p-4 flex-1 overflow-auto flex items-center justify-center bg-zinc-900 min-h-[400px]">
                    <template x-if="!isPdf">
                        <img :src="previewUrl" :alt="previewTitle" class="max-h-[70vh] max-w-full object-contain rounded-lg shadow-md">
                    </template>
                    <template x-if="isPdf">
                        <iframe :src="previewUrl" class="w-full h-[70vh] rounded-lg bg-white"></iframe>
                    </template>
                </div>

                <div class="px-6 py-3 bg-white border-t border-[#E0E7DC] flex justify-end">
                    <button type="button" @click="previewModal = false" 
                            class="px-5 py-2 rounded-xl bg-[#1B3B2B] text-white text-xs font-semibold hover:bg-[#132E22] transition-colors cursor-pointer">
                        Tutup Pratinjau
                    </button>
                </div>

            </div>
        </div>

        {{-- Reject Modal --}}
        <div x-show="rejectModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-[#E0E7DC]"
                 @click.away="rejectModal = false">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#12271E]">Tolak Bukti Pembayaran</h3>
                        <p class="text-xs text-[#526057]" x-text="paymentDesc"></p>
                    </div>
                </div>

                <form :action="rejectUrl" method="POST" class="space-y-4"
                      @submit="if(!$refs.reasonInput.value.trim()){ alert('Alasan penolakan bukti pembayaran wajib diisi.'); $event.preventDefault(); }">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div>
                        <label for="rejection_reason" class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" x-ref="reasonInput" rows="4" required
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/50 focus:outline-none focus:ring-2 focus:ring-red-400/20 focus:border-red-400"
                                  placeholder="Contoh: Nominal transfer pada struk tidak sesuai dengan jumlah tagihan. Silakan kirimkan bukti transfer yang valid."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#E0E7DC]">
                        <button type="button" @click="rejectModal = false" 
                                class="px-4 py-2.5 rounded-xl border border-[#E0E7DC] text-xs font-semibold text-[#526057] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</x-layouts.admin>
