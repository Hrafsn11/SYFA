<div>
    {{-- Page Header --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Laporan Investasi SFinance</h4>
            <p class="text-muted mb-0 small">Data rekapitulasi investasi, CoF bulanan, dan pengembalian dana</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm border-0">

        {{-- Controls Bar --}}
        <div class="card-header bg-white border-bottom py-3">
            <div class="row g-2 align-items-end">

                {{-- Search --}}
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.04em;font-size:.7rem;">
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
                    <label class="form-label small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.04em;font-size:.7rem;">
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
                    <label class="form-label small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.04em;font-size:.7rem;">
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
                <div class="col-12 col-md-4 d-flex align-items-end justify-content-md-end flex-wrap gap-1">
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
        <div class="px-3 pt-2 pb-1 border-bottom bg-white d-flex align-items-center gap-3 flex-wrap">
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
        background: #f8f9fa !important;
        color: #566a7f !important;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 10px 14px;
        white-space: nowrap;
        border-bottom: 2px solid #d9dee3 !important;
        border-color: #e7eaf0 !important;
    }

    /* CoF Per Bulan header accent */
    #laporan-investasi-tabel thead th:nth-child(n+14):nth-child(-n+25) {
        background: #eef3f8 !important;
        color: #3a6186 !important;
    }
    /* Pengembalian header accent */
    #laporan-investasi-tabel thead th:nth-child(n+26) {
        background: #fdf2f2 !important;
        color: #8b2e2e !important;
    }

    /* ── Sticky td background ───────────────────────────── */
    #laporan-investasi-tabel tbody td:nth-child(-n+4) { background: #fff; }
    #laporan-investasi-tabel tbody tr:hover td:nth-child(-n+4) { background: #eef2ff; }
    #laporan-investasi-tabel tbody tr:hover td { background: #eef2ff; }

    /* ── Body ───────────────────────────────────────────── */
    #laporan-investasi-tabel tbody td {
        font-size: 0.82rem;
        padding: 8px 14px;
        white-space: nowrap;
        vertical-align: middle;
        border-color: #e7eaf0;
        color: #444;
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
        border-top: 1px solid #e7eaf0;
        background: #fff;
    }
    .laporan-tabel-wrapper .pagination {
        margin-bottom: 0;
    }
    .laporan-tabel-wrapper .pagination .page-link {
        font-size: 0.82rem;
        padding: 0.3rem 0.6rem;
        color: #566a7f;
        border-color: #d9dee3;
    }
    .laporan-tabel-wrapper .pagination .page-item.active .page-link {
        background-color: #696cff;
        border-color: #696cff;
        color: #fff;
    }
    .laporan-tabel-wrapper .pagination .page-link:hover {
        background-color: #f0f1ff;
        color: #696cff;
    }

    /* ── Legend dots ────────────────────────────────────── */
    .badge-group {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.78rem; color: #6c757d; font-weight: 500;
    }
    .dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
    .dot-info   { background: #566a7f; }
    .dot-cof    { background: #3a6186; }
    .dot-danger { background: #8b2e2e; }
</style>
@endpush
