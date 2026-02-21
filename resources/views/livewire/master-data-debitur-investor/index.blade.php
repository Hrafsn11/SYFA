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
                            <!-- Nama Perusahaan / Nama Investor -->
                            <div class="col-12 mb-3">
                                <label for="nama" class="form-label">
                                    <span id="label-nama">Nama Perusahaan</span> <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama"
                                    placeholder="Masukkan Nama Perusahaan" required>
                                <div class="invalid-feedback">Nama wajib diisi</div>
                            </div>

                            <!-- Jenis Investasi (Khusus Investor) -->
                            <div class="col-12 mb-3" id="div-jenis investasi" style="display: none;">
                                <label class="form-label">Jenis Investasi</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis investasi" id="jenis investasi_reguler"
                                            value="reguler">
                                        <label class="form-check-label" for="jenis investasi_reguler">
                                            Reguler
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis investasi" id="jenis investasi_khusus"
                                            value="khusus">
                                        <label class="form-check-label" for="jenis investasi_khusus">
                                            Khusus
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Nama CEO (Hanya untuk Debitur) -->
                            <div class="col-12 mb-3" id="div-nama-ceo">
                                <label for="nama_ceo" class="form-label">Nama CEO</label>
                                <input type="text" class="form-control" id="nama_ceo" placeholder="Masukkan Nama CEO">
                            </div>

                            <!-- Alamat Perusahaan (Hanya untuk Debitur) -->
                            <div class="col-12 mb-3" id="div-alamat">
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

                            <!-- KOL Perusahaan (Hanya untuk Debitur) -->
                            <div class="col-12 mb-3" id="div-kol">
                                <label for="id_kol" class="form-label">KOL Perusahaan <span
                                        class="text-danger">*</span></label>
                                <select id="id_kol" class="form-select" required>
                                    <option value="">Pilih KOL</option>
                                    @foreach ($kol as $kolItem)
                                        <option value="{{ $kolItem->id_kol }}">{{ $kolItem->kol }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" id="kol-info-text" style="display: none;">
                                    <i class="ti ti-info-circle"></i> Debitur baru otomatis mendapat KOL 0
                                </small>
                                <div class="invalid-feedback">KOL perusahaan wajib dipilih</div>
                            </div>

                            <!-- Upload Tanda Tangan (Hanya untuk Debitur) -->
                            <div class="col-12 mb-3" id="div-tanda-tangan">
                                <label class="form-label">Upload Tanda Tangan Debitur</label>
                                <input type="file" class="form-control" id="tanda_tangan" name="tanda_tangan" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Maximum upload file size: 2 MB. (Type File: jpg, png, jpeg)</small>
                                <div class="invalid-feedback">File tanda tangan tidak valid</div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger"
                                        id="password-required">*</span></label>
                                <input type="password" class="form-control" id="password"
                                    placeholder="Masukkan password" autocomplete="new-password">
                                <div class="invalid-feedback">Password wajib diisi minimal 8 karakter</div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                        class="text-danger" id="password-confirm-required">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    placeholder="Konfirmasi password" autocomplete="new-password">
                                <div class="invalid-feedback">Password tidak cocok</div>
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
                    $('#div-jenis investasi').show();
                    $('#div-nama-ceo').hide();
                    $('#div-alamat').hide();
                    $('#div-kol').hide();
                    $('#div-tanda-tangan').hide(); 
                    $('#label-nama').text('Nama Investor');
                    $('#nama').attr('placeholder', 'Masukkan Nama Investor');
                    $('#id_kol').prop('required', false);
                } else {
                    $('#btnTambahText').text('Debitur');
                    $('#hiddenFlagging').val('tidak');
                    $('#div-jenis investasi').hide();
                    $('#div-nama-ceo').show();
                    $('#div-alamat').show();
                    $('#div-kol').show();
                    $('#div-tanda-tangan').show();
                    $('#label-nama').text('Nama Perusahaan');
                    $('#nama').attr('placeholder', 'Masukkan Nama Perusahaan');
                    $('#id_kol').prop('required', true);
                }
            }

            $('#btnTambahDebitur').on('click', function() {
                $form[0].reset();
                $form.removeClass('was-validated');
                $('#editDebiturId').val('');
                $('#nama_bank').val('').trigger('change');
                $('#btnHapusDataModal').hide();

                $('#password').prop('required', true);
                $('#password_confirmation').prop('required', true);
                $('#password-required').show();
                $('#password-confirm-required').show();
                
                // Reset tanda tangan
                $('#tanda_tangan').val('');

                if (currentTabType === 'investor') {
                    $('#modalTambahDebiturLabel').text('Tambah Investor');
                    $('#hiddenFlagging').val('ya');
                    $('#div-jenis investasi').show();
                    $('#div-nama-ceo').hide();
                    $('#div-alamat').hide();
                    $('#div-kol').hide();
                    $('#div-tanda-tangan').hide();
                    $('#label-nama').text('Nama Investor');
                    $('#nama').attr('placeholder', 'Masukkan Nama Investor');
                    $('#id_kol').prop('required', false);
                    $('#id_kol').val('').trigger('change');
                } else {
                    $('#modalTambahDebiturLabel').text('Tambah Debitur');
                    $('#hiddenFlagging').val('tidak');
                    $('#div-jenis investasi').hide();
                    $('#div-nama-ceo').show();
                    $('#div-alamat').show();
                    $('#div-kol').show();
                    $('#div-tanda-tangan').show(); // Show tanda tangan untuk debitur
                    $('#label-nama').text('Nama Perusahaan');
                    $('#nama').attr('placeholder', 'Masukkan Nama Perusahaan');
                    $('#id_kol').prop('required', true);

                    // Set KOL 0 sebagai default dan disable
                    const defaultKolId = @json($kol->firstWhere('kol', 0)?->id_kol ?? $kol->first()?->id_kol);
                    $('#id_kol').val(defaultKolId).trigger('change');
                    $('#id_kol').prop('disabled', true);
                    $('#kol-info-text').show();

                    $('input[name="jenis investasi"]').prop('checked', false);
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
                            $('#nama').val(d.nama);
                            $('#nama_ceo').val(d.nama_ceo);
                            $('#alamat').val(d.alamat);
                            $('#email').val(d.email);
                            $('#no_telepon').val(d.no_telepon);

                            $('input[name="jenis investasi"]').prop('checked', false);
                            if (d.jenis investasi) {
                                $(`input[name="jenis investasi"][value="${d.jenis investasi}"]`).prop(
                                    'checked', true);
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

                            if (d.flagging === 'ya') {
                                $('#modalTambahDebiturLabel').text('Edit Investor');
                                $('#div-jenis investasi').show();
                                $('#div-nama-ceo').hide();
                                $('#div-alamat').hide();
                                $('#div-kol').hide();
                                $('#div-tanda-tangan').hide(); 
                                $('#label-nama').text('Nama Investor');
                                $('#nama').attr('placeholder', 'Masukkan Nama Investor');
                                $('#id_kol').prop('required', false);
                            } else {
                                $('#modalTambahDebiturLabel').text('Edit Debitur');
                                $('#div-jenis investasi').hide();
                                $('#div-nama-ceo').show();
                                $('#div-alamat').show();
                                $('#div-kol').show();
                                $('#div-tanda-tangan').show(); 
                                $('#label-nama').text('Nama Perusahaan');
                                $('#nama').attr('placeholder', 'Masukkan Nama Perusahaan');
                                $('#id_kol').prop('required', true);
                            }

                            $('#btnHapusDataModal').show();

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

                // Validate file size if uploaded
                const fileInput = $('#tanda_tangan')[0];
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    
                    if (file.size > maxSize) {
                        alert('Ukuran file tanda tangan maksimal 2 MB');
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Tipe file harus JPG, JPEG, atau PNG');
                        return;
                    }
                }

                // Enable KOL sebelum validasi agar bisa disubmit
                $('#id_kol').prop('disabled', false);

                if (!$form[0].checkValidity()) {
                    $form.addClass('was-validated');
                    return;
                }

                const id = $('#editDebiturId').val();
                const flagging = $('#hiddenFlagging').val();

                // Use FormData for file upload
                const formData = new FormData();
                formData.append('id_kol', $('#id_kol').val() || '');
                formData.append('nama', $('#nama').val());
                formData.append('nama_ceo', $('#nama_ceo').val() || '');
                formData.append('alamat', $('#alamat').val() || '');
                formData.append('email', $('#email').val() || '');
                formData.append('no_telepon', $('#no_telepon').val() || '');
                formData.append('status', 'active');
                formData.append('jenis investasi', (flagging === 'ya') ? $('input[name="jenis investasi"]:checked').val() || '' : '');
                formData.append('nama_bank', $('#nama_bank').val() || '');
                formData.append('no_rek', $('#no_rek').val() || '');
                formData.append('flagging', flagging);
                formData.append('_token', '{{ csrf_token() }}');
                
                // Append file if uploaded
                if (fileInput.files.length > 0) {
                    formData.append('tanda_tangan', fileInput.files[0]);
                }
                
                if (password) {
                    formData.append('password', password);
                    formData.append('password_confirmation', passwordConfirm);
                }

                const url = id ? `/master-data/debitur-investor/${id}` : '/master-data/debitur-investor';
                const method = id ? 'POST' : 'POST'; 
                
                if (id) {
                    formData.append('_method', 'PUT');
                }

                $('#btnSimpanSpinner').removeClass('d-none');
                $(this).prop('disabled', true);

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false, // Important for FormData
                    contentType: false, // Important for FormData
                    success: function(response) {
                        if (response.success) {
                            $modal.modal('hide');

                            if (flagging === 'ya') {
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
