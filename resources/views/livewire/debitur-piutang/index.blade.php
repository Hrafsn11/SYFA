<div>
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold mb-0">Debitur Piutang</h4>
            <p class="text-muted mb-0">Data piutang debitur dan riwayat pembayaran</p>
        </div>
        <div class="col-md-6 text-end">
            <button wire:click="export" class="btn btn-success" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export" class="d-flex align-items-center">
                    <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                </span>
                <span wire:loading wire:target="export">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Generating...
                </span>
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <div style="overflow-x: auto; white-space: nowrap;">

                <div class="table-container"
                    style="display: inline-block; vertical-align: top; margin-right: 20px; min-width: 1200px; white-space: normal;">
                    <livewire:debitur-piutang-sfinance />
                </div>
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; margin-right: 20px; min-width: 600px; white-space: normal;">
                    <livewire:debitur-piutang-sfinane-table2 />
                </div>

                <div class="table-container" style="display: inline-block; vertical-align: top;">
                    <livewire:debitur-piutang-table3 />
                </div>

            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .table-container {
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
            white-space: normal;
            min-width: 250px;
        }

        .table-container table {
            width: auto;
        }

        .table-container table th,
        .table-container table td {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table-container {
                display: block;
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
@endpush
