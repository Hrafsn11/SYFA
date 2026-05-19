<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Pembiayaan</h4>
            <div class="dashboard-subtitle text-muted">Ringkasan arus kas pembiayaan</div>
        </div>
    </div>

    @php
        $totalOut = (float) ($summaryData['total_disbursement'] ?? 0);
        $totalIn = (float) ($summaryData['total_pembayaran_masuk'] ?? 0);
        $ratioPaid = $totalOut > 0 ? ($totalIn / $totalOut) * 100 : 0;

        $lastPayment = $paymentStatus['last_payment'] ?? null;
        $nextDue = $paymentStatus['next_due'] ?? null;
        $installment = $paymentStatus['installment'] ?? ['paid' => 0, 'total' => 0];
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Uang Keluar</div>
                            <div class="kpi-value">Rp {{ number_format($totalOut, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Total pinjaman dicairkan</div>
                        </div>
                        <div class="kpi-icon bg-soft-danger">
                            <i class="ti ti-arrow-up-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Uang Masuk</div>
                            <div class="kpi-value">Rp {{ number_format($totalIn, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Total pembayaran diterima</div>
                        </div>
                        <div class="kpi-icon bg-soft-success">
                            <i class="ti ti-arrow-down-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Sisa Belum Dibayar</div>
                            <div class="kpi-value">Rp {{ number_format($summaryData['total_sisa_belum_terbayar'] ?? 0, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Outstanding aktif</div>
                        </div>
                        <div class="kpi-icon bg-soft-warning">
                            <i class="ti ti-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Rasio Terbayar</div>
                            <div class="kpi-value">{{ number_format($ratioPaid, 1) }}%</div>
                            <div class="kpi-meta text-muted">Masuk vs keluar</div>
                        </div>
                        <div class="kpi-icon bg-soft-info">
                            <i class="ti ti-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Arus Kas 6 Bulan Terakhir</h5>
                    <span class="badge bg-label-primary">Masuk vs Keluar</span>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartCashflow" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <div class="status-label">Pembayaran Terakhir</div>
                        <div class="status-value">
                            @if ($lastPayment)
                                Rp {{ number_format($lastPayment['amount'] ?? 0, 0, ',', '.') }}
                                <span class="status-sub">{{ $lastPayment['date'] ?? '-' }}</span>
                            @else
                                <span class="text-muted">Belum ada pembayaran</span>
                            @endif
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Jatuh Tempo Terdekat</div>
                        <div class="status-value">
                            @if ($nextDue)
                                Rp {{ number_format($nextDue['amount'] ?? 0, 0, ',', '.') }}
                                <span class="status-sub">{{ $nextDue['date'] ?? '-' }}</span>
                            @else
                                <span class="text-muted">Belum ada data jatuh tempo</span>
                            @endif
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Angsuran Lunas</div>
                        <div class="status-value">
                            {{ number_format($installment['paid'] ?? 0) }} / {{ number_format($installment['total'] ?? 0) }}
                            <span class="status-sub">Total tenor terbayar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chart-data-holder" class="d-none" data-cashflow='@json($cashflowData ?? [])'></div>
</div>

@push('vendor-scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('styles')
    <style>
        .dashboard-subtitle {
            font-size: 0.9rem;
        }

        .kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .kpi-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .kpi-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .kpi-meta {
            font-size: 0.8rem;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bg-soft-danger { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
        .bg-soft-success { background: rgba(25, 135, 84, 0.12); color: #198754; }
        .bg-soft-warning { background: rgba(255, 193, 7, 0.16); color: #b58100; }
        .bg-soft-info { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }

        .status-item {
            padding: 0.75rem 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .status-value {
            font-weight: 600;
        }

        .status-sub {
            display: block;
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.2rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            let cashflowChart = null;

            function getCashflowData() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return null;
                try {
                    return JSON.parse(holder.getAttribute('data-cashflow') || '{}');
                } catch (e) {
                    console.error('Cashflow data parse error:', e);
                    return null;
                }
            }

            function renderCashflowChart() {
                if (typeof ApexCharts === 'undefined') return;
                const data = getCashflowData();
                if (!data || !data.categories) return;

                const options = {
                    series: [
                        { name: 'Uang Keluar', data: data.out || [] },
                        { name: 'Uang Masuk', data: data.in || [] }
                    ],
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: { columnWidth: '50%', borderRadius: 6 }
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: data.categories || [] },
                    yaxis: {
                        labels: {
                            formatter: (val) => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    colors: ['#f97316', '#22c55e'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: {
                        y: {
                            formatter: (val) => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    noData: { text: 'Tidak ada data' }
                };

                const el = document.querySelector('#chartCashflow');
                if (!el) return;

                if (cashflowChart) {
                    cashflowChart.destroy();
                    cashflowChart = null;
                    el.innerHTML = '';
                }

                cashflowChart = new ApexCharts(el, options);
                cashflowChart.render();
            }

            // Gunakan hanya livewire:navigated — event ini selalu terpicu
            // baik saat navigasi SPA maupun saat halaman pertama kali dimuat.
            // DOMContentLoaded tidak digunakan agar tidak terjadi render ganda.
            document.addEventListener('livewire:navigated', function() {
                setTimeout(renderCashflowChart, 150);
            });
        })();
    </script>
@endpush
