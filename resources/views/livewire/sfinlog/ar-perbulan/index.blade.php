<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">AR Perbulan - SFinlog</h4>
                        <div class="d-flex gap-2">
                            {{-- Export Excel --}}
                            <button type="button" class="btn btn-success" wire:click="exportToExcel"
                                wire:loading.attr="disabled" wire:target="exportToExcel">
                                <i class="ti ti-file-spreadsheet me-1"></i>
                                <span wire:loading.remove wire:target="exportToExcel">Export Excel</span>
                                <span wire:loading wire:target="exportToExcel">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Generating...
                                </span>
                            </button>

                            {{-- Export PDF --}}
                            <button type="button" class="btn btn-primary" wire:click="exportToPdf"
                                wire:loading.attr="disabled" wire:target="exportToPdf">
                                <i class="ti ti-file-type-pdf me-1"></i>
                                <span wire:loading.remove wire:target="exportToPdf">Export PDF</span>
                                <span wire:loading wire:target="exportToPdf">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Generating...
                                </span>
                            </button>

                            {{-- Export ZIP (Excel + PDF) --}}
                            {{-- <button type="button" class="btn btn-sm btn-secondary" wire:click="exportToZip"
                                wire:loading.attr="disabled" wire:target="exportToZip">
                                <i class="ti ti-file-zip me-1"></i>
                                <span wire:loading.remove wire:target="exportToZip">Export All (ZIP)</span>
                                <span wire:loading wire:target="exportToZip">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Generating...
                                </span>
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-datatable">
                    <livewire:SFinlog.ar-perbulan-table />
                </div>
            </div>
        </div>
    </div>
</div>
