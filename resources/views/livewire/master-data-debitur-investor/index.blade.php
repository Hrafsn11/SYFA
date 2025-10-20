@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Debitur dan Investor</h4>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahDebitur">
                        <i class="fa-solid fa-plus"></i>
                        Debitur dan Investor
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable">
                        <livewire:debiturinvestor-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
{{-- Modal Tambah/Edit Debitur --}}
<div class="modal fade" id="modalTambahDebitur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDebiturLabel">Tambah Debitur dan Investor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahDebitur" novalidate>
                    <input type="hidden" id="editDebiturId">
                    
                    <div class="row">
                        <!-- Nama Perusahaan -->
                        <div class="col-12 mb-3">
                            <label for="nama_debitur" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_debitur" placeholder="Masukkan Nama Perusahaan" required>
                            <div class="invalid-feedback">Nama perusahaan wajib diisi</div>
                        </div>

                        <!-- Flagging -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Flagging <span class="text-danger">*</span></label>
                            <p class="text-muted small mb-2">Apakah anda termasuk investor?</p>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input name="flagging" class="form-check-input" type="radio" value="ya" id="flaggingYa" required>
                                    <label class="form-check-label" for="flaggingYa">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input name="flagging" class="form-check-input" type="radio" value="tidak" id="flaggingTidak" required>
                                    <label class="form-check-label" for="flaggingTidak">Tidak</label>
                                </div>
                            </div>
                        </div>

                        <!-- Nama CEO -->
                        <div class="col-12 mb-3">
                            <label for="nama_ceo" class="form-label">Nama CEO</label>
                            <input type="text" class="form-control" id="nama_ceo" placeholder="Masukkan Nama CEO">
                        </div>

                        <!-- Alamat Perusahaan -->
                        <div class="col-12 mb-3">
                            <label for="alamat" class="form-label">Alamat Perusahaan</label>
                            <textarea class="form-control" id="alamat" rows="2" placeholder="Masukkan alamat perusahaan"></textarea>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Masukkan email">
                            <div class="invalid-feedback">Email tidak valid</div>
                        </div>

                        <!-- KOL Perusahaan -->
                        <div class="col-md-6 mb-3">
                            <label for="id_kol" class="form-label">KOL Perusahaan <span class="text-danger">*</span></label>
                            <select id="id_kol" class="form-select" required>
                                <option value="">Pilih KOL</option>
                                @foreach ($kol as $kolItem)
                                    <option value="{{ $kolItem->id_kol }}">{{ $kolItem->kol }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">KOL perusahaan wajib dipilih</div>
                        </div>

                        <!-- Nama Bank -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_bank" class="form-label">Nama Bank</label>
                            <input type="text" class="form-control" id="nama_bank" placeholder="Masukkan nama bank">
                        </div>

                        <!-- No. Rekening -->
                        <div class="col-md-6 mb-3">
                            <label for="no_rek" class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" id="no_rek" placeholder="Masukkan no rekening">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanDebitur">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirm Delete --}}
<div class="modal fade" id="modalConfirmDeleteDebitur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus Debitur/Investor ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteDebitur">
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
    const $modal = $('#modalTambahDebitur');
    const $modalDelete = $('#modalConfirmDeleteDebitur');
    const $form = $('#formTambahDebitur');
    let deleteDebiturId = null;

    // Initialize Select2 for KOL dropdown
    $('#id_kol').select2({
        dropdownParent: $modal,
        placeholder: 'Pilih KOL Perusahaan',
        allowClear: true,
        width: '100%'
    });

    // Button Tambah - Open empty modal
    $('#btnTambahDebitur').on('click', function() {
        $form[0].reset();
        $form.removeClass('was-validated');
        $('#editDebiturId').val('');
        $('#id_kol').val('').trigger('change');
        $('#modalTambahDebiturLabel').text('Tambah Debitur dan Investor');
        $modal.modal('show');
    });

    // Delegated Edit Handler
    $(document).on('click', '.debitur-edit-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            alert('ID tidak valid'); 
            return; 
        }
        
        $.ajax({
            url: `/master-data/debitur-investor/${id}/edit`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const d = response.data;
                    $('#editDebiturId').val(d.id_debitur);
                    $('#nama_debitur').val(d.nama_debitur);
                    $('#nama_ceo').val(d.nama_ceo);
                    $('#alamat').val(d.alamat);
                    $('#email').val(d.email);
                    $('#id_kol').val(d.id_kol).trigger('change');
                    $('#nama_bank').val(d.nama_bank);
                    $('#no_rek').val(d.no_rek);
                    
                    // Set flagging radio button
                    if (d.flagging === 'ya') {
                        $('#flaggingYa').prop('checked', true);
                    } else {
                        $('#flaggingTidak').prop('checked', true);
                    }
                    
                    $('#modalTambahDebiturLabel').text('Edit Debitur dan Investor');
                    $modal.modal('show');
                }
            },
            error: function(xhr) {
                alert('Gagal mengambil data');
            }
        });
    });

    // Delegated Delete Handler
    $(document).on('click', '.debitur-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            alert('ID tidak valid'); 
            return; 
        }
        
        deleteDebiturId = id;
        $modalDelete.modal('show');
    });

    // Submit form (Add or Edit)
    $('#btnSimpanDebitur').on('click', function() {
        if (!$form[0].checkValidity()) {
            $form.addClass('was-validated');
            return;
        }

        const id = $('#editDebiturId').val();
        const formData = {
            id_kol: $('#id_kol').val(),
            nama_debitur: $('#nama_debitur').val(),
            nama_ceo: $('#nama_ceo').val() || null,
            alamat: $('#alamat').val() || null,
            email: $('#email').val() || null,
            nama_bank: $('#nama_bank').val() || null,
            no_rek: $('#no_rek').val() || null,
            flagging: $('input[name="flagging"]:checked').val() || 'tidak',
            _token: '{{ csrf_token() }}'
        };

        const url = id ? `/master-data/debitur-investor/${id}` : '/master-data/debitur-investor';
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
                    Livewire.dispatch('refreshDebiturTable');
                }
            },
            error: function(xhr) {
                alert('Gagal menyimpan data');
            },
            complete: function() {
                $('#btnSimpanSpinner').addClass('d-none');
                $('#btnSimpanDebitur').prop('disabled', false);
            }
        });
    });

    // Confirm Delete
    $('#btnConfirmDeleteDebitur').on('click', function() {
        if (!deleteDebiturId) return;

        $('#btnDeleteSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: `/master-data/debitur-investor/${deleteDebiturId}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $modalDelete.modal('hide');
                    Livewire.dispatch('refreshDebiturTable');
                    deleteDebiturId = null;
                }
            },
            error: function(xhr) {
                alert('Gagal menghapus data');
            },
            complete: function() {
                $('#btnDeleteSpinner').addClass('d-none');
                $('#btnConfirmDeleteDebitur').prop('disabled', false);
            }
        });
    });

    // Reset deleteDebiturId when modal closed
    $modalDelete.on('hidden.bs.modal', function() {
        deleteDebiturId = null;
    });
});
</script>
@endpush