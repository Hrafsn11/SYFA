@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="fw-bold">Penyaluran Dana Investasi</h4>

            <div class="content-wrapper">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No. Kontrak</th>
                                    <th class="text-center">Nama investor</th>
                                    <th class="text-center">Jumlah investasi</th>
                                    <th class="text-center">Lama investasi</th>
                                    <th class="text-center">Penyaluran dana</th>
                                    <th class="text-center">tanggal disalurkan</th>
                                    <th class="text-center">rencana tanggal penagihan</th>
                                    <th class="text-center">Status pembayaran</th>
                                    <th class="text-center">Bukti transfer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">KTR-001</td>
                                    <td class="text-center">John Doe</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-danger">Belum Lunas</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#">Dokumen.jpg</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-center">KTR-002</td>
                                    <td class="text-center">Jane Doe</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">?</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-success">Lunas</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#">Dokumen.jpg</a>
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
            // Initialize DataTable
            $('#tableHistoryKol').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                order: [[0, 'asc']]
            });
        });
    </script>
@endpush
