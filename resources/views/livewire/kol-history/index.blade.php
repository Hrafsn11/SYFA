@extends('layouts.app')

@section('content')
    <div>
        <div class="mb-4">
            <a href="{{ route('master-data.debitur-investor.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>

            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                <h4 class="fw-bold mb-0">History KOL</h4>
                
                <div style="width: 200px;">
                    <div class="input-group input-group-sm">
                        <input type="text" 
                               class="form-control" 
                               placeholder="Pilih Tahun"
                               id="flatpickr-tahun-pencarian" 
                               name="tahun_pencarian" />
                        <span class="input-group-text cursor-pointer">
                            <i class="ti ti-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

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
                        <tr>
                            <td class="text-center">1</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Januari 2025</td>
                            <td class="text-center"><span>KOL 1</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Februari 2025</td>
                            <td class="text-center"><span>KOL 1</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Maret 2025</td>
                            <td class="text-center"><span>KOL 2</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">4</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">April 2025</td>
                            <td class="text-center"><span>KOL 2</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">5</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Mei 2025</td>
                            <td class="text-center"><span>KOL 2</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">6</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Juni 2025</td>
                            <td class="text-center"><span>KOL 3</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">7</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Juli 2025</td>
                            <td class="text-center"><span>KOL 3</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">8</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Agustus 2025</td>
                            <td class="text-center"><span>KOL 3</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">9</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">September 2025</td>
                            <td class="text-center"><span>KOL 4</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">10</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Oktober 2025</td>
                            <td class="text-center"><span>KOL 4</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">11</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">November 2025</td>
                            <td class="text-center"><span>KOL 5</span></td>
                        </tr>
                        <tr>
                            <td class="text-center">12</td>
                            <td>{{ $debitur->nama_debitur }}</td>
                            <td class="text-center">Desember 2025</td>
                            <td class="text-center"><span>KOL 5</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const currentYear = new Date().getFullYear();
            const yearInput = document.getElementById('flatpickr-tahun-pencarian');
            
            yearInput.value = currentYear;
            $('#selected-year').text(currentYear);
            
            $(yearInput).on('focus', function() {
                const years = [];
                for (let i = currentYear - 10; i <= currentYear + 5; i++) {
                    years.push(i);
                }
                
                $(this).attr('type', 'number');
                $(this).attr('min', currentYear - 50);
                $(this).attr('max', currentYear + 10);
                $(this).attr('step', 1);
            });
            
            $(yearInput).on('change', function() {
                const selectedYear = $(this).val();
                $('#selected-year').text(selectedYear);
                console.log('Selected Year:', selectedYear);
            });

            const table = $('#tableHistoryKol').DataTable({
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
                order: [[2, 'asc']], 
                columnDefs: [
                    {
                        targets: 0, 
                        orderable: false
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
        });
    </script>
@endpush
