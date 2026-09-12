<x-layouts.jamaah :title="'Beranda Jamaah — PT. Zein Internasional'">

    {{-- ═══════════════════════════════════════════════════════════════
         1. KHUSUS TAMPILAN MOBILE (< 1024px)
         Konsep App Dashboard: Header → Card Ringkasan Statistik → Shortcut → Aktivitas → Informasi
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="lg:hidden px-3.5 sm:px-4 py-4 space-y-4 max-w-lg mx-auto">

        {{-- Pesan Notifikasi Flash --}}
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5 shadow-2xs">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2.5 shadow-2xs">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Mobile Header: Greeting & User Profile Card --}}
        <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 sm:p-4 shadow-2xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center text-lg font-bold shadow-xs border border-[#CCD8C7] overflow-hidden shrink-0">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ $user->initials }}
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="text-[9.5px] uppercase font-bold tracking-wider text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-full border border-emerald-200 inline-block mb-1">
                        Jamaah Terverifikasi
                    </span>
                    <h1 class="text-sm sm:text-base font-bold text-[#12271E] tracking-tight truncate leading-tight">
                        Assalamu'alaikum, {{ explode(' ', $user->name)[0] }}
                    </h1>
                    <p class="text-[11px] text-[#526057] truncate mt-0.5">
                        {{ $user->phone_formatted }}
                    </p>
                </div>
            </div>
            <a href="{{ route('jamaah.profile') }}" 
               class="p-2.5 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] hover:bg-[#E0E7DC] border border-[#CCD8C7] shrink-0 active:scale-95 transition-all" 
               title="Buka Profil Saya">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </a>
        </div>

        {{-- Mobile Card Utama: Ringkasan & Statistik Ibadah --}}
        @if($registration)
            <div class="bg-gradient-to-br from-[#1B3B2B] via-[#173527] to-[#10241A] text-white rounded-3xl p-4 sm:p-5 shadow-lg border border-white/10 relative overflow-hidden space-y-3.5">
                {{-- Decorative Glow Accent --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#C2A264]/15 rounded-full blur-2xl pointer-events-none"></div>

                {{-- Header Card: Status & No. Registrasi --}}
                <div class="flex items-center justify-between gap-2 border-b border-white/10 pb-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-xs text-[10px] font-bold tracking-wide text-emerald-300 border border-white/15 truncate max-w-[60%]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span class="truncate">{{ $registration->status_label }}</span>
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-white/15 border border-white/25 text-[11px] font-mono font-bold text-emerald-200 tracking-wider shrink-0 shadow-2xs">
                        {{ $registration->registration_number }}
                    </span>
                </div>

                {{-- Paket Title --}}
                <div class="space-y-0.5">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-[#C2A264] block">
                        Paket Pilihan
                    </span>
                    <h2 class="text-base sm:text-lg font-bold text-white tracking-tight leading-snug line-clamp-2">
                        {{ $registration->package->name ?? 'Paket Perjalanan Umrah' }}
                    </h2>
                    @if($registration->packageVariant)
                        <p class="text-xs text-emerald-200 font-medium">
                            Varian {{ $registration->packageVariant->name }} &bull; Kamar {{ ucfirst($registration->room_type ?? 'Quad') }}
                        </p>
                    @endif
                </div>

                {{-- Grid 2-Kolom Ringkasan Statistik (Angka Menonjol, Anti-Overflow pada 320px) --}}
                <div class="grid grid-cols-2 gap-2.5 sm:gap-3 bg-black/30 backdrop-blur-xs rounded-2xl p-3 sm:p-3.5 border border-white/15">
                    <div class="min-w-0">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-emerald-200 block leading-tight">
                            Tahap Ibadah
                        </span>
                        <div class="text-sm sm:text-base font-extrabold text-white mt-1 truncate" title="{{ $registration->status_label }}">
                            {{ $registration->status_label }}
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-0.5">
                            {{ $registration->isCompleted() ? 'Perjalanan Tuntas ✓' : 'Dalam Proses' }}
                        </span>
                    </div>

                    <div class="min-w-0">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-emerald-200 block leading-tight">
                            Keberangkatan
                        </span>
                        <div class="text-sm sm:text-base font-extrabold text-white mt-1 truncate">
                            {{ $registration->package->departure_date ? $registration->package->departure_date->translatedFormat('d M Y') : 'Reguler' }}
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-0.5">{{ $registration->package->duration ?? 9 }} Hari</span>
                    </div>

                    <div class="min-w-0 pt-2.5 border-t border-white/10">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-emerald-200 block leading-tight">
                            Sudah Dibayar
                        </span>
                        <div class="text-sm sm:text-base font-extrabold text-emerald-300 mt-1 truncate">
                            Rp {{ number_format($registration->invoice->total_paid ?? 0, 0, ',', '.') }}
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-0.5">Terverifikasi</span>
                    </div>

                    <div class="min-w-0 pt-2.5 border-t border-white/10">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-[#E5C88F] block leading-tight">
                            Sisa Pelunasan
                        </span>
                        <div class="text-sm sm:text-base font-extrabold {{ ($registration->invoice->remaining_balance ?? 0) <= 0 ? 'text-emerald-300' : 'text-amber-300' }} mt-1 truncate">
                            @if(($registration->invoice->remaining_balance ?? 0) <= 0)
                                LUNAS ✓
                            @else
                                Rp {{ number_format($registration->invoice->remaining_balance, 0, ',', '.') }}
                            @endif
                        </div>
                        <span class="text-[9.5px] text-white/70 block truncate mt-0.5">{{ $registration->members->count() }} Jamaah</span>
                    </div>
                </div>

                {{-- Progress Bar Tahapan --}}
                @if($registration->status !== 'dibatalkan')
                    <div class="space-y-1.5 pt-0.5">
                        <div class="flex items-center justify-between text-[10px] text-white/90 font-medium">
                            <span>Progres Kesiapan Ibadah</span>
                            <span class="font-bold text-[#E5C88F]">{{ $registration->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-white/15 rounded-full h-2.5 overflow-hidden border border-white/10">
                            <div class="bg-gradient-to-r from-[#C2A264] to-emerald-400 h-full rounded-full transition-all duration-500 shadow-sm" style="width: {{ $registration->progress_percentage }}%"></div>
                        </div>
                    </div>
                @endif

                {{-- Tombol Aksi Cepat --}}
                <div class="pt-1 flex items-center gap-2">
                    @if($registration->status === 'menunggu_pembayaran_dp' || $registration->status === 'dokumen_disetujui')
                        <a href="{{ route('jamaah.payment.dp') }}" 
                           class="flex-1 py-2.5 px-3 rounded-xl bg-[#C2A264] hover:bg-[#b09153] text-[#12271E] font-bold text-xs text-center shadow-md transition-all truncate">
                            Bayar DP &rarr;
                        </a>
                    @elseif(in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                        <a href="{{ route('jamaah.payment.pelunasan') }}" 
                           class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs text-center shadow-md transition-all truncate">
                            Setor Pelunasan &rarr;
                        </a>
                    @endif
                    <a href="{{ route('jamaah.my-registration') }}" 
                       class="flex-1 py-2.5 px-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs text-center border border-white/20 transition-all truncate">
                        Detail Pendaftaran
                    </a>
                </div>
            </div>
        @else
            {{-- Mobile Card: Belum Ada Pendaftaran --}}
            <div class="bg-gradient-to-br from-[#1B3B2B] via-[#173527] to-[#10241A] text-white rounded-3xl p-5 shadow-lg border border-white/10 relative overflow-hidden space-y-3.5 text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-white/10 text-emerald-300 flex items-center justify-center border border-white/15 shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-[#C2A264] block">Langkah Awal</span>
                    <h2 class="text-base font-bold text-white tracking-tight">Siap Menuju Baitullah?</h2>
                    <p class="text-xs text-zinc-300 leading-relaxed max-w-xs mx-auto">
                        Pilih paket ibadah Umrah & Haji Khusus terbaik dengan fasilitas nyaman dan bimbingan resmi sesuai Sunnah.
                    </p>
                </div>
                <div class="pt-1 flex flex-col gap-2">
                    <a href="{{ route('paket') }}" class="w-full py-2.5 px-4 rounded-xl bg-[#C2A264] hover:bg-[#b09153] text-[#12271E] font-bold text-xs shadow-md transition-all">
                        Lihat Pilihan Paket &rarr;
                    </a>
                    <a href="{{ route('jamaah.registration.create') }}" class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition-all">
                        Daftar Paket Sekarang
                    </a>
                </div>
            </div>
        @endif

        {{-- Mobile Menu Utama / Shortcut Cepat (Grid 4 Kolom) --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Menu Layanan</h2>
                <span class="text-[10px] text-[#526057]">Akses Cepat</span>
            </div>
            <div class="grid grid-cols-4 gap-2">
                
                {{-- 1. Pendaftaran / Dokumen --}}
                <a href="{{ route('jamaah.my-registration') }}" 
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Dokumen</span>
                </a>

                {{-- 2. Katalog Paket & Manasik --}}
                <a href="{{ route('paket') }}" 
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Paket &amp; Manasik</span>
                </a>

                {{-- 3. Pembayaran --}}
                @php
                    $mobilePayUrl = ($registration && ($registration->status === 'menunggu_pembayaran_dp' || $registration->status === 'dokumen_disetujui'))
                        ? route('jamaah.payment.dp')
                        : (($registration && in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                            ? route('jamaah.payment.pelunasan')
                            : route('jamaah.my-registration'));
                @endphp
                <a href="{{ $mobilePayUrl }}" 
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Pembayaran</span>
                </a>

                {{-- 4. Bantuan CS WhatsApp --}}
                <a href="https://wa.me/6281222222562?text=Assalamu%27alaikum%20Zein%20Tour,%20saya%20jamaah%20ingin%20konsultasi" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="flex flex-col items-center justify-center p-2 rounded-2xl bg-white border border-[#E0E7DC] hover:border-[#1B3B2B]/40 shadow-2xs text-center group active:scale-95 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-1 group-hover:bg-[#1B3B2B] group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a.75.75 0 01-.84-.84 5.97 5.97 0 001.057-3.035C4.606 15.688 4 13.928 4 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                    </div>
                    <span class="text-[9.5px] font-bold text-[#12271E] leading-tight block truncate w-full">Bantuan CS</span>
                </a>

            </div>
        </div>

        {{-- Mobile Informasi Jamaah Terdaftar --}}
        @if($registration && $registration->members->isNotEmpty())
            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 shadow-2xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Anggota Terdaftar</span>
                    <span class="text-[10px] font-bold text-[#1B3B2B] bg-[#EFF3EB] px-2 py-0.5 rounded-full border border-[#CCD8C7]">
                        {{ $registration->members->count() }} Orang
                    </span>
                </div>
                <div class="divide-y divide-[#EFF3EB]">
                    @foreach($registration->members as $idx => $member)
                        <div class="py-2 flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-5 h-5 rounded-full bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center font-bold text-[10px] shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-bold text-[#12271E] truncate">{{ $member->full_name }}</span>
                            </div>
                            <span class="text-[10px] text-[#526057] capitalize shrink-0 font-medium">
                                {{ $member->relationship_label ?? $member->gender }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Mobile Panduan & Informasi Penting --}}
        <div class="space-y-2.5">
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54]">Panduan Ibadah</h2>

            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 shadow-2xs flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#12271E]">Persyaratan Paspor & Berkas</h3>
                    <p class="text-[11px] text-[#526057] leading-relaxed mt-0.5">
                        Pastikan paspor Anda masih berlaku minimal 8 bulan dari tanggal keberangkatan.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#E0E7DC] p-3.5 shadow-2xs flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#12271E]">Izin Resmi PPIU & PIHK</h3>
                    <p class="text-[11px] text-[#526057] leading-relaxed mt-0.5">
                        PT. Zein Internasional memiliki izin PPIU No. U.255/2020 & PIHK No. 599/2021 Kemenag RI.
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         2. KHUSUS TAMPILAN DESKTOP & TABLET LEBAR (>= 1024px)
         Mempertahankan Desain Desktop yang Sudah Ada Tanpa Perubahan
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="hidden lg:block max-w-6xl mx-auto px-6 lg:px-8 py-10 space-y-8">

        {{-- 1. Kartu Sambutan & Profil Jamaah --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-52 h-52 bg-[#EFF3EB]/70 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                
                {{-- Avatar & Informasi Profil --}}
                <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                    {{-- Inisial Nama / Foto Profil --}}
                    <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center text-xl sm:text-2xl font-bold shadow-md shadow-[#1B3B2B]/20 shrink-0 ring-4 ring-[#EFF3EB] border border-[#CCD8C7] overflow-hidden">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $user->initials }}
                        @endif
                    </div>

                    <div class="min-w-0 space-y-1">
                        {{-- LEVEL 1: Greeting --}}
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#12271E] tracking-tight truncate">
                            Assalamu'alaikum, {{ explode(' ', $user->name)[0] }}
                        </h1>

                        {{-- LEVEL 2: Contact Info --}}
                        <p class="text-xs sm:text-sm text-[#526057] flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span class="truncate">{{ $user->email }}</span>
                            <span class="text-[#CCD8C7] hidden sm:inline">&bull;</span>
                            <span class="font-medium text-[#1B3B2B]">{{ $user->phone_formatted }}</span>
                        </p>

                        {{-- LEVEL 3: Badge Status Akun --}}
                        <div class="pt-1">
                            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/80 px-3 py-0.5 rounded-full border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Akun Terverifikasi
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tautan Cepat / Action Buttons --}}
                <div class="flex items-center gap-3 self-stretch sm:self-start lg:self-center shrink-0 flex-wrap sm:flex-nowrap">
                    <a href="{{ route('jamaah.profile') }}" 
                       class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7] shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <span>Lihat Profil</span>
                    </a>
                    <a href="{{ route('paket') }}" 
                       class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        <span>Pilihan Paket</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Pesan Notifikasi --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs sm:text-sm text-amber-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span class="font-medium leading-relaxed">{{ session('warning') }}</span>
            </div>
        @endif

        {{-- 2. Status Pendaftaran Aktif / Tampilan Belum Ada Data --}}
        @if($registration)
            {{-- Kartu Pendaftaran Aktif --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xs overflow-hidden">
                
                {{-- Header Kartu & Informasi Paket --}}
                <div class="p-6 sm:p-8 border-b border-[#E0E7DC] bg-gradient-to-br from-[#EFF3EB]/50 via-white to-white">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                        
                        {{-- Detail Paket --}}
                        <div class="min-w-0 space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#4D5E54] block">
                                Informasi Paket
                            </span>

                            {{-- Nama Paket --}}
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#12271E] tracking-tight leading-tight">
                                {{ $registration->package->name ?? 'Paket Pilihan Umrah' }}
                                @if($registration->packageVariant)
                                    <span class="text-[#526057] font-normal text-lg sm:text-xl block sm:inline">— {{ $registration->packageVariant->name }}</span>
                                @endif
                            </h2>

                            {{-- Metadata Periode, Durasi, Jumlah Jamaah --}}
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm text-[#526057] pt-1">
                                @if($registration->packageVariant)
                                    <span class="inline-flex items-center gap-1.5 bg-[#1B3B2B] text-white px-3 py-1 rounded-xl font-bold">
                                        <span>Varian {{ $registration->packageVariant->name }}</span>
                                    </span>
                                @endif

                                @if($registration->room_type)
                                    <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                        <span>Kamar: {{ ucfirst($registration->room_type) }}</span>
                                    </span>
                                @endif

                                @if($registration->package->departure_date)
                                    <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                                        <span>{{ $registration->package->departure_date->translatedFormat('d F Y') }}</span>
                                    </span>
                                @endif

                                @if($registration->package->duration)
                                    <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $registration->package->duration }} Hari</span>
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1.5 bg-[#EFF3EB] px-3 py-1 rounded-xl font-semibold text-[#12271E] border border-[#CCD8C7]">
                                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                                    <span>Terdaftar untuk {{ $registration->members->count() }} Jamaah</span>
                                </span>
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="shrink-0 self-start lg:self-start">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs sm:text-sm font-bold bg-[#EFF3EB] text-[#1B3B2B] border border-[#CCD8C7] shadow-2xs">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#1B3B2B] animate-pulse shrink-0"></span>
                                <span>{{ $registration->status_label }}</span>
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Konten Rincian & Indikator Tahapan --}}
                <div class="p-6 sm:p-8 space-y-6 sm:space-y-7">
                    
                    {{-- Progres Tahapan --}}
                    @php
                        $step = $registration->step_number;
                        $isCancelled = $registration->status === 'dibatalkan';
                    @endphp
                    @if(!$isCancelled)
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs sm:text-sm font-bold text-[#12271E]">
                                <span>Tahap Persiapan Ibadah</span>
                                <span class="text-[#1B3B2B] font-semibold">Tahap {{ $step }} dari {{ \App\Models\Registration::TOTAL_STEPS }} ({{ $registration->progress_percentage }}%)</span>
                            </div>
                            <div class="w-full bg-[#EFF3EB] rounded-full h-3 overflow-hidden border border-[#CCD8C7]">
                                <div class="bg-[#1B3B2B] h-full rounded-full transition-all duration-500" style="width: {{ $registration->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-900 space-y-1">
                            <span class="font-bold text-red-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Pendaftaran Telah Dibatalkan</span>
                            </span>
                            <p class="text-red-700 text-xs">Pendaftaran ini telah resmi dibatalkan dan tidak memiliki kewajiban pembayaran aktif.</p>
                        </div>
                    @endif

                    {{-- Ringkasan Biaya (3 Kolom Proporsional) --}}
                    @if($registration->invoice)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-[#EFF3EB]/50 p-5 sm:p-6 rounded-2xl border border-[#CCD8C7]">
                            <div class="min-w-0">
                                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#4D5E54] block mb-1">
                                    Total Biaya
                                </span>
                                <span class="text-base sm:text-lg font-bold text-[#12271E] block truncate">
                                    Rp {{ number_format($registration->invoice->total_price, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-800 block mb-1">
                                    Sudah Dibayar
                                </span>
                                <span class="text-base sm:text-lg font-bold text-emerald-700 block truncate">
                                    Rp {{ number_format($registration->invoice->total_paid, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#4D5E54] block mb-1">
                                    Sisa Pembayaran
                                </span>
                                <span class="text-base sm:text-lg font-bold {{ $registration->invoice->remaining_balance > 0 ? 'text-[#1B3B2B]' : 'text-emerald-700' }} block truncate">
                                    {{ $registration->invoice->remaining_balance > 0 ? 'Rp ' . number_format($registration->invoice->remaining_balance, 0, ',', '.') : 'Lunas ✓' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Tombol Aksi Utama --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                        <a href="{{ route('jamaah.my-registration') }}" 
                           class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md shadow-[#1B3B2B]/15">
                            <span>Lihat Rincian Pendaftaran</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>

                        @if($registration->status === 'menunggu_pembayaran_dp' || $registration->status === 'dokumen_disetujui')
                            <a href="{{ route('jamaah.payment.dp') }}" 
                                class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 transition-colors border border-amber-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/></svg>
                                <span>Bayar DP Sekarang</span>
                            </a>
                        @elseif($registration->status === 'menunggu_verifikasi_pembayaran_dp')
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-amber-800 bg-amber-50 border border-amber-200">
                                <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Verifikasi Pembayaran DP</span>
                            </span>
                        @elseif(in_array($registration->status, ['jamaah', 'cicilan_pelunasan']))
                            <a href="{{ route('jamaah.payment.pelunasan') }}" 
                                class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-emerald-900 bg-emerald-100 hover:bg-emerald-200 transition-colors border border-emerald-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Setor Pelunasan</span>
                            </a>
                        @endif
                    </div>

                </div>

            </div>
        @else
            {{-- Tampilan Belum Ada Pendaftaran --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-8 sm:p-12 text-center shadow-xs">
                <div class="w-20 h-20 rounded-3xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mx-auto mb-5 shadow-xs border border-[#CCD8C7]">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-[#12271E] mb-2">
                    Belum Ada Pendaftaran
                </h2>
                <p class="text-xs sm:text-sm text-[#526057] max-w-md mx-auto mb-8 leading-relaxed">
                    Anda belum memiliki paket perjalanan ibadah. Yuk, lihat pilihan paket umrah dan haji khusus yang tersedia bersama PT. Zein Internasional.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('paket') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all shadow-md shadow-[#1B3B2B]/15">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        <span>Lihat Paket Umrah</span>
                    </a>
                    <a href="{{ route('jamaah.registration.create') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs font-bold text-[#12271E] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors border border-[#CCD8C7]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span>Daftar Sekarang</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- 3. Panduan Persiapan & Jaminan Layanan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-4 border border-[#CCD8C7]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#12271E] mb-2">Persyaratan Dokumen</h3>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Siapkan Paspor asli (masa berlaku minimal 8 bulan), KTP, Kartu Keluarga, dan Buku Nikah atau Akta Lahir bagi anggota keluarga.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-4 border border-[#CCD8C7]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#12271E] mb-2">Bimbingan & Manasik</h3>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Setiap calon jamaah mendapatkan pembekalan intensif tata cara ibadah sesuai Sunnah sebelum hari keberangkatan.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-7 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mb-4 border border-[#CCD8C7]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#12271E] mb-2">Izin PPIU Resmi</h3>
                    <p class="text-xs sm:text-sm text-[#526057] leading-relaxed">
                        Izin PPIU No. U.255/2020 oleh Kemenag RI dengan jaminan 5 Pasti Umrah: Pasti Travel, Jadwal, Terbang, Hotel, dan Visa.
                    </p>
                </div>
            </div>

        </div>

    </div>

</x-layouts.jamaah>
