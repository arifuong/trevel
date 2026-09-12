<x-layouts.admin :title="'Validasi Pembatalan Jamaah — PT. Zein Internasional'">

    <div class="space-y-6" x-data="{ 
        detailModal: false, 
        rejectModal: false,
        selectedCancellation: null,
        rejectUrl: '',
        jamaahName: '',
        openDetail(cancellation) {
            this.selectedCancellation = cancellation;
            this.detailModal = true;
            document.body.classList.add('overflow-hidden');
        },
        closeDetail() {
            this.detailModal = false;
            if (!this.rejectModal) {
                document.body.classList.remove('overflow-hidden');
            }
        },
        openReject(url, name) {
            this.rejectUrl = url;
            this.jamaahName = name;
            this.detailModal = false;
            this.rejectModal = true;
            document.body.classList.add('overflow-hidden');
        },
        closeReject() {
            this.rejectModal = false;
            document.body.classList.remove('overflow-hidden');
        }
    }">

        {{-- Header & Title --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#12271E] tracking-tight">
                    Validasi Pembatalan Jamaah
                </h1>
                <p class="text-xs sm:text-sm text-[#526057] mt-0.5">
                    Tinjau permohonan pembatalan pendaftaran dan verifikasi pengembalian dana jamaah.
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                    <span>{{ $pendingCount }} Menunggu Validasi</span>
                </span>
            </div>
        </div>

        {{-- Metric Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-4 shadow-2xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D5E54] block">Total Pengajuan</span>
                <span class="text-2xl font-bold text-[#12271E] block mt-1">{{ $totalCancellations }}</span>
                <span class="text-[11px] text-[#526057] mt-0.5 block">Semua Status</span>
            </div>
            <div class="bg-white rounded-2xl border border-amber-200 p-4 shadow-2xs bg-amber-50/20">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">Menunggu Validasi</span>
                <span class="text-2xl font-bold text-amber-900 block mt-1">{{ $pendingCount }}</span>
                <span class="text-[11px] text-amber-700 mt-0.5 block">Perlu Diproses</span>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-200 p-4 shadow-2xs bg-emerald-50/20">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Disetujui</span>
                <span class="text-2xl font-bold text-emerald-900 block mt-1">{{ $approvedCount }}</span>
                <span class="text-[11px] text-emerald-700 mt-0.5 block">Pendaftaran Dibatalkan</span>
            </div>
            <div class="bg-white rounded-2xl border border-red-200 p-4 shadow-2xs bg-red-50/20">
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-800 block">Ditolak</span>
                <span class="text-2xl font-bold text-red-900 block mt-1">{{ $rejectedCount }}</span>
                <span class="text-[11px] text-red-700 mt-0.5 block">Tetap Aktif</span>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('admin.cancellations.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#526057]/60">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama jamaah, email, paket, atau alasan..."
                           class="w-full pl-9.5 pr-4 py-2.5 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/50 focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                </div>

                {{-- Status Filter --}}
                <div class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" 
                            class="px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-xs font-semibold text-[#12271E] bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B]">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.cancellations.index') }}" 
                           class="p-2.5 rounded-xl border border-[#E0E7DC] text-xs font-semibold text-[#526057] hover:bg-[#EFF3EB] transition-colors" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- Cancellations Table --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#EFF3EB]/50 border-b border-[#E0E7DC] text-[#4D5E54] uppercase text-[10px] font-bold tracking-wider">
                            <th class="py-4 px-4 sm:px-6">Jamaah & Pendaftaran</th>
                            <th class="py-4 px-4">Paket Umrah</th>
                            <th class="py-4 px-4">Tanggal Pengajuan</th>
                            <th class="py-4 px-4 min-w-[200px]">Alasan Pembatalan</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E7DC]">
                        @forelse($cancellations as $cancel)
                            @php
                                $user = $cancel->user ?? $cancel->registration->user;
                                $pkg = $cancel->registration->package;
                                $reg = $cancel->registration;
                            @endphp
                            <tr class="hover:bg-[#EFF3EB]/30 transition-colors">
                                
                                {{-- Jamaah --}}
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="font-bold text-[#12271E] text-xs sm:text-sm">
                                        {{ $user->name ?? 'Jamaah' }}
                                    </div>
                                    <div class="text-[11px] text-[#526057] mt-0.5">
                                        {{ $user->phone_formatted ?? '-' }} &bull; {{ $reg->registration_number ?? '-' }}
                                    </div>
                                </td>

                                {{-- Paket --}}
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-[#12271E]">
                                        {{ $pkg->name ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-[#526057] mt-0.5">
                                        Keberangkatan: {{ $pkg->departure_date ? \Carbon\Carbon::parse($pkg->departure_date)->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>

                                {{-- Tanggal Pengajuan --}}
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-medium text-[#12271E]">
                                        {{ $cancel->requested_at ? $cancel->requested_at->translatedFormat('d M Y, H:i') : $cancel->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                </td>

                                {{-- Alasan Pembatalan (Tampil Jelas) --}}
                                <td class="py-4 px-4">
                                    <div class="p-2.5 rounded-xl bg-[#F8FAF7] border border-[#E0E7DC] max-w-sm">
                                        <p class="text-xs text-[#12271E] font-medium leading-relaxed">
                                            {{ $cancel->reason }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="py-4 px-4 text-center">
                                    @if($cancel->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            Disetujui
                                        </span>
                                        @if($cancel->processed_at)
                                            <div class="text-[10px] text-[#526057] mt-0.5">
                                                {{ $cancel->processed_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    @elseif($cancel->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Ditolak
                                        </span>
                                        @if($cancel->rejection_reason)
                                            <div class="mt-1.5 p-2 rounded-lg bg-red-50 border border-red-200 text-left max-w-xs mx-auto">
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan:</span>
                                                <p class="text-[11px] text-red-900 leading-snug font-medium">{{ $cancel->rejection_reason }}</p>
                                                @if($cancel->rejected_at)
                                                    <span class="text-[9px] text-red-600/80 block mt-0.5">{{ $cancel->rejected_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Menunggu Validasi
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Tombol Detail --}}
                                        <button type="button" 
                                                @click="openDetail({
                                                    id: {{ $cancel->id }},
                                                    reg_id: '{{ $reg->registration_number ?? '-' }}',
                                                    name: '{{ addslashes($user->name ?? '') }}',
                                                    package: '{{ addslashes($pkg->name ?? '') }}',
                                                    reason: '{{ addslashes($cancel->reason) }}',
                                                    status: '{{ $cancel->status }}',
                                                    status_label: '{{ $cancel->status_label }}',
                                                    requested_at: '{{ $cancel->requested_at ? $cancel->requested_at->translatedFormat('d F Y, H:i') : $cancel->created_at->translatedFormat('d F Y, H:i') }} WIB',
                                                    fee_category: '{{ addslashes($cancel->category ?? '-') }}',
                                                    fee_amount: '{{ $cancel->fee_amount_formatted }}',
                                                    refund_amount: '{{ $cancel->refund_amount_formatted }}',
                                                    rejection_reason: '{{ addslashes($cancel->rejection_reason ?? '') }}',
                                                    verify_url: '{{ route('admin.cancellations.verify', $cancel) }}'
                                                })"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        @if($cancel->status === 'pending')
                                            {{-- Approve Direct Button --}}
                                            <form method="POST" action="{{ route('admin.cancellations.verify', $cancel) }}" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" 
                                                        onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pembatalan ini? Pendaftaran akan diubah menjadi dibatalkan, kuota dikembalikan, dan jamaah dikeluarkan dari reminder pembayaran.')"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 active:scale-[0.98] transition-all shadow-xs cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            {{-- Reject Direct Button --}}
                                            <button type="button" 
                                                    @click="openReject('{{ route('admin.cancellations.verify', $cancel) }}', '{{ addslashes($user->name ?? '') }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 active:scale-[0.98] transition-all cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Tolak</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-[#526057]">
                                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#526057] flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="font-semibold text-sm text-[#12271E]">Tidak Ada Pengajuan Pembatalan</p>
                                    <p class="text-xs text-[#526057] mt-1">Belum ada jamaah yang mengajukan pembatalan pendaftaran.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cancellations->hasPages())
                <div class="p-4 border-t border-[#E0E7DC]">
                    {{ $cancellations->links() }}
                </div>
            @endif
        </div>

        {{-- Detail Modal (Fixed Viewport Overlay) --}}
        <template x-teleport="body">
            <div x-show="detailModal" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                 @keydown.escape.window="closeDetail()"
                 style="display: none;"
                 role="dialog"
                 aria-modal="true">
                
                {{-- Backdrop --}}
                <div x-show="detailModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#122B1F]/60 backdrop-blur-xs transition-opacity"
                     @click="closeDetail()"></div>

                {{-- Modal Card --}}
                <div x-show="detailModal"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-[#E0E7DC] z-10 my-auto max-h-[90vh] overflow-y-auto"
                     @click.outside="closeDetail()">
                
                <div class="flex items-center justify-between pb-3 border-b border-[#E0E7DC]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#12271E]">Detail Pembatalan</h3>
                            <p class="text-xs text-[#526057]" x-text="'Nomor Pendaftaran: ' + selectedCancellation?.reg_id"></p>
                        </div>
                    </div>
                    <button type="button" @click="detailModal = false" class="text-[#526057] hover:text-[#12271E] p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs text-[#526057]">
                    <div class="flex justify-between py-1 border-b border-[#E0E7DC]/60">
                        <span class="font-semibold text-[#4D5E54]">Nama Jamaah:</span>
                        <strong class="text-[#12271E] font-bold" x-text="selectedCancellation?.name"></strong>
                    </div>

                    <div class="flex justify-between py-1 border-b border-[#E0E7DC]/60">
                        <span class="font-semibold text-[#4D5E54]">Paket Umrah:</span>
                        <strong class="text-[#12271E] font-bold" x-text="selectedCancellation?.package"></strong>
                    </div>

                    <div class="flex justify-between py-1 border-b border-[#E0E7DC]/60">
                        <span class="font-semibold text-[#4D5E54]">Tanggal Pengajuan:</span>
                        <span class="text-[#12271E] font-medium" x-text="selectedCancellation?.requested_at"></span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-[#E0E7DC]/60">
                        <span class="font-semibold text-[#4D5E54]">Status Pengajuan:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                              :class="{
                                  'bg-amber-100 text-amber-800': selectedCancellation?.status === 'pending',
                                  'bg-emerald-100 text-emerald-800': selectedCancellation?.status === 'approved',
                                  'bg-red-100 text-red-800': selectedCancellation?.status === 'rejected'
                              }"
                              x-text="selectedCancellation?.status_label"></span>
                    </div>

                    <div class="space-y-1 pt-1">
                        <span class="font-semibold text-[#4D5E54] block">Alasan Pembatalan:</span>
                        <div class="p-3 rounded-xl bg-[#F8FAF7] border border-[#E0E7DC] text-[#12271E] font-medium leading-relaxed"
                             x-text="selectedCancellation?.reason"></div>
                    </div>

                    {{-- Rincian Biaya & Refund --}}
                    <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 space-y-2">
                        <div class="flex justify-between">
                            <span>Kategori:</span>
                            <strong class="text-[#12271E]" x-text="selectedCancellation?.fee_category"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Potongan Biaya:</span>
                            <strong class="text-red-700" x-text="selectedCancellation?.fee_amount"></strong>
                        </div>
                        <div class="flex justify-between pt-1.5 border-t border-amber-200/80 font-bold text-sm">
                            <span class="text-[#1B3B2B]">Estimasi Refund:</span>
                            <span class="text-emerald-800" x-text="selectedCancellation?.refund_amount"></span>
                        </div>
                    </div>

                    <template x-if="selectedCancellation?.status === 'rejected' && selectedCancellation?.rejection_reason">
                        <div class="p-3 rounded-xl bg-red-50 border border-red-200 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block">Alasan Penolakan Admin:</span>
                            <p class="text-red-900 font-medium leading-relaxed" x-text="selectedCancellation?.rejection_reason"></p>
                        </div>
                    </template>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-3 border-t border-[#E0E7DC]">
                    <button type="button" @click="detailModal = false" 
                            class="px-4 py-2.5 rounded-xl border border-[#E0E7DC] text-xs font-semibold text-[#526057] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                        Tutup
                    </button>

                    <template x-if="selectedCancellation?.status === 'pending'">
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="openReject(selectedCancellation?.verify_url, selectedCancellation?.name)"
                                    class="px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 text-xs font-bold transition-colors cursor-pointer">
                                Tolak Pembatalan
                            </button>

                            <form :action="selectedCancellation?.verify_url" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pembatalan ini? Pendaftaran akan diubah menjadi dibatalkan dan kuota kursi dikembalikan.')"
                                        class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                    Setujui Pembatalan
                                </button>
                            </form>
                        </div>
                    </template>
                </div>

            </div>
        </template>

        {{-- Reject Modal (Fixed Viewport Overlay) --}}
        <template x-teleport="body">
            <div x-show="rejectModal" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                 @keydown.escape.window="closeReject()"
                 style="display: none;"
                 role="dialog"
                 aria-modal="true">
                
                {{-- Backdrop --}}
                <div x-show="rejectModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#122B1F]/60 backdrop-blur-xs transition-opacity"
                     @click="closeReject()"></div>

                {{-- Modal Card --}}
                <div x-show="rejectModal"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-[#E0E7DC] z-10 my-auto"
                     @click.outside="closeReject()">
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#12271E]">Tolak Pembatalan</h3>
                            <p class="text-xs text-[#526057]" x-text="'Jamaah: ' + jamaahName"></p>
                        </div>
                    </div>

                    <form :action="rejectUrl" method="POST" class="space-y-4"
                          @submit="if(!$refs.rejectReasonInput.value.trim()){ alert('Alasan penolakan pengajuan pembatalan wajib diisi.'); $event.preventDefault(); }">
                        @csrf
                        <input type="hidden" name="action" value="reject">

                        <div>
                            <label for="rejection_reason" class="block text-[11px] font-semibold text-[#4D5E54] uppercase tracking-wider mb-1.5">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="rejection_reason" name="rejection_reason" x-ref="rejectReasonInput" rows="4" required minlength="5" maxlength="1000"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-[#E0E7DC] text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/50 focus:outline-none focus:ring-2 focus:ring-red-400/20 focus:border-red-400"
                                      placeholder="Contoh: Pembatalan tidak dapat diproses karena dokumen visa dan tiket penerbangan non-refundable sudah diterbitkan."></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#E0E7DC]">
                            <button type="button" @click="closeReject()" 
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
        </template>

    </div>

</x-layouts.admin>
