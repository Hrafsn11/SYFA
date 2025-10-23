<!-- Modal Tambah Invoice/Kontrak -->
<div class="modal fade" id="modalPengembalian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Invoice Financing -->
                <div id="formPengembalianInvoice">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Nominal Yang Dibayarkan</label>
                            <input type="text" class="form-control input-rupiah" id="nominal_yang_dibayarkan"
                                placeholder="Masukkan Nominal yang dibayarkan" placeholder="Rp. 0">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="upload">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, png)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSimpanPengembalianInvoice">
                    Simpan Data <i class="ti ti-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>
