<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $payment->receipt_number }} — PT. Zein Internasional</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-container {
            border: 2px solid #1B3B2B;
            border-radius: 6px;
            padding: 16px 20px;
            background-color: #ffffff;
        }
        .company-header-title {
            font-size: 15px;
            font-weight: bold;
            color: #1B3B2B;
            letter-spacing: 0.5px;
        }
        .company-header-sub {
            font-size: 10px;
            font-weight: bold;
            color: #C2A264;
            margin-top: 2px;
        }
        .company-header-address {
            font-size: 9px;
            color: #526057;
            margin-top: 3px;
            line-height: 1.3;
        }
        .badge-kwitansi {
            background-color: #1B3B2B;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 5px 16px;
            border-radius: 4px;
            text-align: center;
            display: inline-block;
        }
        .meta-table {
            margin-top: 8px;
            border-left: 2px solid #1B3B2B;
            padding-left: 8px;
        }
        .meta-label {
            font-weight: bold;
            color: #405146;
            font-size: 9.5px;
            width: 70px;
        }
        .meta-val {
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            color: #12271E;
            font-size: 10px;
        }
        .divider {
            border-top: 2px solid #1B3B2B;
            margin-top: 14px;
            margin-bottom: 14px;
        }
        .receipt-body {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .field-row {
            margin-bottom: 10px;
            width: 100%;
        }
        .field-label {
            width: 150px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1B3B2B;
            font-size: 10px;
            vertical-align: top;
            padding-top: 3px;
        }
        .field-separator {
            width: 15px;
            font-weight: bold;
            color: #1B3B2B;
            vertical-align: top;
            padding-top: 3px;
        }
        .field-content {
            border-bottom: 1.5px dotted #708577;
            padding-bottom: 3px;
            font-size: 10.5px;
            color: #12271E;
            vertical-align: top;
        }
        .nominal-badge {
            background-color: #385623;
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            padding: 6px 16px;
            border-radius: 4px;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .footer-terms {
            border: 1px dashed #b5c4b1;
            background-color: #f7faf6;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 8.5px;
            color: #405146;
            line-height: 1.35;
            font-style: italic;
        }
        .signature-section {
            text-align: center;
            font-size: 9.5px;
            color: #12271E;
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        
        {{-- 1. Header Perusahaan & Badge Kwitansi --}}
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="company-header-title">PT. ZEIN INTERNASIONAL</div>
                    <div class="company-header-sub">ZEIN TOUR &amp; TRAVEL — UMRAH &amp; HAJI KHUSUS</div>
                    <div class="company-header-address">
                        Izin Resmi PPIU Kemenag RI No. U.255/2020 &bull; Akreditasi A<br>
                        Jl. Terusan Buah Batu No. 45, Bandung, Jawa Barat<br>
                        Telp / WhatsApp: 0812-2222-2562 &bull; www.zeintour.com
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="badge-kwitansi">KWITANSI PEMBAYARAN</div>
                    
                    <div class="meta-table" style="margin-top: 10px; text-align: left;">
                        <table>
                            <tr>
                                <td class="meta-label">NO. KWITANSI</td>
                                <td style="width: 8px;">:</td>
                                <td class="meta-val">{{ $payment->receipt_number }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">TANGGAL</td>
                                <td style="width: 8px;">:</td>
                                <td class="meta-val font-sans">
                                    {{ $payment->verified_at ? $payment->verified_at->format('d F Y') : ($payment->created_at ? $payment->created_at->format('d F Y') : date('d F Y')) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- 2. Isi Kwitansi --}}
        <div class="receipt-body">
            <table style="width: 100%;">
                <tr class="field-row">
                    <td class="field-label">TELAH TERIMA DARI</td>
                    <td class="field-separator">:</td>
                    <td class="field-content font-bold">
                        {{ strtoupper($user->name) }}
                    </td>
                </tr>
                <tr style="height: 6px;"><td></td><td></td><td></td></tr>
                <tr class="field-row">
                    <td class="field-label">UANG SEJUMLAH</td>
                    <td class="field-separator">:</td>
                    <td class="field-content" style="font-style: italic; font-weight: bold; color: #1B3B2B;">
                        # {{ strtoupper($terbilang) }} RUPIAH #
                    </td>
                </tr>
                <tr style="height: 6px;"><td></td><td></td><td></td></tr>
                <tr class="field-row">
                    <td class="field-label">UNTUK PEMBAYARAN</td>
                    <td class="field-separator">:</td>
                    <td class="field-content">
                        <strong>{{ $payment->type_label }}</strong> — {{ $package->name ?? 'Paket Perjalanan Umrah' }}
                        @if($registration->packageVariant)
                            (Varian {{ $registration->packageVariant->name }}, Kamar {{ ucfirst($registration->room_type ?? 'Quad') }})
                        @endif
                        <br>
                        <span style="font-size: 9px; color: #526057;">
                            Nomor Pendaftaran: <strong>{{ $registration->registration_number }}</strong> &bull;
                            Metode: {{ $payment->payment_method_label ?? 'Transfer Bank' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- 3. Footer & Tanda Tangan --}}
        <table style="margin-top: 15px;">
            <tr>
                <td style="width: 58%; vertical-align: top; padding-right: 15px;">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 10px; font-weight: bold; color: #1B3B2B; margin-right: 8px;">JUMLAH:</span>
                        <div class="nominal-badge">{{ $payment->amount_formatted }}</div>
                    </div>
                    <div class="footer-terms">
                        <strong>KETENTUAN PEMBAYARAN RESMI:</strong><br>
                        1. Kwitansi ini sah sebagai bukti pembayaran yang telah diverifikasi dan disetujui oleh sistem SIM Travel PT Zein International.<br>
                        2. Pelunasan biaya keberangkatan selambat-lambatnya 35 hari sebelum tanggal keberangkatan yang telah ditentukan.<br>
                        3. Pembayaran hanya sah apabila ditransfer ke rekening resmi atas nama PT. Zein Internasional.
                    </div>
                </td>
                <td style="width: 42%; vertical-align: top;">
                    <div class="signature-section">
                        Bandung, {{ $payment->verified_at ? $payment->verified_at->format('d F Y') : date('d F Y') }}<br>
                        <strong>Penerima / Petugas Kasir PT. Zein Internasional</strong>
                        <div style="height: 50px;"></div>
                        <div style="font-weight: bold; text-decoration: underline;">
                            {{ $payment->verifiedBy?->name ?? 'DIVISI KEUANGAN' }}
                        </div>
                        <div style="font-size: 8.5px; color: #526057;">Finance Officer</div>
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
