<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengembalian Peminjaman</h4>
                @can('pengembalian_pinjaman_finlog.add')
                    <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-pinjaman.create') }}"
                        class="btn btn-primary d-flex justify-content-center align-items-center gap-3">
                        <i class="fa-solid fa-plus"></i>
                        Pengembalian Pinjaman Finlog
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <livewire:pengembalian-peminjaman-finlog-table />
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
