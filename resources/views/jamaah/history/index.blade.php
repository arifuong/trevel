<x-layouts.jamaah :title="'Riwayat Perjalanan Ibadah — PT. Zein Internasional'">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

        {{-- Breadcrumb & Header --}}
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-xs text-[#526057]">
                <a href="{{ route('jamaah.dashboard') }}" class="hover:text-[#1B3B2B] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-[#12271E] font-semibold">Riwayat Perjalanan</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#12271E] tracking-tight">
                        Riwayat Perjalanan Ibadah
                    </h1>
                    <p class="text-xs sm:text-sm text-[#526057] mt-1">
                        Daftar perjalanan Umrah & Haji Khusus yang telah Anda selesaikan bersama PT. Zein Internasional.
                    </p>
                </div>
                <a href="{{ route('paket') }}" 
                   class="self-start sm:self-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#12271E] transition-all shadow-xs shrink-0">
                    <svg class="w-4 h-4 text-[#E5C88F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Daftar Paket Baru</span>
                </a>
            </div>
        </div>

        {{-- Daftar Perjalanan Selesai --}}
        @if($completedRegistrations->count() > 0)
            <div class="space-y-4">
                @foreach($completedRegistrations as $reg)
                    <div class="bg-white rounded-3xl border border-[#E0E7DC] p-5 sm:p-6 shadow-xs hover:border-[#1B3B2B]/30 transition-all flex flex-col md:flex-row md:items-center justify-between gap-5">
                        
                        {{-- Info Utama Paket --}}
                        <div class="space-y-3 min-w-0 flex-1">
                            {{-- Baris Status & Nomor Registrasi --}}
                            <div class="flex items-center flex-wrap gap-2">
                                <x-status-badge status="selesai" size="sm" />
                                
                                <span class="font-mono text-[11px] font-bold px-2.5 py-0.5 rounded-lg bg-[#EFF3EB] text-[#1B3B2B] border border-[#CCD8C7]">
                                    {{ $reg->registration_number }}
                                </span>

                                <span class="text-[11px] text-[#526057]">
                                    Diselesaikan pada {{ $reg->updated_at->translatedFormat('d F Y') }}
                                </span>
                            </div>

                            {{-- Nama Paket & Varian --}}
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-[#12271E] leading-snug">
                                    {{ $reg->package->name ?? 'Paket Ibadah' }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-[#526057]">
                                    @if($reg->packageVariant)
                                        <span class="font-semibold text-[#1B3B2B] bg-[#EFF3EB] px-2 py-0.5 rounded-md border border-[#E0E7DC]">
                                            {{ $reg->packageVariant->name }}
                                        </span>
                                    @endif
                                    <span class="capitalize">Kamar {{ $reg->room_type ?? 'Quad' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $reg->members->count() }} Jamaah</span>
                                </div>
                            </div>

                            {{-- Grid Info Ringkas --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1 text-xs">
                                <div class="bg-[#F7F5F0] rounded-xl p-2.5 border border-[#E0E7DC]">
                                    <span class="text-[10px] uppercase font-bold text-[#526057]/70 block">Keberangkatan</span>
                                    <span class="font-bold text-[#12271E] mt-0.5 block">
                                        {{ $reg->package?->departure_date ? \Carbon\Carbon::parse($reg->package->departure_date)->translatedFormat('d M Y') : 'TBA' }}
                                    </span>
                                    <span class="text-[10px] text-[#526057]">{{ $reg->package?->duration ?? 9 }} Hari</span>
                                </div>

                                <div class="bg-[#F7F5F0] rounded-xl p-2.5 border border-[#E0E7DC]">
                                    <span class="text-[10px] uppercase font-bold text-[#526057]/70 block">Total Biaya</span>
                                    <span class="font-bold text-emerald-800 mt-0.5 block">
                                        Rp {{ number_format($reg->invoice->total_price ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-emerald-700 font-medium">Lunas 100%</span>
                                </div>

                                <div class="col-span-2 sm:col-span-1 bg-[#F7F5F0] rounded-xl p-2.5 border border-[#E0E7DC]">
                                    <span class="text-[10px] uppercase font-bold text-[#526057]/70 block">Status Ibadah</span>
                                    <span class="font-bold text-[#1B3B2B] mt-0.5 block flex items-center gap-1">
                                        <span class="text-[#C2A264]">★</span>
                                        <span>Tuntas Berangkat & Pulang</span>
                                    </span>
                                    <span class="text-[10px] text-[#526057]">Tahap 9 Selesai</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex flex-col sm:flex-row md:flex-col gap-2 shrink-0 border-t md:border-t-0 md:border-l border-[#E0E7DC] pt-3 md:pt-0 md:pl-5">
                            <a href="{{ route('jamaah.my-registration', ['id' => $reg->id]) }}" 
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#12271E] transition-all shadow-xs">
                                <span>Lihat Detail</span>
                                <svg class="w-3.5 h-3.5 text-[#E5C88F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </a>

                            @if($reg->invoice)
                                <a href="{{ route('documents.invoice.download', $reg->id) }}" 
                                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    <span>Unduh Invoice</span>
                                </a>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($completedRegistrations->hasPages())
                <div class="pt-4">
                    {{ $completedRegistrations->links() }}
                </div>
            @endif

        @else
            {{-- Empty State Riwayat --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] p-8 sm:p-12 text-center shadow-xs space-y-4">
                <div class="w-16 h-16 rounded-3xl bg-[#EFF3EB] text-[#1B3B2B] flex items-center justify-center mx-auto border border-[#CCD8C7]">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="space-y-1.5">
                    <h2 class="text-xl font-bold text-[#12271E]">Belum Ada Riwayat Perjalanan</h2>
                    <p class="text-xs sm:text-sm text-[#526057] max-w-md mx-auto leading-relaxed">
                        Anda belum memiliki perjalanan ibadah yang telah selesai. Pendaftaran aktif Anda dapat dipantau melalui menu Pendaftaran.
                    </p>
                </div>
                <div class="pt-2 flex items-center justify-center gap-3">
                    <a href="{{ route('jamaah.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#12271E] bg-[#EFF3EB] hover:bg-[#E0E7DC] border border-[#CCD8C7] transition-colors">
                        &larr; Kembali ke Beranda
                    </a>
                    <a href="{{ route('paket') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#12271E] transition-all shadow-xs">
                        Lihat Pilihan Paket
                    </a>
                </div>
            </div>
        @endif

    </div>

</x-layouts.jamaah>
