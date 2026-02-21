<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengembalian</title>
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
    <h3>Laporan Pengembalian</h3>
    <table>
        <thead>
            <tr>
                <th class="text-center" width="4%">No</th>
                <th>Nomor Peminjaman</th>
                <th>Nomor Invoice</th>
                <th>Due Date</th>
                <th>Hari Keterlambatan</th>
                <th>Total Bulan Pemakaian</th>
                <th class="text-right">Nilai Total Pengembalian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_peminjaman }}</td>
                    <td>{{ $item->nomor_invoice }}</td>
                    <td class="text-center">
                        {{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">{{ $item->hari_keterlambatan ?? '0 Hari' }}</td>
                    <td class="text-center">{{ $item->total_bulan_pemakaian ?? '-' }}</td>
                    <td class="text-right">
                        Rp {{ number_format($item->nilai_total_pengembalian, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data laporan pengembalian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

