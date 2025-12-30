<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Pembiayaan Sfinlog</h4>
        </div>
    </div>

    {{-- Summary Cards Row 1 --}}
    <div class="row g-4 mb-4">
        {{-- Card Disbursement --}}
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
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
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

        {{-- Card Pembayaran --}}
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
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataPembayaran['previous_month_name'] ?? 'bulan lalu' }}</small>
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

        {{-- Card Sisa --}}
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
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataSisa['previous_month_name'] ?? 'bulan lalu' }}</small>
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

        {{-- Card Outstanding --}}
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
                                    <small class="fw-semibold {{ $isIncrease ? 'text-danger' : 'text-success' }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataOutstanding['previous_month_name'] ?? 'bulan lalu' }}</small>
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
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanDisbursement" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach(range(1,12) as $b)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulanDisbursement == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Gunakan wire:key untuk memaksa refresh saat filter berubah --}}
                    @if($hasDataDisbursement)
                        <div id="chartDisbursement" wire:key="chart-disbursement-{{ $bulanDisbursement }}-{{ $tahun }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Tidak ada data untuk periode yang dipilih</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Pokok dan Bagi Hasil Perbulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanPembayaran" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach(range(1,12) as $b)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulanPembayaran == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataPembayaran)
                        <div id="chartPembayaran" wire:key="chart-pembayaran-{{ $bulanPembayaran }}-{{ $tahun }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Tidak ada data untuk periode yang dipilih</p>
                        </div>
                    @endif
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
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanSisa" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach(range(1,12) as $b)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulanSisa == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataSisa)
                        <div id="chartSisaBelumTerbayar" wire:key="chart-sisa-{{ $bulanSisa }}-{{ $tahun }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Tidak ada data untuk periode yang dipilih</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Piutang Per Tahun</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterTahunPiutang" class="form-select select2" data-placeholder="Pilih Tahun">
                            @foreach(range(date('Y')-5, date('Y')+1) as $t)
                                <option value="{{ $t }}" {{ $tahunPiutang == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataPiutang)
                        <div id="chartPembayaranPiutang" wire:key="chart-piutang-{{ $tahunPiutang }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Tidak ada data untuk periode yang dipilih</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- AR Table Row --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-3">Total AR yang Terbagi Berdasarkan Kriteria Keterlambatan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small">Bulan</label>
                            <div wire:ignore>
                                <select id="filterBulanTable" class="form-select select2" data-placeholder="Pilih Bulan">
                                    <option value=""></option>
                                    @foreach(range(1,12) as $b)
                                        <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulanTable == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tahun</label>
                            <div wire:ignore>
                                <select id="filterTahunTable" class="form-select select2" data-placeholder="Pilih Tahun">
                                    @foreach(range(date('Y')-5, date('Y')+1) as $t)
                                        <option value="{{ $t }}" {{ $tahunTable == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
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
        
        {{-- Comparison Chart Row --}}
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Perbandingan AR dan Utang Pengembalian Deposito Perbulan</h5>
                    <div class="d-flex gap-2">
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison1" class="form-select select2" data-placeholder="Bulan 1">
                                <option value=""></option>
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan1 ?? '') == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison2" class="form-select select2" data-placeholder="Bulan 2">
                                <option value=""></option>
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan2 ?? '') == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Passing formatted data via data attribute --}}
                    <div id="chartComparison" 
                         wire:key="chart-comparison-{{ $bulan1 }}-{{ $bulan2 }}-{{ $tahun }}"
                         data-comparison-data="{{ json_encode($chartData['comparison'] ?? []) }}"
                         style="min-height: 300px;"></div>
                    
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
@endpush

@push('styles')
<style>
    /* Fix Select2 dropdown width - Copy from Sfinance */
    .select2-container {
        width: 100% !important;
    }
    
    #filterBulanDisbursement + .select2-container,
    #filterBulanPembayaran + .select2-container,
    #filterBulanSisa + .select2-container {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
    }
    
    #filterBulanComparison1 + .select2-container,
    #filterBulanComparison2 + .select2-container {
        width: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
    }
    
    /* Override purple theme color for Select2 */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd !important;
        color: #fff !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection,
    .select2-container--default.select2-container--open .select2-selection {
        border-color: #86b7fe !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection:focus,
    .select2-container--default.select2-container--open .select2-selection:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        'use strict';
        
        let chartDisbursement, chartPembayaran, chartSisaBelumTerbayar, chartPembayaranPiutang, chartComparison;

        // Initialize Select2 (Sama persis dengan Sfinance)
        function initSelect2() {
            // Destroy existing instances first
            const selectIds = [
                'filterBulanDisbursement', 'filterBulanPembayaran', 'filterBulanSisa', 
                'filterTahunPiutang', 'filterBulanTable', 'filterTahunTable',
                'filterBulanComparison1', 'filterBulanComparison2'
            ];
            
            selectIds.forEach(function(id) {
                const $select = $('#' + id);
                if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
            });

            // Initialize Select2 with consistent styling
            function initSelect(id, width, placeholder) {
                const $select = $('#' + id);
                if (!$select.length) return;
                
                $select.select2({
                    placeholder: placeholder || 'Pilih...',
                    minimumResultsForSearch: Infinity,
                    width: 'resolve',
                    allowClear: true,
                    dropdownAutoWidth: false
                });
                
                // Force set width after initialization
                setTimeout(function() {
                    $select.next('.select2-container').css({
                        'width': width + 'px',
                        'min-width': width + 'px',
                        'max-width': width + 'px'
                    });
                }, 10);
            }

            // Initialize all selects
            initSelect('filterBulanDisbursement', 150, 'Pilih Bulan');
            initSelect('filterBulanPembayaran', 150, 'Pilih Bulan');
            initSelect('filterBulanSisa', 150, 'Pilih Bulan');
            initSelect('filterTahunPiutang', 150, 'Pilih Tahun');
            initSelect('filterBulanTable', null, 'Pilih Bulan');
            initSelect('filterTahunTable', null, 'Pilih Tahun');
            initSelect('filterBulanComparison1', 120, 'Bulan 1');
            initSelect('filterBulanComparison2', 120, 'Bulan 2');

            // Handle change events with ISOLATED property mapping per filter
            
            $(document).off('change', '#filterBulanDisbursement');
            $(document).on('change', '#filterBulanDisbursement', function() {
                const bulan = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set('bulanDisbursement', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanPembayaran');
            $(document).on('change', '#filterBulanPembayaran', function() {
                const bulan = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set('bulanPembayaran', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanSisa');
            $(document).on('change', '#filterBulanSisa', function() {
                const bulan = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set('bulanSisa', bulan || null);
                }
            });

            $(document).off('change', '#filterTahunPiutang');
            $(document).on('change', '#filterTahunPiutang', function() {
                const tahun = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set('tahunPiutang', tahun);
                }
            });

            $(document).off('change', '#filterBulanTable');
            $(document).on('change', '#filterBulanTable', function() {
                const bulan = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set('bulanTable', bulan || null);
                }
            });

            $(document).off('change', '#filterTahunTable');
            $(document).on('change', '#filterTahunTable', function() {
                const tahun = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set('tahunTable', tahun);
                }
            });

            $(document).off('change', '#filterBulanComparison1');
            $(document).on('change', '#filterBulanComparison1', function() {
                const bulan = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    if (chartComparison) { chartComparison.destroy(); chartComparison = null; }
                    Livewire.find(componentId).set('bulan1', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanComparison2');
            $(document).on('change', '#filterBulanComparison2', function() {
                const bulan = $(this).val();
                let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    if (chartComparison) { chartComparison.destroy(); chartComparison = null; }
                    Livewire.find(componentId).set('bulan2', bulan || null);
                }
            });
        }

        function initCharts() {
            if (typeof ApexCharts === 'undefined') return;

            // Destroy existing charts
            const charts = [
                { ref: chartDisbursement, name: 'Disbursement' },
                { ref: chartPembayaran, name: 'Pembayaran' },
                { ref: chartSisaBelumTerbayar, name: 'Sisa' },
                { ref: chartPembayaranPiutang, name: 'Piutang' },
                { ref: chartComparison, name: 'Comparison' }
            ];
            
            charts.forEach(chart => {
                try { if (chart.ref) chart.ref.destroy(); } catch(e) {}
            });
            
            chartDisbursement = null;
            chartPembayaran = null;
            chartSisaBelumTerbayar = null;
            chartPembayaranPiutang = null;
            chartComparison = null;

            // Chart Disbursement
            const disbursementEl = document.querySelector("#chartDisbursement");
            if (disbursementEl) {
                const options = {
                    series: [
                        { name: 'Pokok', data: @json($chartData['disbursement']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['disbursement']['bagi_hasil'] ?? []) }
                    ],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: @json($chartData['disbursement']['categories'] ?? []) },
                    yaxis: { labels: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } },
                    fill: { opacity: 1 },
                    colors: ['#71dd37', '#ffab00'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: { y: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } }
                };
                chartDisbursement = new ApexCharts(disbursementEl, options);
                chartDisbursement.render();
            }

            // Chart Pembayaran
            const pembayaranEl = document.querySelector("#chartPembayaran");
            if (pembayaranEl) {
                const options = {
                    series: [
                        { name: 'Pokok', data: @json($chartData['pembayaran']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['pembayaran']['bagi_hasil'] ?? []) }
                    ],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: @json($chartData['pembayaran']['categories'] ?? []) },
                    yaxis: { labels: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } },
                    fill: { opacity: 1 },
                    colors: ['#71dd37', '#ffab00'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: { y: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } }
                };
                chartPembayaran = new ApexCharts(pembayaranEl, options);
                chartPembayaran.render();
            }

            // Chart Sisa
            const sisaEl = document.querySelector("#chartSisaBelumTerbayar");
            if (sisaEl) {
                const options = {
                    series: [
                        { name: 'Pokok', data: @json($chartData['sisa_belum_terbayar']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['sisa_belum_terbayar']['bagi_hasil'] ?? []) }
                    ],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: @json($chartData['sisa_belum_terbayar']['categories'] ?? []) },
                    yaxis: { labels: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } },
                    fill: { opacity: 1 },
                    colors: ['#71dd37', '#ffab00'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: { y: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } }
                };
                chartSisaBelumTerbayar = new ApexCharts(sisaEl, options);
                chartSisaBelumTerbayar.render();
            }

            // Chart Piutang
            const piutangEl = document.querySelector("#chartPembayaranPiutang");
            if (piutangEl) {
                const options = {
                    series: [
                        { name: 'Pokok', data: @json($chartData['pembayaran_piutang_tahun']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['pembayaran_piutang_tahun']['bagi_hasil'] ?? []) }
                    ],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: @json($chartData['pembayaran_piutang_tahun']['categories'] ?? []) },
                    yaxis: { labels: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } },
                    fill: { opacity: 1 },
                    colors: ['#71dd37', '#ffab00'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: { y: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } }
                };
                chartPembayaranPiutang = new ApexCharts(piutangEl, options);
                chartPembayaranPiutang.render();
            }

            // Chart Comparison
            const comparisonEl = document.querySelector("#chartComparison");
            if (comparisonEl) {
                // Read fresh data from data attribute that we formatted in Livewire
                const comparisonData = JSON.parse(comparisonEl.getAttribute('data-comparison-data') || '{}');
                
                const options = {
                    series: [
                        { name: 'AR', data: comparisonData.ar || [] },
                        { name: 'Utang Pengembalian', data: comparisonData.utang_pengembalian_deposito || [] }
                    ],
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: comparisonData.categories || ['Bulan 2', 'Bulan 1'] },
                    yaxis: { labels: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } },
                    fill: { opacity: 1 },
                    colors: ['#71dd37', '#ffab00'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: { y: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } }
                };
                chartComparison = new ApexCharts(comparisonEl, options);
                chartComparison.render();
            }
        }

        // Main Initialization Logic
        function initializeDashboard() {
            if (typeof ApexCharts === 'undefined' || typeof $ === 'undefined') {
                setTimeout(initializeDashboard, 200);
                return;
            }
            
            // Collect existing charts
            const chartSelectors = ['#chartDisbursement', '#chartPembayaran', '#chartSisaBelumTerbayar', '#chartPembayaranPiutang', '#chartComparison'];
            const present = chartSelectors.filter(sel => document.querySelector(sel));

            // Init Select2 regardless of charts
            try { initSelect2(); } catch(e) { console.error(e); }

            // If charts exist, init them
            if (present.length > 0) {
                try { initCharts(); } catch(e) { console.error(e); }
            }
        }

        // Event Listeners
        $(document).ready(function() { setTimeout(initializeDashboard, 500); });
        document.addEventListener('livewire:init', function() {
            setTimeout(initializeDashboard, 300);
            
            // Re-init on updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                setTimeout(() => {
                    initSelect2();
                    if (window._chartReinitTimeout) clearTimeout(window._chartReinitTimeout);
                    window._chartReinitTimeout = setTimeout(() => {
                        // Destroy first
                        if(chartDisbursement) chartDisbursement.destroy();
                        if(chartPembayaran) chartPembayaran.destroy();
                        if(chartSisaBelumTerbayar) chartSisaBelumTerbayar.destroy();
                        if(chartPembayaranPiutang) chartPembayaranPiutang.destroy();
                        if(chartComparison) chartComparison.destroy();
                        
                        setTimeout(initCharts, 200);
                    }, 300);
                }, 200);
            });
        });

        // Specific listeners for chart updates
        const updateChart = (chartVar, destroyAndInit) => {
            if (chartVar) chartVar.destroy();
            setTimeout(initCharts, 100);
        };

        if (window.livewire) {
            window.livewire.on('updateChartDisbursement', () => updateChart(chartDisbursement));
            window.livewire.on('updateChartPembayaran', () => updateChart(chartPembayaran));
            window.livewire.on('updateChartSisa', () => updateChart(chartSisaBelumTerbayar));
            window.livewire.on('updateChartPiutang', () => updateChart(chartPembayaranPiutang));
        }
    })();
</script>
@endpush
</div>