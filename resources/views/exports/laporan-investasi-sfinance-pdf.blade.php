<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Investasi SFinance</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #222;
        }

        .meta {
            margin-bottom: 8px;
        }

        .meta h2 {
            margin: 0 0 4px 0;
            font-size: 12px;
        }

        .meta p {
            margin: 1px 0;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #cfcfcf;
            padding: 3px 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f3f5f8;
            font-weight: 700;
            text-align: center;
            font-size: 7px;
        }

        td {
            font-size: 7px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 8px;
            font-size: 7px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="meta">
        <h2>Laporan Investasi SFinance</h2>
        <p>Tanggal Export: {{ now()->format('d-m-Y H:i') }}</p>
        <p>Tahun: {{ $year ?: 'Semua Tahun' }} | Status: {{ $filterStatus ?: 'Semua Status' }}</p>
        <p>Pencarian: {{ $globalSearch ?: '-' }}</p>
        <p>Total Baris: {{ count($rows) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td class="{{ is_numeric($cell) ? 'text-right' : '' }}">{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headings) }}" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem laporan investasi SFinance.
    </div>
</body>
</html>
