<x-layouts.admin :title="'Verifikasi Dokumen ' . $registration->registration_number . ' — Admin'">

    @php
        $membersJson = $registration->members->map(function($m) use ($registration) {
            $isOwner = ($m->relationship === 'diri_sendiri');
            return [
                'id' => $m->id,
                'name' => $m->name,
                'relationship' => $m->relationship,
                'relationship_label' => $m->relationship_label,
                'nik' => $m->nik,
                'no_kk' => $m->no_kk,
                'no_passport' => $m->no_passport ?? '',
                'ktp_url' => $m->ktp_url ?? '',
                'kk_url' => $m->kk_url ?? '',
                'passport_url' => $m->passport_url ?? '',
                'marriage_book_url' => $m->marriage_book_url ?? '',
                'birth_certificate_url' => $m->birth_certificate_url ?? '',
                'document_status' => $m->document_status,
                'document_status_label' => $m->document_status_label,
                'rejection_reason' => $m->rejection_reason ?? '',
                'verified_at' => $m->verified_at ? $m->verified_at->translatedFormat('d M Y, H:i') . ' WIB' : '',
                'verified_by_name' => $m->verifiedByAdmin->name ?? '',
                'verify_url' => route('admin.members.verify', $m),
                'birth_place' => $m->birth_place ?? ($isOwner ? ($registration->user->birth_place ?? '-') : '-'),
                'birth_date' => $m->birth_date ? $m->birth_date->translatedFormat('d F Y') : ($isOwner && $registration->user->birth_date ? $registration->user->birth_date->translatedFormat('d F Y') : '-'),
                'gender' => ($m->gender ? ($m->gender === 'laki-laki' ? 'Laki-laki' : ($m->gender === 'perempuan' ? 'Perempuan' : ucfirst($m->gender))) : ($isOwner ? ($registration->user->gender === 'laki-laki' ? 'Laki-laki' : ($registration->user->gender === 'perempuan' ? 'Perempuan' : '-')) : '-')),
                'address' => $m->address ?? ($isOwner ? ($registration->user->address ?? '-') : '-'),
                'phone' => $isOwner ? ($registration->user->phone ?? '-') : '-',
            ];
        })->values()->toJson();
    @endphp

    <div x-data="{
        currentPhaseTab: '{{ in_array($registration->status, [\App\Models\Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN, \App\Models\Registration::STATUS_BERANGKAT, \App\Models\Registration::STATUS_SELESAI]) ? 'keberangkatan' : 'awal' }}',
        completeModal: false,
        departureRejectModal: false,
        departureRejectAction: '',
        departureRejectDocName: '',
        departureRejectReason: '',
        openDepartureReject(actionUrl, docName) {
            this.departureRejectAction = actionUrl;
            this.departureRejectDocName = docName;
            this.departureRejectReason = '';
            this.departureRejectModal = true;
        },
        splitViewer: false,
        members: {{ $membersJson }},
        activeMemberId: {{ $registration->members->first()?->id ?? 'null' }},
        activeDoc: 'ktp', // 'ktp', 'kk', 'passport', 'marriage', 'birth'
        zoomLevel: 100,
        rotation: 0,
        rejectMode: false,
        rejectionReason: '',

        get activeMember() {
            return this.members.find(m => m.id === this.activeMemberId) || this.members[0] || {};
        },

        get currentDocUrl() {
            const m = this.activeMember;
            if (!m) return '';
            if (this.activeDoc === 'ktp') return m.ktp_url || '';
            if (this.activeDoc === 'kk') return m.kk_url || '';
            if (this.activeDoc === 'passport') return m.passport_url || '';
            if (this.activeDoc === 'marriage') return m.marriage_book_url || '';
            if (this.activeDoc === 'birth') return m.birth_certificate_url || '';
            return '';
        },

        get currentDocTitle() {
            const m = this.activeMember;
            if (!m) return '';
            if (this.activeDoc === 'ktp') return 'Foto KTP — ' + m.name;
            if (this.activeDoc === 'kk') return 'Kartu Keluarga (KK) — ' + m.name;
            if (this.activeDoc === 'passport') return 'Paspor — ' + m.name;
            if (this.activeDoc === 'marriage') return 'Buku Nikah — ' + m.name;
            if (this.activeDoc === 'birth') return 'Akta Kelahiran — ' + m.name;
            return 'Dokumen — ' + m.name;
        },

        get isPdf() {
            return this.currentDocUrl.toLowerCase().endsWith('.pdf');
        },

        openSplitViewer(memberId, docType = 'ktp') {
            this.activeMemberId = memberId;
            this.activeDoc = docType;
            this.zoomLevel = 100;
            this.rotation = 0;
            this.rejectMode = false;
            this.rejectionReason = '';
            this.splitViewer = true;
            document.body.classList.add('overflow-hidden');
        },

        closeSplitViewer() {
            this.splitViewer = false;
            this.zoomLevel = 100;
            this.rotation = 0;
            this.rejectMode = false;
            document.body.classList.remove('overflow-hidden');
        },

        setDoc(type) {
            this.activeDoc = type;
            this.zoomLevel = 100;
            this.rotation = 0;
        },

        zoomIn() {
            if (this.zoomLevel < 300) this.zoomLevel += 25;
        },

        zoomOut() {
            if (this.zoomLevel > 50) this.zoomLevel -= 25;
        },

        resetZoom() {
            this.zoomLevel = 100;
            this.rotation = 0;
        },

        rotate() {
            this.rotation = (this.rotation + 90) % 360;
        }
    }">

        {{-- Breadcrumb & Back --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="animation: fadeSlideUp 0.4s ease both">
            <div class="flex items-center gap-2 text-xs text-[#526057]">
                <a href="{{ route('admin.registrations.index') }}" class="hover:text-[#1B3B2B] flex items-center gap-1 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>Kembali ke Daftar Pendaftaran</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-[#526057]">Status Pendaftaran:</span>
                <span class="px-3 py-1 text-xs font-bold rounded-full 
                    {{ $registration->status === 'selesai' ? 'bg-purple-100 text-purple-800 border border-purple-200' : ($registration->status === 'berangkat' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($registration->status === 'menunggu_pembayaran_dp' ? 'bg-blue-100 text-blue-800' : ($registration->status === 'menunggu_verifikasi_dokumen' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'))) }}">
                    {{ $registration->status_label }}
                </span>
            </div>
        </div>

        {{-- Header Summary Card --}}
        <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs mb-8" style="animation: fadeSlideUp 0.4s ease 0.05s both">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-8">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-[#12271E]">
                            Pendaftaran {{ $registration->registration_number }}
                        </h1>
                        <span class="text-xs font-mono bg-[#EFF3EB] text-[#1B3B2B] px-2.5 py-0.5 rounded-lg">
                            {{ $registration->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                    </div>

                    <p class="text-xs text-[#526057] mb-4">
                        Pemesan Akun: <strong class="text-[#12271E]">{{ $registration->user->name }}</strong> &bull; Email: {{ $registration->user->email }} &bull; Nomor WhatsApp: {{ $registration->user->phone }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs">
                        <div class="bg-[#EFF3EB] px-3.5 py-2 rounded-xl border border-[#E0E7DC]">
                            <span class="text-[#4D5E54] block text-[10px] uppercase font-bold">Paket Dipilih</span>
                            <strong class="text-[#12271E] text-sm">{{ $registration->package->name }}</strong>
                        </div>
                        @if($registration->packageVariant)
                        <div class="bg-[#EFF3EB] px-3.5 py-2 rounded-xl border border-[#E0E7DC]">
                            <span class="text-[#4D5E54] block text-[10px] uppercase font-bold">Sub-Paket / Varian</span>
                            <strong class="text-[#12271E] text-sm">{{ $registration->packageVariant->name }}</strong>
                        </div>
                        @endif
                        @if($registration->room_type)
                        <div class="bg-[#EFF3EB] px-3.5 py-2 rounded-xl border border-[#E0E7DC]">
                            <span class="text-[#4D5E54] block text-[10px] uppercase font-bold">Tipe Kamar</span>
                            <strong class="text-[#12271E] text-sm">{{ ucfirst($registration->room_type) }}</strong>
                        </div>
                        @endif
                        <div class="bg-[#EFF3EB] px-3.5 py-2 rounded-xl border border-[#E0E7DC]">
                            <span class="text-[#4D5E54] block text-[10px] uppercase font-bold">Keberangkatan</span>
                            <strong class="text-[#12271E] text-sm">{{ $registration->package->departure_date ? $registration->package->departure_date->translatedFormat('d M Y') : '-' }}</strong>
                        </div>
                        <div class="bg-[#EFF3EB] px-3.5 py-2 rounded-xl border border-[#E0E7DC]">
                            <span class="text-[#4D5E54] block text-[10px] uppercase font-bold">Total Biaya ({{ $totalMembers }} Jamaah)</span>
                            <strong class="text-[#1B3B2B] font-bold text-sm">Rp {{ number_format($registration->invoice->total_price ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#E0E7DC] flex flex-wrap items-center gap-3">
                        <a href="{{ route('documents.invoice', $registration) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <span>Lihat & Cetak Invoice</span>
                        </a>
                        <a href="{{ route('documents.invoice.download', $registration) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            <span>Unduh Excel</span>
                        </a>
                        <a href="{{ route('documents.invoice.pdf', $registration) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-red-800 bg-red-50 hover:bg-red-100 border border-red-300 transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <span>Unduh PDF</span>
                        </a>

                        @if($registration->status === \App\Models\Registration::STATUS_BERANGKAT)
                        <button type="button" @click="completeModal = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-purple-700 hover:bg-purple-800 border border-purple-600 transition-all shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tandai Perjalanan Selesai</span>
                        </button>
                        @elseif($registration->status === \App\Models\Registration::STATUS_SELESAI)
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-purple-800 bg-purple-50 border border-purple-200">
                            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Perjalanan Selesai (Arsip)</span>
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Status Progress Box --}}
                <div class="lg:col-span-4 bg-[#F8FAF7] rounded-2xl p-5 border border-[#E0E7DC] text-center space-y-2">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-[#4D5E54] block">Progres Verifikasi Dokumen</span>
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-3xl font-bold {{ $approvedMembers === $totalMembers ? 'text-emerald-700' : 'text-[#12271E]' }}">
                            {{ $approvedMembers }} / {{ $totalMembers }}
                        </span>
                        <span class="text-xs text-[#526057]">Diverifikasi</span>
                    </div>
                    @if($registration->status === \App\Models\Registration::STATUS_SELESAI)
                        <div class="p-2 rounded-lg bg-purple-50 text-purple-800 text-[11px] font-semibold border border-purple-200">
                            ✓ Seluruh tahapan ibadah & kepulangan selesai (Arsip)
                        </div>
                    @elseif($registration->status === \App\Models\Registration::STATUS_BERANGKAT)
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] font-semibold border border-emerald-200">
                            ✓ Siap berangkat / Dalam pelaksanaan ibadah
                        </div>
                    @elseif($approvedMembers === $totalMembers)
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] font-semibold border border-emerald-200">
                            ✓ Seluruh dokumen disetujui (Menunggu Pembayaran DP)
                        </div>
                    @elseif($rejectedMembers > 0)
                        <div class="p-2 rounded-lg bg-red-50 text-red-800 text-[11px] font-semibold border border-red-200">
                            {{ $rejectedMembers }} Dokumen perlu diperbaiki oleh jamaah
                        </div>
                    @else
                        <div class="p-2 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-semibold border border-amber-200">
                            {{ $pendingMembers }} Dokumen menunggu pemeriksaan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB SWITCHER: TAHAP AWAL VS TAHAP KEBERANGKATAN                       --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <div class="flex items-center gap-3 border-b border-[#E0E7DC] pb-4 mb-6 overflow-x-auto">
            <button type="button" @click="currentPhaseTab = 'awal'"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shrink-0"
                    :class="currentPhaseTab === 'awal' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'bg-white text-[#526057] hover:bg-[#EFF3EB] border border-[#E0E7DC]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <span>Tahap 1: Dokumen Pendaftaran Awal</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="currentPhaseTab === 'awal' ? 'bg-white/20 text-white' : 'bg-[#EFF3EB] text-[#12271E]'">
                    {{ $totalMembers }} Jamaah
                </span>
            </button>

            <button type="button" @click="currentPhaseTab = 'keberangkatan'"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shrink-0"
                    :class="currentPhaseTab === 'keberangkatan' ? 'bg-[#1B3B2B] text-white shadow-xs' : 'bg-white text-[#526057] hover:bg-[#EFF3EB] border border-[#E0E7DC]'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                <span>Tahap 7: Dokumen Keberangkatan</span>
                @if(isset($departureSummary) && $departureSummary['total_pending'] > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-400 text-amber-950 font-bold animate-pulse">
                        {{ $departureSummary['total_pending'] }} Pending
                    </span>
                @elseif(isset($departureSummary) && $departureSummary['is_all_valid'])
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-400 text-emerald-950 font-bold">
                        ✓ Lengkap
                    </span>
                @endif
            </button>
        </div>

        {{-- Daftar Anggota Keluarga & Dokumen Tahap Awal --}}
        <div x-show="currentPhaseTab === 'awal'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-lg font-bold text-[#12271E]">Daftar Dokumen Calon Jamaah (Tahap Awal)</h2>
                    <p class="text-xs text-[#526057] mt-0.5">Gunakan Verification Split View untuk memeriksa foto dokumen asli dan data sistem secara berdampingan.</p>
                </div>
                
                @if($registration->members->isNotEmpty())
                    <button type="button" @click="openSplitViewer({{ $registration->members->first()->id }}, 'ktp')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                        <span>Buka Verification Split View</span>
                    </button>
                @endif
            </div>

            @foreach($registration->members as $idx => $member)
                <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs space-y-5" style="animation: fadeSlideUp 0.4s ease {{ 0.1 + ($idx * 0.05) }}s both">
                    
                    {{-- Header Member --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E0E7DC]">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center font-bold text-xs">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-[#12271E] flex items-center gap-2">
                                    <span>{{ $member->name }}</span>
                                    <span class="text-xs font-normal text-[#526057]">({{ $member->relationship_label }})</span>
                                </h3>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[#526057] mt-0.5">
                                    <span>NIK: <strong class="font-mono text-[#12271E]">{{ $member->nik }}</strong></span>
                                    <span>&bull; No. KK: <strong class="font-mono text-[#12271E]">{{ $member->no_kk }}</strong></span>
                                    @if($member->birth_place || $member->birth_date)
                                        <span>&bull; TTL: <strong class="text-[#12271E]">{{ $member->birth_place ?? '-' }}, {{ $member->birth_date ? $member->birth_date->translatedFormat('d M Y') : '-' }}</strong></span>
                                    @endif
                                    @if($member->gender)
                                        <span>&bull; <strong class="text-[#12271E]">{{ $member->gender_label }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" @click="openSplitViewer({{ $member->id }}, 'ktp')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors cursor-pointer border border-[#CCD8C7]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Periksa & Bandingkan</span>
                            </button>

                            @if($member->document_status === 'disetujui')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Sudah Diverifikasi
                                </span>
                            @elseif($member->document_status === 'ditolak')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Perlu Diperbaiki
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Menunggu Verifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Rejection Reason Alert if any --}}
                    @if($member->document_status === 'ditolak' && $member->rejection_reason)
                        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800">
                            <strong>Catatan Perbaikan:</strong> {{ $member->rejection_reason }}
                        </div>
                    @endif

                    {{-- Verified Info --}}
                    @if($member->document_status === 'disetujui' && $member->verified_at)
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
                            <strong>✓ Final</strong> — Diverifikasi pada {{ $member->verified_at->translatedFormat('d M Y, H:i') }} WIB
                            @if($member->verifiedByAdmin)
                                oleh <strong>{{ $member->verifiedByAdmin->name }}</strong>
                            @endif
                        </div>
                    @endif

                    {{-- Document Files Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                        
                        {{-- 1. KTP --}}
                        <div class="p-3.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-1">1. Foto / Scan KTP</span>
                                <span class="text-xs font-mono text-[#12271E] block truncate">{{ $member->nik }}</span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-[#E0E7DC]">
                                @if($member->ktp_url)
                                    <button type="button" @click="openSplitViewer({{ $member->id }}, 'ktp')"
                                            class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-semibold text-[#1B3B2B] bg-white border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Bandingkan KTP</span>
                                    </button>
                                @else
                                    <span class="text-xs text-red-500 font-semibold block text-center py-1">Belum Diunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- 2. KK --}}
                        <div class="p-3.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-1">2. Kartu Keluarga (KK)</span>
                                <span class="text-xs font-mono text-[#12271E] block truncate">{{ $member->no_kk }}</span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-[#E0E7DC]">
                                @if($member->kk_url)
                                    <button type="button" @click="openSplitViewer({{ $member->id }}, 'kk')"
                                            class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-semibold text-[#1B3B2B] bg-white border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Bandingkan KK</span>
                                    </button>
                                @else
                                    <span class="text-xs text-red-500 font-semibold block text-center py-1">Belum Diunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Paspor --}}
                        <div class="p-3.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-1">3. Paspor (Opsional)</span>
                                <span class="text-xs font-mono text-[#12271E] block truncate">{{ $member->no_passport ?: 'Tidak ada nomor' }}</span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-[#E0E7DC]">
                                @if($member->passport_url)
                                    <button type="button" @click="openSplitViewer({{ $member->id }}, 'passport')"
                                            class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-semibold text-[#1B3B2B] bg-white border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Bandingkan Paspor</span>
                                    </button>
                                @else
                                    <span class="text-xs text-[#526057] block text-center py-1">Tidak Diunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- 4. Dokumen Tambahan --}}
                        <div class="p-3.5 rounded-xl border border-[#E0E7DC] bg-[#F8FAF7] flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-[#4D5E54] block mb-1">
                                    4. {{ $member->relationship === 'anak' ? 'Akta Kelahiran' : ($member->relationship === 'suami' || $member->relationship === 'istri' ? 'Buku Nikah' : 'Dokumen Pelengkap') }}
                                </span>
                                <span class="text-xs text-[#526057] block truncate">
                                    {{ $member->relationship === 'anak' ? 'Persyaratan Anak' : ($member->relationship === 'suami' || $member->relationship === 'istri' ? 'Persyaratan Pasutri' : 'Tidak Wajib') }}
                                </span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-[#E0E7DC]">
                                @if($member->marriage_book_url)
                                    <button type="button" @click="openSplitViewer({{ $member->id }}, 'marriage')"
                                            class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-semibold text-[#1B3B2B] bg-white border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Buku Nikah</span>
                                    </button>
                                @elseif($member->birth_certificate_url)
                                    <button type="button" @click="openSplitViewer({{ $member->id }}, 'birth')"
                                            class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-semibold text-[#1B3B2B] bg-white border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Akta Kelahiran</span>
                                    </button>
                                @else
                                    <span class="text-xs text-[#526057] block text-center py-1">-</span>
                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- Admin Action Buttons --}}
                    <div class="pt-3 border-t border-[#E0E7DC]">
                        @if($member->document_status === 'disetujui')
                            <div class="flex items-center justify-end gap-2">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>✓ Disetujui — Final</span>
                                </span>
                            </div>
                        @elseif($member->document_status === 'ditolak')
                            <div class="flex items-center justify-end gap-2">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-red-800 bg-red-50 border border-red-200">
                                    <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>✕ Ditolak — Menunggu Jamaah Unggah Ulang</span>
                                </span>
                            </div>
                        @else
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <form method="POST" action="{{ route('admin.members.verify', $member) }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 active:scale-[0.98] transition-all shadow-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        <span>Verifikasi & Setujui</span>
                                    </button>
                                </form>

                                <button type="button" @click="openSplitViewer({{ $member->id }}, 'ktp'); rejectMode = true;"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 active:scale-[0.98] transition-all cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Minta Perbaikan</span>
                                </button>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- DAFTAR DOKUMEN TAHAP KEBERANGKATAN (VISA, VAKSIN, FOTO VISA)           --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <div x-show="currentPhaseTab === 'keberangkatan'" x-cloak class="space-y-6">
            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#12271E] flex items-center gap-2">
                        <span>Verifikasi Dokumen Keberangkatan</span>
                    </h2>
                    <p class="text-xs text-[#526057] mt-0.5">
                        Periksa keabsahan Visa Umrah, Sertifikat Vaksin Meningitis, dan Pas Foto Khusus Visa. Status registrasi akan otomatis naik menjadi <strong>'Siap Berangkat'</strong> jika seluruh berkas wajib jamaah telah disetujui.
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0 bg-[#F8FAF7] p-2.5 rounded-xl border border-[#E0E7DC]">
                    <div class="text-center px-3 py-1">
                        <span class="text-[10px] uppercase font-bold text-[#4D5E54] block">Valid</span>
                        <span class="text-sm font-bold text-emerald-700">{{ $departureSummary['total_valid'] ?? 0 }} / {{ $departureSummary['total_required'] ?? 0 }}</span>
                    </div>
                    @if(($departureSummary['total_pending'] ?? 0) > 0)
                        <div class="text-center px-3 py-1 border-l border-[#E0E7DC]">
                            <span class="text-[10px] uppercase font-bold text-amber-700 block">Menunggu</span>
                            <span class="text-sm font-bold text-amber-800">{{ $departureSummary['total_pending'] }}</span>
                        </div>
                    @endif
                    @if(($departureSummary['total_missing'] ?? 0) > 0)
                        <div class="text-center px-3 py-1 border-l border-[#E0E7DC]">
                            <span class="text-[10px] uppercase font-bold text-red-700 block">Belum Diunggah</span>
                            <span class="text-sm font-bold text-red-700">{{ $departureSummary['total_missing'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($departureSummary))
                @foreach($departureSummary['members_data'] as $mIdx => $item)
                    @php
                        $mem = $item['member'];
                        $docs = $item['docs'];
                    @endphp
                    <div class="bg-white rounded-2xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs space-y-5">
                        
                        {{-- Header Member --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E0E7DC]">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-[#1B3B2B] text-white flex items-center justify-center font-bold text-xs">
                                    {{ $mIdx + 1 }}
                                </span>
                                <div>
                                    <h3 class="text-base font-bold text-[#12271E] flex items-center gap-2">
                                        <span>{{ $mem->name }}</span>
                                        <span class="text-xs font-normal text-[#526057]">({{ $mem->relationship_label }})</span>
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[#526057] mt-0.5">
                                        <span>NIK: <strong class="font-mono text-[#12271E]">{{ $mem->nik }}</strong></span>
                                        <span>&bull; No. Paspor: <strong class="font-mono text-[#12271E]">{{ $mem->no_passport ?: '-' }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Grid 3 Dokumen Keberangkatan --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($docs as $docItem)
                                @php
                                    $docType = $docItem['doc_type'];
                                    $doc = $docItem['document'];
                                    $status = $docItem['status'];
                                @endphp
                                <div class="p-4 rounded-2xl border flex flex-col justify-between space-y-3.5 shadow-2xs {{ $status === 'valid' ? 'border-emerald-200 bg-emerald-50/10' : ($status === 'ditolak' ? 'border-red-200 bg-red-50/20' : ($status === 'menunggu_verifikasi' ? 'border-amber-300 bg-amber-50/20 ring-2 ring-amber-100' : 'border-[#E0E7DC] bg-[#F8FAF7]')) }}">
                                    
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="text-xs font-bold text-[#12271E]">{{ $docType->name }}</h4>
                                            @if($docType->is_required)
                                                <span class="text-[10px] font-bold text-red-500">*Wajib</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-[#526057] leading-relaxed line-clamp-2">{{ $docType->description }}</p>
                                    </div>

                                    <div>
                                        @if($status === 'valid')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                <span>✓ Terverifikasi Valid</span>
                                            </span>
                                            @if($doc?->verified_at)
                                                <span class="text-[10px] text-[#526057] block mt-1">Diverifikasi: {{ $doc->verified_at->translatedFormat('d M Y, H:i') }}</span>
                                            @endif
                                        @elseif($status === 'ditolak')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Ditolak / Perlu Perbaikan</span>
                                            </span>
                                        @elseif($status === 'menunggu_verifikasi')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Menunggu Verifikasi</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-medium bg-gray-100 text-[#526057]">
                                                <span>Belum Diunggah Jamaah</span>
                                            </span>
                                        @endif
                                    </div>

                                    @if($status === 'ditolak' && $doc?->rejection_reason)
                                        <div class="p-2.5 rounded-xl bg-red-50 border border-red-200 text-[11px] text-red-900 space-y-1">
                                            <span class="font-bold text-red-700 block">Alasan Penolakan:</span>
                                            <p class="leading-snug">{{ $doc->rejection_reason }}</p>
                                        </div>
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="pt-3 border-t border-[#E0E7DC] space-y-2">
                                        @if($doc && $doc->file_url)
                                            <a href="{{ $doc->file_url }}" target="_blank"
                                               class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2.5 rounded-xl text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-all">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>{{ $doc->is_pdf ? 'Buka File PDF' : 'Buka Foto Dokumen' }}</span>
                                            </a>

                                            @if($status === 'valid' && $registration->status === \App\Models\Registration::STATUS_BERANGKAT)
                                                {{-- Mode Koreksi Darurat: Muncul HANYA jika booking sudah berstatus 'berangkat' --}}
                                                <div class="pt-1">
                                                    <button type="button" @click="openDepartureReject('{{ route('admin.documents.verify', $doc) }}', '{{ $docType->name }} — {{ $mem->name }}')"
                                                            class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-xl text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-300 transition-all cursor-pointer shadow-2xs"
                                                            title="Koreksi darurat: Batalkan verifikasi dokumen ini dan kembalikan status pendaftaran ke kelengkapan keberangkatan">
                                                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span>Batalkan Verifikasi (Koreksi)</span>
                                                    </button>
                                                </div>
                                            @elseif($status !== 'valid')
                                                {{-- Alur Verifikasi Normal: Setujui / Tolak --}}
                                                <div class="flex items-center gap-2 pt-1">
                                                    <form method="POST" action="{{ route('admin.documents.verify', $doc) }}" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit"
                                                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition-all cursor-pointer shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                            <span>Setujui</span>
                                                        </button>
                                                    </form>

                                                    <button type="button" @click="openDepartureReject('{{ route('admin.documents.verify', $doc) }}', '{{ $docType->name }} — {{ $mem->name }}')"
                                                            class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-xl text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-all cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        <span>Tolak</span>
                                                    </button>
                                                </div>
                                            @else
                                                {{-- Dokumen valid & booking belum / sudah lewat status 'berangkat' -> Terkunci rapat, hanya tombol Buka Foto Dokumen yang tampil --}}
                                            @endif
                                        @else
                                            <p class="text-center text-[11px] text-[#526057] italic py-1">Menunggu unggahan dari jamaah</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- VERIFICATION SPLIT VIEW MODAL (FOTO DOKUMEN & DATA BERDAMPINGAN)       --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <template x-teleport="body">
            <div x-show="splitViewer" 
                 x-cloak
                 class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-2 sm:p-4 bg-black/80 backdrop-blur-xs"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="closeSplitViewer()"
                 style="display: none;"
                 role="dialog"
                 aria-modal="true">
                
                <div class="bg-white rounded-2xl max-w-7xl w-full h-[94vh] max-h-[94vh] flex flex-col shadow-2xl overflow-hidden border border-[#E0E7DC]">
                
                {{-- 1. TOP HEADER BAR --}}
                <div class="px-5 py-3.5 bg-[#1B3B2B] text-white flex items-center justify-between gap-4 shrink-0">
                    
                    {{-- Active Member Info & Selector --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold text-xs shrink-0">
                            <span x-text="members.findIndex(m => m.id === activeMember.id) + 1"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm sm:text-base font-bold truncate" x-text="activeMember.name"></h3>
                                <span class="text-[11px] px-2 py-0.2 rounded-md bg-white/15 text-white/90" x-text="activeMember.relationship_label"></span>
                            </div>
                            <span class="text-[11px] text-white/70 block truncate" x-text="'NIK: ' + activeMember.nik + ' • No. KK: ' + activeMember.no_kk"></span>
                        </div>
                    </div>

                    {{-- Document Switcher Tabs --}}
                    <div class="hidden md:flex items-center gap-1.5 bg-black/20 p-1 rounded-xl border border-white/10 shrink-0">
                        <button type="button" @click="setDoc('ktp')" 
                                :class="activeDoc === 'ktp' ? 'bg-white text-[#1B3B2B] font-bold shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer">
                            <span>KTP</span>
                            <span x-show="activeMember.ktp_url" class="text-emerald-500 font-bold">✓</span>
                        </button>

                        <button type="button" @click="setDoc('kk')" 
                                :class="activeDoc === 'kk' ? 'bg-white text-[#1B3B2B] font-bold shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer">
                            <span>KK</span>
                            <span x-show="activeMember.kk_url" class="text-emerald-500 font-bold">✓</span>
                        </button>

                        <button type="button" @click="setDoc('passport')" 
                                :class="activeDoc === 'passport' ? 'bg-white text-[#1B3B2B] font-bold shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer">
                            <span>Paspor</span>
                            <span x-show="activeMember.passport_url" class="text-emerald-500 font-bold">✓</span>
                        </button>

                        <template x-if="activeMember.relationship === 'suami' || activeMember.relationship === 'istri'">
                            <button type="button" @click="setDoc('marriage')" 
                                    :class="activeDoc === 'marriage' ? 'bg-white text-[#1B3B2B] font-bold shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer">
                                <span>Buku Nikah</span>
                                <span x-show="activeMember.marriage_book_url" class="text-emerald-500 font-bold">✓</span>
                            </button>
                        </template>

                        <template x-if="activeMember.relationship === 'anak'">
                            <button type="button" @click="setDoc('birth')" 
                                    :class="activeDoc === 'birth' ? 'bg-white text-[#1B3B2B] font-bold shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10'"
                                    class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer">
                                <span>Akta Lahir</span>
                                <span x-show="activeMember.birth_certificate_url" class="text-emerald-500 font-bold">✓</span>
                            </button>
                        </template>
                    </div>

                    {{-- Member Switcher (if > 1) & Close --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <template x-if="members.length > 1">
                            <select x-model="activeMemberId" @change="resetZoom()" 
                                    class="bg-white/10 text-white text-xs border border-white/20 rounded-lg px-2 py-1.5 focus:outline-none focus:bg-[#12271E]">
                                <template x-for="(m, i) in members" :key="m.id">
                                    <option :value="m.id" class="text-zinc-900" x-text="(i+1) + '. ' + m.name"></option>
                                </template>
                            </select>
                        </template>

                        <button type="button" @click="closeSplitViewer()" 
                                class="p-1.5 rounded-lg bg-white/10 text-white/80 hover:text-white hover:bg-white/20 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                </div>

                {{-- Mobile Doc Selector --}}
                <div class="flex md:hidden items-center justify-around bg-[#12271E] px-2 py-2 text-white text-xs border-b border-white/10">
                    <button type="button" @click="setDoc('ktp')" :class="activeDoc === 'ktp' ? 'text-emerald-400 font-bold underline' : 'text-white/70'">KTP</button>
                    <button type="button" @click="setDoc('kk')" :class="activeDoc === 'kk' ? 'text-emerald-400 font-bold underline' : 'text-white/70'">KK</button>
                    <button type="button" @click="setDoc('passport')" :class="activeDoc === 'passport' ? 'text-emerald-400 font-bold underline' : 'text-white/70'">Paspor</button>
                    <template x-if="activeMember.relationship === 'suami' || activeMember.relationship === 'istri'">
                        <button type="button" @click="setDoc('marriage')" :class="activeDoc === 'marriage' ? 'text-emerald-400 font-bold underline' : 'text-white/70'">Buku Nikah</button>
                    </template>
                    <template x-if="activeMember.relationship === 'anak'">
                        <button type="button" @click="setDoc('birth')" :class="activeDoc === 'birth' ? 'text-emerald-400 font-bold underline' : 'text-white/70'">Akta Lahir</button>
                    </template>
                </div>

                {{-- 2. SPLIT BODY CONTAINER --}}
                <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 min-h-0 overflow-hidden">
                    
                    {{-- ═══════════════════════════════════════════════════════ --}}
                    {{-- LEFT PANEL: DOKUMEN ASLI DENGAN ZOOM & PAN (55% Width)   --}}
                    {{-- ═══════════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-7 flex flex-col h-full bg-[#111815] text-white border-r border-[#E0E7DC]/20 min-h-0">
                        
                        {{-- Zoom & Tool Bar --}}
                        <div class="px-4 py-2 bg-black/40 border-b border-white/10 flex items-center justify-between flex-wrap gap-2 shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <span x-text="activeDoc === 'ktp' ? 'Foto KTP' : (activeDoc === 'kk' ? 'Kartu Keluarga' : (activeDoc === 'passport' ? 'Paspor' : 'Dokumen Tambahan'))"></span>
                                    <span class="text-[10px] text-zinc-400 font-normal">(Dokumen Asli)</span>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Zoom Controls --}}
                                <div class="inline-flex items-center bg-white/10 rounded-lg p-0.5 border border-white/10 text-xs">
                                    <button type="button" @click="zoomOut()" title="Zoom Out" 
                                            class="px-2 py-1 hover:bg-white/20 rounded text-white font-bold transition-colors cursor-pointer">−</button>
                                    <button type="button" @click="resetZoom()" title="Reset Zoom" 
                                            class="px-2.5 py-1 hover:bg-white/20 rounded font-mono text-[11px] text-emerald-400 font-semibold transition-colors cursor-pointer"
                                            x-text="zoomLevel + '%'"></button>
                                    <button type="button" @click="zoomIn()" title="Zoom In" 
                                            class="px-2 py-1 hover:bg-white/20 rounded text-white font-bold transition-colors cursor-pointer">+</button>
                                </div>

                                {{-- Rotate Button --}}
                                <button type="button" @click="rotate()" title="Putar 90 Derajat" 
                                        class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-xs text-white/90 transition-colors flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                    <span class="hidden sm:inline">Putar</span>
                                </button>

                                {{-- Open In New Tab --}}
                                <template x-if="currentDocUrl">
                                    <a :href="currentDocUrl" target="_blank" title="Buka gambar di tab baru"
                                       class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-xs text-white/90 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                </template>
                            </div>
                        </div>

                        {{-- Image Viewport Container --}}
                        <div class="flex-1 overflow-auto p-4 flex items-center justify-center relative min-h-0 bg-zinc-950 select-none">
                            
                            <template x-if="currentDocUrl && !isPdf">
                                <div class="flex items-center justify-center min-w-full min-h-full">
                                    <img :src="currentDocUrl" 
                                         :alt="currentDocTitle" 
                                         class="max-w-none rounded-lg shadow-2xl transition-transform duration-150 ease-out pointer-events-auto"
                                         :style="'transform: scale(' + (zoomLevel / 100) + ') rotate(' + rotation + 'deg); transform-origin: center center; max-height: ' + (zoomLevel === 100 ? '70vh' : 'none') + ';'">
                                </div>
                            </template>

                            <template x-if="currentDocUrl && isPdf">
                                <iframe :src="currentDocUrl" class="w-full h-full rounded-lg bg-white"></iframe>
                            </template>

                            <template x-if="!currentDocUrl">
                                <div class="text-center p-8 space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-white/10 text-white/40 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                    <p class="text-xs text-zinc-400 font-medium">Dokumen ini belum diunggah oleh calon jamaah.</p>
                                </div>
                            </template>

                        </div>

                        <div class="px-4 py-1.5 bg-black/50 border-t border-white/10 text-[11px] text-zinc-400 flex items-center justify-between shrink-0">
                            <span>Tip: Gunakan tombol zoom [−] [+] untuk memperbesar bagian NIK / nama pada dokumen.</span>
                            <span class="font-mono text-zinc-400" x-text="'Zoom: ' + zoomLevel + '%'"></span>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════════════════ --}}
                    {{-- RIGHT PANEL: DATA JAMAAH DI SISTEM (45% Width)          --}}
                    {{-- ═══════════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-5 flex flex-col h-full bg-[#F8FAF7] overflow-hidden min-h-0">
                        
                        {{-- Header Panel Kanan --}}
                        <div class="px-5 py-3 bg-white border-b border-[#E0E7DC] flex items-center justify-between shrink-0">
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-[#12271E]">Data Jamaah di Sistem</h4>
                                <p class="text-[11px] text-[#526057]">Bandingkan data yang terdaftar dengan foto dokumen asli di sebelah kiri.</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold"
                                  :class="activeMember.document_status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : (activeMember.document_status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')"
                                  x-text="activeMember.document_status_label"></span>
                        </div>

                        {{-- Scrollable Data Area --}}
                        <div class="flex-1 overflow-y-auto p-5 space-y-4 min-h-0">

                            {{-- ══════════════════════════════════════════════ --}}
                            {{-- TAB KTP: DATA IDENTITAS KTP                    --}}
                            {{-- ══════════════════════════════════════════════ --}}
                            <template x-if="activeDoc === 'ktp'">
                                <div class="space-y-3.5">
                                    
                                    {{-- NIK Card Highlight --}}
                                    <div class="p-4 rounded-2xl bg-white border-2 border-[#1B3B2B]/30 shadow-2xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057]">Nomor Induk Kependudukan (NIK)</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✓ 16 Digit Angka</span>
                                        </div>
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-xl font-mono font-extrabold text-[#12271E] tracking-wider" x-text="activeMember.nik"></span>
                                        </div>
                                    </div>

                                    {{-- Nama Lengkap Card --}}
                                    <div class="p-4 rounded-2xl bg-white border border-[#E0E7DC] space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057]">Nama Lengkap Sesuai KTP</span>
                                        <span class="text-base font-bold text-[#12271E] block" x-text="activeMember.name"></span>
                                    </div>

                                    {{-- Tempat & Tanggal Lahir --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3.5 rounded-xl bg-white border border-[#E0E7DC]">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Tempat Lahir</span>
                                            <span class="text-xs font-semibold text-[#12271E] block mt-0.5" x-text="activeMember.birth_place || '-'"></span>
                                        </div>
                                        <div class="p-3.5 rounded-xl bg-white border border-[#E0E7DC]">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Tanggal Lahir</span>
                                            <span class="text-xs font-semibold text-[#12271E] block mt-0.5" x-text="activeMember.birth_date || '-'"></span>
                                        </div>
                                    </div>

                                    {{-- Jenis Kelamin & Hubungan --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3.5 rounded-xl bg-white border border-[#E0E7DC]">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Jenis Kelamin</span>
                                            <span class="text-xs font-semibold text-[#12271E] block mt-0.5" x-text="activeMember.gender || '-'"></span>
                                        </div>
                                        <div class="p-3.5 rounded-xl bg-white border border-[#E0E7DC]">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Hubungan Keluarga</span>
                                            <span class="text-xs font-semibold text-[#1B3B2B] block mt-0.5" x-text="activeMember.relationship_label"></span>
                                        </div>
                                    </div>

                                    {{-- Alamat --}}
                                    <div class="p-3.5 rounded-xl bg-white border border-[#E0E7DC]">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Alamat Tinggal</span>
                                        <span class="text-xs text-[#12271E] block mt-0.5 leading-relaxed" x-text="activeMember.address || '-'"></span>
                                    </div>

                                </div>
                            </template>

                            {{-- ══════════════════════════════════════════════ --}}
                            {{-- TAB KK: DATA KARTU KELUARGA                    --}}
                            {{-- ══════════════════════════════════════════════ --}}
                            <template x-if="activeDoc === 'kk'">
                                <div class="space-y-3.5">
                                    
                                    {{-- No KK Card Highlight --}}
                                    <div class="p-4 rounded-2xl bg-white border-2 border-[#1B3B2B]/30 shadow-2xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057]">Nomor Kartu Keluarga (KK)</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✓ 16 Digit Angka</span>
                                        </div>
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-xl font-mono font-extrabold text-[#12271E] tracking-wider" x-text="activeMember.no_kk"></span>
                                        </div>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white border border-[#E0E7DC] space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057]">NIK Anggota pada KK Ini</span>
                                        <span class="text-sm font-mono font-bold text-[#12271E] block" x-text="activeMember.nik"></span>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white border border-[#E0E7DC] space-y-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Anggota Terdaftar Lainnya dalam Pendaftaran Ini:</span>
                                        <div class="space-y-1.5 text-xs">
                                            <template x-for="(m, idx) in members" :key="m.id">
                                                <div class="p-2 rounded-lg bg-[#F8FAF7] flex items-center justify-between border border-[#E0E7DC]">
                                                    <span class="font-medium text-[#12271E]" x-text="(idx+1) + '. ' + m.name"></span>
                                                    <span class="font-mono text-[11px] text-[#526057]" x-text="m.no_kk"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                </div>
                            </template>

                            {{-- ══════════════════════════════════════════════ --}}
                            {{-- TAB PASPOR: DATA PASPOR                        --}}
                            {{-- ══════════════════════════════════════════════ --}}
                            <template x-if="activeDoc === 'passport'">
                                <div class="space-y-3.5">
                                    
                                    <div class="p-4 rounded-2xl bg-white border border-[#E0E7DC] space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057]">Nomor Paspor</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#EFF3EB] text-[#1B3B2B] font-semibold">Dokumen Opsional</span>
                                        </div>
                                        <span class="text-xl font-mono font-bold text-[#12271E] block pt-1" 
                                              x-text="activeMember.no_passport ? activeMember.no_passport : 'Belum Ada Nomor Paspor'"></span>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white border border-[#E0E7DC] space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057]">Nama Lengkap Calon Jamaah</span>
                                        <span class="text-base font-bold text-[#12271E] block" x-text="activeMember.name"></span>
                                    </div>

                                </div>
                            </template>

                            {{-- ══════════════════════════════════════════════ --}}
                            {{-- TAB BUKU NIKAH / AKTA LAHIR                    --}}
                            {{-- ══════════════════════════════════════════════ --}}
                            <template x-if="activeDoc === 'marriage' || activeDoc === 'birth'">
                                <div class="space-y-3.5">
                                    <div class="p-4 rounded-2xl bg-white border border-[#E0E7DC] space-y-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block"
                                              x-text="activeDoc === 'marriage' ? 'Persyaratan Dokumen Pasangan Suami/Istri' : 'Persyaratan Dokumen Anak'"></span>
                                        <p class="text-xs text-[#526057] leading-relaxed">
                                            Dokumen ini digunakan untuk verifikasi hubungan keluarga resmi bagi pasangan suami/istri atau anak.
                                        </p>
                                        <div class="pt-2 border-t border-[#E0E7DC]">
                                            <span class="text-xs font-bold text-[#12271E]" x-text="'Nama Anggota: ' + activeMember.name"></span>
                                            <span class="text-xs text-[#526057] block" x-text="'Hubungan: ' + activeMember.relationship_label"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Rejection Note if exists --}}
                            <template x-if="activeMember.document_status === 'ditolak' && activeMember.rejection_reason">
                                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-1">
                                    <strong class="block text-red-800">Catatan Perbaikan Sebelumnya:</strong>
                                    <p x-text="activeMember.rejection_reason"></p>
                                </div>
                            </template>

                        </div>

                        {{-- ══════════════════════════════════════════════════ --}}
                        {{-- 3. BOTTOM ACTION BAR DALAM SPLIT VIEWER            --}}
                        {{-- ══════════════════════════════════════════════════ --}}
                        <div class="p-4 bg-white border-t border-[#E0E7DC] shrink-0 space-y-3">
                            
                            {{-- STATUS DISPENSAI / FINAL --}}
                            <template x-if="activeMember.document_status === 'disetujui'">
                                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>✓ Dokumen Telah Diverifikasi & Disetujui (Final)</span>
                                    </span>
                                    <span class="text-[11px] text-emerald-700" x-text="activeMember.verified_at"></span>
                                </div>
                            </template>

                            <template x-if="activeMember.document_status === 'ditolak'">
                                <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800 flex items-center justify-between">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>✕ Dokumen Berstatus Ditolak (Menunggu Revisi Jamaah)</span>
                                    </span>
                                </div>
                            </template>

                            {{-- STATUS PENDING: APPROVE & REJECT ACTIONS --}}
                            <template x-if="activeMember.document_status === 'menunggu_verifikasi'">
                                <div>
                                    {{-- Default Action Buttons --}}
                                    <div x-show="!rejectMode" class="flex items-center justify-end gap-3">
                                        <button type="button" @click="rejectMode = true" 
                                                class="px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Minta Perbaikan</span>
                                        </button>

                                        <form :action="activeMember.verify_url" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" 
                                                    class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs active:scale-[0.98] transition-all cursor-pointer flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                <span>Verifikasi & Setujui Dokumen</span>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Rejection Form Drawer inside viewer --}}
                                    <div x-show="rejectMode" class="space-y-3">
                                        <form :action="activeMember.verify_url" method="POST" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">

                                            <div>
                                                <label class="block text-[11px] font-bold text-red-800 uppercase tracking-wider mb-1">
                                                    Alasan / Catatan Penolakan <span class="text-red-500">*</span>
                                                </label>
                                                <textarea name="rejection_reason" rows="2" required minlength="5" x-model="rejectionReason"
                                                          class="w-full px-3.5 py-2 rounded-xl border border-red-300 text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-red-400/20 focus:border-red-500"
                                                          placeholder="Contoh: Foto KTP buram, nomor NIK tidak terbaca jelas. Mohon unggah ulang foto yang lebih terang."></textarea>
                                            </div>

                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" @click="rejectMode = false; rejectionReason = '';" 
                                                        class="px-3.5 py-1.5 rounded-lg border border-[#CCD8C7] text-xs text-[#526057] hover:bg-[#EFF3EB] cursor-pointer">
                                                    Batal
                                                </button>
                                                <button type="submit" 
                                                        class="px-4 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors cursor-pointer">
                                                    Konfirmasi Penolakan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>

                        </div>

                    </div>

                </div>

            </div>
        </template>

        <!-- Modal Tolak Dokumen Keberangkatan -->
        <template x-teleport="body">
            <div x-show="departureRejectModal" x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-red-100 animate-in fade-in zoom-in-95 duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#12271E]">Tolak / Koreksi Dokumen Keberangkatan</h3>
                            <p class="text-xs text-[#526057]" x-text="'Dokumen: ' + departureRejectDocName"></p>
                        </div>
                    </div>

                    @if($registration->status === \App\Models\Registration::STATUS_BERANGKAT)
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="leading-relaxed"><strong>Perhatian Mode Koreksi:</strong> Membatalkan/menolak dokumen yang sebelumnya valid ini akan secara otomatis mengembalikan status pendaftaran jamaah dari <em>"Siap Berangkat"</em> kembali ke tahap <em>"Kelengkapan Dokumen Keberangkatan"</em>.</span>
                        </div>
                    @endif

                    <form :action="departureRejectAction" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <div>
                            <label class="block text-xs font-bold text-[#12271E] mb-1.5">
                                Alasan Penolakan / Koreksi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" rows="3" required minlength="5" x-model="departureRejectReason"
                                      class="w-full px-3 py-2 border border-red-200 rounded-xl text-xs text-[#12271E] focus:outline-none focus:ring-2 focus:ring-red-400/20 focus:border-red-500 placeholder:text-gray-400"
                                      placeholder="Contoh: File scan visa buram atau barcode tidak terbaca. Harap unggah ulang file yang jelas."></textarea>
                            <p class="text-[10px] text-[#526057] mt-1">Alasan penolakan akan ditampilkan kepada jamaah agar dapat mengunggah ulang dokumen yang benar.</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="departureRejectModal = false; departureRejectReason = '';" 
                                    class="px-4 py-2 text-xs font-medium rounded-xl border border-[#CCD8C7] text-[#526057] hover:bg-gray-50 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-xs font-bold rounded-xl bg-red-600 hover:bg-red-700 text-white shadow-sm transition-colors cursor-pointer">
                                Konfirmasi Penolakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Modal Konfirmasi Tandai Perjalanan Selesai -->
        <template x-teleport="body">
            <div x-show="completeModal" x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-[#E0E7DC] animate-in fade-in zoom-in-95 duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#12271E]">Tandai Perjalanan Selesai</h3>
                            <p class="text-xs text-[#526057]">Booking: {{ $registration->registration_number }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-[#526057]">
                        <p>
                            Apakah Anda yakin ingin menandai pendaftaran jamaah <span class="font-bold text-[#12271E]">{{ $registration->user->name }}</span> ({{ $registration->members->count() }} orang) sebagai <strong class="text-purple-700">"Selesai"</strong> (Tahap 9)?
                        </p>
                        <p class="bg-purple-50 p-3 rounded-xl border border-purple-200 text-purple-900">
                            Status pendaftaran akan diarsipkan sebagai tuntas. Jamaah tidak lagi dihitung sebagai jamaah aktif di dashboard operasional, dan riwayat penyelesaian akan dicatat pada audit log.
                        </p>
                    </div>

                    <form action="{{ route('admin.registrations.complete', $registration) }}" method="POST">
                        @csrf
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E0E7DC]">
                            <button type="button" @click="completeModal = false" 
                                    class="px-4 py-2 text-xs font-medium rounded-xl border border-[#CCD8C7] text-[#526057] hover:bg-gray-50 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-xs font-bold rounded-xl bg-purple-700 hover:bg-purple-800 text-white shadow-xs transition-colors cursor-pointer">
                                Ya, Tandai Selesai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

</x-layouts.admin>
