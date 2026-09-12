<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }} — PT. Zein Internasional</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            margin-bottom: 8px;
        }
        .company-title {
            font-size: 14px;
            font-weight: bold;
            color: #1B3B2B;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 9px;
            color: #526057;
            margin-top: 2px;
        }
        .meta-box {
            border: 1px solid #c8d3c5;
            background-color: #f7faf6;
            padding: 6px 10px;
            border-radius: 4px;
        }
        .meta-row {
            margin-bottom: 3px;
        }
        .meta-label {
            font-weight: bold;
            color: #405146;
            width: 90px;
            display: inline-block;
            font-size: 9px;
        }
        .meta-val {
            font-weight: bold;
            color: #12271E;
            font-size: 9.5px;
        }
        .badge-invoice {
            background-color: #1B3B2B;
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1.5px;
            padding: 4px 12px;
            text-align: center;
            border-radius: 3px;
            display: inline-block;
        }
        .table-items {
            width: 100%;
            border: 1px solid #b5c4b1;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .table-items th {
            padding: 6px 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            border-right: 1px solid rgba(255,255,255,0.3);
        }
        .th-green-dark {
            background-color: #1B3B2B;
        }
        .th-green-mid {
            background-color: #385623;
        }
        .th-green-light {
            background-color: #588040;
        }
        .table-items td {
            padding: 5px 8px;
            border-bottom: 1px solid #e0e7dc;
            border-right: 1px solid #e0e7dc;
            font-size: 9px;
        }
        .table-items td:last-child {
            border-right: none;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }
        .summary-row td {
            padding: 4px 8px;
            font-size: 9.5px;
        }
        .terbilang-box {
            background-color: #f7faf6;
            border: 1px solid #c8d3c5;
            padding: 6px 10px;
            font-style: italic;
            font-size: 9px;
            color: #2b3a30;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1B3B2B;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
            border-bottom: 1.5px solid #1B3B2B;
            padding-bottom: 2px;
        }
        .footer-table {
            margin-top: 15px;
            width: 100%;
        }
        .footer-note {
            font-size: 8px;
            color: #526057;
            line-height: 1.3;
            border: 1px dashed #b5c4b1;
            padding: 6px 8px;
            border-radius: 4px;
            background-color: #fafbfa;
        }
        .signature-box {
            text-align: center;
            font-size: 9px;
            color: #12271E;
        }
    </style>
</head>
<body>

    {{-- 1. Header & Identitas Perusahaan --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-title">PT. ZEIN INTERNASIONAL</div>
                <div style="font-size: 10px; font-weight: bold; color: #C2A264; margin-top: 1px;">
                    ZEIN TOUR &amp; TRAVEL — UMRAH &amp; HAJI KHUSUS
                </div>
                <div class="company-subtitle">
                    Izin PPIU Kemenag RI No. U.255/2020 &bull; Akreditasi A<br>
                    Jl. Terusan Buah Batu No. 45, Bandung, Jawa Barat<br>
                    Telp / CS WhatsApp: 0812-2222-2562 &bull; Website: www.zeintour.com
                </div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div class="badge-invoice">INVOICE TAGIHAN</div>
                <div style="margin-top: 6px;" class="meta-box text-left">
                    <div class="meta-row">
                        <span class="meta-label">NO. INVOICE:</span>
                        <span class="meta-val font-mono">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">TANGGAL:</span>
                        <span class="meta-val">{{ $invoice->created_at ? $invoice->created_at->format('d/m/Y') : date('d/m/Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">CUSTOMER ID:</span>
                        <span class="meta-val font-mono">{{ $invoice->customer_id }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">KEPADA YTH:</span>
                        <span class="meta-val">{{ strtoupper($user->name) }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. Rincian Paket Pilihan --}}
    <div class="section-title">RINCIAN PAKET IBADAH</div>
    <table class="table-items">
        <thead>
            <tr>
                <th class="th-green-dark text-center" style="width: 30px;">NO</th>
                <th class="th-green-mid text-left">PAKET PERJALANAN</th>
                <th class="th-green-dark text-center" style="width: 105px;">HARGA / PAX</th>
                <th class="th-green-mid text-center" style="width: 50px;">JAMAAH</th>
                <th class="th-green-dark text-right" style="width: 115px;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $memberCount = max(1, $members->count());
                $packagePrice = (float) ($invoice->total_price / $memberCount);
            @endphp
            <tr>
                <td class="text-center">1</td>
                <td class="text-left font-bold">
                    {{ $package->name ?? 'Paket Umrah' }}
                    @if($registration->packageVariant)
                        <br><span style="font-weight: normal; color: #526057;">Varian {{ $registration->packageVariant->name }} &bull; Kamar {{ ucfirst($registration->room_type ?? 'Quad') }}</span>
                    @endif
                </td>
                <td class="text-center font-mono">Rp {{ number_format($packagePrice, 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ $memberCount }} Pax</td>
                <td class="text-right font-mono font-bold">Rp {{ number_format($invoice->total_price, 0, ',', '.') }}</td>
            </tr>

            {{-- 2 baris kosong penutup untuk layout formal --}}
            <tr>
                <td class="text-center" style="color: #bbb;">2</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center" style="color: #bbb;">3</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- Ringkasan Biaya --}}
            <tr class="summary-row" style="background-color: #fafbfa;">
                <td colspan="4" class="text-right font-bold">TOTAL BIAYA PAKET:</td>
                <td class="text-right font-mono font-bold" style="color: #12271E;">
                    Rp {{ number_format($invoice->total_price, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="summary-row" style="background-color: #f2f7f1;">
                <td colspan="4" class="text-right font-bold" style="color: #1B3B2B;">SUDAH DIBAYAR (TERVERIFIKASI):</td>
                <td class="text-right font-mono font-bold" style="color: #1B3B2B;">
                    Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="summary-row" style="background-color: #faf7f2;">
                <td colspan="4" class="text-right font-bold" style="color: {{ $invoice->remaining_balance <= 0 ? '#1B3B2B' : '#9c5400' }};">
                    SISA PELUNASAN:
                </td>
                <td class="text-right font-mono font-bold" style="color: {{ $invoice->remaining_balance <= 0 ? '#1B3B2B' : '#9c5400' }};">
                    @if($invoice->remaining_balance <= 0)
                        LUNAS ✓
                    @else
                        Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Terbilang Sisa Pembayaran --}}
    <div class="terbilang-box">
        <strong>Terbilang:</strong> 
        @if($invoice->remaining_balance <= 0)
            NOL RUPIAH (TAGIHAN TELAH LUNAS)
        @else
            {{ strtoupper($terbilangRemaining) }} RUPIAH
        @endif
    </div>

    {{-- 3. Riwayat Transaksi Pembayaran Terverifikasi --}}
    <div class="section-title">RIWAYAT PEMBAYARAN TERVERIFIKASI</div>
    <table class="table-items" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="th-green-dark text-center" style="width: 30px;">NO</th>
                <th class="th-green-mid text-center" style="width: 90px;">TANGGAL</th>
                <th class="th-green-dark text-center" style="width: 110px;">NO. KWITANSI</th>
                <th class="th-green-mid text-left">DESKRIPSI PEMBAYARAN</th>
                <th class="th-green-dark text-right" style="width: 115px;">NOMINAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($verifiedPayments as $idx => $vp)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $vp->verified_at ? $vp->verified_at->format('d/m/Y') : $vp->created_at->format('d/m/Y') }}</td>
                    <td class="text-center font-mono font-bold">{{ $vp->receipt_number ?? '-' }}</td>
                    <td class="text-left font-bold">{{ $vp->type_label }}</td>
                    <td class="text-right font-mono font-bold">{{ $vp->amount_formatted }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #777; padding: 8px;">
                        Belum ada riwayat pembayaran yang disetujui.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. Footer & Tanda Tangan --}}
    <table class="footer-table">
        <tr>
            <td style="width: 65%; vertical-align: top; padding-right: 15px;">
                <div class="footer-note">
                    <strong>CATATAN PEMBAYARAN:</strong><br>
                    &bull; Pembayaran resmi hanya diakui jika disetor ke Rekening Bank Perusahaan PT. Zein Internasional.<br>
                    &bull; Batas akhir pelunasan biaya ibadah selambat-lambatnya 35 hari sebelum tanggal keberangkatan.<br>
                    &bull; Dokumen ini adalah bukti penagihan resmi yang sah dan diterbitkan secara digital oleh sistem SIM Travel PT Zein International.
                </div>
            </td>
            <td style="width: 35%; vertical-align: top;">
                <div class="signature-box">
                    Bandung, {{ date('d F Y') }}<br>
                    <strong>Divisi Keuangan PT. Zein Internasional</strong>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">FINANCE &amp; ACCOUNTING</div>
                    <div style="font-size: 8px; color: #526057;">SIM Travel Umrah PT Zein</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
