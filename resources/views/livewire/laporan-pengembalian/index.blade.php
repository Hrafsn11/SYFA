@section('title', 'Laporan Pengembalian')

<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Laporan Pengembalian</h4>
                <a href="{{ route('report-pengembalian.export-pdf') }}" class="btn btn-success" target="_blank">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <livewire:report-pengembalian-table />
                </div>
            </div>
        </div>
    </div>
</div>
