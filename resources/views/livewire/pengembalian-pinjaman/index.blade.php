<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Pengembalian Peminjaman</h4>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('pengembalian.export-pdf') }}" class="btn btn-success" target="_blank">
                        <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                    </a>
                    
                    <a wire:navigate.hover href="{{ route('pengembalian.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Pengembalian Pinjaman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <livewire:pengembalian-peminjamanTable />
                </div>
            </div>
        </div>
    </div>
</div>