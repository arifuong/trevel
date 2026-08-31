<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $payment->receipt_number }} — PT. Zein Internasional</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .receipt-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
        .dotted-line {
            border-bottom: 1.5px dotted #64748b;
        }
    </style>
</head>
<body class="bg-zinc-100 text-zinc-900 py-4 sm:py-8 antialiased">

    {{-- Top Action Bar (Screen Only) --}}
    <div class="no-print max-w-4xl mx-auto px-4 mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ url()->previous() ?: route('jamaah.my-registration') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-zinc-700 bg-white hover:bg-zinc-50 border border-zinc-300 shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Kembali</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-95 shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.056.867-1.829 1.89-1.829h7.78c1.023 0 2.13.773 1.89 1.829l-1.004 4.417c-.172.756-.84 1.284-1.616 1.284H8.34c-.776 0-1.444-.528-1.616-1.284l-1.004-4.417zM6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 9V4a1 1 0 011-1h10a1 1 0 011 1v5"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- Sheet Canvas (2-Ply Format: Lembar 1 Asli & Lembar 2 Copy) --}}
    <div class="receipt-page max-w-4xl mx-auto bg-white border border-zinc-200 shadow-xl rounded-2xl p-6 sm:p-8 space-y-6">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- LEMBAR 1: ASLI (UNTUK JAMAAH)                                --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="space-y-4 pb-4">
            
            {{-- Header Kwitansi (100% Menggunakan Aset Template Master Asli) --}}
            <div class="flex justify-between items-start gap-4">
                
                {{-- Logo & Legalitas Perusahaan --}}
                <div class="max-w-[320px]">
                    <img src="{{ asset('images/kwt_logo.png') }}" alt="Zein Tour — PT. ZEIN INTERNASIONAL" class="w-full h-auto object-contain select-none">
                </div>

                {{-- Header Kanan: KWITANSI Badge & Metadata --}}
                <div class="w-64 space-y-2 pt-1">
                    {{-- Badge KWITANSI Asli Master --}}
                    <div class="flex justify-end">
                        <img src="{{ asset('images/kwt_badge.png') }}" alt="KWITANSI" class="h-8 w-auto object-contain select-none">
                    </div>

                    {{-- Grid Metadata NOMOR / TANGGAL dengan Garis Vertikal --}}
                    <div class="flex items-center justify-end text-[11px] font-sans pt-1">
                        <div class="text-right pr-3.5 space-y-1 font-bold text-black tracking-wider text-[11px]">
                            <div>NOMOR</div>
                            <div>TANGGAL</div>
                        </div>
                        <div class="w-[1.5px] h-9 bg-[#385623]/80"></div>
                        <div class="text-center pl-3.5 space-y-1 font-bold font-mono text-black text-[11px] min-w-[130px]">
                            <div>{{ $payment->receipt_number }}</div>
                            <div>{{ $payment->verified_at ? $payment->verified_at->translatedFormat('d F Y') : ($payment->created_at ? $payment->created_at->translatedFormat('d F Y') : date('d F Y')) }}</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Body Kwitansi (Dotted Fields Sesuai Template) --}}
            <div class="space-y-2 text-[11px] pt-1">
                <div class="flex items-end">
                    <span class="w-44 font-bold text-black uppercase shrink-0">TELAH TERIMA DARI</span>
                    <span class="mx-1 font-bold shrink-0">:</span>
                    <div class="grow dotted-line font-bold text-black uppercase px-1 pb-0.5 tracking-wide">
                        {{ $user->name }}
                    </div>
                </div>

                <div class="flex items-end">
                    <span class="w-44 font-bold text-black uppercase shrink-0">UANG SEJUMLAH</span>
                    <span class="mx-1 font-bold shrink-0">:</span>
                    <div class="grow dotted-line font-bold italic text-black px-1 pb-0.5">
                        {{ strtoupper($terbilang) }}
                    </div>
                </div>

                <div class="flex items-end">
                    <span class="w-44 font-bold text-black uppercase shrink-0">UNTUK PEMBAYARAN</span>
                    <span class="mx-1 font-bold shrink-0">:</span>
                    <div class="grow dotted-line font-bold text-black px-1 pb-0.5">
                        {{ $package->name ?? 'Paket Umrah' }}
                    </div>
                </div>

                {{-- Garis Dotted Lanjutan Kedua --}}
                <div class="flex items-end">
                    <span class="w-44 shrink-0"></span>
                    <span class="mx-1 shrink-0 invisible">:</span>
                    <div class="grow dotted-line pb-0.5"></div>
                </div>
            </div>

            {{-- Footer Kwitansi Lembar 1 --}}
            <div class="pt-3 flex justify-between items-end gap-6 text-[10px]">
                
                {{-- Nominal Badge & Ketentuan Box --}}
                <div class="space-y-3 max-w-md">
                    <div class="flex items-center gap-3">
                        <span class="font-black italic uppercase text-black text-sm tracking-wider">NOMINAL</span>
                        <span class="inline-block bg-[#70AD47] text-black font-black text-sm font-mono py-1 px-4 rounded shadow-2xs">
                            {{ $payment->amount_formatted }}
                        </span>
                    </div>

                    <div class="border border-[#70AD47] rounded p-2 text-[9.5px] italic text-zinc-900 text-center font-serif leading-snug">
                        Untuk menghindari hal-hal yang tidak diinginkan, mohon pastikan semua pembayaran dilakukan sebelum batas waktu yang telah ditentukan (Paling Lambat 35 Hari sebelum keberangkatan)
                    </div>
                </div>

                {{-- Tanda Tangan Penerima --}}
                <div class="text-center w-56 space-y-12 pb-1">
                    <p class="text-[10px] text-zinc-900">
                        Bandung, {{ $payment->verified_at ? $payment->verified_at->translatedFormat('d F Y') : date('d F Y') }}
                        <span class="block font-bold uppercase mt-0.5">PENERIMA,</span>
                    </p>

                    <div class="font-normal text-zinc-900 tracking-widest text-[11px]">
                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                    </div>
                </div>

            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- PEMBATAS POTONG (DOTTED CUT LINE)                            --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="relative py-1 border-t-2 border-dashed border-zinc-400">
            <span class="absolute right-0 -top-3 bg-yellow-300 border border-yellow-500 text-yellow-950 font-black text-[9px] px-3 py-0.5 rounded shadow-2xs">
                Copy
            </span>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- LEMBAR 2: COPY / ARSIP KANTOR                                --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="space-y-4 pt-1">
            
            {{-- Header Kwitansi Copy --}}
            <div class="flex justify-between items-start gap-4">
                
                {{-- Logo & Legalitas Perusahaan (Official Master Asset) --}}
                <div class="max-w-[320px]">
                    <img src="{{ asset('images/kwt_logo2.png') }}" alt="Zein Tour — PT. ZEIN INTERNASIONAL" class="w-full h-auto object-contain select-none">
                </div>

                {{-- Header Kanan: KWITANSI Badge & Metadata --}}
                <div class="w-64 space-y-2 pt-1">
                    {{-- Badge KWITANSI Asli Master --}}
                    <div class="flex justify-end">
                        <img src="{{ asset('images/kwt_badge.png') }}" alt="KWITANSI" class="h-8 w-auto object-contain select-none">
                    </div>

                    {{-- Grid Metadata NOMOR / TANGGAL dengan Garis Vertikal --}}
                    <div class="flex items-center justify-end text-[10.5px] font-sans pt-1">
                        <div class="text-right pr-3.5 space-y-1 font-bold text-black tracking-wider text-[11px]">
                            <div>NOMOR</div>
                            <div>TANGGAL</div>
                        </div>
                        <div class="w-[1.5px] h-9 bg-[#385623]/80"></div>
                        <div class="text-center pl-3.5 space-y-1 font-bold font-mono text-black text-[11px] min-w-[130px]">
                            <div>{{ $payment->receipt_number }}</div>
                            <div>{{ $payment->verified_at ? $payment->verified_at->translatedFormat('d F Y') : ($payment->created_at ? $payment->created_at->translatedFormat('d F Y') : date('d F Y')) }}</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Body Kwitansi Copy (Dotted Fields) --}}
            <div class="space-y-2 text-[11px] pt-1">
                <div class="flex items-end">
                    <span class="w-44 font-bold text-black uppercase shrink-0">TELAH TERIMA DARI</span>
                    <span class="mx-1 font-bold shrink-0">:</span>
                    <div class="grow dotted-line font-bold text-black uppercase px-1 pb-0.5 tracking-wide">
                        {{ $user->name }}
                    </div>
                </div>

                <div class="flex items-end">
                    <span class="w-44 font-bold text-black uppercase shrink-0">UANG SEJUMLAH</span>
                    <span class="mx-1 font-bold shrink-0">:</span>
                    <div class="grow dotted-line font-bold italic text-black px-1 pb-0.5">
                        {{ strtoupper($terbilang) }}
                    </div>
                </div>

                <div class="flex items-end">
                    <span class="w-44 font-bold text-black uppercase shrink-0">UNTUK PEMBAYARAN</span>
                    <span class="mx-1 font-bold shrink-0">:</span>
                    <div class="grow dotted-line font-bold text-black px-1 pb-0.5">
                        {{ $package->name ?? 'Paket Umrah' }}
                    </div>
                </div>

                {{-- Garis Dotted Lanjutan Kedua --}}
                <div class="flex items-end">
                    <span class="w-44 shrink-0"></span>
                    <span class="mx-1 shrink-0 invisible">:</span>
                    <div class="grow dotted-line pb-0.5"></div>
                </div>
            </div>

            {{-- Footer Kwitansi Lembar 2 --}}
            <div class="pt-3 flex justify-between items-end gap-6 text-[10px]">
                
                {{-- Nominal Badge & Catatan Box --}}
                <div class="space-y-3 max-w-md">
                    <div class="flex items-center gap-3">
                        <span class="font-black italic uppercase text-black text-sm tracking-wider">NOMINAL</span>
                        <span class="inline-block bg-[#70AD47] text-black font-black text-sm font-mono py-1 px-4 rounded shadow-2xs">
                            {{ $payment->amount_formatted }}
                        </span>
                    </div>

                    <div class="border border-[#70AD47] rounded p-2 text-[9.5px] italic text-zinc-900 text-center font-serif leading-snug">
                        Untuk menghindari hal-hal yang tidak diinginkan, mohon pastikan semua pembayaran dilakukan sebelum batas waktu yang telah ditentukan (Paling Lambat 35 Hari sebelum keberangkatan)
                    </div>
                </div>

                {{-- Tanda Tangan Penerima --}}
                <div class="text-center w-56 space-y-12 pb-1">
                    <p class="text-[10px] text-zinc-900">
                        Bandung, {{ $payment->verified_at ? $payment->verified_at->translatedFormat('d F Y') : date('d F Y') }}
                        <span class="block font-bold uppercase mt-0.5">PENERIMA,</span>
                    </p>

                    <div class="font-normal text-zinc-900 tracking-widest text-[11px]">
                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                    </div>
                </div>

            </div>

            {{-- Catatan di Lembar Copy Sesuai Template Asli --}}
            <div class="pt-1.5 text-[10px] text-black flex items-end">
                <span class="font-bold shrink-0">Catatan :</span>
                <div class="grow dotted-line ml-2 pb-0.5"></div>
            </div>

        </div>

    </div>

</body>
</html>
