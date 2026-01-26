<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0">Kertas Kerja Investor SFinlog</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <div style="overflow-x: auto; white-space: nowrap;">
                {{-- Tabel 1: Info Dasar --}}
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; margin-right: 20px; min-width: 1200px; white-space: normal;">
                    <livewire:s-finlog.kertas-kerja-investor-s-finlog-table1 :year="$year" :key="'table1-' . $year" />
                </div>

                {{-- Tabel 2: COF Per Bulan --}}
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; margin-right: 20px; min-width: 800px; white-space: normal;">
                    <livewire:s-finlog.kertas-kerja-investor-s-finlog-table2 :year="$year" :key="'table2-' . $year" />
                </div>

                {{-- Tabel 3: Pengembalian --}}
                <div class="table-container"
                    style="display: inline-block; vertical-align: top; min-width: 600px; white-space: normal;">
                    <livewire:s-finlog.kertas-kerja-investor-s-finlog-table3 :year="$year" :key="'table3-' . $year" />
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @if ($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-edit me-2"></i>Edit {{ $editFieldLabel }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ $editFieldLabel }}</label>

                            @if ($editFieldType === 'date')
                                <input type="date" class="form-control" wire:model="editValue">
                            @elseif ($editFieldType === 'number')
                                <input type="number" class="form-control" wire:model="editValue" step="any">
                            @elseif ($editFieldType === 'select' && $editField === 'status')
                                <select class="form-select" wire:model="editValue">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            @else
                                <input type="text" class="form-control" wire:model="editValue">
                            @endif

                            @error('editValue')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($editFieldType === 'number' && in_array($editField, ['nominal_investasi', 'sisa_pokok', 'sisa_bagi_hasil', 'nominal_bagi_hasil_yang_didapat']))
                            <div class="alert alert-info small mb-0">
                                <i class="ti ti-info-circle me-1"></i>
                                Preview: <strong>Rp {{ number_format((float) $editValue, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditModal">
                            <i class="ti ti-x me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveEdit">
                            <i class="ti ti-device-floppy me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .table-container {
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
            white-space: normal;
            min-width: 250px;
        }

        .table-container table {
            width: auto;
        }

        .table-container table th,
        .table-container table td {
            white-space: nowrap;
        }

        /* Edit icon styling */
        .edit-icon {
            cursor: pointer;
            color: #696cff;
            font-size: 0.85rem;
            margin-left: 4px;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .edit-icon:hover {
            opacity: 1;
        }

        .editable-cell {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        @media (max-width: 768px) {
            .table-container {
                display: block;
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
@endpush