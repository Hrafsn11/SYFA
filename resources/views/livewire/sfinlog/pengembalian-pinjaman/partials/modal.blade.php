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
                                <livewire:components.currency-field
                                    model_name="nominal_yang_dibayarkan"
                                    placeholder="Rp 0"
                                    prefix="Rp "
                                    :value="0"
                                    :key="'currency-modal-field'"
                                />
                            </div>
                            @error('nominal_yang_dibayarkan') 
                                <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" 
                                wire:model.live="bukti_pembayaran_invoice" 
                                accept=".pdf,.png,.jpg,.jpeg" 
                                id="file-upload-bukti">
                            <small class="text-muted d-block mt-1">Maximum upload file size: 2 MB. (Type File: pdf, png, jpg)</small>
                            @error('bukti_pembayaran_invoice') 
                                <small class="text-danger d-block">{{ $message }}</small> 
                            @enderror
                            
                            {{-- Enhanced Loading State with Progress --}}
                            <div wire:loading wire:target="bukti_pembayaran_invoice" class="mt-2">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                        role="progressbar" 
                                        style="width: 100%"
                                        id="upload-progress-bar">
                                        <span id="upload-progress-text">Uploading file... 0%</span>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Please wait while the file is being uploaded...</small>
                            </div>
                            
                            {{-- Success State --}}
                            @if ($bukti_pembayaran_invoice)
                                @if (!$errors->first('bukti_pembayaran_invoice'))
                                    <div class="alert alert-success mt-2 py-2" wire:loading.remove wire:target="bukti_pembayaran_invoice">
                                        <i class="ti ti-check-circle me-1"></i> 
                                        File berhasil dipilih: <strong>{{ $bukti_pembayaran_invoice->getClientOriginalName() }}</strong>
                                        <small class="d-block text-muted">Size: {{ number_format($bukti_pembayaran_invoice->getSize() / 1024, 2) }} KB</small>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="addPengembalian" 
                    wire:loading.attr="disabled" 
                    wire:target="addPengembalian"
                    @if(!$bukti_pembayaran_invoice) disabled @endif>
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
<script>
// Initialize modal management - runs on every page load
function initPengembalianModal() {
    console.log('Initializing pengembalian modal...');
    
    const modalElement = document.getElementById('modal-pengembalian-invoice');
    if (!modalElement) {
        console.log('Modal element not found');
        return;
    }
    
    // Reset modal when closed manually
    modalElement.addEventListener('hidden.bs.modal', function () {
        if (window.Livewire) {
            Livewire.find(modalElement.closest('[wire\\:id]')?.getAttribute('wire:id'))?.set('nominal_yang_dibayarkan', 0, false);
            Livewire.find(modalElement.closest('[wire\\:id]')?.getAttribute('wire:id'))?.set('bukti_pembayaran_invoice', null, false);
        }
        const fileInput = document.getElementById('file-upload-bukti');
        if (fileInput) {
            fileInput.value = '';
        }
        console.log('Modal manually closed and reset');
    });
    
    console.log('Modal initialized successfully');
}

// Function to close modal - can be called anytime
function closePengembalianModal() {
    console.log('Attempting to close modal...');
    const modalElement = document.getElementById('modal-pengembalian-invoice');
    
    if (!modalElement) {
        console.log('Modal element not found');
        return;
    }
    
    // Try Bootstrap 5 getInstance first
    let bsModal = bootstrap.Modal.getInstance(modalElement);
    
    if (bsModal) {
        console.log('Closing with existing Bootstrap instance');
        bsModal.hide();
    } else {
        // Create new instance and hide
        console.log('Creating new Bootstrap instance to close');
        bsModal = new bootstrap.Modal(modalElement);
        bsModal.hide();
    }
    
    console.log('Modal close triggered');
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPengembalianModal);
} else {
    initPengembalianModal();
}

// Re-initialize when Livewire loads/navigates
document.addEventListener('livewire:initialized', () => {
    console.log('Livewire initialized - setting up modal listeners');
    initPengembalianModal();
});

// Also handle Livewire navigations
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated - re-initializing modal');
    initPengembalianModal();
});

// Listen to close event - this persists across page loads
if (window.Livewire) {
    Livewire.on('close-pengembalian-modal', () => {
        console.log('Close modal event received');
        setTimeout(closePengembalianModal, 150);
    });
}

// Backup: also listen after Livewire is initialized
document.addEventListener('livewire:initialized', () => {
    Livewire.on('close-pengembalian-modal', () => {
        console.log('Close modal event received (initialized listener)');
        setTimeout(closePengembalianModal, 150);
    });
});

// File upload progress tracking
document.addEventListener('livewire:initialized', () => {
    let uploadStartTime = null;
    
    Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
        if (payload.updates && payload.updates.some(u => u.path && u.path.includes('bukti_pembayaran_invoice'))) {
            uploadStartTime = Date.now();
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                if (progress <= 90) {
                    const progressBar = document.getElementById('upload-progress-bar');
                    const progressText = document.getElementById('upload-progress-text');
                    if (progressBar && progressText) {
                        progressBar.style.width = progress + '%';
                        progressText.textContent = `Uploading file... ${progress}%`;
                    }
                }
            }, 200);
            
            succeed(({ status, response }) => {
                clearInterval(progressInterval);
                const progressBar = document.getElementById('upload-progress-bar');
                const progressText = document.getElementById('upload-progress-text');
                if (progressBar && progressText) {
                    progressBar.style.width = '100%';
                    progressText.textContent = 'Upload complete! 100%';
                }
                const uploadTime = ((Date.now() - uploadStartTime) / 1000).toFixed(2);
                console.log(`File upload completed in ${uploadTime}s`);
            });
            
            fail(({ status, response }) => {
                clearInterval(progressInterval);
                console.error('File upload failed');
            });
        }
    });
});
</script>
@endpush

