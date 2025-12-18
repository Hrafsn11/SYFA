<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Pembiayaan SFinlog</h4>
        </div>
    </div>

    {{-- Summary Cards Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Disbursement Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp 120.000.000</h4>
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
                            <h6 class="text-muted mb-2">Total Pembayaran Masuk Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp 80.000.000</h4>
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
                            <h6 class="text-muted mb-2">Total Sisa yang Belum Terbayar Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp 40.000.000</h4>
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
                            <h6 class="text-muted mb-2">Total Outstanding Piutang</h6>
                            <h4 class="mb-0 fw-bold">Rp 200.000.000</h4>
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
                    <h5 class="card-title mb-0">Total Disburse Pokok dan Bagi Hasil Perbulan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanDisbursement">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartDisbursement"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Pokok dan Bagi Hasil Perbulan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanPembayaran">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartPembayaran"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa yang Belum Terbayar Pokok dan Bagi Hasil Perbulan</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanSisa">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartSisaBelumTerbayar"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Piutang Per Tahun</h5>
                    <div style="width: 150px; flex-shrink: 0;">
                        <select class="form-select select2" id="filterBulanPiutang">
                            <option>2025</option>
                            <option>2024</option>
                            <option>2023</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartPembayaranPiutang"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Total AR yang Terbagi Berdasarkan Kriteria Keterlambatan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan</label>
                            <select class="form-select select2" id="filterBulanTable">
                                <option>Desember</option>
                                <option>November</option>
                                <option>Oktober</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <select class="form-select select2" id="filterTahunTable">
                                <option>2025</option>
                                <option>2024</option>
                                <option>2023</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>DEBITUR</th>
                                    <th>DEL 1-30</th>
                                    <th>DEL 31-60</th>
                                    <th>DEL 61-90</th>
                                    <th>NPL 91-179</th>
                                    <th>WRITE OFF >180</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Super Admin</td>
                                    <td>Rp 150.987.787</td>
                                    <td>Rp 0</td>
                                    <td>Rp 0</td>
                                    <td>Rp 0</td>
                                    <td>Rp 0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Perbandingan AR dan Utang Pengembalian Deposito Perbulan</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select select2" id="filterBulanComparison1" style="width:120px;">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                        <select class="form-select select2" id="filterBulanComparison2" style="width:120px;">
                            <option>Desember</option>
                            <option>November</option>
                            <option>Oktober</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartComparison"></div>
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
        
        let chartDisbursement, chartPembayaran, chartSisaBelumTerbayar, chartPembayaranPiutang, chartComparison;

        function initCharts() {
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded');
                return;
            }

            // Destroy existing charts
            try { if (chartDisbursement) chartDisbursement.destroy(); } catch(e) {}
            try { if (chartPembayaran) chartPembayaran.destroy(); } catch(e) {}
            try { if (chartSisaBelumTerbayar) chartSisaBelumTerbayar.destroy(); } catch(e) {}
            try { if (chartPembayaranPiutang) chartPembayaranPiutang.destroy(); } catch(e) {}
            try { if (chartComparison) chartComparison.destroy(); } catch(e) {}

            // Chart Disbursement
            const disbursementOptions = {
                series: [
                    { name: 'Pokok', data: [188000000, 165000000, 120000000, 95000000, 30000000] },
                    { name: 'Bagi Hasil', data: [16800000, 14850000, 10800000, 8550000, 2700000] }
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

            const chartDisbursementEl = document.querySelector("#chartDisbursement");
            if (chartDisbursementEl) {
                chartDisbursement = new ApexCharts(chartDisbursementEl, disbursementOptions);
                chartDisbursement.render();
            }

            // Chart Pembayaran
            const pembayaranOptions = {
                series: [
                    { name: 'Pokok', data: [150000000, 135000000, 100000000, 80000000, 25000000] },
                    { name: 'Bagi Hasil', data: [13500000, 12150000, 9000000, 7200000, 2250000] }
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

            const chartPembayaranEl = document.querySelector("#chartPembayaran");
            if (chartPembayaranEl) {
                chartPembayaran = new ApexCharts(chartPembayaranEl, pembayaranOptions);
                chartPembayaran.render();
            }

            // Chart Sisa Belum Terbayar
            const sisaOptions = {
                series: [
                    { name: 'Pokok', data: [175000000, 155000000, 115000000, 90000000, 28000000] },
                    { name: 'Bagi Hasil', data: [15750000, 13950000, 10350000, 8100000, 2520000] }
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

            const chartSisaEl = document.querySelector("#chartSisaBelumTerbayar");
            if (chartSisaEl) {
                chartSisaBelumTerbayar = new ApexCharts(chartSisaEl, sisaOptions);
                chartSisaBelumTerbayar.render();
            }

            // Chart Pembayaran Piutang
            const piutangOptions = {
                series: [
                    { name: 'Pokok', data: [200000000, 175000000, 145000000, 120000000, 50000000] }
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

            const chartPiutangEl = document.querySelector("#chartPembayaranPiutang");
            if (chartPiutangEl) {
                chartPembayaranPiutang = new ApexCharts(chartPiutangEl, piutangOptions);
                chartPembayaranPiutang.render();
            }

            // Chart Comparison
            const comparisonOptions = {
                series: [
                    { name: 'Januari', data: [175000000, 155000000, 120000000, 95000000, 40000000] },
                    { name: 'Maret', data: [188000000, 165000000, 130000000, 100000000, 35000000] },
                    { name: 'Selisih', data: [13000000, 10000000, 10000000, 5000000, 5000000] }
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
                colors: ['#71dd37', '#ffab00', '#ff3e1d'],
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartComparisonEl = document.querySelector("#chartComparison");
            if (chartComparisonEl) {
                chartComparison = new ApexCharts(chartComparisonEl, comparisonOptions);
                chartComparison.render();
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
            if (typeof ApexCharts !== 'undefined' && !chartDisbursement) {
                initCharts();
            }
        }, 500);
    })();
</script>
@endpush
