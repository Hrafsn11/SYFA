<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">AR Perbulan</h4>
                        <div class="d-flex gap-2">
                            <div style="width: 200px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Pilih Bulan & Tahun" 
                                    id="month-filter" readonly />
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="clear-filter" title="Reset Filter">
                                <i class="ti ti-x"></i>
                            </button>
                            <button class="btn btn-sm btn-success" type="button" wire:click="exportToExcel" title="Export ke Excel">
                                <i class="ti ti-file-excel"></i> Export
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthFilter = document.getElementById('month-filter');
        const clearBtn = document.getElementById('clear-filter');

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