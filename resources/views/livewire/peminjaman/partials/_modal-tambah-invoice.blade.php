<!-- Modal Tambah Invoice/Kontrak -->
<div class="modal fade" id="modalTambahInvoice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Invoice Financing -->
                <div id="formModalInvoiceFinancing" class="modal-form-content">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Invoice</label>
                            <input type="text" class="form-control" id="modal_no_invoice"
                                placeholder="Masukkan No. Invoice">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client</label>
                            <input type="text" class="form-control" id="modal_nama_client"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice</label>
                            <input type="number" class="form-control" id="modal_nilai_invoice"
                                placeholder="Masukkan Nilai Invoice">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nilai Pinjaman</label>
                            <input type="number" class="form-control" id="modal_nilai_pinjaman"
                                placeholder="Masukkan Nilai Pinjaman">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Bagi Hasil</label>
                            <input type="number" class="form-control" id="modal_nilai_bagi_hasil"
                                placeholder="Masukkan Nilai Bagi Hasil">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Invoice Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date" id="modal_invoice_date"
                                    placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date" id="modal_due_date"
                                    placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="modal_dokumen_invoice">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png,
                                rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Kontrak</label>
                            <input type="file" class="form-control" id="modal_dokumen_kontrak">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png,
                                rar, zip)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen SO</label>
                            <input type="file" class="form-control" id="modal_dokumen_so">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png,
                                rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen BAST</label>
                            <input type="file" class="form-control" id="modal_dokumen_bast">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png,
                                rar, zip)</small>
                        </div>
                    </div>
                </div>

                <!-- Form PO Financing -->
                <div id="formModalPOFinancing" class="modal-form-content" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Kontrak</label>
                            <input type="text" class="form-control" id="modal_no_kontrak_po"
                                placeholder="Masukkan No. Kontrak">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client</label>
                            <input type="text" class="form-control" id="modal_nama_client_po"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice</label>
                            <input type="number" class="form-control" id="modal_nilai_invoice_po"
                                placeholder="Masukkan Nilai Invoice">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nilai Pinjaman</label>
                            <input type="number" class="form-control" id="modal_nilai_pinjaman_po"
                                placeholder="Masukkan Nilai Pinjaman">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Bagi Hasil</label>
                            <input type="number" class="form-control" id="modal_nilai_bagi_hasil_po"
                                placeholder="Masukkan Nilai Bagi Hasil">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contract Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date"
                                    id="modal_contract_date_po" placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date"
                                    id="modal_due_date_po" placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Kontrak  <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="modal_dokumen_kontrak_po">
                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen SO</label>
                            <input type="file" class="form-control" id="modal_dokumen_so_po">
                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen BAST</label>
                            <input type="file" class="form-control" id="modal_dokumen_bast_po">
                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Lainnya</label>
                            <input type="file" class="form-control" id="modal_dokumen_lainnya_po">
                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Form Installment -->
                <div id="formModalInstallment" class="modal-form-content" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Invoice</label>
                            <input type="text" class="form-control" id="modal_no_invoice_inst"
                                placeholder="Masukkan No. Invoice">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client</label>
                            <input type="text" class="form-control" id="modal_nama_client_inst"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice</label>
                            <input type="number" class="form-control" id="modal_nilai_invoice_inst"
                                placeholder="Masukkan Nilai Invoice">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Invoice Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date"
                                    id="modal_invoice_date_inst" placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="modal_nama_barang"
                                placeholder="Masukkan Nama Barang">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Invoice <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="modal_dokumen_invoice_inst">
                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Lainnya</label>
                            <input type="file" class="form-control" id="modal_dokumen_lainnya_inst">
                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Form Factoring -->
                <div id="formModalFactoring" class="modal-form-content" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Kontrak</label>
                            <input type="text" class="form-control" id="modal_no_kontrak_fact"
                                placeholder="Masukkan No. Kontrak">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client</label>
                            <input type="text" class="form-control" id="modal_nama_client_fact"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice</label>
                            <input type="number" class="form-control" id="modal_nilai_invoice_fact"
                                placeholder="Masukkan Nilai Invoice">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Nilai Pinjaman</label>
                            <input type="number" class="form-control" id="modal_nilai_pinjaman_fact"
                                placeholder="Masukkan Nilai Pinjaman">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Bagi Hasil</label>
                            <input type="number" class="form-control" id="modal_nilai_bagi_hasil_fact"
                                placeholder="Masukkan Nilai Bagi Hasil">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contract Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date"
                                    id="modal_contract_date_fact" placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start flatpickr-modal-date"
                                    id="modal_due_date_fact" placeholder="DD/MM/YYYY">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="modal_dokumen_invoice_fact">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Kontrak <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="modal_dokumen_kontrak_fact">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen SO</label>
                            <input type="file" class="form-control" id="modal_dokumen_so_fact">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen BAST</label>
                            <input type="file" class="form-control" id="modal_dokumen_bast_fact">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Hapus Data</button>
                <button type="button" class="btn btn-primary" id="btnSimpanInvoice">
                    Simpan Data <i class="ti ti-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>
