<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Piutang & Sisa Tagihan — PT. Zein Internasional</title>
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
                <strong style="font-size: 13px; color: #1B3B2B;">LAPORAN PIUTANG KEBERANGKATAN</strong><br>
                <span style="font-size: 9px; color: #526057;">Jadwal Berangkat: {{ $periodData['label'] }}</span>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td><strong>Total Tagihan:</strong> Rp {{ number_format($items->sum(fn($r) => (float)($r->invoice->total_price ?? 0)), 0, ',', '.') }}</td>
            <td><strong>Sudah Diterima:</strong> Rp {{ number_format($items->sum(fn($r) => (float)($r->invoice->total_paid ?? 0)), 0, ',', '.') }}</td>
            <td><strong>Sisa Piutang:</strong> Rp {{ number_format($items->sum(fn($r) => (float)($r->invoice->remaining_balance ?? 0)), 0, ',', '.') }}</td>
            <td class="text-right">Dicetak: {{ $generatedAt->translatedFormat('d M Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">No. Pendaftaran</th>
                <th width="18%">Nama Jamaah & No. HP</th>
                <th width="20%">Paket Umrah</th>
                <th width="11%">Tgl Berangkat</th>
                <th width="11%">Jatuh Tempo</th>
                <th width="12%" class="text-right">Sudah Bayar</th>
                <th width="12%" class="text-right">Sisa Piutang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $idx => $reg)
                @php
                    $inv = $reg->invoice;
                    $rem = $inv ? (float) $inv->remaining_balance : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $reg->registration_number }}</td>
                    <td>
                        <strong>{{ $reg->user->name ?? '-' }}</strong><br>
                        <small>{{ $reg->user->phone ?? '-' }}</small>
                    </td>
                    <td>{{ $reg->package->name ?? '-' }}</td>
                    <td>{{ $reg->package?->departure_date ? $reg->package->departure_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $inv?->due_date ? $inv->due_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($inv->total_paid ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: {{ $rem > 0 ? '#9B2C2C' : '#276749' }}">
                        Rp {{ number_format($rem, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada data piutang untuk jadwal keberangkatan periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis dari Sistem Informasi Manajemen Travel Umrah & Haji PT. Zein Internasional.
    </div>
</body>
</html>
