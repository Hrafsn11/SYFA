<div>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Laporan Investasi SFinance</h4>
            <p class="text-muted mb-0 small">Data rekapitulasi investasi, CoF bulanan, dan pengembalian dana</p>
        </div>
    </div>

  
    {{-- Table Card (filter inside) --}}
    <div class="card shadow-sm border-0">
        {{-- Filter Bar --}}
        <div class="card-header bg-white border-bottom py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ti ti-search text-muted"></i>
                        </span>
                        <input type="text"
                            class="form-control border-start-0 ps-0"
                            placeholder="Deposan, no kontrak, status..."
                            wire:model.live.debounce.400ms="globalSearch">
                        @if($globalSearch)
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('globalSearch', '')" title="Hapus pencarian">
                                <i class="ti ti-x"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                    <select class="form-select" wire:model.live="year">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y') + 3; $y >= date('Y') - 10; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                    @if($globalSearch || $year || $filterStatus)
                        <button class="btn btn-outline-secondary btn-sm" wire:click="clearFilters">
                            <i class="ti ti-filter-off me-1"></i>Reset Filter
                        </button>
                    @endif
                    <small class="text-muted">
                    </small>
                </div>
            </div>
        </div>

        {{-- Table --}}
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

    /* ── Sticky td bg ───────────────────────────────────── */
    #laporan-investasi-tabel tbody td:nth-child(-n+4) { background: #fff; }
    #laporan-investasi-tabel tbody tr:hover td:nth-child(-n+4) { background: #f0f5ff; }
    #laporan-investasi-tabel tbody tr:hover td { background: #f0f5ff; }

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
