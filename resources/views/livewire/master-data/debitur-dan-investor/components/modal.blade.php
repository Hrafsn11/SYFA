{{-- Modal Tambah/Edit Debitur/Investor --}}
<div class="modal fade" id="modalTambahDebitur" wire:ignore>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDebiturLabel">Tambah Debitur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahDebitur" wire:submit="{{ $urlAction['store_master_debitur_dan_investor'] }}">
                <input type="hidden" id="hiddenFlagging" wire:model.blur="flagging">
                <div class="modal-body">
                    <div class="row">
                        <!-- Nama Perusahaan / Nama Investor -->
                        <div class="col-12 mb-3 form-group">
                            <label for="nama" class="form-label">
                                <span id="label-nama">Nama Perusahaan</span> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama"
                                placeholder="Masukkan Nama Perusahaan" wire:model.blur="nama">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3 form-group debitur-section d-none">
                            <label for="kode_perusahaan" class="form-label">
                                Kode Perusahaan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="kode_perusahaan"
                                placeholder="Masukan Kode Perusahaan" wire:model.blur="kode_perusahaan" minlength="2" maxlength="4" style="text-transform: uppercase;">
                            <small class="text-muted">
                               Minimal 2 karakter, maksimal 4 karakter (huruf dan angka, contoh: TECH)
                            </small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Deposito (Khusus Investor) -->
                        <div class="col-12 mb-3 form-group investor-section d-none">
                            <label class="form-label">Deposito</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="deposito_reguler" value="reguler"
                                        wire:model.blur="deposito">
                                    <label class="form-check-label" for="deposito_reguler">
                                        Reguler
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="deposito_khusus" value="khusus"
                                        wire:model.blur="deposito">
                                    <label class="form-check-label" for="deposito_khusus">
                                        Khusus
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Flagging Investor (Khusus Investor) -->
                        <div class="col-12 mb-3 form-group investor-section d-none">
                            <label class="form-label">Tipe Investor <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('flagging_investor') is-invalid @enderror" type="radio" id="flagging_investor_sfinance" value="sfinance"
                                        wire:model.blur="flagging_investor" name="flagging_investor">
                                    <label class="form-check-label" for="flagging_investor_sfinance">
                                        SFinance
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('flagging_investor') is-invalid @enderror" type="radio" id="flagging_investor_sfinlog" value="sfinlog"
                                        wire:model.blur="flagging_investor" name="flagging_investor">
                                    <label class="form-check-label" for="flagging_investor_sfinlog">
                                        SFinlog
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('flagging_investor') is-invalid @enderror" type="radio" id="flagging_investor_both" value="sfinance,sfinlog"
                                        wire:model.blur="flagging_investor" name="flagging_investor">
                                    <label class="form-check-label" for="flagging_investor_both">
                                        Keduanya
                                    </label>
                                </div>
                            </div>
                            @error('flagging_investor')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama CEO (Hanya untuk Debitur) -->
                        <div class="col-12 mb-3 form-group debitur-section d-none">
                            <label for="nama_ceo" class="form-label">Nama CEO <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_ceo" placeholder="Masukkan Nama CEO"
                                wire:model.blur="nama_ceo">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Alamat (Untuk Debitur dan Investor) -->
                        <div class="col-12 mb-3 form-group">
                            <label for="alamat" class="form-label">
                                <span id="label-alamat">Alamat</span> <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="alamat" rows="2" placeholder="Masukkan alamat" wire:model.blur="alamat"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" placeholder="Masukkan email"
                                wire:model.blur="email">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- No. Telepon -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="no_telepon" class="form-label">No. Telepon <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_telepon" placeholder="Masukkan no telepon"
                                wire:model.blur="no_telepon">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Nama Bank -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="nama_bank" class="form-label">Nama Bank <span
                                    class="text-danger">*</span></label>
                            <select id="nama_bank" class="form-select select2" wire:model.blur="nama_bank">
                                <option value="">Pilih Bank</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- No. Rekening -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="no_rek" class="form-label">No. Rekening <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_rek"
                                placeholder="Masukkan no rekening" wire:model.blur="no_rek">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- NPWP -->
                        <div class="col-md-6 mb-3 form-group debitur-section d-none">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control" id="npwp" placeholder="Masukkan NPWP"
                                wire:model.blur="npwp">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- KOL Perusahaan (Hanya untuk Debitur) -->
                        <div class="col-12 mb-3 form-group debitur-section d-none">
                            <label for="id_kol" class="form-label">KOL Perusahaan <span
                                    class="text-danger">*</span></label>
                            <select id="id_kol" class="form-select select2" wire:model.blur="id_kol">
                                <option value="">Pilih KOL</option>
                                @foreach ($kol as $kolItem)
                                    <option value="{{ $kolItem->id_kol }}">{{ $kolItem->kol }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" id="kol-info-text" style="display: none;">
                                <i class="ti ti-info-circle"></i> Debitur baru otomatis mendapat KOL 0
                            </small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Upload Tanda Tangan (Untuk Debitur dan Investor) -->
                        <div class="col-12 mb-3 form-group">
                            <label class="form-label">
                                <span id="label-ttd">Upload Tanda Tangan</span> <span class="text-danger" id="ttd-required">*</span>
                            </label>
                            <input type="file" class="form-control" id="tanda_tangan"
                                wire:model.blur="tanda_tangan" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: jpg, png,
                                jpeg)</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6 mb-3 form-group password-section">
                            <label for="password" class="form-label" id="password-label">Password <span class="text-danger"
                                    id="password-required">*</span></label>
                            <input type="password" class="form-control" id="password"
                                placeholder="Masukkan password" wire:model.blur="password"
                                autocomplete="new-password">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">
                                Min. 8 karakter, harus ada huruf kapital, huruf kecil, dan angka.
                            </small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 mb-3 form-group password-section">
                            <label for="password_confirmation" class="form-label" id="password-confirm-label">Konfirmasi Password <span
                                    class="text-danger" id="password-confirm-required">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation"
                                wire:model.blur="password_confirmation" placeholder="Konfirmasi password"
                                autocomplete="new-password">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapusDataModal" style="display: none;">
                        <i class="ti ti-trash me-1"></i> Hapus Data
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-2" wire:loading
                            wire:target="saveData"></span>
                        Simpan
                    </button>
                </div>
            </form>
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
