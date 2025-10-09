<div class="modal fade" id="modalTambahInvoice" tabindex="-1" aria-hidden="true" wire:ignore.self>
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
                            <label class="form-label">No. Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_no_invoice"
                                placeholder="Masukkan No. Invoice">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nama_client"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_invoice"
                                placeholder="Masukkan Nilai Kontrak">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nilai Pinjaman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_pinjaman"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Bagi Hasil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_bagi_hasil"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-modal-date" id="invoiceContractDate"
                                placeholder="01/09/2025">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-modal-date" id="invoiceDueDate"
                                placeholder="01/09/2025">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="new_dokumen_invoice">
                            <div wire:loading wire:target="new_dokumen_invoice" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Kontrak</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_kontrak">
                            <div wire:loading wire:target="new_dokumen_kontrak" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen SO</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_so">
                            <div wire:loading wire:target="new_dokumen_so" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen BAST</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_bast">
                            <div wire:loading wire:target="new_dokumen_bast" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                </div>

                <!-- Form PO Financing -->
                <div id="formModalPOFinancing" class="modal-form-content" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Kontrak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_no_invoice"
                                placeholder="Masukkan No. Kontrak">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nama_client"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_invoice"
                                placeholder="Masukkan Nilai Kontrak">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nilai Pinjaman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_pinjaman"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Bagi Hasil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_bagi_hasil"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contract Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control flatpickr-modal-date" id="poContractDate"
                                    placeholder="01/09/2025">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control flatpickr-modal-date" id="poDueDate"
                                    placeholder="01/09/2025">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Kontrak <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="new_dokumen_kontrak">
                            <div wire:loading wire:target="new_dokumen_kontrak" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen SO</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_so">
                            <div wire:loading wire:target="new_dokumen_so" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen BAST</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_bast">
                            <div wire:loading wire:target="new_dokumen_bast" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Lainnya</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_lainnya">
                            <div wire:loading wire:target="new_dokumen_lainnya" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                </div>

                <!-- Form Installment -->
                <div id="formModalInstallment" class="modal-form-content" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_no_invoice"
                                placeholder="Masukkan No. Invoice">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nama_client"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_invoice"
                                placeholder="Masukkan Nilai Invoice">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control flatpickr-modal-date"
                                    id="installmentInvoiceDate" placeholder="01/09/2025">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nama_barang"
                                placeholder="Masukkan Nama Barang">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Invoice <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="new_dokumen_invoice">
                            <div wire:loading wire:target="new_dokumen_invoice" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Lainnya</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_lainnya">
                            <div wire:loading wire:target="new_dokumen_lainnya" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                </div>

                <!-- Form Factoring -->
                <div id="formModalFactoring" class="modal-form-content" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Kontrak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_no_invoice"
                                placeholder="Masukkan No. Kontrak">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nama_client"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_invoice"
                                placeholder="Masukkan Nilai Kontrak">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Nilai Pinjaman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_pinjaman"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Bagi Hasil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="new_nilai_bagi_hasil"
                                placeholder="Masukkan Nama Client">
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contract Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control flatpickr-modal-date"
                                    id="factoringContractDate" placeholder="01/09/2025">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control flatpickr-modal-date" id="factoringDueDate"
                                placeholder="01/09/2025">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Invoice</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_invoice">
                            <div wire:loading wire:target="new_dokumen_invoice" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen Kontrak  <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="new_dokumen_kontrak">
                            <div wire:loading wire:target="new_dokumen_kontrak" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen SO</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_so">
                            <div wire:loading wire:target="new_dokumen_so" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen BAST</label>
                            <input type="file" class="form-control" wire:model="new_dokumen_bast">
                            <div wire:loading wire:target="new_dokumen_bast" class="text-primary">
                                <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                            </div>
                            <small class="text-muted">Maximum upload file size : 2 MB. (Type File : pdf,
                                docx, xls, png, rar, zip)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Hapus
                    Data</button>
                <button type="button" class="btn btn-primary" wire:click="tambahInvoice"
                    data-bs-dismiss="modal">Simpan
                    Data <i class="ti ti-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>
