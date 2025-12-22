<div>
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-0">Debitur Piutang</h4>
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
