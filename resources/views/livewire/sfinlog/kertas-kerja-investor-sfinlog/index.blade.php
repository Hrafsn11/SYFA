<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0">Kertas Kerja Investor SFinlog</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <div style="overflow-x: auto; white-space: nowrap;">
                {{-- Tabel 1: Info Dasar --}}
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; margin-right: 20px; min-width: 1200px; white-space: normal;">
                    <livewire:s-finlog.kertas-kerja-investor-sfinlog-table1 :year="$year" :key="'table1-' . $year" />
                </div>

                {{-- Tabel 2: Pembayaran Per Bulan --}}
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; margin-right: 20px; min-width: 800px; white-space: normal;">
                    <livewire:s-finlog.kertas-kerja-investor-sfinlog-table2 :year="$year" :key="'table2-' . $year" />
                </div>

                {{-- Tabel 3: Pengembalian --}}
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; min-width: 600px; white-space: normal;">
                    <livewire:s-finlog.kertas-kerja-investor-sfinlog-table3 :year="$year" :key="'table3-' . $year" />
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