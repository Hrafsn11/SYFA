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
                        <span id="btnTambahText">Debitur</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <ul class="nav nav-pills mb-4" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                    data-bs-target="#tab-debitur" aria-controls="tab-debitur" aria-selected="true" data-tab-type="debitur">
                    Debitur
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-investor"
                    aria-controls="tab-investor" aria-selected="false" data-tab-type="investor">
                    Investor
                </button>
            </li>
        </ul>

        {{-- Tabs Content --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="tab-content">
                    {{-- Tab Debitur --}}
                    <div class="tab-pane fade show active" id="tab-debitur" role="tabpanel">
                        <div class="card-datatable">
                            <livewire:debitur-table />
                        </div>
                    </div>

                    {{-- Tab Investor --}}
                    <div class="tab-pane fade" id="tab-investor" role="tabpanel">
                        <div class="card-datatable">
                            <livewire:investor-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    {{-- Modal Tambah/Edit Debitur/Investor --}}
    <div class="modal fade" id="modalTambahDebitur" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahDebiturLabel">Tambah Debitur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahDebitur" novalidate>
                        <input type="hidden" id="editDebiturId">
                        <input type="hidden" id="hiddenFlagging" value="tidak">

                        <div class="row">
                            <!-- Nama Perusahaan -->
                            <div class="col-12 mb-3">
                                <label for="nama_debitur" class="form-label">Nama Perusahaan <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_debitur"
                                    placeholder="Masukkan Nama Perusahaan" required>
                                <div class="invalid-feedback">Nama perusahaan wajib diisi</div>
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

                            <!-- No. Telepon -->
                            <div class="col-md-6 mb-3">
                                <label for="no_telepon" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="no_telepon"
                                    placeholder="Masukkan no telepon">
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger" id="password-required">*</span></label>
                                <input type="password" class="form-control" id="password" 
                                    placeholder="Masukkan password" autocomplete="new-password">
                                <div class="invalid-feedback">Password wajib diisi minimal 8 karakter</div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger" id="password-confirm-required">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                    placeholder="Konfirmasi password" autocomplete="new-password">
                                <div class="invalid-feedback">Password tidak cocok</div>
                            </div>

                            <!-- KOL Perusahaan -->
                            <div class="col-12 mb-3">
                                <label for="id_kol" class="form-label">KOL Perusahaan <span
                                        class="text-danger">*</span></label>
                                <select id="id_kol" class="form-select" required>
                                    <option value="">Pilih KOL</option>
                                    @foreach ($kol as $kolItem)
                                        <option value="{{ $kolItem->id_kol }}">{{ $kolItem->kol }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">KOL perusahaan wajib dipilih</div>
                            </div>

                            <!-- Deposito (Khusus Investor) -->
                            <div class="col-12 mb-3" id="div-deposito" style="display: none;">
                                <label class="form-label">Deposito</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="deposito" id="deposito_reguler" value="reguler">
                                        <label class="form-check-label" for="deposito_reguler">
                                            Reguler
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="deposito" id="deposito_khusus" value="khusus">
                                        <label class="form-check-label" for="deposito_khusus">
                                            Khusus
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Nama Bank -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank</label>
                                <select id="nama_bank" class="form-select">
                                    <option value="">Pilih Bank</option>
                                    @foreach ($banks as $b)
                                        <option value="{{ $b }}">{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- No. Rekening -->
                            <div class="col-md-6 mb-3">
                                <label for="no_rek" class="form-label">No. Rekening</label>
                                <input type="text" class="form-control" id="no_rek"
                                    placeholder="Masukkan no rekening">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapusDataModal" style="display: none;">
                        <i class="ti ti-trash me-1"></i> Hapus Data
                    </button>
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
                    <p class="mb-0" id="deleteConfirmText">Apakah Anda yakin ingin menghapus data ini? Tindakan ini
                        tidak dapat dibatalkan.</p>
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

    {{-- Modal Confirm Toggle Status --}}
    <div class="modal fade" id="modalConfirmToggleStatus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Ubah Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="toggleStatusText">Apakah Anda yakin ingin mengubah status data ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" id="btnConfirmToggleStatus">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="btnToggleSpinner"></span>
                        Ya, Ubah Status
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
            const $modalToggleStatus = $('#modalConfirmToggleStatus');
            const $form = $('#formTambahDebitur');
            let deleteDebiturId = null;
            let toggleStatusData = {
                id: null,
                currentStatus: null
            };
            let currentTabType = 'debitur';

            $('#id_kol').select2({
                dropdownParent: $modal,
                placeholder: 'Pilih KOL Perusahaan',
                allowClear: true,
                width: '100%'
            });

            $('#nama_bank').select2({
                dropdownParent: $modal,
                placeholder: 'Pilih Bank',
                allowClear: true,
                width: '100%'
            });

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                currentTabType = $(e.target).data('tab-type');
                updateUIBasedOnTab();
            });

            function updateUIBasedOnTab() {
                if (currentTabType === 'investor') {
                    $('#btnTambahText').text('Investor');
                    $('#hiddenFlagging').val('ya');
                    $('#div-deposito').show(); 
                } else {
                    $('#btnTambahText').text('Debitur');
                    $('#hiddenFlagging').val('tidak');
                    $('#div-deposito').hide(); 
                }
            }

            $('#btnTambahDebitur').on('click', function() {
                $form[0].reset();
                $form.removeClass('was-validated');
                $('#editDebiturId').val('');
                $('#nama_bank').val('').trigger('change');
                $('#btnHapusDataModal').hide();
                
                const defaultKolId = @json($kol->firstWhere('kol', 0)?->id_kol ?? $kol->first()?->id_kol);
                $('#id_kol').val(defaultKolId).trigger('change');
                $('#id_kol').prop('disabled', true);
                $('#kol-info-text').show();
                
                $('#password').prop('required', true);
                $('#password_confirmation').prop('required', true);
                $('#password-required').show();
                $('#password-confirm-required').show();

                if (currentTabType === 'investor') {
                    $('#modalTambahDebiturLabel').text('Tambah Investor');
                    $('#hiddenFlagging').val('ya');
                    $('#div-deposito').show(); 
                } else {
                    $('#modalTambahDebiturLabel').text('Tambah Debitur');
                    $('#hiddenFlagging').val('tidak');
                    $('#div-deposito').hide(); 
                    $('input[name="deposito"]').prop('checked', false);
                }

                $modal.modal('show');
            });

            $(document).on('click', '.debitur-edit-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                if (!id) return;

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
                            $('#no_telepon').val(d.no_telepon);
                            
                            $('input[name="deposito"]').prop('checked', false);
                            if (d.deposito) {
                                $(`input[name="deposito"][value="${d.deposito}"]`).prop('checked', true);
                            }
                            
                            $('#id_kol').prop('disabled', false);
                            $('#id_kol').val(d.id_kol).trigger('change');
                            $('#kol-info-text').hide();
                            
                            $('#nama_bank').val(d.nama_bank).trigger('change');
                            $('#no_rek').val(d.no_rek);
                            $('#hiddenFlagging').val(d.flagging);
                            
                            $('#password').val('');
                            $('#password_confirmation').val('');
                            $('#password').prop('required', false);
                            $('#password_confirmation').prop('required', false);
                            $('#password-required').hide();
                            $('#password-confirm-required').hide();

                            const title = d.flagging === 'ya' ? 'Edit Investor' :
                            'Edit Debitur';
                            $('#modalTambahDebiturLabel').text(title);

                            $('#btnHapusDataModal').show();
                    
                            if (d.flagging === 'ya') {
                                $('#div-deposito').show();
                            } else {
                                $('#div-deposito').hide();
                            }

                            $modal.modal('show');
                        }
                    }
                });
            });

            $(document).on('click', '.debitur-delete-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (!id) return;

                deleteDebiturId = id;
                $modalDelete.modal('show');
            });

            $('#btnHapusDataModal').on('click', function(e) {
                e.preventDefault();
                const id = $('#editDebiturId').val();
                if (!id) return;

                deleteDebiturId = id;
                $modal.modal('hide'); 
                $modalDelete.modal('show'); 
            });

            $(document).on('click', '.debitur-toggle-status-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const currentStatus = $(this).data('status');

                if (!id) return;

                toggleStatusData = {
                    id: id,
                    currentStatus: currentStatus
                };

                const actionText = currentStatus === 'active' ? 'menonaktifkan' : 'mengaktifkan';
                $('#toggleStatusText').text(`Apakah Anda yakin ingin ${actionText} data ini?`);

                $modalToggleStatus.modal('show');
            });

            $('#btnConfirmToggleStatus').on('click', function() {
                const {
                    id,
                    currentStatus
                } = toggleStatusData;

                if (!id) return;

                $('#btnToggleSpinner').removeClass('d-none');
                $(this).prop('disabled', true);

                $.ajax({
                    url: `/master-data/debitur-investor/${id}/toggle-status`,
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $modalToggleStatus.modal('hide');

                            Livewire.dispatch('refreshDebiturTable');
                            Livewire.dispatch('refreshInvestorTable');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Gagal mengubah status. Silakan coba lagi.');
                    },
                    complete: function() {
                        $('#btnToggleSpinner').addClass('d-none');
                        $('#btnConfirmToggleStatus').prop('disabled', false);
                    }
                });
            });

            $('#btnSimpanDebitur').on('click', function() {
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();
                
                if (password || passwordConfirm) {
                    if (password.length < 8) {
                        $('#password').addClass('is-invalid');
                        alert('Password minimal 8 karakter');
                        return;
                    }
                    
                    if (password !== passwordConfirm) {
                        $('#password_confirmation').addClass('is-invalid');
                        alert('Password dan Konfirmasi Password tidak cocok');
                        return;
                    }
                }
                
                $('#id_kol').prop('disabled', false);
                
                if (!$form[0].checkValidity()) {
                    $form.addClass('was-validated');
                    return;
                }

                const id = $('#editDebiturId').val();
                const flagging = $('#hiddenFlagging').val();

                const formData = {
                    id_kol: $('#id_kol').val(),
                    nama_debitur: $('#nama_debitur').val(),
                    nama_ceo: $('#nama_ceo').val() || null,
                    alamat: $('#alamat').val() || null,
                    email: $('#email').val() || null,
                    no_telepon: $('#no_telepon').val() || null,
                    status: 'active', 
                    deposito: (flagging === 'ya') ? $('input[name="deposito"]:checked').val() || null : null, // ✅ Get radio value
                    nama_bank: $('#nama_bank').val() || null,
                    no_rek: $('#no_rek').val() || null,
                    flagging: flagging,
                    _token: '{{ csrf_token() }}'
                };
                
                if (password) {
                    formData.password = password;
                    formData.password_confirmation = passwordConfirm;
                }

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

                            if (formData.flagging === 'ya') {
                                Livewire.dispatch('refreshInvestorTable');
                            } else {
                                Livewire.dispatch('refreshDebiturTable');
                            }
                        }
                    },
                    complete: function() {
                        $('#btnSimpanSpinner').addClass('d-none');
                        $('#btnSimpanDebitur').prop('disabled', false);
                    }
                });
            });

            $('#btnConfirmDeleteDebitur').on('click', function() {
                if (!deleteDebiturId) return;

                $('#btnDeleteSpinner').removeClass('d-none');
                $(this).prop('disabled', true);

                $.ajax({
                    url: `/master-data/debitur-investor/${deleteDebiturId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $modalDelete.modal('hide');

                            Livewire.dispatch('refreshDebiturTable');
                            Livewire.dispatch('refreshInvestorTable');

                            deleteDebiturId = null;
                        }
                    },
                    complete: function() {
                        $('#btnDeleteSpinner').addClass('d-none');
                        $('#btnConfirmDeleteDebitur').prop('disabled', false);
                    }
                });
            });

            $modalDelete.on('hidden.bs.modal', function() {
                deleteDebiturId = null;
            });
        });
    </script>
@endpush
