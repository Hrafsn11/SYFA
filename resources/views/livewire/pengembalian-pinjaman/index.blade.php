<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengembalian Peminjaman</h4>
                <a wire:navigate.hover href="{{ route('pengembalian.create') }}" class="btn btn-primary d-flex justify-content-center align-items-center gap-3">
                    <i class="fa-solid fa-plus"></i>
                    Pengembalian Pinjaman
                </a>
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