<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">AR Perbulan</h4>
                        <div>
                            <button type="button" class="btn btn-primary d-flex justify-center align-items-center gap-3" wire:click="exportToZip"
                                wire:loading.attr="disabled">
                                <i class="ti ti-file-zip"></i>
                                <span wire:loading.remove>Export (Excel + PDF)</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                        aria-hidden="true"></span>
                                    Generating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-datatable">
                    <livewire:ar-perbulan-table />
                </div>
            </div>
        </div>
    </div>
</div>
