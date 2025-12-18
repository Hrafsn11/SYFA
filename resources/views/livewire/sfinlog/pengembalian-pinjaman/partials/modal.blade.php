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
                            <div wire:ignore>
                                <livewire:components.currency-field model_name="nominal_yang_dibayarkan"
                                    placeholder="Rp 0" prefix="Rp " :value="0" :key="'currency-modal-field'" />
                            </div>
                            @error('nominal_yang_dibayarkan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model.live="bukti_pembayaran_invoice"
                                accept=".pdf,.png,.jpg,.jpeg" id="file-upload-bukti">
                            <small class="text-muted d-block mt-1">Maximum upload file size: 2 MB. (Type File: pdf, png,
                                jpg)</small>
                            @error('bukti_pembayaran_invoice')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror

                            {{-- Native Livewire Loading State --}}
                            <div wire:loading wire:target="bukti_pembayaran_invoice" class="w-100 mt-2">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated w-100"
                                        role="progressbar">
                                        Uploading...
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Sedang mengunggah file...</small>
                            </div>

                            {{-- Success State --}}
                            @if ($bukti_pembayaran_invoice && !$errors->has('bukti_pembayaran_invoice'))
                                <div class="alert alert-success mt-2 py-2" wire:loading.remove
                                    wire:target="bukti_pembayaran_invoice">
                                    <i class="ti ti-check-circle me-1"></i>
                                    File siap: <strong>{{ $bukti_pembayaran_invoice->getClientOriginalName() }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="addPengembalian" wire:loading.attr="disabled"
                    wire:target="addPengembalian" @if (!$bukti_pembayaran_invoice) disabled @endif>
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

@push('scripts')
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const modalElement = document.getElementById('modal-pengembalian-invoice');

                if (modalElement) {
                    // Reset fields when modal is closed
                    modalElement.addEventListener('hidden.bs.modal', () => {
                        @this.set('nominal_yang_dibayarkan', 0, false);
                        @this.set('bukti_pembayaran_invoice', null, false);

                        const fileInput = document.getElementById('file-upload-bukti');
                        if (fileInput) fileInput.value = '';
                    });
                }
            });
        </script>
    @endpush
@endpush
