<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Pembiayaan Sfinance</h4>
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
                                    // Sesuai TC-DASH-001: Turun = Merah (Indikasi negatif)
                                    $color = $isIncrease ? 'text-success' : 'text-danger';
                                    $icon = $isIncrease ? 'ti-arrow-up-right' : 'ti-arrow-down-right';
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @else
                                        <i class="ti {{ $icon }} {{ $color }}"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $color }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
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
                                    $color = $isIncrease ? 'text-success' : 'text-danger';
                                    $icon = $isIncrease ? 'ti-arrow-up-right' : 'ti-arrow-down-right';
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @else
                                        <i class="ti {{ $icon }} {{ $color }}"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryDataPembayaran['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $color }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataPembayaran['previous_month_name'] ?? 'bulan lalu' }}</small>
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
                                    // Sisa Naik = Merah (Buruk), Sisa Turun = Hijau (Bagus)
                                    $color = $isIncrease ? 'text-danger' : 'text-success';
                                    $icon = $isIncrease ? 'ti-arrow-up-right' : 'ti-arrow-down-right';
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @else
                                        <i class="ti {{ $icon }} {{ $color }}"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryDataSisa['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $color }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataSisa['previous_month_name'] ?? 'bulan lalu' }}</small>
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
                                    // Sesuai TC-DASH-004: Outstanding Naik = Merah (Bahaya)
                                    $color = $isIncrease ? 'text-danger' : 'text-success';
                                    $icon = $isIncrease ? 'ti-arrow-up-right' : 'ti-arrow-down-right';
                                @endphp
                                <span class="me-1">
                                    @if($isNew)
                                        <i class="ti ti-sparkles text-info"></i>
                                    @else
                                        <i class="ti {{ $icon }} {{ $color }}"></i>
                                    @endif
                                </span>
                                @if($isNew)
                                    <small class="fw-semibold text-info">Baru 100% dari {{ $summaryDataOutstanding['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <small class="fw-semibold {{ $color }}">{{ number_format($persen, 1) }}% {{ $isIncrease ? 'naik' : 'turun' }} dari {{ $summaryDataOutstanding['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; background: #28c76f;">
                            <i class="ti ti-report-money text-white fs-5"></i>
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
                            @php
                                $bulanNama = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                            @endphp
                            @foreach(range(1,12) as $b)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulanDisbursement == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$b] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
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
                                    {{ $bulanNama[$b] }}
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
                                    {{ $bulanNama[$b] }}
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

    {{-- Row 4: Table and Comparison (Side-by-Side like Sfinlog) --}}
    <div class="row g-4 mb-4">
        {{-- AR Table --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100">
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
                                            {{ $bulanNama[$b] }}
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
                                @forelse(($arTableData ?? []) as $row)
                                    <tr>
                                        <td>{{ $row['debitur'] ?? '-' }}</td>
                                        <td>Rp {{ number_format($row['del_1_30'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['del_31_60'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['del_61_90'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['npl_91_179'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['write_off'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Comparison Chart --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Perbandingan AR dan Utang Pengembalian Deposito Perbulan</h5>
                    <div class="d-flex gap-2">
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison1" class="form-select select2" data-placeholder="Bulan 1">
                                <option value=""></option>
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan1 ?? '') == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $bulanNama[$b] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison2" class="form-select select2" data-placeholder="Bulan 2">
                                <option value=""></option>
                                @foreach(range(1,12) as $b)
                                    <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan2 ?? '') == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $bulanNama[$b] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Chart container with data attribute --}}
                    <div id="chartComparison" 
                         wire:key="chart-comparison-{{ $bulan1 }}-{{ $bulan2 }}-{{ $tahun }}"
                         data-comparison-data="{{ json_encode($chartData['comparison'] ?? []) }}"
                         style="min-height: 300px;"></div>
                    
                    {{-- Selisih / Perubahan Boxes (Sesuai Sfinlog) --}}
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
    /* Fix Select2 dropdown width */
    .select2-container {
        width: 100% !important;
    }
    
    #filterBulanDisbursement + .select2-container,
    #filterBulanPembayaran + .select2-container,
    #filterBulanSisa + .select2-container,
    #filterTahunPiutang + .select2-container {
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

        function initSelect2() {
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
                
                setTimeout(function() {
                    $select.next('.select2-container').css({
                        'width': width + 'px',
                        'min-width': width + 'px',
                        'max-width': width + 'px'
                    });
                }, 10);
            }

            initSelect('filterBulanDisbursement', 150, 'Pilih Bulan');
            initSelect('filterBulanPembayaran', 150, 'Pilih Bulan');
            initSelect('filterBulanSisa', 150, 'Pilih Bulan');
            initSelect('filterTahunPiutang', 150, 'Pilih Tahun');
            initSelect('filterBulanTable', null, 'Pilih Bulan');
            initSelect('filterTahunTable', null, 'Pilih Tahun');
            initSelect('filterBulanComparison1', 120, 'Bulan 1');
            initSelect('filterBulanComparison2', 120, 'Bulan 2');

            // Handle change events
            const handleSelectChange = (id, property) => {
                $(document).off('change', '#' + id);
                $(document).on('change', '#' + id, function() {
                    const val = $(this).val();
                    let componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                    if (componentId && typeof Livewire !== 'undefined') {
                        if (id.includes('Comparison') && chartComparison) {
                            chartComparison.destroy();
                            chartComparison = null;
                        }
                        Livewire.find(componentId).set(property, val || null);
                    }
                });
            };

            handleSelectChange('filterBulanDisbursement', 'bulanDisbursement');
            handleSelectChange('filterBulanPembayaran', 'bulanPembayaran');
            handleSelectChange('filterBulanSisa', 'bulanSisa');
            handleSelectChange('filterTahunPiutang', 'tahunPiutang');
            handleSelectChange('filterBulanTable', 'bulanTable');
            handleSelectChange('filterTahunTable', 'tahunTable');
            handleSelectChange('filterBulanComparison1', 'bulan1');
            handleSelectChange('filterBulanComparison2', 'bulan2');
        }

        function initCharts() {
            if (typeof ApexCharts === 'undefined') return;

            // Clean up
            [chartDisbursement, chartPembayaran, chartSisaBelumTerbayar, chartPembayaranPiutang, chartComparison].forEach(chart => {
                try { if (chart) chart.destroy(); } catch(e) {}
            });
            chartDisbursement = chartPembayaran = chartSisaBelumTerbayar = chartPembayaranPiutang = chartComparison = null;

            // Config
            const getBarOptions = (series, categories, colors) => ({
                series: series,
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: categories },
                yaxis: { labels: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } },
                fill: { opacity: 1 },
                colors: colors || ['#71dd37', '#ffab00'],
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { y: { formatter: (val) => "Rp " + val.toLocaleString('id-ID') } }
            });

            // 1. Disbursement
            const disbursementEl = document.querySelector("#chartDisbursement");
            if (disbursementEl) {
                chartDisbursement = new ApexCharts(disbursementEl, getBarOptions(
                    [
                        { name: 'Pokok', data: @json($chartData['disbursement']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['disbursement']['bagi_hasil'] ?? []) }
                    ],
                    @json($chartData['disbursement']['categories'] ?? [])
                ));
                chartDisbursement.render();
            }

            // 2. Pembayaran
            const pembayaranEl = document.querySelector("#chartPembayaran");
            if (pembayaranEl) {
                chartPembayaran = new ApexCharts(pembayaranEl, getBarOptions(
                    [
                        { name: 'Pokok', data: @json($chartData['pembayaran']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['pembayaran']['bagi_hasil'] ?? []) }
                    ],
                    @json($chartData['pembayaran']['categories'] ?? [])
                ));
                chartPembayaran.render();
            }

            // 3. Sisa
            const sisaEl = document.querySelector("#chartSisaBelumTerbayar");
            if (sisaEl) {
                const sisaData = @json($chartData['sisa_belum_terbayar'] ?? []);
                chartSisaBelumTerbayar = new ApexCharts(sisaEl, getBarOptions(
                    [
                        { name: 'Pokok', data: sisaData.pokok || [] },
                        { name: 'Bagi Hasil', data: sisaData.bagi_hasil || [] }
                    ],
                    sisaData.categories || []
                ));
                chartSisaBelumTerbayar.render();
            }

            // 4. Piutang
            const piutangEl = document.querySelector("#chartPembayaranPiutang");
            if (piutangEl) {
                chartPembayaranPiutang = new ApexCharts(piutangEl, getBarOptions(
                    [
                        { name: 'Pokok', data: @json($chartData['pembayaran_piutang_tahun']['pokok'] ?? []) },
                        { name: 'Bagi Hasil', data: @json($chartData['pembayaran_piutang_tahun']['bagi_hasil'] ?? []) }
                    ],
                    @json($chartData['pembayaran_piutang_tahun']['categories'] ?? [])
                ));
                chartPembayaranPiutang.render();
            }

            // 5. Comparison
            const elComp = document.querySelector("#chartComparison");
            if (elComp) {
                const compData = JSON.parse(elComp.getAttribute('data-comparison-data') || '{}');
                // Sfinance service NOW updated to match Sfinlog return structure
                // Keys: ar_bulan1, ar_bulan2, utang_bulan1, utang_bulan2
                
                chartComparison = new ApexCharts(elComp, getBarOptions(
                    [
                        { name: 'AR', data: [compData.ar_bulan2 || 0, compData.ar_bulan1 || 0] },
                        { name: 'Utang Pengembalian', data: [compData.utang_bulan2 || 0, compData.utang_bulan1 || 0] }
                    ],
                    compData.categories || ['Bulan Lalu', 'Bulan Ini']
                ));
                chartComparison.render();
            }
        }

        // Init
        function initializeDashboard() {
            if (typeof ApexCharts === 'undefined') {
                setTimeout(initializeDashboard, 200); return;
            }
            try { initSelect2(); initCharts(); } catch(e) { console.error(e); }
        }

        $(document).ready(() => setTimeout(initializeDashboard, 500));
        document.addEventListener('livewire:init', () => {
            setTimeout(initializeDashboard, 300);
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    initSelect2();
                    if(window._tm) clearTimeout(window._tm);
                    window._tm = setTimeout(initCharts, 200);
                }, 200);
            });
        });

        const refresh = (c) => { if(c) c.destroy(); setTimeout(initCharts, 100); };
        if(window.livewire){
            window.livewire.on('updateChartDisbursement', () => refresh(chartDisbursement));
            window.livewire.on('updateChartPembayaran', () => refresh(chartPembayaran));
            window.livewire.on('updateChartSisa', () => refresh(chartSisaBelumTerbayar));
            window.livewire.on('updateChartPiutang', () => refresh(chartPembayaranPiutang));
        }
    })();
</script>
@endpush
</div>