<div>
    {{-- Header & Filter --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">AR Performance</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ar-performance.export-pdf', ['tahun' => $tahun, 'bulan' => $bulan]) }}" 
               class="btn btn-primary btn-sm" 
               target="_blank"
               title="Export ke PDF"
               wire:key="export-pdf-{{ $tahun }}-{{ $bulan ?? 'all' }}"
               id="btnExportPDF">
                <i class="ti ti-file-type-pdf me-1"></i>
                Export PDF
            </a>
            <div wire:ignore>
                <select id="filterBulan" class="form-select" style="width: 150px;">
                    <option value="" {{ $bulan == null || $bulan == '' ? 'selected' : '' }}>Semua Bulan</option>
                    @php
                        $bulanNama = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @for($month = 1; $month <= 12; $month++)
                        <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ $bulanNama[$month] }}
                        </option>
                    @endfor
                </select>
            </div>
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
            <livewire:ar-performance-table :arData="$arData" :tahun="$tahun" :bulan="$bulan" :key="$tahun-$bulan" />
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
</script>
@endpush
