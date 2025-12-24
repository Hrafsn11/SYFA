<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Pembiayaan Sfinlog</h4>
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
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryData['total_disbursement'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @php
                                    $persen = $summaryData['total_disbursement_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_disbursement_is_increase'] ?? false;
                                    $isNew = $summaryData['total_disbursement_is_new'] ?? false;
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @elseif($isIncrease)
                                        <i class="ti ti-arrow-up-right text-danger"></i>
                                    @else
                                        <i class="ti ti-arrow-down-right text-success"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ $persen }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Pembayaran Masuk Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryDataPembayaran['total_pembayaran_masuk'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @php
                                    $persen = $summaryDataPembayaran['total_pembayaran_masuk_percentage'] ?? 0;
                                    $isIncrease = $summaryDataPembayaran['total_pembayaran_masuk_is_increase'] ?? false;
                                    $isNew = $summaryDataPembayaran['total_pembayaran_masuk_is_new'] ?? false;
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @elseif($isIncrease)
                                        <i class="ti ti-arrow-up-right text-danger"></i>
                                    @else
                                        <i class="ti ti-arrow-down-right text-success"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryDataPembayaran['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ $persen }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataPembayaran['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Sisa yang Belum Terbayar Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryDataSisa['total_sisa_belum_terbayar'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @php
                                    $persen = $summaryDataSisa['total_sisa_belum_terbayar_percentage'] ?? 0;
                                    $isIncrease = $summaryDataSisa['total_sisa_belum_terbayar_is_increase'] ?? false;
                                    $isNew = $summaryDataSisa['total_sisa_belum_terbayar_is_new'] ?? false;
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @elseif($isIncrease)
                                        <i class="ti ti-arrow-up-right text-danger"></i>
                                    @else
                                        <i class="ti ti-arrow-down-right text-success"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryDataSisa['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ $persen }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataSisa['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Outstanding Piutang</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryDataOutstanding['total_outstanding_piutang'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @php
                                    $persen = $summaryDataOutstanding['total_outstanding_piutang_percentage'] ?? 0;
                                    $isIncrease = $summaryDataOutstanding['total_outstanding_piutang_is_increase'] ?? false;
                                    $isNew = $summaryDataOutstanding['total_outstanding_piutang_is_new'] ?? false;
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @elseif($isIncrease)
                                        <i class="ti ti-arrow-up-right text-danger"></i>
                                    @else
                                        <i class="ti ti-arrow-down-right text-success"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryDataOutstanding['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ $persen }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataOutstanding['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
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
                    <select wire:model.live="bulanDisbursement" class="form-select form-select-sm select2" style="max-width: 150px;">
                        @foreach(range(1,12) as $b)
                            <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}">{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div id="chartDisbursement">
                        @unless($hasDataDisbursement)
                            <div class="text-center text-muted py-5">
                                <p>Tidak ada data untuk periode yang dipilih</p>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Pokok dan Bagi Hasil Perbulan</h5>
                    <select wire:model.live="bulanPembayaran" class="form-select form-select-sm select2" style="max-width: 150px;">
                        @foreach(range(1,12) as $b)
                            <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}">{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div id="chartPembayaran">
                        @unless($hasDataPembayaran)
                            <div class="text-center text-muted py-5">
                                <p>Tidak ada data untuk periode yang dipilih</p>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 3 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa yang Belum Terbayar Perbulan</h5>
                    <select wire:model.live="bulanSisa" class="form-select form-select-sm select2" style="max-width: 150px;">
                        @foreach(range(1,12) as $b)
                            <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}">{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div id="chartSisaBelumTerbayar">
                        @unless($hasDataSisa)
                            <div class="text-center text-muted py-5">
                                <p>Tidak ada data untuk periode yang dipilih</p>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Piutang Per Tahun</h5>
                    <select wire:model.live="tahunPiutang" class="form-select form-select-sm select2" style="max-width: 120px;">
                        @foreach(range(date('Y')-5, date('Y')+1) as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div id="chartPembayaranPiutang">
                        @unless($hasDataPiutang)
                            <div class="text-center text-muted py-5">
                                <p>Tidak ada data untuk periode yang dipilih</p>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AR Table Row --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-3">Total AR yang Terbagi Berdasarkan Kriteria Keterlambatan</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Bulan</label>
                            <select wire:model.live="bulanTable" class="form-select form-select-sm">
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}">{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tahun</label>
                            <select wire:model.live="tahunTable" class="form-select form-select-sm">
                                @foreach(range(date('Y')-5, date('Y')+1) as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>DEBITUR</th>
                                    <th>DEL 1-30</th>
                                    <th>DEL 31-60</th>
                                    <th>DEL 61-90</th>
                                    <th>NPL 91-179</th>
                                    <th>WRITE OFF >180</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($arTableData ?? []) as $row)
                                    <tr>
                                        <td>{{ $row['debitur'] ?? '-' }}</td>
                                        <td>Rp {{ number_format($row['del_1_30'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['del_31_60'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['del_61_90'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['npl_91_179'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['write_off'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Comparison Chart Row --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Perbandingan AR dan Utang Pengembalian Deposito Perbulan</h5>
                    <div class="d-flex gap-2">
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison1" class="form-select form-select-sm select2" data-placeholder="Bulan 1">
                                <option value=""></option>
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan1 ?? '') == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison2" class="form-select form-select-sm select2" data-placeholder="Bulan 2">
                                <option value=""></option>
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan2 ?? '') == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartComparison"></div>
                    
                    {{-- Selisih / Perubahan --}}
                    <div class="row g-3 mt-4">
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3" style="background-color: #f0f7ff;">
                                <small class="text-muted d-block mb-2">Selisih AR</small>
                                <h5 class="mb-0 fw-bold">
                                    Rp {{ number_format(abs($chartData['comparison']['ar_selisih'] ?? 0), 0, ',', '.') }}
                                </h5>
                                @php
                                    $arSelisih = $chartData['comparison']['ar_selisih'] ?? 0;
                                @endphp
                                <small class="text-muted">
                                    @if($arSelisih > 0)
                                        <i class="ti ti-arrow-up-right text-danger"></i> Naik
                                    @elseif($arSelisih < 0)
                                        <i class="ti ti-arrow-down-right text-success"></i> Turun
                                    @else
                                        <i class="ti ti-minus text-secondary"></i> Tidak ada perubahan
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3" style="background-color: #fff8f0;">
                                <small class="text-muted d-block mb-2">Selisih Utang Pengembalian Deposito</small>
                                <h5 class="mb-0 fw-bold">
                                    Rp {{ number_format(abs($chartData['comparison']['utang_selisih'] ?? 0), 0, ',', '.') }}
                                </h5>
                                @php
                                    $utangSelisih = $chartData['comparison']['utang_selisih'] ?? 0;
                                @endphp
                                <small class="text-muted">
                                    @if($utangSelisih > 0)
                                        <i class="ti ti-arrow-up-right text-danger"></i> Naik
                                    @elseif($utangSelisih < 0)
                                        <i class="ti ti-arrow-down-right text-success"></i> Turun
                                    @else
                                        <i class="ti ti-minus text-secondary"></i> Tidak ada perubahan
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('vendor-scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@push('styles')
<style>
    /* Form Select Base Styling */
    .form-select.form-select-sm {
        font-size: 0.95rem;
        padding: 0.25rem 1.75rem 0.25rem 0.75rem;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
        background-color: #fff;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    
    .form-select.form-select-sm:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    
    /* Select2 Container Styling */
    .select2-container--default .select2-selection--single {
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        height: 31px !important;
        padding: 0 !important;
        font-size: 0.95rem !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 31px !important;
        padding-left: 0.75rem !important;
        color: #212529;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 31px !important;
        right: 0.75rem !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        margin-top: 5px !important;
    }
    
    /* Select2 Dropdown Styling */
    .select2-dropdown {
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        margin-top: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .select2-results__option {
        padding: 8px 0.75rem !important;
        font-size: 0.95rem;
    }
    
    .select2-results__option--highlighted {
        background-color: #28c76f !important;
        color: #fff !important;
    }
    
    .select2-results__option--selected {
        background-color: #e8f5e9 !important;
        color: #212529 !important;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e8f5e9 !important;
        color: #212529 !important;
    }
    
    /* Container Width Control */
    .select2-container {
        min-width: 100px !important;
        max-width: 100% !important;
    }
    
    .select2-container.form-select-sm {
        width: 100% !important;
    }
    
    /* Dropdown Menu Positioning */
    .select2-dropdown.select2-dropdown--below {
        border-top: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    
    .select2-dropdown.select2-dropdown--above {
        border-bottom: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    /* Search Field in Dropdown */
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.95rem !important;
    }
    
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #86b7fe !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        'use strict';
        
        let chartDisbursement, chartPembayaran, chartSisaBelumTerbayar, chartPembayaranPiutang, chartComparison;

        function initCharts() {
            // Check if ApexCharts is available
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded');
                return;
            }

            // Destroy existing charts first with robust error handling
            const charts = [
                { ref: chartDisbursement, name: 'Disbursement' },
                { ref: chartPembayaran, name: 'Pembayaran' },
                { ref: chartSisaBelumTerbayar, name: 'Sisa' },
                { ref: chartPembayaranPiutang, name: 'Piutang' },
                { ref: chartComparison, name: 'Comparison' }
            ];
            
            charts.forEach(chart => {
                try {
                    if (chart.ref && typeof chart.ref.destroy === 'function') {
                        chart.ref.destroy();
                    }
                } catch(e) {
                    console.warn('Error destroying chart ' + chart.name + ':', e);
                }
            });
            
            chartDisbursement = null;
            chartPembayaran = null;
            chartSisaBelumTerbayar = null;
            chartPembayaranPiutang = null;
            chartComparison = null;

            // Helper function untuk check apakah data valid
            const isValidChartData = (data) => {
                return data && data.categories && data.categories.length > 0;
            };

            // Chart Disbursement
            const disbursementData = @json($chartData['disbursement'] ?? []);
            if (isValidChartData(disbursementData)) {
                const disbursementOptions = {
                    series: [
                        { name: 'Pokok', data: disbursementData.pokok ?? [] },
                        { name: 'Bagi Hasil', data: disbursementData.bagi_hasil ?? [] }
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: disbursementData.categories ?? []
                    },
                    yaxis: {},
                    fill: {
                        opacity: 1
                    },
                    colors: ['#71dd37', '#ffab00'],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + val.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                const chartDisbursementEl = document.querySelector("#chartDisbursement");
                if (chartDisbursementEl) {
                    try {
                        chartDisbursement = new ApexCharts(chartDisbursementEl, disbursementOptions);
                        chartDisbursement.render();
                    } catch(e) {
                        console.error('Error rendering Disbursement chart:', e);
                    }
                }
            }

            // Chart Pembayaran
            const pembayaranData = @json($chartData['pembayaran'] ?? []);
            if (isValidChartData(pembayaranData)) {
                const pembayaranOptions = {
                    series: [
                        { name: 'Pokok', data: pembayaranData.pokok ?? [] },
                        { name: 'Bagi Hasil', data: pembayaranData.bagi_hasil ?? [] }
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: pembayaranData.categories ?? []
                    },
                    yaxis: {},
                    fill: {
                        opacity: 1
                    },
                    colors: ['#71dd37', '#ffab00'],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + val.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                const chartPembayaranEl = document.querySelector("#chartPembayaran");
                if (chartPembayaranEl) {
                    try {
                        chartPembayaran = new ApexCharts(chartPembayaranEl, pembayaranOptions);
                        chartPembayaran.render();
                    } catch(e) {
                        console.error('Error rendering Pembayaran chart:', e);
                    }
                }
            }

            // Chart Sisa Belum Terbayar
            const sisaData = @json($chartData['sisa_belum_terbayar'] ?? []);
            if (isValidChartData(sisaData)) {
                const sisaOptions = {
                    series: [
                        { name: 'Pokok', data: sisaData.pokok || [] },
                        { name: 'Bagi Hasil', data: sisaData.bagi_hasil || [] }
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: sisaData.categories || []
                    },
                    yaxis: {},
                    fill: {
                        opacity: 1
                    },
                    colors: ['#71dd37', '#ffab00'],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + val.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                const chartSisaEl = document.querySelector("#chartSisaBelumTerbayar");
                if (chartSisaEl) {
                    try {
                        chartSisaBelumTerbayar = new ApexCharts(chartSisaEl, sisaOptions);
                        chartSisaBelumTerbayar.render();
                    } catch(e) {
                        console.error('Error rendering Sisa chart:', e);
                    }
                }
            }

            // Chart Pembayaran Piutang
            const piutangData = @json($chartData['pembayaran_piutang_tahun'] ?? []);
            if (isValidChartData(piutangData)) {
                const piutangOptions = {
                    series: [
                        { name: 'Pokok', data: piutangData.pokok ?? [] },
                        { name: 'Bagi Hasil', data: piutangData.bagi_hasil ?? [] }
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: piutangData.categories ?? []
                    },
                    yaxis: {},
                    fill: {
                        opacity: 1
                    },
                    colors: ['#71dd37'],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + val.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                const chartPiutangEl = document.querySelector("#chartPembayaranPiutang");
                if (chartPiutangEl) {
                    try {
                        chartPembayaranPiutang = new ApexCharts(chartPiutangEl, piutangOptions);
                        chartPembayaranPiutang.render();
                    } catch(e) {
                        console.error('Error rendering Piutang chart:', e);
                    }
                }
            }

            // Chart Comparison
            const comparisonData = @json($chartData['comparison'] ?? []);
            if (comparisonData && comparisonData.categories) {
                const comparisonOptions = {
                    series: [
                        { name: 'AR', data: [comparisonData.ar_bulan2 || 0, comparisonData.ar_bulan1 || 0] },
                        { name: 'Utang Pengembalian', data: [comparisonData.utang_bulan2 || 0, comparisonData.utang_bulan1 || 0] }
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: comparisonData.categories || ['Bulan 1', 'Bulan 2']
                    },
                    yaxis: {},
                    fill: {
                        opacity: 1
                    },
                    colors: ['#71dd37', '#ffab00'],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Rp " + val.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                const chartComparisonEl = document.querySelector("#chartComparison");
                if (chartComparisonEl) {
                    try {
                        chartComparison = new ApexCharts(chartComparisonEl, comparisonOptions);
                        chartComparison.render();
                    } catch(e) {
                        console.error('Error rendering Comparison chart:', e);
                    }
                }
            }
        }

        // Initialize Select2 ONLY for chart filter dropdowns (not for comparison/table)
        function initSelect2() {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                try {
                    // Initialize main chart filter selects (with select2 class)
                    $('select.select2:not(#filterBulanComparison1):not(#filterBulanComparison2)').each(function() {
                        try {
                            if ($(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2('destroy');
                            }
                            
                            $(this).select2({
                                minimumResultsForSearch: Infinity,
                                width: '100%',
                                placeholder: 'Pilih Periode',
                                allowClear: false,
                                closeOnSelect: true
                            });

                        } catch(e) {
                            console.warn('Error with Select2 on element:', $(this), e);
                        }
                    });

                    // Initialize comparison chart selects separately with ID targeting
                    // (these use wire:ignore and manual event handling)
                    $('#filterBulanComparison1, #filterBulanComparison2').each(function() {
                        try {
                            if ($(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2('destroy');
                            }
                            
                            $(this).select2({
                                minimumResultsForSearch: Infinity,
                                width: '100%',
                                closeOnSelect: true
                            });

                        } catch(e) {
                            console.warn('Error with comparison Select2:', $(this), e);
                        }
                    });

                    // Add change event handlers for comparison selects to trigger Livewire update
                    $('#filterBulanComparison1').on('change', function() {
                        const value = $(this).val();
                        if (window.Livewire && Livewire.first()) {
                            Livewire.first().set('bulan1', value);
                        }
                    });

                    $('#filterBulanComparison2').on('change', function() {
                        const value = $(this).val();
                        if (window.Livewire && Livewire.first()) {
                            Livewire.first().set('bulan2', value);
                        }
                    });

                } catch (error) {
                    console.error('Error initializing Select2:', error);
                }
            }
        }

        // Initialize when everything is ready
        function initializeDashboard() {
            // Check if ApexCharts is loaded
            if (typeof ApexCharts === 'undefined') {
                console.log('ApexCharts not loaded yet, retrying...');
                setTimeout(initializeDashboard, 500);
                return;
            }
            
            // Check if jQuery is loaded
            if (typeof $ === 'undefined') {
                console.log('jQuery not loaded yet, retrying...');
                setTimeout(initializeDashboard, 500);
                return;
            }
            
            // Initialize Select2 and Charts
            try {
                initSelect2();
                initCharts();
            } catch (error) {
                console.error('Error initializing dashboard:', error);
            }
        }

        $(document).ready(function() {
            // Wait for all scripts to load
            setTimeout(function() {
                initializeDashboard();
            }, 500);
        });

        // Also initialize when Livewire is ready
        document.addEventListener('livewire:init', function() {
            setTimeout(function() {
                initializeDashboard();
            }, 300);
        });

        // Reinitialize when Livewire updates
        document.addEventListener('livewire:updated', function() {
            // Give Livewire time to update the DOM before re-initializing
            setTimeout(() => {
                try {
                    initSelect2();
                    initCharts();
                    console.log('Dashboard re-initialized after Livewire update');
                } catch(e) {
                    console.error('Error during Livewire update reinit:', e);
                }
            }, 300);
        });
    })();
</script>
@endpush

