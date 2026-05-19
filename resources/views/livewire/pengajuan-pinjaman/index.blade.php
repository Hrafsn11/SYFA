<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengajuan Peminjaman</h4>
                @can('peminjaman_dana.add')
                <a wire:navigate.hover href="{{ route('peminjaman.create') }}"
                    class="btn btn-primary d-flex justify-center align-items-center gap-3">
                    <i class="fa-solid fa-plus"></i>
                    Ajukan Peminjaman
                </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body mt-3">
                    <livewire:pengajuan-pinjaman-table />
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', function() {
        initializeEditButtons();

        function initializeEditButtons() {
            document.querySelectorAll('.edit-btn').forEach(btn => {
                const status = btn.getAttribute('data-status');
                const canEdit = ['Draft', 'Validasi Ditolak'].includes(status);

                if (!canEdit) {
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-outline-secondary', 'disabled');
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.5';
                    btn.setAttribute('title', 'Edit tidak tersedia (Status: ' + status + ')');
                }
            });
        }

        // Re-initialize ketika Livewire update (untuk pagination, search, dll)
        Livewire.hook('message.processed', (message, component) => {
            initializeEditButtons();
        });
    });
</script>
@endpush