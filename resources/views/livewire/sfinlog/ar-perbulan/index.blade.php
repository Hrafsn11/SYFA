<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">AR Perbulan - SFinlog</h4>
                        <div class="d-flex gap-2">
                            <div style="width: 200px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Pilih Bulan & Tahun" 
                                    id="month-filter-sfinlog" readonly />
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="clear-filter-sfinlog" title="Reset Filter">
                                <i class="ti ti-x"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success" wire:click="exportToZip" wire:loading.attr="disabled">
                                <i class="ti ti-file-zip"></i> 
                                <span wire:loading.remove>Export (Excel + PDF)</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Generating...
                                </span>
                            </button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthFilter = document.getElementById('month-filter-sfinlog');
        const clearBtn = document.getElementById('clear-filter-sfinlog');

        if (monthFilter) {
            const picker = $(monthFilter).datepicker({
                format: 'MM yyyy',
                startView: 'months',
                minViewMode: 'months',
                autoclose: true,
                language: 'id',
                todayHighlight: true,
                orientation: 'bottom auto'
            });

            picker.on('changeDate', function(e) {
                if (e.date) {
                    const year = e.date.getFullYear();
                    const month = String(e.date.getMonth() + 1).padStart(2, '0');
                    const yearMonth = year + '-' + month;
                    Livewire.dispatch('filterByMonth', { month: yearMonth });
                }
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    $(monthFilter).datepicker('clearDates');
                    monthFilter.value = '';
                    Livewire.dispatch('filterByMonth', { month: '' });
                });
            }
        }
    });
</script>
@endpush

