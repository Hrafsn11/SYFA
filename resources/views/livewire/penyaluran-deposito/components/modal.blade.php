{{-- Modal Tambah/Edit Penyaluran Deposito --}}
<div class="modal fade" id="modalPenyaluranDeposito">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenyaluranDepositoLabel">Tambah Penyaluran Deposito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPenyaluranDeposito" wire:submit="{{ $urlAction['store_penyaluran_deposito'] }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- No Kontrak -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_pengajuan_investasi" class="form-label">
                                No Kontrak <span class="text-danger">*</span>
                            </label>
                            <select id="id_pengajuan_investasi" class="form-select select2" wire:model="id_pengajuan_investasi" data-placeholder="Pilih No Kontrak">
                                <option value=""></option>
                                @foreach ($pengajuanInvestasi as $item)
                                    <option value="{{ $item->id_pengajuan_investasi }}" 
                                            data-nilai-investasi="{{ $item->jumlah_investasi }}"
                                            data-sisa-dana="{{ $item->sisa_dana ?? 0 }}">
                                        {{ $item->nomor_kontrak }} - {{ $item->nama_investor }} (Sisa Dana: Rp {{ number_format($item->sisa_dana ?? 0, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Nama Perusahaan (Debitur) -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_debitur" class="form-label">
                                Nama Perusahaan <span class="text-danger">*</span>
                            </label>
                            <select id="id_debitur" class="form-select select2" wire:model="id_debitur" data-placeholder="Pilih Nama Perusahaan">
                                <option value=""></option>
                                @foreach ($debitur as $item)
                                    <option value="{{ $item->id_debitur }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Nominal yang Disalurkan -->
                        <div class="col-12 mb-3 form-group">
                            <label for="nominal_yang_disalurkan" class="form-label">
                                Nominal yang Disalurkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_yang_disalurkan" 
                                   placeholder="Ketik angka saja (contoh: 10000000)" 
                                   autocomplete="off">
                            <input type="hidden" id="nominal_raw" wire:model="nominal_yang_disalurkan">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tanggal Pengiriman Dana -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="tanggal_pengiriman_dana" class="form-label">
                                Tanggal Pengiriman Dana <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tanggal_pengiriman_dana" 
                                   placeholder="Pilih tanggal" wire:model="tanggal_pengiriman_dana">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tanggal Pengembalian -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="tanggal_pengembalian" class="form-label">
                                Tanggal Pengembalian <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tanggal_pengembalian" 
                                   placeholder="Pilih tanggal" wire:model="tanggal_pengembalian">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapusData" style="display: none;">
                        <i class="ti ti-trash me-1"></i> Hapus Data
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-2" wire:loading wire:target="saveData"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Upload Bukti Pengembalian --}}
<div class="modal fade" id="modalUploadBukti">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pengembalian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUploadBukti">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bukti_pengembalian" class="form-label">
                            File Bukti <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="bukti_pengembalian" 
                               name="bukti_pengembalian" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="uploadSpinner"></span>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Preview Bukti --}}
<div class="modal fade" id="modalPreviewBukti">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Bukti Pengembalian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="previewContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirm Delete --}}
<div class="modal fade" id="modalConfirmDelete">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="deleteSpinner"></span>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
