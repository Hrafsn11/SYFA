<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Investasi Deposito SFinlog</h4>
        </div>
    </div>

    {{-- Summary Cards Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Deposito Pokok Masuk Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp 850.000.000</h4>
                            <div class="d-flex align-items-center mt-2">
                                <i class="ti ti-info-circle text-muted me-1"></i>
                                <small class="text-muted">Baru</small>
                            </div>
                        </div>
                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; background: #28c76f;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total CoF (Cost of Fund) Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp 120.500.000</h4>
                            <div class="d-flex align-items-center mt-2">
                                <i class="ti ti-info-circle text-muted me-1"></i>
                                <small class="text-muted">Baru</small>
                            </div>
                        </div>
                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; background: #28c76f;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Pengembalian Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp 780.500.000</h4>
                            <div class="d-flex align-items-center mt-2">
                                <i class="ti ti-info-circle text-muted me-1"></i>
                                <small class="text-muted">Baru</small>
                            </div>
                        </div>
                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; background: #28c76f;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Outstanding Deposito</h6>
                            <h4 class="mb-0 fw-bold">Rp 1.250.500.000</h4>
                            <div class="d-flex align-items-center mt-2">
                                <i class="ti ti-info-circle text-muted me-1"></i>
                                <small class="text-muted">Baru</small>
                            </div>
                        </div>
                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; background: #28c76f;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Deposito Pokok yang masuk Per Bulan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanDepositoPokok">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartDepositoPokok"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total CoF per bulan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanCoF">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartCoF"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pengembalian Pokok dan Bagi Hasil Perbulan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanPengembalian">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartPengembalian"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanSisaDeposito">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartSisaDeposito"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('vendor-scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('scripts')
<script>
    (function() {
        'use strict';
        
        let chartDepositoPokok, chartCoF, chartPengembalian, chartSisaDeposito;

        function initCharts() {
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded');
                return;
            }

            // Destroy existing charts
            try { if (chartDepositoPokok) chartDepositoPokok.destroy(); } catch(e) {}
            try { if (chartCoF) chartCoF.destroy(); } catch(e) {}
            try { if (chartPengembalian) chartPengembalian.destroy(); } catch(e) {}
            try { if (chartSisaDeposito) chartSisaDeposito.destroy(); } catch(e) {}

            // Chart Deposito Pokok
            const depositoPokokOptions = {
                series: [
                    { name: 'Pokok', data: [188000000, 165000000, 120000000, 95000000, 30000000] }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: ['Techno', 'Proseis', 'Malaka', 'Hukum', 'Kredit'] },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(0) + 'M';
                        }
                    }
                },
                fill: { opacity: 1 },
                colors: ['#71dd37'],
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartDepositoPokokEl = document.querySelector("#chartDepositoPokok");
            if (chartDepositoPokokEl) {
                chartDepositoPokok = new ApexCharts(chartDepositoPokokEl, depositoPokokOptions);
                chartDepositoPokok.render();
            }

            // Chart CoF
            const cofOptions = {
                series: [
                    { name: 'Pokok', data: [95000000, 82500000, 60000000, 47500000, 15000000] }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: ['Techno', 'Proseis', 'Malaka', 'Hukum', 'Kredit'] },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(0) + 'M';
                        }
                    }
                },
                fill: { opacity: 1 },
                colors: ['#71dd37'],
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartCofEl = document.querySelector("#chartCoF");
            if (chartCofEl) {
                chartCoF = new ApexCharts(chartCofEl, cofOptions);
                chartCoF.render();
            }

            // Chart Pengembalian
            const pengembalianOptions = {
                series: [
                    { name: 'Pokok', data: [170000000, 150000000, 110000000, 85000000, 27000000] },
                    { name: 'Bagi Hasil', data: [15300000, 13500000, 9900000, 7650000, 2430000] }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: ['Techno', 'Proseis', 'Malaka', 'Hukum', 'Kredit'] },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(0) + 'M';
                        }
                    }
                },
                fill: { opacity: 1 },
                colors: ['#71dd37', '#ffab00'],
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartPengembalianEl = document.querySelector("#chartPengembalian");
            if (chartPengembalianEl) {
                chartPengembalian = new ApexCharts(chartPengembalianEl, pengembalianOptions);
                chartPengembalian.render();
            }

            // Chart Sisa Deposito
            const sisaDepositoOptions = {
                series: [
                    { name: 'Pokok', data: [195000000, 172500000, 127500000, 100000000, 33000000] },
                    { name: 'Bagi Hasil', data: [17550000, 15525000, 11475000, 9000000, 2970000] }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: ['Techno', 'Proseis', 'Malaka', 'Hukum', 'Kredit'] },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(0) + 'M';
                        }
                    }
                },
                fill: { opacity: 1 },
                colors: ['#71dd37', '#ffab00'],
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartSisaDepositoEl = document.querySelector("#chartSisaDeposito");
            if (chartSisaDepositoEl) {
                chartSisaDeposito = new ApexCharts(chartSisaDepositoEl, sisaDepositoOptions);
                chartSisaDeposito.render();
            }
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCharts);
        } else {
            initCharts();
        }

        // Retry if ApexCharts not loaded yet
        setTimeout(function() {
            if (typeof ApexCharts !== 'undefined' && !chartDepositoPokok) {
                initCharts();
            }
        }, 500);
    })();
</script>
@endpush
