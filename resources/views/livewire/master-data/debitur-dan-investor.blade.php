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

                            <!-- Deposito (Khusus Investor) -->
                            <div class="col-12 mb-3" id="div-deposito" style="display: none;">
                                <label class="form-label">Deposito</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="deposito" id="deposito_reguler"
                                            value="reguler">
                                        <label class="form-check-label" for="deposito_reguler">
                                            Reguler
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="deposito" id="deposito_khusus"
                                            value="khusus">
                                        <label class="form-check-label" for="deposito_khusus">
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
{{-- script  --}}
@endpush