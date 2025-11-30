<div>
    <div class="modal fade" id="modalTambahInvoice" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $modal_title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="saveDataInvoice">
                    <div class="modal-body">
                        @switch($jenis_pembiayaan)
                            @case('Invoice Financing')
                                <!-- Form Invoice Financing -->
                                <div class="modal-form-content">
                                    <div class="row mb-3">
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="no_invoice">No. Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" placeholder="Masukkan No. Invoice" wire:model.blur="no_invoice">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nama_client">Nama Client</label>
                                            <input type="text" class="form-control" id="nama_client" placeholder="Masukkan Nama Client" wire:model.blur="nama_client">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nilai_invoice">Nilai Invoice</label>
                                            <livewire:components.currency-field 
                                                model_name="nilai_invoice"
                                                :value="$nilai_invoice"
                                                placeholder="Rp 0"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nilai_pinjaman">Nilai Pinjaman</label>
                                            <livewire:components.currency-field 
                                                model_name="nilai_pinjaman"
                                                :value="$nilai_pinjaman"
                                                placeholder="Rp 0"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nilai_bagi_hasil">Nilai Bagi Hasil</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control input-rupiah" id="nilai_bagi_hasil" placeholder="Rp 0" readonly disabled wire:model.live="nilai_bagi_hasil">
                                                <span class="input-group-text">/Bulan</span>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="invoice_date">Invoice Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="invoice_date"
                                                :value="$invoice_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="due_date">Due Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="due_date"
                                                :value="$due_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_invoice_file">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="dokumen_invoice_file" wire:model.blur="dokumen_invoice_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_kontrak_file">Upload Dokumen Kontrak</label>
                                            <input type="file" class="form-control" id="dokumen_kontrak_file"  wire:model.blur="dokumen_kontrak_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_so_file">Upload Dokumen SO</label>
                                            <input type="file" class="form-control" id="dokumen_so_file" wire:model.blur="dokumen_so_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_bast_file">Upload Dokumen BAST</label>
                                            <input type="file" class="form-control" id="dokumen_bast_file"  wire:model.blur="dokumen_bast_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('PO Financing')
                                <!-- Form PO Financing -->
                                <div class="modal-form-content">
                                    <div class="row mb-3">
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="no_kontrak">No. Kontrak</label>
                                            <input type="text" class="form-control" id="no_kontrak" placeholder="Masukkan No. Kontrak" wire:model.blur="no_kontrak">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nama_client">Nama Client</label>
                                            <input type="text" class="form-control" id="nama_client" placeholder="Masukkan Nama Client" wire:model.blur="nama_client">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nilai_invoice">Nilai Invoice</label>
                                            <livewire:components.currency-field 
                                                model_name="nilai_invoice"
                                                :value="$nilai_invoice"
                                                placeholder="Rp 0"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nilai_pinjaman">Nilai Pinjaman</label>
                                            <livewire:components.currency-field 
                                                model_name="nilai_pinjaman"
                                                :value="$nilai_pinjaman"
                                                placeholder="Rp 0"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nilai_bagi_hasil">Nilai Bagi Hasil</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control input-rupiah non-editable" id="nilai_bagi_hasil" placeholder="Rp 0" readonly disabled>
                                                <span class="input-group-text">/Bulan</span>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="contract_date">Contract Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="contract_date"
                                                :value="$contract_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="due_date">Due Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="due_date"
                                                :value="$due_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_kontrak_file">Upload Dokumen Kontrak  <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="dokumen_kontrak_file" wire:model.blur="dokumen_kontrak_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_so_file">Upload Dokumen SO</label>
                                            <input type="file" class="form-control" id="dokumen_so_file" wire:model.blur="dokumen_so_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_bast_file">Upload Dokumen BAST</label>
                                            <input type="file" class="form-control" id="dokumen_bast_file" wire:model.blur="dokumen_bast_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_lainnya_file">Upload Dokumen Lainnya</label>
                                            <input type="file" class="form-control" id="dokumen_lainnya_file" wire:model.blur="dokumen_lainnya_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('Installment')
                                <!-- Form Installment -->
                                <div class="modal-form-content">
                                    <div class="row mb-3">
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="no_invoice">No. Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" placeholder="Masukkan No. Invoice"  wire:model.blur="no_invoice">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nama_client">Nama Client</label>
                                            <input type="text" class="form-control" id="nama_client" placeholder="Masukkan Nama Client" wire:model.blur="nama_client">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nilai_invoice">Nilai Invoice</label>
                                            <input type="text" class="form-control input-rupiah" id="nilai_invoice" placeholder="Rp 0" wire:model.blur="nilai_invoice">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="invoice_date">Invoice Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="invoice_date"
                                                :value="$invoice_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nama_barang">Nama Barang</label>
                                            <input type="text" class="form-control" id="nama_barang" placeholder="Masukkan Nama Barang" wire:model.blur="nama_barang">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_invoice_file">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="dokumen_invoice_file" wire:model.blur="dokumen_invoice_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_lainnya_file">Upload Dokumen Lainnya</label>
                                            <input type="file" class="form-control" id="dokumen_lainnya_file" wire:model.blur="dokumen_lainnya_file">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('Factoring')
                                <!-- Form Factoring -->
                                <div class="modal-form-content">
                                    <div class="row mb-3">
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="no_kontrak">No. Kontrak</label>
                                            <input type="text" class="form-control" id="no_kontrak" placeholder="Masukkan No. Kontrak"  wire:model.blur="no_kontrak">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nama_client">Nama Client</label>
                                            <input type="text" class="form-control" id="nama_client" placeholder="Masukkan Nama Client" wire:model.blur="nama_client">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label" for="nilai_invoice">Nilai Invoice</label>
                                            <input type="text" class="form-control input-rupiah" id="nilai_invoice" placeholder="Rp 0" wire:model.blur="nilai_invoice">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nilai_pinjaman">Nilai Pinjaman</label>
                                            <input type="text" class="form-control input-rupiah" id="nilai_pinjaman" placeholder="Rp 0" wire:model.blur="nilai_pinjaman">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="nilai_bagi_hasil">Nilai Bagi Hasil</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control input-rupiah non-editable" id="nilai_bagi_hasil" placeholder="Rp 0" readonly disabled>
                                                <span class="input-group-text">/Bulan</span>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="contract_date">Contract Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="contract_date"
                                                :value="$contract_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="due_date">Due Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="due_date"
                                                :value="$due_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_invoice_file">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="dokumen_invoice_file" wire:model.blur="dokumen_invoice_file">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_kontrak_file">Upload Dokumen Kontrak <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="dokumen_kontrak_file" wire:model.blur="dokumen_kontrak_file">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_so_file">Upload Dokumen SO</label>
                                            <input type="file" class="form-control" id="dokumen_so_file" wire:model.blur="dokumen_so_file">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="dokumen_bast_file">Upload Dokumen BAST</label>
                                            <input type="file" class="form-control" id="dokumen_bast_file" wire:model.blur="dokumen_bast_file">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @default
                        @endswitch
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" id="btnHapusDataModal">Hapus Data</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2" wire:loading wire:target="saveDataInvoice"></span>
                            Simpan Data <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        Livewire.on('invoice-saved', (event) => {
            $('.modal').modal('hide');
        });

        Livewire.on('edit-invoice', (event) => {
            const data = event[0];
            
            Object.entries(data).forEach(([key, value]) => {
                if (['invoice_date', 'due_date'].includes(key)) {
                    $('#' + key).datepicker('setDate', value);
                }
            });

            $('.modal').modal('show');
        });
    });
</script>
@endpush