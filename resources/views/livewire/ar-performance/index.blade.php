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

    {{-- Loading State --}}
    <div wire:loading.delay class="text-center py-3">
        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
        <span>Loading...</span>
    </div>

    <div wire:loading.remove>
        @if($arData->isEmpty())
            <div class="alert alert-info" role="alert">
                <i class="ti ti-info-circle me-2"></i>
                Tidak ada data pembayaran untuk periode ini. Data akan muncul setelah debitur melakukan pembayaran.
            </div>
        @else
            {{-- Table Component --}}
            <livewire:ar-performance-table :arData="$arData" :tahun="$tahun" :key="$tahun" />
        @endif
    </div>
    
    {{-- Modal Component --}}
    @include('livewire.ar-performance.partials._modal')
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-alert', (event) => {
            const data = event[0] || event;
            Swal.fire({
                icon: data.type || 'success',
                title: data.type === 'success' ? 'Berhasil!' : 'Perhatian!',
                text: data.message || 'Success!',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: `btn btn-${data.type || 'success'}`
                }
            });
        });
    });

    // Auto-refresh every 60 seconds for real-time updates
    setInterval(() => {
        Livewire.dispatch('refresh-data');
    }, 60000);
</script>
@endpush
