<div>
    {{-- Header & Filter --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">AR Performance</h4>
        </div>
        <div class="d-flex gap-2">
            <div wire:ignore>
                <select id="filterTahun" class="form-select" style="width: 150px;">
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    @if($arData->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="ti ti-info-circle me-2"></i>
            Tidak ada data pembayaran untuk periode ini. Data akan muncul setelah debitur melakukan pembayaran.
        </div>
    @endif

    {{-- Table Component --}}
    <livewire:ar-performance-table :arData="$arData" :tahun="$tahun" />
    
    {{-- Modal Component --}}
    @include('livewire.ar-performance.partials._modal')
</div>
