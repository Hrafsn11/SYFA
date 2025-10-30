@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h4 class="fw-bold">Penagihan Deposito Penerima Dana</h4>
            </div>

            <div class="content-wrapper">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No. Kontrak</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">nominal yang disalurkan</th>
                                    <th class="text-center">tanggal pengiriman dana</th>
                                    <th class="text-center">tanggal pengembalian dana</th>
                                    <th class="text-center">Upload Bukti pengembalian dana</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">KTR-001</td>
                                    <td class="text-center">Malaka</td>
                                    <td class="text-center">Rp. 100.000.000</td>
                                    <td class="text-center">15 Maret 2025</td>
                                    <td class="text-center">15 Maret 2025</td>
                                    <td class="text-center">
                                        <a href="#" class="ti ti-upload"></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-center">KTR-002</td>
                                    <td class="text-center">Techno Infinity</td>
                                    <td class="text-center">Rp. 50.000.000</td>
                                    <td class="text-center">16 Maret 2025</td>
                                    <td class="text-center">16 Maret 2025</td>
                                    <td class="text-center">
                                        <a href="#" class="ti ti-upload"></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tableHistoryKol').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                order: [[0, 'asc']]
            });
        });
    </script>
@endpush

