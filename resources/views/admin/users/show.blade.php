<x-layouts.admin :title="'Detail Jamaah — ' . $user->name . ' — PT. Zein Internasional'">

    <div x-data="{ deleteModalOpen: false }" class="max-w-5xl mx-auto space-y-6 sm:space-y-8">
        
        {{-- Header & Navigasi Kembali --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-[#12271E] bg-white border border-[#E0E7DC] hover:border-[#1B3B2B] hover:bg-[#F8FAF7] transition-all shadow-2xs">
                <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span>Kembali ke Manajemen Jamaah</span>
            </a>
            @if($user->role === 'jamaah')
            <button type="button" 
                    @click="deleteModalOpen = true"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all shadow-2xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Hapus Akun Jamaah</span>
            </button>
            @endif
        </div>

        {{-- 1. Kartu Profil Utama --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 bg-[#EFF3EB]/60 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10 text-center sm:text-left">
                
                {{-- Foto Profil --}}
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden bg-[#1B3B2B] text-white flex items-center justify-center text-3xl font-bold shadow-lg shadow-[#1B3B2B]/20 ring-4 ring-[#EFF3EB] shrink-0 border border-[#CCD8C7]">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Foto Profil {{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span>{{ $user->initials }}</span>
                    @endif
                </div>

                <div class="flex-grow space-y-2">
                    <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        @if($user->phone_verified_at)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-100/80 px-3 py-0.5 rounded-full border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Akun Terverifikasi WhatsApp
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-800 bg-amber-100 px-3 py-0.5 rounded-full border border-amber-200">
                                Belum Verifikasi OTP
                            </span>
                        @endif

                        @if($activeRegistration)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#1B3B2B] bg-[#EFF3EB] px-3 py-0.5 rounded-full border border-[#CCD8C7]">
                                Status Pendaftaran: {{ $activeRegistration->status_label }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                        {{ $user->name }}
                    </h1>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-y-1 gap-x-4 text-xs sm:text-sm text-[#526057]">
                        <span class="font-medium text-[#12271E]">{{ $user->email }}</span>
                        <span class="text-[#CCD8C7] hidden sm:inline">&bull;</span>
                        <span class="font-semibold text-[#1B3B2B]">{{ $user->phone_formatted }}</span>
                        <span class="text-[#CCD8C7] hidden sm:inline">&bull;</span>
                        <span>Terdaftar {{ $user->created_at?->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                {{-- Tautan Cepat Kontak WhatsApp --}}
                <div class="shrink-0 pt-2 sm:pt-0">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-900 bg-emerald-100 hover:bg-emerald-200 transition-colors border border-emerald-300 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-700" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Hubungi WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. Informasi Pribadi & Identitas --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs">
            <div class="border-b border-[#E0E7DC] pb-4 mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                    Informasi Pribadi & Identitas
                </h2>
                <p class="text-xs text-[#526057] mt-0.5">
                    Data profil dan administrasi resmi jamaah yang tercatat di sistem.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nama Lengkap</span>
                    <span class="font-semibold text-sm text-[#12271E] block mt-1">{{ $user->name }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nomor Induk Kependudukan (NIK)</span>
                    <span class="font-mono font-semibold text-sm text-[#12271E] block mt-1">
                        {{ $primaryMember?->nik ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nomor Kartu Keluarga (KK)</span>
                    <span class="font-mono font-semibold text-sm text-[#12271E] block mt-1">
                        {{ $primaryMember?->no_kk ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nomor Paspor</span>
                    <span class="font-mono font-semibold text-sm text-[#12271E] block mt-1">
                        {{ $primaryMember?->no_passport ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Jenis Kelamin</span>
                    <span class="font-medium text-sm text-[#12271E] block mt-1">
                        {{ $user->gender_label ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Tempat / Tanggal Lahir</span>
                    <span class="font-medium text-sm text-[#12271E] block mt-1">
                        @if($user->birth_place || $user->birth_date)
                            {{ $user->birth_place ?? '' }}{{ $user->birth_place && $user->birth_date ? ', ' : '' }}{{ $user->birth_date?->translatedFormat('d F Y') ?? '' }}
                        @else
                            -
                        @endif
                    </span>
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Alamat Tempat Tinggal</span>
                    <span class="font-medium text-sm text-[#12271E] block mt-1 leading-relaxed">
                        {{ $user->address ?: '-' }}
                    </span>
                </div>

            </div>
        </div>

        {{-- 3. Dokumen Jamaah --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs">
            <div class="border-b border-[#E0E7DC] pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                        Dokumen Persyaratan Ibadah
                    </h2>
                    <p class="text-xs text-[#526057] mt-0.5">
                        Berkas identitas resmi jamaah untuk pengurusan visa dan manifes penerbangan.
                    </p>
                </div>
            </div>

            @if($primaryMember)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    {{-- 1. KTP --}}
                    <div class="p-4 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#12271E]">KTP (Kartu Tanda Penduduk)</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $primaryMember->ktp_file ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }}">
                                {{ $primaryMember->ktp_file ? 'Tersedia' : 'Belum Ada' }}
                            </span>
                        </div>
                        @if($primaryMember->ktp_file)
                            <div class="h-32 rounded-xl overflow-hidden bg-zinc-200 border border-[#CCD8C7] relative group">
                                <img src="{{ $primaryMember->ktp_url }}" alt="KTP {{ $primaryMember->name }}" class="w-full h-full object-cover">
                                <a href="{{ route('admin.members.documents.preview', ['member' => $primaryMember->id, 'type' => 'ktp_file']) }}" 
                                   target="_blank"
                                   class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Lihat Dokumen</span>
                                </a>
                            </div>
                        @else
                            <div class="h-32 rounded-xl border border-dashed border-[#CCD8C7] flex items-center justify-center text-xs text-[#526057]/60">
                                Berkas belum diunggah
                            </div>
                        @endif
                    </div>

                    {{-- 2. Kartu Keluarga (KK) --}}
                    <div class="p-4 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#12271E]">Kartu Keluarga (KK)</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $primaryMember->kk_file ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }}">
                                {{ $primaryMember->kk_file ? 'Tersedia' : 'Belum Ada' }}
                            </span>
                        </div>
                        @if($primaryMember->kk_file)
                            <div class="h-32 rounded-xl overflow-hidden bg-zinc-200 border border-[#CCD8C7] relative group">
                                <img src="{{ $primaryMember->kk_url }}" alt="KK {{ $primaryMember->name }}" class="w-full h-full object-cover">
                                <a href="{{ route('admin.members.documents.preview', ['member' => $primaryMember->id, 'type' => 'kk_file']) }}" 
                                   target="_blank"
                                   class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Lihat Dokumen</span>
                                </a>
                            </div>
                        @else
                            <div class="h-32 rounded-xl border border-dashed border-[#CCD8C7] flex items-center justify-center text-xs text-[#526057]/60">
                                Berkas belum diunggah
                            </div>
                        @endif
                    </div>

                    {{-- 3. Paspor --}}
                    <div class="p-4 rounded-2xl border border-[#E0E7DC] bg-[#F8FAF7] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#12271E]">Paspor</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $primaryMember->passport_file ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }}">
                                {{ $primaryMember->passport_file ? 'Tersedia' : 'Belum Ada' }}
                            </span>
                        </div>
                        @if($primaryMember->passport_file)
                            <div class="h-32 rounded-xl overflow-hidden bg-zinc-200 border border-[#CCD8C7] relative group">
                                <img src="{{ $primaryMember->passport_url }}" alt="Paspor {{ $primaryMember->name }}" class="w-full h-full object-cover">
                                <a href="{{ route('admin.members.documents.preview', ['member' => $primaryMember->id, 'type' => 'passport_file']) }}" 
                                   target="_blank"
                                   class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Lihat Dokumen</span>
                                </a>
                            </div>
                        @else
                            <div class="h-32 rounded-xl border border-dashed border-[#CCD8C7] flex items-center justify-center text-xs text-[#526057]/60">
                                Berkas belum diunggah
                            </div>
                        @endif
                    </div>

                </div>
            @else
                <div class="p-8 rounded-2xl bg-[#EFF3EB]/40 border border-[#E0E7DC] text-center">
                    <p class="text-xs text-[#526057]">Jamaah belum melakukan pendaftaran paket ibadah dan belum mengunggah dokumen persyaratan.</p>
                </div>
            @endif
        </div>

        {{-- 4. Data Pendaftaran & Rombongan --}}
        @if($activeRegistration)
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-[#E0E7DC] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                            Data Pendaftaran & Paket Terpilih
                        </h2>
                        <p class="text-xs text-[#526057] mt-0.5">
                            Rincian paket umrah/haji yang didaftarkan oleh jamaah.
                        </p>
                    </div>

                    <a href="{{ route('admin.registrations.show', $activeRegistration->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7]">
                        <span>Buka Menu Verifikasi Pendaftaran</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>

                {{-- Ringkasan Paket --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-[#EFF3EB]/40 p-4 sm:p-5 rounded-2xl border border-[#E0E7DC]">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Paket Pilihan</span>
                        <span class="font-bold text-sm text-[#12271E] block mt-0.5">{{ $activeRegistration->package->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Rencana Keberangkatan</span>
                        <span class="font-bold text-sm text-[#12271E] block mt-0.5">{{ $activeRegistration->package?->departure_date?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Jumlah Jamaah</span>
                        <span class="font-bold text-sm text-[#1B3B2B] block mt-0.5">{{ $activeRegistration->members->count() }} Orang</span>
                    </div>
                </div>

                {{-- Tabel Anggota Rombongan --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#12271E] mb-3">Daftar Anggota Rombongan</h3>
                    <div class="overflow-x-auto rounded-2xl border border-[#E0E7DC]">
                        <table class="w-full text-left text-xs text-[#526057]">
                            <thead class="bg-[#EFF3EB]/70 border-b border-[#E0E7DC] text-[10px] uppercase font-bold tracking-wider text-[#12271E]">
                                <tr>
                                    <th class="py-3 px-4">Nama Jamaah</th>
                                    <th class="py-3 px-4">NIK</th>
                                    <th class="py-3 px-4">No. Paspor</th>
                                    <th class="py-3 px-4">Hubungan</th>
                                    <th class="py-3 px-4">Status Dokumen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E0E7DC]">
                                @foreach($activeRegistration->members as $member)
                                    <tr class="hover:bg-[#F8FAF7]">
                                        <td class="py-3 px-4 font-bold text-[#12271E]">{{ $member->name }}</td>
                                        <td class="py-3 px-4 font-mono">{{ $member->nik ?: '-' }}</td>
                                        <td class="py-3 px-4 font-mono">{{ $member->no_passport ?: '-' }}</td>
                                        <td class="py-3 px-4">{{ $member->relationship_label ?? $member->relationship }}</td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full {{ $member->document_status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($member->document_status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ $member->document_status_label ?? $member->document_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- 5. Section Dokumen Invoice Tagihan Jamaah --}}
        @if($activeRegistration)
            @php
                $inv = $activeRegistration->invoice;
                if (!$inv) {
                    $memberCount = max(1, $activeRegistration->members->count());
                    $packagePrice = (float) ($activeRegistration->package?->price ?? 0);
                    $totalPrice = $packagePrice * $memberCount;
                    $inv = $activeRegistration->invoice()->create([
                        'total_price' => $totalPrice,
                        'total_paid' => 0,
                        'remaining_balance' => $totalPrice,
                        'due_date' => now()->addDays(7),
                    ]);
                    $activeRegistration->setRelation('invoice', $inv);
                }
            @endphp
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-[#E0E7DC] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B]"></span>
                            <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                                Invoice Tagihan Jamaah
                            </h2>
                        </div>
                        <p class="text-xs text-[#526057] mt-0.5">
                            Dokumen tagihan pendaftaran dan rincian pembiayaan paket ibadah jamaah.
                        </p>
                    </div>

                    {{-- Tombol Aksi: [ Lihat Invoice ], [ Cetak ], [ Download ] --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('documents.invoice', $activeRegistration) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] border border-[#CCD8C7] transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Lihat Invoice</span>
                        </a>
                        <a href="{{ route('documents.invoice', ['registration' => $activeRegistration, 'print' => 1]) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-zinc-800 bg-white hover:bg-zinc-50 border border-zinc-300 transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.056.867-1.829 1.89-1.829h7.78c1.023 0 2.13.773 1.89 1.829l-1.004 4.417c-.172.756-.84 1.284-1.616 1.284H8.34c-.776 0-1.444-.528-1.616-1.284l-1.004-4.417zM6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 9V4a1 1 0 011-1h10a1 1 0 011 1v5"/></svg>
                            <span>Cetak</span>
                        </a>
                        <a href="{{ route('documents.invoice.download', $activeRegistration) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            <span>Download</span>
                        </a>
                    </div>
                </div>

                {{-- Grid Informasi Rincian Invoice --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nomor Invoice</span>
                        <span class="font-mono font-bold text-sm text-[#1B3B2B] block">{{ $inv->invoice_number }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Tanggal Invoice</span>
                        <span class="font-semibold text-sm text-[#12271E] block">{{ $inv->created_at ? $inv->created_at->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nama Jamaah</span>
                        <span class="font-bold text-sm text-[#12271E] block truncate">{{ $user->name }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Nama Paket</span>
                        <span class="font-bold text-sm text-[#12271E] block truncate">{{ $activeRegistration->package->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Total Harga</span>
                        <span class="font-mono font-bold text-sm text-[#12271E] block">
                            Rp {{ number_format($inv->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Total Pembayaran Terverifikasi</span>
                        <span class="font-mono font-bold text-sm text-emerald-700 block">
                            Rp {{ number_format($inv->total_paid, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Sisa Pembayaran</span>
                        <span class="font-mono font-bold text-sm {{ $inv->remaining_balance > 0 ? 'text-amber-700' : 'text-emerald-700' }} block">
                            {{ $inv->remaining_balance > 0 ? 'Rp ' . number_format($inv->remaining_balance, 0, ',', '.') : 'Rp 0 (Lunas)' }}
                        </span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#EFF3EB]/50 border border-[#E0E7DC] space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Status Pembayaran</span>
                        <div class="mt-0.5">
                            @if($inv->remaining_balance <= 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>Lunas</span>
                                </span>
                            @elseif($inv->total_paid > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    <span>Sebagian (DP/Cicilan)</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    <span>Belum Bayar</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 6. Data Riwayat Pembayaran --}}
        @if($activeRegistration && $activeRegistration->invoice)
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-[#E0E7DC] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                            Riwayat & Status Pembayaran
                        </h2>
                        <p class="text-xs text-[#526057] mt-0.5">
                            Catatan tagihan, setoran DP, dan pelunasan bertahap jamaah.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('documents.invoice', $activeRegistration) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-all shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <span>Lihat Invoice</span>
                        </a>
                        <a href="{{ route('documents.invoice.download', $activeRegistration) }}" 
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-all shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            <span>Unduh Excel</span>
                        </a>
                    </div>
                </div>

                {{-- Ringkasan Biaya --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-[#EFF3EB]/40 p-4 sm:p-5 rounded-2xl border border-[#E0E7DC]">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Total Biaya Paket</span>
                        <span class="text-base font-bold text-[#12271E] block mt-0.5">
                            Rp {{ number_format($activeRegistration->invoice->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Total Sudah Terbayar</span>
                        <span class="text-base font-bold text-emerald-700 block mt-0.5">
                            Rp {{ number_format($activeRegistration->invoice->total_paid, 0, ',', '.') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#526057] block">Sisa Tagihan</span>
                        <span class="text-base font-bold {{ $activeRegistration->invoice->remaining_balance > 0 ? 'text-amber-700' : 'text-emerald-700' }} block mt-0.5">
                            {{ $activeRegistration->invoice->remaining_balance > 0 ? 'Rp ' . number_format($activeRegistration->invoice->remaining_balance, 0, ',', '.') : 'Lunas ✓' }}
                        </span>
                    </div>
                </div>

                {{-- Tabel Setoran Pembayaran --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#12271E] mb-3">Daftar Transaksi Pembayaran</h3>
                    <div class="overflow-x-auto rounded-2xl border border-[#E0E7DC]">
                        <table class="w-full text-left text-xs text-[#526057]">
                            <thead class="bg-[#EFF3EB]/70 border-b border-[#E0E7DC] text-[10px] uppercase font-bold tracking-wider text-[#12271E]">
                                <tr>
                                    <th class="py-3 px-4">Tanggal Setor</th>
                                    <th class="py-3 px-4">Jenis</th>
                                    <th class="py-3 px-4">Nominal</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Kwitansi Resmi</th>
                                    <th class="py-3 px-4 text-right">Bukti Transfer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E0E7DC]">
                                @forelse($activeRegistration->payments as $payment)
                                    <tr class="hover:bg-[#F8FAF7]">
                                        <td class="py-3 px-4">{{ $payment->created_at?->translatedFormat('d M Y, H:i') }}</td>
                                        <td class="py-3 px-4 font-semibold text-[#12271E] uppercase">{{ $payment->type_label }}</td>
                                        <td class="py-3 px-4 font-bold text-[#12271E]">{{ $payment->amount_formatted }}</td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full {{ $payment->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($payment->status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ $payment->status_label ?? $payment->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($payment->status === 'disetujui')
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('documents.receipt', $payment) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7]">
                                                        <span>{{ $payment->receipt_number }}</span>
                                                    </a>
                                                    <a href="{{ route('documents.receipt.download', $payment) }}" title="Unduh Excel Kwitansi" class="p-1 rounded-lg text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300">
                                                        <svg class="w-3 h-3 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-[#526057]/60 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @if($payment->proof_file)
                                                <a href="{{ $payment->proof_url }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-[#1B3B2B] hover:underline">
                                                    <span>Lihat Bukti</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                </a>
                                            @else
                                                <span class="text-[#526057]/60 italic">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 px-4 text-center text-xs text-[#526057]">
                                            Belum ada data setoran pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Konfirmasi Hapus Jamaah --}}
        <div x-show="deleteModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             @keydown.escape.window="deleteModalOpen = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-[#122B1F]/60 backdrop-blur-xs" @click="deleteModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E0E7DC] relative z-10"
                     @click.outside="deleteModalOpen = false">
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-bold text-[#122B1F]">Hapus Akun Jamaah?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[#526057]">
                                        Apakah Anda yakin ingin menghapus akun jamaah <span class="font-bold text-[#122B1F]">"{{ $user->name }}"</span> ({{ $user->phone_formatted }})?
                                    </p>
                                    <p class="text-xs text-red-600 mt-2 bg-red-50 p-2.5 rounded-lg border border-red-100">
                                        Perhatian: Seluruh data pendaftaran, berkas dokumen (KTP, KK, Paspor), dan riwayat setoran pembayaran milik akun ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl px-5 py-2.5 bg-red-600 text-xs font-bold text-white hover:bg-red-700 focus:outline-none transition-colors shadow-sm cursor-pointer">
                                Ya, Hapus Akun Jamaah
                            </button>
                        </form>
                        <button type="button" @click="deleteModalOpen = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-[#E0E7DC] bg-white px-5 py-2.5 text-xs font-semibold text-[#122B1F] hover:bg-[#F8FAF7] focus:outline-none transition-colors cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layouts.admin>
