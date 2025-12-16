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
                                :value="0"
                            />
                            @error('nominal_yang_dibayarkan') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="bukti_pembayaran_invoice" 
                                accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted">Maximum upload file size: 2 MB. (Type File: pdf, png, jpg)</small>
                            @error('bukti_pembayaran_invoice') 
                                <small class="text-danger d-block">{{ $message }}</small> 
                            @enderror
                            
                            <div wire:loading wire:target="bukti_pembayaran_invoice" class="mt-2">
                                <small class="text-info">
                                    <i class="ti ti-loader"></i> Uploading...
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="addPengembalian" 
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="addPengembalian">
                        Simpan <i class="ti ti-check ms-1"></i>
                    </span>
                    <span wire:loading wire:target="addPengembalian">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

