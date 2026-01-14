@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="fw-bold">Penyaluran Dana Investasi</h4>

            <div class="content-wrapper">
                <div class="card">
                    <div class="card-datatable">
                        @livewire('penyaluran-dana-investasi-table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Kontrak -->
    <div wire:ignore.self class="modal fade" id="detailKontrakModal" tabindex="-1" aria-labelledby="detailKontrakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailKontrakModalLabel">Detail Penyaluran Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailKontrakContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('kontrakDetailLoaded', (data) => {
                const kontrakData = data[0];
                
                let html = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">Informasi Kontrak</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%"><strong>No. Kontrak:</strong></td>
                                            <td>${kontrakData.nomor_kontrak || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Investor:</strong></td>
                                            <td>${kontrakData.nama_investor || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jumlah Investasi:</strong></td>
                                            <td>Rp ${new Intl.NumberFormat('id-ID').format(kontrakData.jumlah_investasi || 0)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lama Investasi:</strong></td>
                                            <td>${kontrakData.lama_investasi || '-'} Bulan</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Riwayat Penyaluran Dana</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th class="text-center">Nominal Disalurkan</th>
                                    <th class="text-center">Tanggal Disalurkan</th>
                                    <th class="text-center">Rencana Tanggal Penagihan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Bukti Transfer</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                let totalNominal = 0;
                kontrakData.details.forEach((item, index) => {
                    totalNominal += parseFloat(item.nominal_yang_disalurkan || 0);
                    const tglDisalurkan = item.tanggal_pengiriman_dana ? new Date(item.tanggal_pengiriman_dana).toLocaleDateString('id-ID') : '-';
                    const tglPenagihan = item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID') : '-';
                    
                    html += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal_yang_disalurkan || 0)}</td>
                            <td class="text-center">${tglDisalurkan}</td>
                            <td class="text-center">${tglPenagihan}</td>
                            <td class="text-center">
                                ${item.bukti_pengembalian 
                                    ? '<span class="badge bg-label-success">Lunas</span>' 
                                    : '<span class="badge bg-label-danger">Belum Lunas</span>'}
                            </td>
                            <td class="text-center">
                                ${item.bukti_pengembalian 
                                    ? `<a href="/storage/${item.bukti_pengembalian}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-file"></i> Lihat</a>` 
                                    : '<span class="text-muted">-</span>'}
                            </td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="1" class="text-end">Total:</th>
                                    <th class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(totalNominal)}</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;

                document.getElementById('detailKontrakContent').innerHTML = html;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('detailKontrakModal'));
                modal.show();
            });
        });
    </script>
    @endpush
@endsection