<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} — PT. Zein Internasional</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
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
            .invoice-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
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
            <a href="{{ route('documents.invoice.pdf', $registration) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] shadow-xs transition-all">
                <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                <span>Unduh PDF</span>
            </a>
            <a href="{{ route('documents.invoice.download', $registration) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 shadow-xs transition-all">
                <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                <span>Unduh Excel</span>
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-zinc-700 bg-white hover:bg-zinc-50 border border-zinc-300 active:scale-95 shadow-xs transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.056.867-1.829 1.89-1.829h7.78c1.023 0 2.13.773 1.89 1.829l-1.004 4.417c-.172.756-.84 1.284-1.616 1.284H8.34c-.776 0-1.444-.528-1.616-1.284l-1.004-4.417zM6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 9V4a1 1 0 011-1h10a1 1 0 011 1v5"/></svg>
                <span>Cetak Browser</span>
            </button>
        </div>
    </div>

    {{-- Sheet Canvas --}}
    <div class="invoice-page max-w-4xl mx-auto bg-white border border-zinc-200 shadow-xl rounded-2xl p-8 sm:p-10 text-[11px] leading-tight space-y-6">

        {{-- 1. Header Bagian Atas (100% Identik dengan Master Template) --}}
        <div class="space-y-2">
            {{-- Top Banner: Logo Zein Tour & Badge INVOICE --}}
            <div class="w-full">
                <img src="{{ asset('images/image2.png') }}" alt="Zein Tour Umrah & Haji Khusus — Invoice" class="w-full h-auto object-contain select-none">
            </div>

            {{-- Sub-Header: Identitas Perusahaan (Kiri) & Metadata Box (Kanan) --}}
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pt-1">
                {{-- Identitas Perusahaan Lengkap & Kontak --}}
                <div class="w-full sm:w-[58%]">
                    <img src="{{ asset('images/image1.png') }}" alt="PT. ZEIN INTERNASIONAL" class="w-full h-auto object-contain select-none">
                </div>

                {{-- Metadata Box: DATE / INVOICE / CUSTOMER ID & KEPADA YTH --}}
                <div class="w-full sm:w-[40%] space-y-3 pt-0.5">
                    {{-- Grid Input Metadata --}}
                    <div class="space-y-1 text-[10px]">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-zinc-700 tracking-wider w-28 text-right sm:text-left">DATE</span>
                            <div class="flex-1 border border-zinc-300 rounded px-2.5 py-1 text-center font-bold font-mono text-zinc-900 bg-white shadow-2xs">
                                {{ $invoice->created_at ? $invoice->created_at->format('d/m/Y') : date('d/m/Y') }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-zinc-700 tracking-wider w-28 text-right sm:text-left">INVOICE</span>
                            <div class="flex-1 border border-zinc-300 rounded px-2.5 py-1 text-center font-bold font-mono text-zinc-900 bg-white shadow-2xs">
                                {{ $invoice->invoice_number }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-zinc-700 tracking-wider w-28 text-right sm:text-left">CUSTOMER ID</span>
                            <div class="flex-1 border border-zinc-300 rounded px-2.5 py-1 text-center font-bold font-mono text-zinc-900 bg-white shadow-2xs">
                                {{ $invoice->customer_id }}
                            </div>
                        </div>
                    </div>

                    {{-- Kepada Yth Box --}}
                    <div class="flex items-center justify-between gap-2 pt-1.5">
                        <span class="font-bold text-zinc-700 tracking-wider w-28 text-right sm:text-left text-[10px]">KEPADA YTH,</span>
                        <div class="flex-1 border border-zinc-300 rounded px-2.5 py-1 text-center font-bold text-[11px] text-zinc-900 bg-white uppercase shadow-2xs truncate">
                            {{ $user->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Tabel Utama Paket & Rincian Harga --}}
        <div class="border border-zinc-400 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-white text-[10px] uppercase font-bold tracking-wide text-center">
                        <th class="py-1.5 px-2 bg-[#385623] border-r border-white w-10">NO.</th>
                        <th class="py-1.5 px-3 bg-[#70AD47] border-r border-white text-center">PAKET</th>
                        <th class="py-1.5 px-2 bg-[#385623] border-r border-white w-28 text-center">HARGA</th>
                        <th class="py-1.5 px-2 bg-[#70AD47] border-r border-white w-20 text-center">DISKON</th>
                        <th class="py-1.5 px-2 bg-[#385623] border-r border-white w-28 text-center">JUMLAH</th>
                        <th class="py-1.5 px-2 bg-[#70AD47] border-r border-white w-20 text-center">JAMAAH</th>
                        <th class="py-1.5 px-3 bg-[#385623] text-center w-32">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="text-[9.5px]">
                    {{-- Row 1: Data Paket --}}
                    <tr class="font-normal text-zinc-900 h-7">
                        <td class="text-center border border-zinc-300">1</td>
                        <td class="px-2 text-left border border-zinc-300 font-medium">
                            {{ $package->name ?? 'Paket Umrah' }}
                            @if($registration->packageVariant)
                                &bull; {{ $registration->packageVariant->name }} ({{ ucfirst($registration->room_type) }})
                            @endif
                        </td>
                        <td class="text-center border border-zinc-300 font-mono">
                            Rp {{ number_format((float) ($invoice->total_price / max(1, $members->count())), 0, ',', '.') }}
                        </td>
                        <td class="text-center border border-zinc-300 text-zinc-400">-</td>
                        <td class="text-center border border-zinc-300 font-mono">
                            Rp {{ number_format((float) $invoice->total_price, 0, ',', '.') }}
                        </td>
                        <td class="text-center border border-zinc-300">
                            {{ max(1, $members->count()) }} Pax
                        </td>
                        <td class="text-center border border-zinc-300 font-mono">
                            Rp {{ number_format((float) $invoice->total_price, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Rows 2 - 5: Baris Kosong Bergaris Lengkap --}}
                    @for($i = 2; $i <= 5; $i++)
                        <tr class="h-6">
                            <td class="text-center border border-zinc-300 text-zinc-700">{{ $i }}</td>
                            <td class="border border-zinc-300"></td>
                            <td class="border border-zinc-300"></td>
                            <td class="border border-zinc-300"></td>
                            <td class="border border-zinc-300"></td>
                            <td class="border border-zinc-300"></td>
                            <td class="border border-zinc-300"></td>
                        </tr>
                    @endfor

                    {{-- Summary Rows --}}
                    <tr class="h-6 text-[10px]">
                        <td colspan="6" class="px-2 text-left border border-zinc-300 font-normal text-zinc-900">
                            Total Pembayaran
                        </td>
                        <td class="px-2 text-right border border-zinc-300 font-mono font-medium text-zinc-900">
                            Rp {{ number_format((float) $invoice->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="h-6 text-[10px]">
                        <td colspan="6" class="px-2 text-left border border-zinc-300 font-normal text-zinc-900">
                            Payment
                        </td>
                        <td class="px-2 text-right border border-zinc-300 font-mono font-medium text-zinc-900">
                            Rp {{ number_format((float) $invoice->total_paid, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="h-6 text-[10px]">
                        <td colspan="6" class="px-2 text-left border border-zinc-300 font-normal text-zinc-900">
                            Sisa Pembayaran
                        </td>
                        <td class="px-2 text-right border border-zinc-300 font-mono font-bold text-zinc-900">
                            Rp {{ number_format((float) $invoice->remaining_balance, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Terbilang Baris Bawah --}}
                    <tr class="h-7 bg-white">
                        <td colspan="7" class="px-2 border border-zinc-300 text-[10px]">
                            <span class="text-zinc-800">Terbilang : </span>
                            <span class="font-normal italic text-zinc-900">
                                {{ strtoupper($terbilangRemaining) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 3. Tabel Transaksi Pembayaran Terverifikasi --}}
        <div class="space-y-1">
            <h2 class="text-xs font-black text-black tracking-wider uppercase">TRANSAKSI</h2>
            
            <div class="border border-zinc-400 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-white text-[9.5px] uppercase font-bold tracking-wide text-center">
                            <th class="py-1.5 px-3 bg-[#385623] border-r border-white w-64">PEMBAYARAN</th>
                            <th class="py-1.5 px-3 bg-[#70AD47] border-r border-white text-center w-40">NOMINAL</th>
                            <th class="py-1.5 px-3 bg-[#385623] border-r border-white text-center">JAMAAH</th>
                            <th class="py-1.5 px-3 bg-[#70AD47] text-center w-40">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody class="text-[9.5px]">
                        @php
                            $rowLimit = 4;
                            $count = 0;
                        @endphp
                        @foreach($verifiedPayments as $vp)
                            @php $count++; @endphp
                            <tr class="h-6 font-normal text-zinc-900">
                                <td class="text-center border border-zinc-300 font-mono px-2">
                                    {{ $vp->verified_at ? $vp->verified_at->translatedFormat('d/m/Y') : $vp->created_at->translatedFormat('d/m/Y') }} - {{ $vp->type_label }}
                                </td>
                                <td class="text-center border border-zinc-300 font-mono px-2">
                                    {{ $vp->amount_formatted }}
                                </td>
                                <td class="text-center border border-zinc-300 uppercase px-2">
                                    {{ $user->name }}
                                </td>
                                <td class="text-center border border-zinc-300 font-mono font-medium px-2">
                                    {{ $vp->amount_formatted }}
                                </td>
                            </tr>
                        @endforeach

                        @if($count === 0)
                            @php $count++; @endphp
                            <tr class="h-6 text-zinc-500">
                                <td class="text-center border border-zinc-300 font-mono">-</td>
                                <td class="text-center border border-zinc-300 font-mono">Rp 0</td>
                                <td class="text-center border border-zinc-300 uppercase">{{ $user->name }}</td>
                                <td class="text-center border border-zinc-300 font-mono">Rp 0</td>
                            </tr>
                        @endif

                        {{-- Baris Kosong Cadangan untuk Menyempurnakan Grid Tabel Sesuai Template --}}
                        @for($k = $count; $k < $rowLimit; $k++)
                            <tr class="h-6">
                                <td class="border border-zinc-300"></td>
                                <td class="border border-zinc-300"></td>
                                <td class="border border-zinc-300"></td>
                                <td class="border border-zinc-300"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. Footer: Rekening Bank & Tanda Tangan --}}
        <div class="pt-4 flex justify-between items-end gap-6 text-[10px]">
            
            {{-- Info Rekening --}}
            <div class="space-y-1 max-w-xs text-[#12271E]">
                <p class="font-bold text-[11px]">Pembayaran Bank Mandiri</p>
                <p class="font-bold text-[11px] text-[#1B3B2B]">a/n PT ZEIN INTERNASIONAL</p>
                <p class="font-black text-sm font-mono tracking-wider text-[#12271E]">No. Rekening 132-0025416-463</p>
            </div>

            {{-- Tanda Tangan Kabag Keuangan --}}
            <div class="text-center w-52 space-y-12">
                <span class="font-extrabold uppercase tracking-wider text-zinc-800 block">
                    KABAG KEUANGAN
                </span>
                
                <div class="border-b border-zinc-800 font-bold text-zinc-900 pb-0.5 tracking-wide">
                    ( FITRIANI, SE. )
                </div>
            </div>

        </div>

    </div>

    @if(!empty($autoPrint))
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    window.print();
                }, 400);
            });
        </script>
    @endif

</body>
</html>
