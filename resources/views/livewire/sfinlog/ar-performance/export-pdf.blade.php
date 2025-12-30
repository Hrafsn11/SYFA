<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header .date {
            font-size: 10px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }
        
        table td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            font-size: 8px;
        }
        
        table td.text-center {
            text-align: center;
        }
        
        table td.text-end {
            text-align: right;
        }
        
        table td.text-start {
            text-align: left;
        }
        
        .text-success {
            color: #28a745;
        }
        
        .text-warning {
            color: #ffc107;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .text-dark {
            color: #343a40;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="date">Dicetak pada: {{ date('d F Y') }}</div>
    </div>

    @if($arData->isEmpty())
        <div class="no-data">
            <p>Tidak ada data pembayaran untuk periode ini.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 12%;">Debitur</th>
                    <th style="width: 10%;">Belum Jatuh Tempo</th>
                    <th style="width: 6%;">By Transaction</th>
                    <th style="width: 10%;">DEL (1-30)</th>
                    <th style="width: 6%;">By Transaction</th>
                    <th style="width: 10%;">DEL (31-60)</th>
                    <th style="width: 6%;">By Transaction</th>
                    <th style="width: 10%;">DEL (61-90)</th>
                    <th style="width: 6%;">By Transaction</th>
                    <th style="width: 10%;">NPL (91-179)</th>
                    <th style="width: 6%;">By Transaction</th>
                    <th style="width: 10%;">WriteOff (>180)</th>
                    <th style="width: 6%;">By Transaction</th>
                </tr>
            </thead>
            <tbody>
                @foreach($arData as $index => $debitur)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-start">{{ $debitur['nama_debitur'] }}</td>

                        {{-- Belum Jatuh Tempo --}}
                        <td class="text-end">
                            @if($debitur['belum_jatuh_tempo']['total'] > 0)
                                <span class="text-success">
                                    Rp {{ number_format($debitur['belum_jatuh_tempo']['total'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($debitur['belum_jatuh_tempo']['count'] > 0)
                                {{ $debitur['belum_jatuh_tempo']['count'] }} transaksi
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- DEL (1-30) --}}
                        <td class="text-end">
                            @if($debitur['del_1_30']['total'] > 0)
                                <span class="text-warning">
                                    Rp {{ number_format($debitur['del_1_30']['total'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($debitur['del_1_30']['count'] > 0)
                                {{ $debitur['del_1_30']['count'] }} transaksi
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- DEL (31-60) --}}
                        <td class="text-end">
                            @if($debitur['del_31_60']['total'] > 0)
                                <span class="text-warning">
                                    Rp {{ number_format($debitur['del_31_60']['total'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($debitur['del_31_60']['count'] > 0)
                                {{ $debitur['del_31_60']['count'] }} transaksi
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- DEL (61-90) --}}
                        <td class="text-end">
                            @if($debitur['del_61_90']['total'] > 0)
                                <span class="text-warning">
                                    Rp {{ number_format($debitur['del_61_90']['total'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($debitur['del_61_90']['count'] > 0)
                                {{ $debitur['del_61_90']['count'] }} transaksi
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- NPL (91-179) --}}
                        <td class="text-end">
                            @if($debitur['npl_91_179']['total'] > 0)
                                <span class="text-danger">
                                    Rp {{ number_format($debitur['npl_91_179']['total'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($debitur['npl_91_179']['count'] > 0)
                                {{ $debitur['npl_91_179']['count'] }} transaksi
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- WriteOff (>180) --}}
                        <td class="text-end">
                            @if($debitur['writeoff_180']['total'] > 0)
                                <span class="text-dark">
                                    Rp {{ number_format($debitur['writeoff_180']['total'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($debitur['writeoff_180']['count'] > 0)
                                {{ $debitur['writeoff_180']['count'] }} transaksi
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Halaman 1
    </div>
</body>
</html>
