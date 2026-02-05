<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #0070C0;
            color: white;
        }

        table th {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tfoot {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN AR PERBULAN - SFINLOG</h2>
        <p>Tanggal Export: {{ now()->format('d-m-Y H:i:s') }}</p>
        @if ($selectedMonth)
            <p>Bulan: {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}</p>
        @else
            <p>Semua Data</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No.</th>
                <th class="text-center" width="15%">Periode</th>
                <th width="30%">Nama Perusahaan</th>
                <th class="text-right" width="17%">Sisa AR Pokok</th>
                <th class="text-right" width="17%">Sisa Bagi Hasil</th>
                <th class="text-right" width="16%">Total AR</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPokok = 0;
                $totalBagiHasil = 0;
                $totalAR = 0;
            @endphp
            @forelse($data as $index => $row)
                @php
                    $totalPokok += $row->sisa_ar_pokok ?? 0;
                    $totalBagiHasil += $row->sisa_bagi_hasil ?? 0;
                    $totalAR += $row->sisa_ar_total ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ $row->bulan ? \Carbon\Carbon::parse($row->bulan)->translatedFormat('F Y') : '-' }}</td>
                    <td>{{ $row->nama_perusahaan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($row->sisa_ar_pokok ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->sisa_bagi_hasil ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($row->sisa_ar_total ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        @if ($data->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalPokok, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalAR, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>{{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>
