<div>
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold py-3 mb-4">Dashboard Investasi Deposito SFinance</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total Deposito Pokok Masuk Bulan Ini</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_deposito_pokok'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-up text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $summaryData['total_deposito_pokok_percent'] }}% dari bulan lalu</span>
                            </div>
                            <small class="text-muted">Compared to {{ $summaryData['total_deposito_pokok_period'] }}</small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total CoF (Cost of Fund) Bulan Ini</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_cof'], 0, '.', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-up text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $summaryData['total_cof_percent'] }}% lebih lancar</span>
                            </div>
                            <small class="text-muted">Compared to {{ $summaryData['total_cof_period'] }}</small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total Pengembalian Bulan Ini</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_pengembalian'], 0, '.', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-up text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $summaryData['total_pengembalian_percent'] }}%</span>
                            </div>
                            <small class="text-muted">Compared to {{ $summaryData['total_pengembalian_period'] }}</small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total Outstanding Deposito</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_outstanding'], 0, '.', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-down text-warning me-1"></i>
                                <span class="text-warning fw-semibold">{{ $summaryData['total_outstanding_percent'] }}% dari bulan lalu</span>
                            </div>
                            <small class="text-muted">Compared to {{ $summaryData['total_outstanding_period'] }}</small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Deposito Pokok yang masuk Per Bulan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMonth1" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulan
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMonth1">
                            <li><a class="dropdown-item" href="#">Januari</a></li>
                            <li><a class="dropdown-item" href="#">Februari</a></li>
                            <li><a class="dropdown-item" href="#">Maret</a></li>
                            <li><a class="dropdown-item" href="#">April</a></li>
                            <li><a class="dropdown-item" href="#">Mei</a></li>
                            <li><a class="dropdown-item" href="#">Juni</a></li>
                            <li><a class="dropdown-item" href="#">Juli</a></li>
                            <li><a class="dropdown-item" href="#">Agustus</a></li>
                            <li><a class="dropdown-item" href="#">September</a></li>
                            <li><a class="dropdown-item" href="#">Oktober</a></li>
                            <li><a class="dropdown-item" href="#">November</a></li>
                            <li><a class="dropdown-item" href="#">Desember</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartDepositoPokok" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total CoF per bulan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMonth2" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulan
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMonth2">
                            <li><a class="dropdown-item" href="#">Januari</a></li>
                            <li><a class="dropdown-item" href="#">Februari</a></li>
                            <li><a class="dropdown-item" href="#">Maret</a></li>
                            <li><a class="dropdown-item" href="#">April</a></li>
                            <li><a class="dropdown-item" href="#">Mei</a></li>
                            <li><a class="dropdown-item" href="#">Juni</a></li>
                            <li><a class="dropdown-item" href="#">Juli</a></li>
                            <li><a class="dropdown-item" href="#">Agustus</a></li>
                            <li><a class="dropdown-item" href="#">September</a></li>
                            <li><a class="dropdown-item" href="#">Oktober</a></li>
                            <li><a class="dropdown-item" href="#">November</a></li>
                            <li><a class="dropdown-item" href="#">Desember</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartCoF" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pengembalian Pokok dan Bagi Hasil Perbulan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMonth3" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulan
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMonth3">
                            <li><a class="dropdown-item" href="#">Januari</a></li>
                            <li><a class="dropdown-item" href="#">Februari</a></li>
                            <li><a class="dropdown-item" href="#">Maret</a></li>
                            <li><a class="dropdown-item" href="#">April</a></li>
                            <li><a class="dropdown-item" href="#">Mei</a></li>
                            <li><a class="dropdown-item" href="#">Juni</a></li>
                            <li><a class="dropdown-item" href="#">Juli</a></li>
                            <li><a class="dropdown-item" href="#">Agustus</a></li>
                            <li><a class="dropdown-item" href="#">September</a></li>
                            <li><a class="dropdown-item" href="#">Oktober</a></li>
                            <li><a class="dropdown-item" href="#">November</a></li>
                            <li><a class="dropdown-item" href="#">Desember</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartPengembalian" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMonth4" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulan
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMonth4">
                            <li><a class="dropdown-item" href="#">Januari</a></li>
                            <li><a class="dropdown-item" href="#">Februari</a></li>
                            <li><a class="dropdown-item" href="#">Maret</a></li>
                            <li><a class="dropdown-item" href="#">April</a></li>
                            <li><a class="dropdown-item" href="#">Mei</a></li>
                            <li><a class="dropdown-item" href="#">Juni</a></li>
                            <li><a class="dropdown-item" href="#">Juli</a></li>
                            <li><a class="dropdown-item" href="#">Agustus</a></li>
                            <li><a class="dropdown-item" href="#">September</a></li>
                            <li><a class="dropdown-item" href="#">Oktober</a></li>
                            <li><a class="dropdown-item" href="#">November</a></li>
                            <li><a class="dropdown-item" href="#">Desember</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartSisaDeposito" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    // Inisialisasi variabel global di awal untuk chart instances
    window.chartDepositoPokok = null;
    window.chartCoF = null;
    window.chartPengembalian = null;
    window.chartSisaDeposito = null;

    // Store chart options globally for re-rendering
    // PASTIKAN data ini diisi di Livewire/PHP backend Anda agar tidak kosong
    window.chartOptions = {
        depositoPokok: @json($chartDepositoPokok),
        coF: @json($chartCoF),
        pengembalian: @json($chartPengembalian),
        sisaDeposito: @json($chartSisaDeposito)
    };

    function initCharts() {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not loaded');
            return;
        }

        // =========================================================
        // PERBAIKAN: Penghancuran (Destroy) Chart yang Aman
        // Memeriksa keberadaan dan fungsionalitas sebelum destroy
        // =========================================================
        if (window.chartDepositoPokok && typeof window.chartDepositoPokok.destroy === 'function') {
            window.chartDepositoPokok.destroy();
            window.chartDepositoPokok = null;
        }
        if (window.chartCoF && typeof window.chartCoF.destroy === 'function') {
            window.chartCoF.destroy();
            window.chartCoF = null;
        }
        if (window.chartPengembalian && typeof window.chartPengembalian.destroy === 'function') {
            window.chartPengembalian.destroy();
            window.chartPengembalian = null;
        }
        if (window.chartSisaDeposito && typeof window.chartSisaDeposito.destroy === 'function') {
            window.chartSisaDeposito.destroy();
            window.chartSisaDeposito = null;
        }

        // Chart 1: Total Deposito Pokok yang masuk Per Bulan
        if (document.querySelector("#chartDepositoPokok")) {
            // Gunakan spread operator untuk menyalin opsi dasar dari backend
            var chartDepositoPokokOptions = {
                series: @json($chartDepositoPokok['series']),
                chart: {
                    type: 'bar',
                    height: 350,
                    width: '100%',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: { 
                        horizontal: false, 
                        columnWidth: '55%', 
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    show: true, 
                    width: 2, 
                    colors: ['transparent'] 
                },
                grid: {
                    show: true,
                    borderColor: '#e0e0e0',
                    strokeDashArray: 0,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            offsetX: 0
                        }
                    },
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: @json($chartDepositoPokok['categories']),
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            if (val === 0) return 'Rp. 0';
                            var formatted = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            return 'Rp. ' + formatted;
                        },
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    min: 0,
                    max: 200000000,
                    tickAmount: 4,
                    forceNiceScale: false,
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                fill: { opacity: 1, colors: ['#71dd37'] },
                colors: ['#71dd37'],
                legend: { 
                    show: true, 
                    position: 'top', 
                    horizontalAlign: 'right', 
                    markers: { width: 12, height: 12, radius: 12 } 
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            window.chartDepositoPokok = new ApexCharts(document.querySelector("#chartDepositoPokok"), chartDepositoPokokOptions);
            window.chartDepositoPokok.render();
        }

        // Chart 2: Total CoF per bulan
        if (document.querySelector("#chartCoF")) {
            var chartCoFOptions = {
                series: @json($chartCoF['series']),
                chart: {
                    type: 'bar',
                    height: 350,
                    width: '100%',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: { 
                        horizontal: false, 
                        columnWidth: '55%', 
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    show: true, 
                    width: 2, 
                    colors: ['transparent'] 
                },
                grid: {
                    show: true,
                    borderColor: '#e0e0e0',
                    strokeDashArray: 0,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            offsetX: 0
                        }
                    },
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: @json($chartCoF['categories']),
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            if (val === 0) return 'Rp. 0';
                            var formatted = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            return 'Rp. ' + formatted;
                        },
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    min: 0,
                    max: 200000000,
                    tickAmount: 4,
                    forceNiceScale: false,
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                fill: { opacity: 1, colors: ['#71dd37'] },
                colors: ['#71dd37'],
                legend: { 
                    show: true, 
                    position: 'top', 
                    horizontalAlign: 'right', 
                    markers: { width: 12, height: 12, radius: 12 } 
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            window.chartCoF = new ApexCharts(document.querySelector("#chartCoF"), chartCoFOptions);
            window.chartCoF.render();
        }

        // Chart 3: Total Pengembalian Pokok dan Bagi Hasil Perbulan (GROUPED BAR - bukan stacked)
        if (document.querySelector("#chartPengembalian")) {
            var chartPengembalianOptions = {
                series: @json($chartPengembalian['series']),
                chart: {
                    type: 'bar',
                    height: 350,
                    width: '100%',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: { 
                        horizontal: false, 
                        columnWidth: '55%', 
                        endingShape: 'rounded',
                        borderRadius: 4,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    show: true, 
                    width: 2, 
                    colors: ['transparent'] 
                },
                grid: {
                    show: true,
                    borderColor: '#e0e0e0',
                    strokeDashArray: 0,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            offsetX: 0
                        }
                    },
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: @json($chartPengembalian['categories']),
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            if (val === 0) return 'Rp. 0';
                            var formatted = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            return 'Rp. ' + formatted;
                        },
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    min: 50000000,
                    max: 200000000,
                    tickAmount: 4,
                    forceNiceScale: false,
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                fill: { opacity: 1 },
                colors: ['#71dd37', '#ffab00'], // Hijau & Oranye
                legend: { 
                    show: true, 
                    position: 'top', 
                    horizontalAlign: 'right', 
                    markers: { width: 12, height: 12, radius: 12 } 
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            window.chartPengembalian = new ApexCharts(document.querySelector("#chartPengembalian"), chartPengembalianOptions);
            window.chartPengembalian.render();
        }

        // Chart 4: Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan (GROUPED BAR - bukan stacked)
        if (document.querySelector("#chartSisaDeposito")) {
            var chartSisaDepositoOptions = {
                series: @json($chartSisaDeposito['series']),
                chart: {
                    type: 'bar',
                    height: 350,
                    width: '100%',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: { 
                        horizontal: false, 
                        columnWidth: '55%', 
                        endingShape: 'rounded',
                        borderRadius: 4,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    show: true, 
                    width: 2, 
                    colors: ['transparent'] 
                },
                grid: {
                    show: true,
                    borderColor: '#e0e0e0',
                    strokeDashArray: 0,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            offsetX: 0
                        }
                    },
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: @json($chartSisaDeposito['categories']),
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            if (val === 0) return 'Rp. 0';
                            var formatted = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            return 'Rp. ' + formatted;
                        },
                        style: {
                            fontSize: '12px',
                            colors: '#697a8d'
                        }
                    },
                    min: 50000000,
                    max: 200000000,
                    tickAmount: 4,
                    forceNiceScale: false,
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    }
                },
                fill: { opacity: 1 },
                colors: ['#71dd37', '#ffab00'], // Hijau & Oranye
                legend: { 
                    show: true, 
                    position: 'top', 
                    horizontalAlign: 'right', 
                    markers: { width: 12, height: 12, radius: 12 } 
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            window.chartSisaDeposito = new ApexCharts(document.querySelector("#chartSisaDeposito"), chartSisaDepositoOptions);
            window.chartSisaDeposito.render();
        }
    }

    // Function to resize all charts
    function resizeCharts() {
        setTimeout(function() {
            if (window.chartDepositoPokok && typeof window.chartDepositoPokok.resize === 'function') {
                window.chartDepositoPokok.resize();
            }
            if (window.chartCoF && typeof window.chartCoF.resize === 'function') {
                window.chartCoF.resize();
            }
            if (window.chartPengembalian && typeof window.chartPengembalian.resize === 'function') {
                window.chartPengembalian.resize();
            }
            if (window.chartSisaDeposito && typeof window.chartSisaDeposito.resize === 'function') {
                window.chartSisaDeposito.resize();
            }
        }, 100);
    }
    
    // Setup ResizeObserver for chart containers
    function setupResizeObservers() {
        const chartContainers = [
            document.querySelector("#chartDepositoPokok"),
            document.querySelector("#chartCoF"),
            document.querySelector("#chartPengembalian"),
            document.querySelector("#chartSisaDeposito")
        ];
        
        chartContainers.forEach(function(container) {
            if (container && window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(function(entries) {
                    resizeCharts();
                });
                resizeObserver.observe(container);
            }
        });
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Beri sedikit jeda waktu (100ms) untuk memastikan DOM siap sepenuhnya
        setTimeout(function() {
            initCharts();
            setupResizeObservers();
        }, 100);
        
        // Listen for sidebar toggle events
        const menuToggle = document.querySelector('.layout-menu-toggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                setTimeout(resizeCharts, 200);
            });
        }
        
        // Listen for menu collapse/expand events
        document.addEventListener('menu:collapsed', function() {
            setTimeout(resizeCharts, 200);
        });
        document.addEventListener('menu:expanded', function() {
            setTimeout(resizeCharts, 200);
        });
        
        // Listen for window resize with debounce
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resizeCharts, 250);
        });
    });

    // Re-render charts when Livewire updates
    document.addEventListener('livewire:navigated', function() {
        // Beri jeda waktu lebih lama (300ms) untuk Livewire update
        setTimeout(function() {
            initCharts();
            setupResizeObservers();
        }, 300);
        
        // Setup resize listeners again after navigation
        setTimeout(function() {
            const menuToggle = document.querySelector('.layout-menu-toggle');
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    setTimeout(resizeCharts, 200);
                });
            }
            document.addEventListener('menu:collapsed', function() {
                setTimeout(resizeCharts, 200);
            });
            document.addEventListener('menu:expanded', function() {
                setTimeout(resizeCharts, 200);
            });
        }, 100);
    });
</script>
@endpush