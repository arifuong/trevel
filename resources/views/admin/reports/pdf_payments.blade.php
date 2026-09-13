<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran & Arus Kas — PT. Zein Internasional</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 15mm; }
        body { font-family: sans-serif; font-size: 10px; color: #12271E; line-height: 1.3; }
        .header-table { width: 100%; border-bottom: 2px solid #1B3B2B; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 16px; font-weight: bold; color: #1B3B2B; margin: 0; }
        .subtitle { font-size: 9px; color: #526057; margin: 2px 0 0 0; }
        .meta-table { width: 100%; margin-bottom: 12px; font-size: 9.5px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .data-table th { background-color: #EFF3EB; color: #1B3B2B; border: 1px solid #CCD8C7; padding: 6px 5px; font-size: 9px; text-transform: uppercase; }
        .data-table td { border: 1px solid #E0E7DC; padding: 5px; font-size: 9px; }
        .data-table tr:nth-child(even) { background-color: #FBFDFB; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 15px; font-size: 8px; color: #718096; text-align: right; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <h1 class="title">PT. ZEIN INTERNASIONAL</h1>
                <p class="subtitle">Izin Umrah PPIU Kemenag RI No. U.255/2020 &bull; Sistem Informasi Manajemen Travel</p>
            </td>
            <td class="text-right">
                <strong style="font-size: 13px; color: #1B3B2B;">LAPORAN PEMBAYARAN & ARUS KAS</strong><br>
                <span style="font-size: 9px; color: #526057;">Periode: {{ $periodData['label'] }}</span>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td><strong>Total Transaksi:</strong> {{ $items->count() }} Transaksi</td>
            <td><strong>Total Kas Masuk:</strong> Rp {{ number_format($items->where('status', 'disetujui')->sum('amount'), 0, ',', '.') }}</td>
            <td><strong>Total DP:</strong> Rp {{ number_format($items->where('status', 'disetujui')->where('type', 'dp')->sum('amount'), 0, ',', '.') }}</td>
            <td class="text-right">Dicetak: {{ $generatedAt->translatedFormat('d M Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">No. Kwitansi</th>
                <th width="12%">Tgl Verifikasi</th>
                <th width="18%">Nama Jamaah & Booking</th>
                <th width="20%">Paket Umrah</th>
                <th width="12%" class="text-center">Jenis Setoran</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $p->receipt_number ?? '-' }}</td>
                    <td>{{ $p->verified_at ? $p->verified_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <strong>{{ $p->registration->user->name ?? '-' }}</strong><br>
                        <small>{{ $p->registration->registration_number ?? '-' }}</small>
                    </td>
                    <td>{{ $p->registration->package->name ?? '-' }}</td>
                    <td class="text-center">{{ $p->type_label }}</td>
                    <td class="text-center">{{ $p->status_label }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada transaksi pembayaran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis dari Sistem Informasi Manajemen Travel Umrah & Haji PT. Zein Internasional.
    </div>
</body>
</html>
