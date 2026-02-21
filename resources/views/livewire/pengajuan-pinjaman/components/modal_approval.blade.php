{{-- Modal Approval Components --}}

{{-- Global Modal Styles --}}
<style>
.approval-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
    padding: 2rem;
}

.approval-modal-overlay .modal-dialog {
    margin: 0 auto;
    max-width: 800px;
    width: 100%;
}

.approval-modal-overlay .modal-dialog.modal-lg {
    max-width: 800px;
}

.approval-modal-overlay .modal-dialog:not(.modal-lg) {
    max-width: 500px;
}

.approval-modal-overlay .modal-content {
    position: relative;
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.approval-modal-overlay .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.approval-modal-overlay .modal-header .btn-close {
    padding: 0.5rem;
    margin: 0;
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    border: 0;
    width: 1.5rem;
    height: 1.5rem;
    opacity: 0.5;
    cursor: pointer;
}

.approval-modal-overlay .modal-header .btn-close:hover {
    opacity: 1;
}

.approval-modal-overlay .modal-body {
    padding: 1.5rem;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.approval-modal-overlay .modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
}

[x-cloak] {
    display: none !important;
}

/* Flatpickr z-index fix */
.flatpickr-calendar {
    z-index: 10100 !important;
}

.flatpickr-calendar .flatpickr-day {
    display: inline-flex !important;
    visibility: visible !important;
}
</style>

{{-- ================================================ --}}
{{-- Modal Validasi Dokumen (Step 2 -> 3) --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-validasi-dokumen') { show = true; $nextTick(() => { window.initModalValidasiInputs && window.initModalValidasiInputs(); }); }"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0">
                    <i class="ti ti-file-check me-2"></i>
                    Validasi Dokumen - Konfirmasi Pencairan
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="validasiDokumenSetuju">
                <div class="modal-body">
                    {{-- Deviasi --}}
                    <div class="mb-3">
                        <label class="form-label">Deviasi <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       wire:model="deviasi" value="ya" id="modal_deviasi_ya">
                                <label class="form-check-label" for="modal_deviasi_ya">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       wire:model="deviasi" value="tidak" id="modal_deviasi_tidak">
                                <label class="form-check-label" for="modal_deviasi_tidak">Tidak</label>
                            </div>
                        </div>
                        @error('deviasi') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="row">
                        {{-- Nominal Pengajuan --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal Pengajuan</label>
                            <input type="text" class="form-control" 
                                   value="Rp {{ number_format($nominal_pinjaman ?? 0, 0, ',', '.') }}" disabled>
                        </div>
                        
                        {{-- Nominal Disetujui --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal Disetujui <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="input_nominal_disetujui"
                                   placeholder="Rp 0"
                                   autocomplete="off">
                            <input type="hidden" wire:model="nominal_yang_disetujui" id="hidden_nominal_disetujui">
                            @error('nominal_yang_disetujui') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        {{-- Persentase Bunga --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Persentase Bunga (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" 
                                   wire:model="persentase_bunga"
                                   step="0.01" min="0" max="100"
                                   placeholder="Contoh: 2">
                            @error('persentase_bunga') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- Tanggal Pencairan --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pencairan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="input_tanggal_pencairan"
                                       placeholder="DD/MM/YYYY"
                                       autocomplete="off">
                                <span class="input-group-text">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                            <input type="hidden" wire:model="tanggal_pencairan" id="hidden_tanggal_pencairan">
                            @error('tanggal_pencairan') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    {{-- Catatan --}}
                    <div class="mb-0">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" rows="3" 
                                  wire:model="catatan_approval"
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" 
                            @click="show = false; setTimeout(() => { $dispatch('open-modal', { modal: 'modal-tolak-validasi' }); }, 100)">
                        <i class="ti ti-x me-1"></i> Tolak
                    </button>
                    <button type="submit" class="btn btn-success" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="validasiDokumenSetuju">
                            <i class="ti ti-check me-1"></i> Setujui
                        </span>
                        <span wire:loading wire:target="validasiDokumenSetuju">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Tolak Validasi --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-tolak-validasi') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger mb-0">
                    <i class="ti ti-x-circle me-2"></i>
                    Tolak Validasi Dokumen
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="validasiDokumenTolak">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Anda akan menolak validasi dokumen. Pengajuan akan dikembalikan ke status Draft.
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" 
                                  wire:model="catatan_approval"
                                  placeholder="Jelaskan alasan penolakan (minimal 10 karakter)"
                                  required></textarea>
                        @error('catatan_approval') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">Batal</button>
                    <button type="submit" class="btn btn-danger" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="validasiDokumenTolak">
                            <i class="ti ti-x me-1"></i> Tolak
                        </span>
                        <span wire:loading wire:target="validasiDokumenTolak">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Tolak Debitur --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-tolak-debitur') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger mb-0">
                    <i class="ti ti-x-circle me-2"></i>
                    Tolak Persetujuan Debitur
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="persetujuanDebiturTolak">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Anda akan menolak pengajuan ini sebagai Debitur.
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" 
                                  wire:model="catatan_approval"
                                  placeholder="Jelaskan alasan penolakan"
                                  required></textarea>
                        @error('catatan_approval') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">Batal</button>
                    <button type="submit" class="btn btn-danger" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="persetujuanDebiturTolak">
                            <i class="ti ti-x me-1"></i> Tolak
                        </span>
                        <span wire:loading wire:target="persetujuanDebiturTolak">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Tolak CEO --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-tolak-ceo') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger mb-0">
                    <i class="ti ti-x-circle me-2"></i>
                    Tolak Persetujuan CEO SKI
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="persetujuanCEOTolak">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Anda akan menolak pengajuan ini sebagai CEO SKI.
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" 
                                  wire:model="catatan_approval"
                                  placeholder="Jelaskan alasan penolakan"
                                  required></textarea>
                        @error('catatan_approval') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">Batal</button>
                    <button type="submit" class="btn btn-danger" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="persetujuanCEOTolak">
                            <i class="ti ti-x me-1"></i> Tolak
                        </span>
                        <span wire:loading wire:target="persetujuanCEOTolak">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Tolak Direktur --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-tolak-direktur') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger mb-0">
                    <i class="ti ti-x-circle me-2"></i>
                    Tolak Persetujuan Direktur SKI
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="persetujuanDirekturTolak">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Anda akan menolak pengajuan ini sebagai Direktur SKI.
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" 
                                  wire:model="catatan_approval"
                                  placeholder="Jelaskan alasan penolakan"
                                  required></textarea>
                        @error('catatan_approval') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">Batal</button>
                    <button type="submit" class="btn btn-danger" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="persetujuanDirekturTolak">
                            <i class="ti ti-x me-1"></i> Tolak
                        </span>
                        <span wire:loading wire:target="persetujuanDirekturTolak">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Tolak Konfirmasi Debitur --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-tolak-konfirmasi') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger mb-0">
                    <i class="ti ti-x-circle me-2"></i>
                    Tolak Konfirmasi
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="konfirmasiDebiturTolak">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Anda akan menolak konfirmasi penerimaan dana.
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" 
                                  wire:model="catatan_approval"
                                  placeholder="Jelaskan alasan penolakan"
                                  required></textarea>
                        @error('catatan_approval') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">Batal</button>
                    <button type="submit" class="btn btn-danger" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="konfirmasiDebiturTolak">
                            <i class="ti ti-x me-1"></i> Tolak
                        </span>
                        <span wire:loading wire:target="konfirmasiDebiturTolak">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Persetujuan Debitur (Step 3 -> 4) --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-persetujuan-debitur') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0">
                    <i class="ti ti-user-check me-2"></i>
                    Persetujuan Debitur
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Silakan berikan keputusan Anda terkait pengajuan peminjaman ini.
                </div>
                
                {{-- Info Readonly --}}
                @include('livewire.pengajuan-pinjaman.components._modal-info-readonly')
                
                {{-- Catatan --}}
                <div class="mb-0">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" rows="3" 
                              wire:model="catatan_approval"
                              placeholder="Berikan catatan (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" 
                        @click="show = false; setTimeout(() => { $dispatch('open-modal', { modal: 'modal-tolak-debitur' }); }, 100)">
                    <i class="ti ti-x me-1"></i> Tolak
                </button>
                <button type="button" class="btn btn-success" 
                        wire:click="persetujuanDebiturSetuju"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="persetujuanDebiturSetuju">
                        <i class="ti ti-check me-1"></i> Setujui
                    </span>
                    <span wire:loading wire:target="persetujuanDebiturSetuju">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Persetujuan CEO SKI (Step 4 -> 5) --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-persetujuan-ceo') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0">
                    <i class="ti ti-user-check me-2"></i>
                    Persetujuan CEO SKI
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Silakan berikan keputusan CEO terkait pengajuan peminjaman ini.
                </div>
                
                {{-- Info Readonly --}}
                @include('livewire.pengajuan-pinjaman.components._modal-info-readonly')
                
                {{-- Catatan --}}
                <div class="mb-0">
                    <label class="form-label">Catatan CEO</label>
                    <textarea class="form-control" rows="3" 
                              wire:model="catatan_approval"
                              placeholder="Berikan catatan (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" 
                        @click="show = false; setTimeout(() => { $dispatch('open-modal', { modal: 'modal-tolak-ceo' }); }, 100)">
                    <i class="ti ti-x me-1"></i> Tolak
                </button>
                <button type="button" class="btn btn-success" 
                        wire:click="persetujuanCEOSetuju"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="persetujuanCEOSetuju">
                        <i class="ti ti-check me-1"></i> Setujui
                    </span>
                    <span wire:loading wire:target="persetujuanCEOSetuju">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal Persetujuan Direktur SKI (Step 5 -> 6) --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-persetujuan-direktur') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0">
                    <i class="ti ti-user-check me-2"></i>
                    Persetujuan Direktur SKI
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Silakan berikan keputusan Direktur terkait pengajuan peminjaman ini.
                </div>
                
                {{-- Info Readonly --}}
                @include('livewire.pengajuan-pinjaman.components._modal-info-readonly')
                
                {{-- Catatan --}}
                <div class="mb-0">
                    <label class="form-label">Catatan Direktur</label>
                    <textarea class="form-control" rows="3" 
                              wire:model="catatan_approval"
                              placeholder="Berikan catatan (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" 
                        @click="show = false; setTimeout(() => { $dispatch('open-modal', { modal: 'modal-tolak-direktur' }); }, 100)">
                    <i class="ti ti-x me-1"></i> Tolak
                </button>
                <button type="button" class="btn btn-success" 
                        wire:click="persetujuanDirekturSetuju"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="persetujuanDirekturSetuju">
                        <i class="ti ti-check me-1"></i> Setujui
                    </span>
                    <span wire:loading wire:target="persetujuanDirekturSetuju">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Modal History Detail --}}
{{-- ================================================ --}}
<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail.modal === 'modal-history-detail') show = true"
     @close-all-modals.window="show = false"
     x-show="show"
     x-cloak
     class="approval-modal-overlay"
     @click.self="show = false">
    <div class="modal-dialog" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0">
                    <i class="ti ti-file-info me-2"></i>
                    Detail History
                </h5>
                <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($selectedHistory)
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Status</label>
                        <p class="mb-0 fw-semibold">{{ $selectedHistory['status'] ?? '-' }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small mb-1">Tanggal</label>
                            <p class="mb-0">{{ $selectedHistory['created_at'] ? \Carbon\Carbon::parse($selectedHistory['created_at'])->format('d M Y H:i') : '-' }}</p>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small mb-1">Deviasi</label>
                            <p class="mb-0">{{ $selectedHistory['deviasi'] ?? '-' }}</p>
                        </div>
                    </div>
                    
                    @if($selectedHistory['nominal_yang_disetujui'] ?? null)
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small mb-1">Nominal Disetujui</label>
                            <p class="mb-0 text-success">Rp {{ number_format($selectedHistory['nominal_yang_disetujui'], 0, ',', '.') }}</p>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small mb-1">Tanggal Pencairan</label>
                            <p class="mb-0">{{ $selectedHistory['tanggal_pencairan'] ? \Carbon\Carbon::parse($selectedHistory['tanggal_pencairan'])->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($selectedHistory['persentase_bunga'] ?? null)
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small mb-1">Persentase Bunga</label>
                            <p class="mb-0">{{ $selectedHistory['persentase_bunga'] }}%</p>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small mb-1">Total Bunga</label>
                            <p class="mb-0">Rp {{ number_format($selectedHistory['total_bunga'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Catatan</label>
                        <p class="mb-0">{{ $selectedHistory['catatan'] ?? '-' }}</p>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-muted small mb-1">Diproses Oleh</label>
                        <p class="mb-0">
                            @if($selectedHistory['approved_by'] ?? null)
                                <span class="badge bg-success">{{ $selectedHistory['approved_by'] }}</span>
                            @elseif($selectedHistory['rejected_by'] ?? null)
                                <span class="badge bg-danger">{{ $selectedHistory['rejected_by'] }}</span>
                            @elseif($selectedHistory['submitted_by'] ?? null)
                                <span class="badge bg-primary">{{ $selectedHistory['submitted_by'] }}</span>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Tidak ada data history</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="show = false">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- Initialize Flatpickr and Cleave for Modal --}}
{{-- ================================================ --}}
<script>
// Global function to initialize modal inputs
window.initModalValidasiInputs = function() {
    setTimeout(function() {
        // Initialize Flatpickr for date input
        const dateInput = document.getElementById('input_tanggal_pencairan');
        const hiddenDate = document.getElementById('hidden_tanggal_pencairan');
        
        if (dateInput && typeof flatpickr !== 'undefined') {
            // Destroy existing instance if any
            if (dateInput._flatpickr) {
                dateInput._flatpickr.destroy();
            }
            
            flatpickr(dateInput, {
                dateFormat: 'd/m/Y',
                allowInput: true,
                disableMobile: true,
                onChange: function(selectedDates, dateStr) {
                    if (hiddenDate) {
                        hiddenDate.value = dateStr;
                        hiddenDate.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            });
        }

        // Initialize Cleave.js for currency input
        const currencyInput = document.getElementById('input_nominal_disetujui');
        const hiddenCurrency = document.getElementById('hidden_nominal_disetujui');
        
        if (currencyInput && typeof Cleave !== 'undefined') {
            // Destroy existing instance if any
            if (currencyInput._cleaveInstance) {
                currencyInput._cleaveInstance.destroy();
            }
            
            const cleaveInstance = new Cleave(currencyInput, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: 0,
                prefix: 'Rp ',
                rawValueTrimPrefix: true,
                noImmediatePrefix: false
            });
            
            currencyInput._cleaveInstance = cleaveInstance;
            
            // Sync on input change
            currencyInput.addEventListener('input', function() {
                if (hiddenCurrency) {
                    const rawValue = cleaveInstance.getRawValue();
                    hiddenCurrency.value = rawValue;
                    hiddenCurrency.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
            
            // Also sync on blur
            currencyInput.addEventListener('blur', function() {
                if (hiddenCurrency) {
                    const rawValue = cleaveInstance.getRawValue();
                    hiddenCurrency.value = rawValue;
                    hiddenCurrency.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
    }, 200);
};

// Listen for modal open event
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('open-modal', function(e) {
        if (e.detail && e.detail.modal === 'modal-validasi-dokumen') {
            window.initModalValidasiInputs();
        }
    });
});

// Listen for Livewire closeModal event
document.addEventListener('livewire:init', function() {
    Livewire.on('closeModal', () => {
        // Dispatch Alpine event to close all modals
        window.dispatchEvent(new CustomEvent('close-all-modals'));
    });
    
    Livewire.on('approvalSuccess', (data) => {
        // Close all modals on success
        window.dispatchEvent(new CustomEvent('close-all-modals'));
    });
});
</script>

