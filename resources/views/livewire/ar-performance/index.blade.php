@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <h4 class="fw-bold mb-3">AR Performance</h4>

                <div class="mb-3" style="width: 250px;">
                    <div class="input-group input-group-md">
                        <input type="text" class="form-control" placeholder="Select Period" id="flatpickr-tahun-pencarian"
                            name="tahun_pencarian" />
                        <span class="input-group-text cursor-pointer">
                            <i class="ti ti-filter"></i>
                        </span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-history-kol table table-bordered" id="tableHistoryKol"
                            style="white-space: nowrap;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="padding: 12px 20px;">No</th>
                                    <th class="text-center" style="padding: 12px 20px;">Debitur (PT)</th>
                                    <th class="text-center" style="padding: 12px 20px;">Belum Jatuh tempo</th>
                                    <th class="text-center" style="padding: 12px 20px;">By Transaction</th>
                                    <th class="text-center" style="padding: 12px 20px;">DEL (1 - 30)</th>
                                    <th class="text-center" style="padding: 12px 20px;">By Transaction</th>
                                    <th class="text-center" style="padding: 12px 20px;">DEL (31 - 60)</th>
                                    <th class="text-center" style="padding: 12px 20px;">By Transaction</th>
                                    <th class="text-center" style="padding: 12px 20px;">DEL (61 - 90)</th>
                                    <th class="text-center" style="padding: 12px 20px;">By Transaction</th>
                                    <th class="text-center" style="padding: 12px 20px;">NPL (91 - 179)</th>
                                    <th class="text-center" style="padding: 12px 20px;">By Transaction</th>
                                    <th class="text-center" style="padding: 12px 20px;">WriteOff(>180)</th>
                                    <th class="text-center" style="padding: 12px 20px;">By Transaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" style="padding: 12px 20px;">1</td>
                                    <td style="padding: 12px 20px;">PT. ABC</td>
                                    <td class="text-end" style="padding: 12px 20px;">10,000,000</td>
                                    <td class="text-center" style="padding: 12px 20px;">
                                        <a href="javascript:void(0);"
                                            class="text-primary view-transactions text-decoration-none"
                                            data-debitur="PT. ABC" data-category="Belum Jatuh Tempo" data-count="5">
                                            5 <small class="text-muted">Lihat Detail</small>
                                        </a>
                                    </td>
                                    <td class="text-end" style="padding: 12px 20px;">2,000,000</td>
                                    <td class="text-center" style="padding: 12px 20px;">
                                        <a href="javascript:void(0);"
                                            class="text-primary view-transactions text-decoration-none"
                                            data-debitur="PT. ABC" data-category="DEL (1 - 30)" data-count="1">
                                            1 <small class="text-muted">Lihat Detail</small>
                                        </a>
                                    </td>
                                    <td class="text-end" style="padding: 12px 20px;">1,500,000</td>
                                    <td class="text-center" style="padding: 12px 20px;">
                                        <a href="javascript:void(0);"
                                            class="text-primary view-transactions text-decoration-none"
                                            data-debitur="PT. ABC" data-category="DEL (31 - 60)" data-count="1">
                                            1 <small class="text-muted">Lihat Detail</small>
                                        </a>
                                    </td>
                                    <td class="text-end" style="padding: 12px 20px;">1,000,000</td>
                                    <td class="text-center" style="padding: 12px 20px;">
                                        <a href="javascript:void(0);"
                                            class="text-primary view-transactions text-decoration-none"
                                            data-debitur="PT. ABC" data-category="DEL (61 - 90)" data-count="1">
                                            1 <small class="text-muted">Lihat Detail</small>
                                        </a>
                                    </td>
                                    <td class="text-end" style="padding: 12px 20px;">500,000</td>
                                    <td class="text-center" style="padding: 12px 20px;">
                                        <a href="javascript:void(0);"
                                            class="text-primary view-transactions text-decoration-none"
                                            data-debitur="PT. ABC" data-category="NPL (91 - 179)" data-count="1">
                                            1 <small class="text-muted">Lihat Detail</small>
                                        </a>
                                    </td>
                                    <td class="text-end" style="padding: 12px 20px;">200,000</td>
                                    <td class="text-center" style="padding: 12px 20px;">
                                        <a href="javascript:void(0);"
                                            class="text-primary view-transactions text-decoration-none"
                                            data-debitur="PT. ABC" data-category="WriteOff (>180)" data-count="1">
                                            1 <small class="text-muted">Lihat Detail</small>
                                        </a>
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

@push('modals')
    <div class="modal fade" id="modalDetailTransaksi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span id="modalTitle">Detail Transaksi</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="10%">No</th>
                                    <th class="text-center">Nomor Kontrak</th>
                                    <th class="text-center">No Invoice</th>
                                    <th class="text-center">Nilai Invoice</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailTransaksi">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const $modal = $('#modalDetailTransaksi');

            $(document).on('click', '.view-transactions', function(e) {
                e.preventDefault();

                const debitur = $(this).data('debitur');
                const category = $(this).data('category');
                const count = $(this).data('count');

                $('#modalDebitur').text(debitur);
                $('#modalKategori').text(category);
                $('#modalTotal').text(count + ' transaksi');

                $('#tableDetailTransaksi').html(`
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            Memuat data...
                        </td>
                    </tr>
                `);

                $modal.modal('show');

                $.ajax({
                    url: '/ar-performance/transactions',
                    method: 'GET',
                    data: {
                        debitur: debitur,
                        category: category
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let html = '';
                            response.data.forEach((item, index) => {
                                html += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td class="text-center">${item.nomor_kontrak || '-'}</td>
                                        <td class="text-center">${item.no_invoice || '-'}</td>
                                        <td class="text-end">${formatRupiah(item.nilai_invoice)}</td>
                                    </tr>
                                `;
                            });
                            $('#tableDetailTransaksi').html(html);
                        } else {
                            $('#tableDetailTransaksi').html(`
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Tidak ada data transaksi
                                    </td>
                                </tr>
                            `);
                        }
                    },
                    error: function() {
                        $('#tableDetailTransaksi').html(`
                            <tr>
                                <td colspan="4" class="text-center text-danger">
                                    Gagal memuat data transaksi
                                </td>
                            </tr>
                        `);
                    }
                });
            });

            function formatRupiah(angka) {
                if (!angka) return '0';
                return new Intl.NumberFormat('id-ID').format(angka);
            }
        });
    </script>
@endpush
