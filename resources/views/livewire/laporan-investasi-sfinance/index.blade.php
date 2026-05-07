<div>
    {{-- Page Header --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Laporan Investasi SFinance</h4>
            <p class="text-muted mb-0 small">Data rekapitulasi investasi, CoF bulanan, dan pengembalian dana</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card">

        {{-- Controls Bar --}}
        <div class="card-header bg-white border-bottom py-3">
            <div class="row g-2 align-items-end">

                {{-- Search --}}
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1 laporan-filter-label">
                        <i class="ti ti-search me-1"></i>Pencarian
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text"
                            class="form-control"
                            placeholder="Deposan, no kontrak, status..."
                            wire:model.live.debounce.400ms="globalSearch">
                        @if($globalSearch)
                            <button class="btn btn-outline-secondary" type="button"
                                wire:click="$set('globalSearch', '')" title="Hapus pencarian">
                                <i class="ti ti-x"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Tahun --}}
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1 laporan-filter-label">
                        <i class="ti ti-calendar me-1"></i>Tahun
                    </label>
                    <select class="form-select form-select-sm" wire:model.live="year">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y') + 3; $y >= date('Y') - 10; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1 laporan-filter-label">
                        <i class="ti ti-filter me-1"></i>Status
                    </label>
                    <select class="form-select form-select-sm" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>

                {{-- Active-filter badges + Reset --}}
                <div class="col-12 col-md-4 d-flex align-items-end justify-content-md-end flex-wrap gap-1 laporan-action-wrap">
                    <button class="btn btn-sm btn-success d-flex align-items-center gap-1"
                        wire:click="exportExcel"
                        wire:loading.attr="disabled"
                        wire:target="exportExcel">
                        <span wire:loading.remove wire:target="exportExcel">
                            <i class="ti ti-file-spreadsheet"></i>
                        </span>
                        <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Export Excel</span>
                    </button>

                    <button class="btn btn-sm btn-danger d-flex align-items-center gap-1"
                        wire:click="exportPdf"
                        wire:loading.attr="disabled"
                        wire:target="exportPdf">
                        <span wire:loading.remove wire:target="exportPdf">
                            <i class="ti ti-file-type-pdf"></i>
                        </span>
                        <span wire:loading wire:target="exportPdf" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Export PDF</span>
                    </button>

                    @if($globalSearch)
                        <span class="badge bg-label-primary d-flex align-items-center gap-1" style="font-size:.78rem;">
                            <i class="ti ti-search" style="font-size:.75rem;"></i>
                            {{ Str::limit($globalSearch, 20) }}
                        </span>
                    @endif
                    @if($year)
                        <span class="badge bg-label-info d-flex align-items-center gap-1" style="font-size:.78rem;">
                            <i class="ti ti-calendar" style="font-size:.75rem;"></i>
                            {{ $year }}
                        </span>
                    @endif
                    @if($filterStatus)
                        <span class="badge bg-label-secondary d-flex align-items-center gap-1" style="font-size:.78rem;">
                            <i class="ti ti-tag" style="font-size:.75rem;"></i>
                            {{ $filterStatus }}
                        </span>
                    @endif
                    @if($globalSearch || $year || $filterStatus)
                        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                            wire:click="clearFilters">
                            <i class="ti ti-filter-off"></i>
                            <span>Reset</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Color legend --}}
        <div class="px-3 pt-2 pb-2 border-bottom bg-white d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="badge-group">
                    <span class="dot dot-info"></span>
                    <span>Data Investasi</span>
                </span>
                <span class="badge-group">
                    <span class="dot dot-cof"></span>
                    <span>CoF Per Bulan</span>
                </span>
                <span class="badge-group">
                    <span class="dot dot-danger"></span>
                    <span>Pengembalian</span>
                </span>
            </div>
        </div>

        {{-- Table (scrollable) --}}
        <div class="laporan-tabel-wrapper">
            <livewire:laporan-investasi-s-finance-table
                :year="$year"
                :filterStatus="$filterStatus"
                :key="'ltable-' . $year . '-' . $filterStatus" />
        </div>
    </div>
</div>

@push('styles')
<style>
    .laporan-filter-label {
        letter-spacing: normal;
        font-size: 0.78rem;
    }

    /* ── Wrapper scroll ────────────────────────────────── */
    .laporan-tabel-wrapper {
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }
    /* Rappasoft wrapper inside — must also allow scroll */
    .laporan-tabel-wrapper > div {
        min-width: 0;
    }

    /* ── Table base ─────────────────────────────────────── */
    #laporan-investasi-tabel {
        border-collapse: separate;
        border-spacing: 0;
        min-width: 2800px;
        margin-bottom: 0;
    }

    /* ── Sticky columns ─────────────────────────────────── */
    #laporan-investasi-tabel th:nth-child(1),
    #laporan-investasi-tabel td:nth-child(1) {
        position: sticky; left: 0; min-width: 52px; width: 52px; z-index: 3;
    }
    #laporan-investasi-tabel th:nth-child(2),
    #laporan-investasi-tabel td:nth-child(2) {
        position: sticky; left: 52px; min-width: 140px; z-index: 3;
    }
    #laporan-investasi-tabel th:nth-child(3),
    #laporan-investasi-tabel td:nth-child(3) {
        position: sticky; left: 192px; min-width: 120px; z-index: 3;
    }
    #laporan-investasi-tabel th:nth-child(4),
    #laporan-investasi-tabel td:nth-child(4) {
        position: sticky; left: 312px; min-width: 150px; z-index: 3;
        border-right: 2px solid #d9dee3 !important;
    }

    /* ── Header ─────────────────────────────────────────── */
    #laporan-investasi-tabel thead th {
        background: #fff !important;
        color: #696c87 !important;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 0.75rem 0.95rem;
        white-space: nowrap;
        border-top: 0 !important;
        border-bottom: 1px solid #eceef1 !important;
        border-color: #eceef1 !important;
    }

    /* CoF Per Bulan header accent */
    #laporan-investasi-tabel thead th:nth-child(n+14):nth-child(-n+25) {
        background: #fbfcff !important;
        color: #696c87 !important;
    }
    /* Pengembalian header accent */
    #laporan-investasi-tabel thead th:nth-child(n+26) {
        background: #fcfcfd !important;
        color: #696c87 !important;
    }

    /* ── Sticky td background ───────────────────────────── */
    #laporan-investasi-tabel tbody td:nth-child(-n+4) { background: #fff; }
    #laporan-investasi-tabel tbody tr:hover td:nth-child(-n+4) { background: #f5f7ff; }
    #laporan-investasi-tabel tbody tr:hover td { background: #f5f7ff; }

    /* ── Body ───────────────────────────────────────────── */
    #laporan-investasi-tabel tbody td {
        font-size: 0.86rem;
        padding: 0.78rem 0.95rem;
        white-space: nowrap;
        vertical-align: middle;
        border-top: 1px solid #eceef1;
        border-color: #eceef1;
        color: #697a8d;
    }
    #laporan-investasi-tabel tbody tr:nth-child(odd)  { background: #fafbfc; }
    #laporan-investasi-tabel tbody tr:nth-child(even) { background: #fff; }

    /* ── Empty state ─────────────────────────────────────── */
    #laporan-investasi-tabel tbody td[colspan] {
        text-align: center;
        padding: 3rem 1rem;
        color: #a0aec0;
        font-size: 0.875rem;
    }

    /* ── Rappasoft per-page & pagination row ─────────────── */
    .laporan-tabel-wrapper [wire\:id] > div:first-child,
    .laporan-tabel-wrapper .d-flex.justify-content-between {
        padding: 0.6rem 1rem;
        border-top: 1px solid #eceef1;
        background: #fff;
    }
    .laporan-tabel-wrapper .pagination {
        margin-bottom: 0;
    }
    .laporan-tabel-wrapper .pagination .page-link {
        font-size: 0.84rem;
        padding: 0.3rem 0.6rem;
        color: #697a8d;
        border-color: #d9dee3;
    }
    .laporan-tabel-wrapper .pagination .page-item.active .page-link {
        background-color: #03c3ec;
        border-color: #03c3ec;
        color: #fff;
    }
    .laporan-tabel-wrapper .pagination .page-link:hover {
        background-color: rgba(3, 195, 236, 0.08);
        color: #03c3ec;
    }

    .laporan-tabel-wrapper .form-select,
    .laporan-tabel-wrapper .form-control {
        border-color: #d9dee3;
        color: #697a8d;
        font-size: 0.95rem;
    }

    .laporan-tabel-wrapper .form-select:focus,
    .laporan-tabel-wrapper .form-control:focus {
        border-color: rgba(3, 195, 236, 0.45);
        box-shadow: 0 0 0 0.15rem rgba(3, 195, 236, 0.18);
    }

    .laporan-action-wrap .btn-success {
        background-color: #03c3ec;
        border-color: #03c3ec;
    }

    .laporan-action-wrap .btn-success:hover,
    .laporan-action-wrap .btn-success:focus {
        background-color: #02afd4;
        border-color: #02afd4;
    }

    /* ── Legend dots ────────────────────────────────────── */
    .badge-group {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.82rem; color: #6c757d; font-weight: 500;
    }
    .dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
    .dot-info   { background: #566a7f; }
    .dot-cof    { background: #3a6186; }
    .dot-danger { background: #8b2e2e; }

    @media (max-width: 991.98px) {
        .laporan-action-wrap {
            justify-content: flex-start !important;
        }

        .laporan-action-wrap .btn {
            min-width: 128px;
            justify-content: center;
        }
    }

    @media (max-width: 767.98px) {
        #laporan-investasi-tabel {
            min-width: 2400px;
        }

        .laporan-action-wrap .btn {
            min-width: 120px;
        }
    }
</style>
@endpush
