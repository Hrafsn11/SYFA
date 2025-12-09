<div>
    <div class="mb-4">
        <a href="{{ route('master-data.debitur-investor.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-arrow-left me-1"></i> Kembali
        </a>

        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h4 class="fw-bold mb-0">History KOL - {{ $debitur->nama }}</h4>
            
            <div style="width: 200px;" wire:ignore>
                <div class="input-group input-group-md">
                    <input type="number" 
                           class="form-control" 
                           placeholder="Pilih Tahun"
                           id="filterTahun" 
                           wire:model="tahun"
                           min="{{ date('Y') - 10 }}"
                           max="{{ date('Y') + 5 }}"
                           step="1" />
                    <span class="input-group-text cursor-pointer">
                        <i class="ti ti-calendar"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.delay class="text-center py-3">
        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
        <span>Memuat data...</span>
    </div>

    <div wire:loading.remove>
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Perusahaan</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">Nilai KOL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kolHistory as $monthKey => $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $debitur->nama }}</td>
                                <td class="text-center" data-order="{{ $data['bulan_number'] }}">{{ $data['bulan'] }} {{ $tahun }}</td>
                                <td class="text-center">
                                    @php
                                        $kolClass = match($data['kol']) {
                                            0 => 'bg-secondary',  // Belum meminjam
                                            1 => 'bg-success',     // Lancar
                                            2 => 'bg-warning',    // 1-29 hari
                                            3 => 'bg-warning',    // 30-59 hari
                                            4 => 'bg-danger',     // 60-179 hari
                                            5 => 'bg-dark',       // ≥180 hari
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $kolClass }}">{{ $data['kol_label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="ti ti-inbox mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data KOL untuk tahun {{ $tahun }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = null;

            function initDataTable() {
                if (table) {
                    table.destroy();
                }

                table = $('#tableHistoryKol').DataTable({
                    responsive: true,
                    pageLength: 12,
                    lengthMenu: [12, 24, 50, 100],
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Tidak ada data yang tersedia",
                        zeroRecords: "Data tidak ditemukan"
                    },
                    order: [[2, 'asc']], // Sort by bulan (using data-order attribute)
                    columnDefs: [
                        {
                            targets: 0, 
                            orderable: false
                        },
                        {
                            targets: 2, // Bulan column
                            type: 'num', // Sort as number using data-order attribute
                            orderable: true
                        }
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                         '<"table-responsive"t>' +
                         '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                });

                table.on('draw', function() {
                    table.column(0, {search: 'applied', order: 'applied'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                });
            }

            initDataTable();

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        initDataTable();
                    }, 100);
                });
            });

            $('#filterTahun').on('change', function() {
                @this.set('tahun', $(this).val());
            });
        });
    </script>
@endpush
