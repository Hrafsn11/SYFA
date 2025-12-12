<div class="modal fade" id="modal-pengembalian-invoice" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pengembalian Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="formPengembalianInvoice">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Nominal Yang Dibayarkan <span class="text-danger">*</span></label>
                            <livewire:components.currency-field
                                model_name="nominal_yang_dibayarkan"
                                placeholder="Rp 0"
                                prefix="Rp "
                                :value="null"
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                            <div id="currentFileInfo" class="alert alert-info mb-2" style="display: none;">
                                <small>
                                    <i class="ti ti-file me-1"></i>
                                    File saat ini: <strong id="currentFileName"></strong>
                                    <br>
                                    <em>Upload file baru untuk mengganti</em>
                                </small>
                            </div>
                            <input type="file" class="form-control" id="bukti_pembayaran_invoice" accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, png, jpg)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSavePengembalian">
                    Simpan <i class="ti ti-check ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

