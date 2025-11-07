@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Menu Pengajuan Peminjaman</h4>
                    <a href="{{ route('ajukanpeminjaman') }}"
                        class="btn btn-primary d-flex justify-center align-items-center gap-3">
                        <i class="fa-solid fa-plus"></i>
                        Ajukan Peminjaman
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <livewire:pengajuan-pinjaman-table />
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Toggle Status -->
    <div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="toggleStatusModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="toggleStatusMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmToggleStatus">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentToggleButton = null;
        let currentRowId = null;
        const toggleModal = new bootstrap.Modal(document.getElementById('toggleStatusModal'));
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
        
        // Event delegation untuk button toggle
        document.addEventListener('click', function(e) {
            if (e.target.closest('.pengajuan-toggle-status-btn')) {
                const button = e.target.closest('.pengajuan-toggle-status-btn');
                const isActive = button.getAttribute('data-active') === 'true';
                const rowId = button.getAttribute('data-id');
                
                currentToggleButton = button;
                currentRowId = rowId;
                
                // Set pesan modal
                const message = isActive 
                    ? 'Apakah Anda yakin ingin menonaktifkan pengajuan ini? Semua tombol aksi akan dinonaktifkan.'
                    : 'Apakah Anda yakin ingin mengaktifkan kembali pengajuan ini?';
                
                document.getElementById('toggleStatusMessage').textContent = message;
                
                // Tampilkan modal
                toggleModal.show();
            }
        });
        
        // Konfirmasi toggle
        document.getElementById('confirmToggleStatus').addEventListener('click', function() {
            if (currentToggleButton && currentRowId) {
                // Call API untuk update database
                fetch(`/peminjaman/${currentRowId}/toggle-active`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI setelah berhasil
                        toggleRowStatus(currentRowId, currentToggleButton, data.is_active);
                        
                        // Tampilkan notifikasi sukses (optional)
                        // alert(data.message);
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengubah status');
                });
                
                toggleModal.hide();
            }
        });
        
        function toggleRowStatus(id, button, newStatus) {
            const row = document.getElementById('action-row-' + id);
            const isActive = newStatus === 'active';
            const actionButtons = row.querySelectorAll('.action-btn');
            const icon = button.querySelector('i');
            
            if (!isActive) {
                // Set to non-active (disabled)
                actionButtons.forEach(btn => {
                    const status = btn.getAttribute('data-status');
                    const canEdit = status ? ['Draft', 'Validasi Ditolak'].includes(status) : true;
                    
                    if (btn.tagName === 'A') {
                        btn.style.pointerEvents = 'none';
                        btn.style.opacity = '0.5';
                        btn.classList.add('disabled');
                        btn.setAttribute('data-originally-disabled', !canEdit);
                    } else {
                        btn.setAttribute('disabled', 'disabled');
                        btn.style.opacity = '0.5';
                    }
                });
                
                // Ubah button menjadi success (aktifkan)
                button.classList.remove('btn-text-danger');
                button.classList.add('btn-text-success');
                icon.className = 'ti ti-circle-check';
                button.setAttribute('data-active', 'false');
                button.setAttribute('title', 'Aktifkan');
            } else {
                // Set to active (enabled)
                actionButtons.forEach(btn => {
                    const originallyDisabled = btn.getAttribute('data-originally-disabled') === 'true';
                    
                    if (btn.tagName === 'A') {
                        if (!originallyDisabled) {
                            btn.style.pointerEvents = '';
                            btn.style.opacity = '';
                            btn.classList.remove('disabled');
                        } else {
                            btn.style.pointerEvents = 'none';
                            btn.style.opacity = '0.5';
                        }
                    } else {
                        if (!originallyDisabled) {
                            btn.removeAttribute('disabled');
                            btn.style.opacity = '';
                        }
                    }
                });
                
                // Ubah button kembali menjadi danger (nonaktifkan)
                button.classList.remove('btn-text-success');
                button.classList.add('btn-text-danger');
                icon.className = 'ti ti-circle-x';
                button.setAttribute('data-active', 'true');
                button.setAttribute('title', 'Nonaktifkan');
            }
        }
        
        // Re-initialize ketika Livewire update (untuk pagination, search, dll)
        Livewire.hook('message.processed', (message, component) => {
            initializeEditButtons();
            initializeActiveStatus();
        });
        
        // Initialize active status dari database saat page load
        function initializeActiveStatus() {
            document.querySelectorAll('.action-btn').forEach(btn => {
                const row = btn.closest('[id^="action-row-"]');
                if (row) {
                    const toggleBtn = row.querySelector('.pengajuan-toggle-status-btn');
                    if (toggleBtn && toggleBtn.getAttribute('data-active') === 'false') {
                        const status = btn.getAttribute('data-status');
                        const canEdit = status ? ['Draft', 'Validasi Ditolak'].includes(status) : true;
                        btn.setAttribute('data-originally-disabled', !canEdit);
                    }
                }
            });
        }
        
        // Jalankan saat page load
        initializeActiveStatus();
    });
</script>
@endpush
