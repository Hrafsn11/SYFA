<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pengembalian Pinjaman</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h3 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h3>Daftar Pengembalian Pinjaman</h3>
    <table>
        <thead>
            <tr>
                <th class="text-center" width="4%">No</th>
                <th>Nama Perusahaan</th>
                <th>Nomor Peminjaman</th>
                <th>Invoice Dibayarkan</th>
                <th>Tanggal Pencairan</th>
                <th class="text-right">Total Pinjaman</th>
                <th class="text-right">Nominal Dibayarkan</th>
                <th class="text-right">Sisa Bayar Pokok</th>
                <th class="text-right">Sisa Bagi Hasil</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                @php
                    $totalDibayarkan = $item->pengembalianInvoices->sum('nominal_yg_dibayarkan');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_perusahaan }}</td>
                    <td>{{ $item->nomor_peminjaman }}</td>
                    <td>{{ $item->invoice_dibayarkan ?? '-' }}</td>
                    <td>{{ optional($item->tanggal_pencairan)->format('d-m-Y') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_pinjaman, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalDibayarkan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->sisa_bayar_pokok, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->sisa_bunga, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->status ?? 'Belum Lunas' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data pengembalian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
