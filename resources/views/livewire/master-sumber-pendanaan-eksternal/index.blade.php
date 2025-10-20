@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Sumber Pendanaan Eksternal</h4>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahSumberPendanaan">
                        <i class="fa-solid fa-plus"></i>
                        Sumber Pendanaan
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable">
                        <livewire:sumber-pendanaan-eksternal-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
{{-- Modal Tambah/Edit Sumber Pendanaan --}}
<div class="modal fade" id="modalTambahSumberPendanaan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahSumberPendanaanLabel">Tambah Sumber Pendanaan Eksternal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahSumberPendanaan" novalidate>
                    <input type="hidden" id="editSumberId">
                    <div class="mb-3">
                        <label for="nama_instansi" class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_instansi" 
                            placeholder="Masukkan nama instansi" required>
                        <div class="invalid-feedback">Nama instansi wajib diisi</div>
                    </div>
                    <div class="mb-3">
                        <label for="persentase_bagi_hasil" class="form-label">
                            Persentase Bagi Hasil <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="persentase_bagi_hasil"
                            placeholder="Masukkan persentase bagi hasil" required min="0" max="100" step="1">
                        <div class="invalid-feedback">Persentase wajib diisi (0-100)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanSumberPendanaan">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirm Delete --}}
<div class="modal fade" id="modalConfirmDeleteSumber" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus Sumber Pendanaan ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteSumber">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnDeleteSpinner"></span>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const $modal = $('#modalTambahSumberPendanaan');
    const $modalDelete = $('#modalConfirmDeleteSumber');
    const $form = $('#formTambahSumberPendanaan');
    let deleteSumberId = null;

    // Button Tambah - Open empty modal
    $('#btnTambahSumberPendanaan').on('click', function() {
        $form[0].reset();
        $form.removeClass('was-validated');
        $('#editSumberId').val('');
        $('#modalTambahSumberPendanaanLabel').text('Tambah Sumber Pendanaan Eksternal');
        $modal.modal('show');
    });

    // Delegated Edit Handler
    $(document).on('click', '.sumber-edit-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            alert('ID tidak valid'); 
            return; 
        }
        
        $.ajax({
            url: `/master-data/sumber-pendanaan-eksternal/${id}/edit`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const d = response.data;
                    $('#editSumberId').val(d.id_instansi);
                    $('#nama_instansi').val(d.nama_instansi);
                    $('#persentase_bagi_hasil').val(d.persentase_bagi_hasil);
                    $('#modalTambahSumberPendanaanLabel').text('Edit Sumber Pendanaan Eksternal');
                    $modal.modal('show');
                }
            },
            error: function(xhr) {
                alert('Gagal mengambil data');
            }
        });
    });

    // Delegated Delete Handler
    $(document).on('click', '.sumber-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            alert('ID tidak valid'); 
            return; 
        }
        
        deleteSumberId = id;
        $modalDelete.modal('show');
    });

    // Submit form (Add or Edit)
    $('#btnSimpanSumberPendanaan').on('click', function() {
        if (!$form[0].checkValidity()) {
            $form.addClass('was-validated');
            return;
        }

        const id = $('#editSumberId').val();
        const formData = {
            nama_instansi: $('#nama_instansi').val(),
            persentase_bagi_hasil: parseInt($('#persentase_bagi_hasil').val()) || 0,
            _token: '{{ csrf_token() }}'
        };

        const url = id ? `/master-data/sumber-pendanaan-eksternal/${id}` : '/master-data/sumber-pendanaan-eksternal';
        const method = id ? 'PUT' : 'POST';

        $('#btnSimpanSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $modal.modal('hide');
                    Livewire.dispatch('refreshSumberPendanaanEksternalTable');
                }
            },
            error: function(xhr) {
                alert('Gagal menyimpan data');
            },
            complete: function() {
                $('#btnSimpanSpinner').addClass('d-none');
                $('#btnSimpanSumberPendanaan').prop('disabled', false);
            }
        });
    });

    // Confirm Delete
    $('#btnConfirmDeleteSumber').on('click', function() {
        if (!deleteSumberId) return;

        $('#btnDeleteSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: `/master-data/sumber-pendanaan-eksternal/${deleteSumberId}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $modalDelete.modal('hide');
                    Livewire.dispatch('refreshSumberPendanaanEksternalTable');
                    deleteSumberId = null;
                }
            },
            error: function(xhr) {
                alert('Gagal menghapus data');
            },
            complete: function() {
                $('#btnDeleteSpinner').addClass('d-none');
                $('#btnConfirmDeleteSumber').prop('disabled', false);
            }
        });
    });

    // Reset deleteSumberId when modal closed
    $modalDelete.on('hidden.bs.modal', function() {
        deleteSumberId = null;
    });
});
</script>
@endpush

