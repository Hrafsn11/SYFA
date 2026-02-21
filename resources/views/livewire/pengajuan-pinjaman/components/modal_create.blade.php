<div>
    <div class="modal fade" id="modalTambahInvoice" wire:ignore.self>
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
                                                wire:key="invoice_financing_nilai_invoice"
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
                                                wire:key="invoice_financing_nilai_pinjaman"
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
                                                wire:key="invoice_financing_invoice_date"
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
                                                wire:key="invoice_financing_due_date"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_invoice">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                                                @if (isset($dokumen_invoice_current))
                                                    <a href="{{ getFileUrl($dokumen_invoice_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_invoice" wire:model.blur="dokumen_invoice">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_kontrak">Upload Dokumen Kontrak</label>
                                                @if (isset($dokumen_kontrak_current))
                                                    <a href="{{ getFileUrl($dokumen_kontrak_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_kontrak"  wire:model.blur="dokumen_kontrak">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_so">Upload Dokumen SO</label>
                                                @if (isset($dokumen_so_current))
                                                    <a href="{{ getFileUrl($dokumen_so_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_so" wire:model.blur="dokumen_so">
                                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_bast">Upload Dokumen BAST</label>
                                                @if (isset($dokumen_bast_current))
                                                    <a href="{{ getFileUrl($dokumen_bast_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_bast"  wire:model.blur="dokumen_bast">
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
                                                wire:key="po_financing_nilai_invoice"
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
                                                wire:key="po_financing_nilai_pinjaman"
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
                                            <label class="form-label" for="kontrak_date">Contract Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="kontrak_date"
                                                :value="$kontrak_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                                wire:key="po_financing_kontrak_date"
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
                                                wire:key="po_financing_due_date"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_kontrak">Upload Dokumen Kontrak  <span class="text-danger">*</span></label>
                                                @if (isset($dokumen_kontrak_current))
                                                    <a href="{{ getFileUrl($dokumen_kontrak_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_kontrak" wire:model.blur="dokumen_kontrak">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_so">Upload Dokumen SO</label>
                                                @if (isset($dokumen_so_current))
                                                    <a href="{{ getFileUrl($dokumen_so_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_so" wire:model.blur="dokumen_so">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_bast">Upload Dokumen BAST</label>
                                                @if (isset($dokumen_bast_current))
                                                    <a href="{{ getFileUrl($dokumen_bast_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_bast" wire:model.blur="dokumen_bast">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_lainnya">Upload Dokumen Lainnya</label>
                                                @if (isset($dokumen_lainnnya_current))
                                                    <a href="{{ getFileUrl($dokumen_lainnnya_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_lainnya" wire:model.blur="dokumen_lainnya">
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
                                                wire:key="installment_invoice_date"
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
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_invoice">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                                                @if (isset($dokumen_invoice_current))
                                                    <a href="{{ getFileUrl($dokumen_invoice_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_invoice" wire:model.blur="dokumen_invoice">
                                            <small class="text-muted">Maximum upload file size: 2 MB.</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_lainnya">Upload Dokumen Lainnya</label>
                                                @if (isset($dokumen_lainnnya_current))
                                                    <a href="{{ getFileUrl($dokumen_lainnnya_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_lainnya" wire:model.blur="dokumen_lainnya">
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
                                            <label class="form-label" for="kontrak_date">Contract Date</label>
                                            <livewire:components.datepicker-bootstrap 
                                                model_name="kontrak_date"
                                                :value="$kontrak_date"
                                                data_placeholder="DD/MM/YYYY"
                                                format="dd/mm/yyyy"
                                                :autoclose="true"
                                                :today_highlight="true"
                                                wire:key="factoring_kontrak_date"
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
                                                wire:key="factoring_due_date"
                                            />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_invoice">Upload Dokumen Invoice <span class="text-danger">*</span></label>
                                                @if (isset($dokumen_invoice_current))
                                                    <a href="{{ getFileUrl($dokumen_invoice_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_invoice" wire:model.blur="dokumen_invoice">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_kontrak">Upload Dokumen Kontrak <span class="text-danger">*</span></label>
                                                @if (isset($dokumen_kontrak_current))
                                                    <a href="{{ getFileUrl($dokumen_kontrak_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_kontrak" wire:model.blur="dokumen_kontrak">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                            <label class="form-label" for="dokumen_so">Upload Dokumen SO</label>
                                                @if (isset($dokumen_so_current))
                                                    <a href="{{ getFileUrl($dokumen_so_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_so" wire:model.blur="dokumen_so">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="dokumen_bast">Upload Dokumen BAST</label>
                                                @if (isset($dokumen_bast_current))
                                                    <a href="{{ getFileUrl($dokumen_bast_current) }}" target="_blank"><small>Current File</small></a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control" id="dokumen_bast" wire:model.blur="dokumen_bast">
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
            $('.modal').modal('show');
        });

        // Re-initialize datepickers and currency fields when modal is shown
        $('#modalTambahInvoice').on('shown.bs.modal', function () {
            // Re-init datepickers in modal
            $(this).find('[datepicker-livewire]').each(function() {
                const $input = $(this);
                const inputId = $input.attr('id');
                
                if ($input.data('datepicker')) {
                    $input.datepicker('destroy');
                }

                const datepickerOptions = {
                    format: $input.attr('data-format') || 'dd/mm/yyyy',
                    autoclose: $input.attr('data-autoclose') === 'true',
                    todayHighlight: $input.attr('data-today-highlight') === 'true',
                    orientation: 'bottom auto'
                };

                $input.datepicker(datepickerOptions).on('hide', function (e) {
                    e.stopPropagation();
                });

                // Sync value on change
                $input.off('changeDate.modal').on('changeDate.modal', function (e) {
                    const dateValue = $input.val();
                    if (dateValue) {
                        const $wrapper = $input.closest('.input-group');
                        let $componentElement = $wrapper.parents('[wire\\:id]').first();

                        if ($componentElement.length) {
                            const componentId = $componentElement.attr('wire:id');
                            try {
                                const component = Livewire.find(componentId);
                                if (component) {
                                    const modelName = $input.attr('datepicker-livewire') || inputId;
                                    component.set(modelName, dateValue);
                                }
                            } catch (e) {
                                console.warn('Livewire component not found:', e);
                            }
                        }
                    }
                });
            });

            // Re-init Cleave.js for currency fields in modal
            $(this).find('.currency-field-wrapper input').each(function() {
                const input = this;
                const inputId = $(this).attr('id');
                
                if (input._cleaveInstance) {
                    return; // Already initialized
                }

                const cleaveInstance = new Cleave(input, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalScale: 0,
                    prefix: input.dataset.prefix || 'Rp ',
                    rawValueTrimPrefix: true,
                    noImmediatePrefix: false
                });

                input._cleaveInstance = cleaveInstance;
                input.dataset.cleaveInitialized = 'true';
            });
        });
    });
</script>
@endpush