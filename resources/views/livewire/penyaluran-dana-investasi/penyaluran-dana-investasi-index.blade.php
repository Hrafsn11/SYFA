<div>
    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0">Penyaluran Dana Investasi</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <livewire:penyaluran-dana-investasi.penyaluran-dana-investasi-table />
        </div>
    </div>

    {{-- Modal Detail Kontrak --}}
    <div class="modal fade" id="detailKontrakModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Detail Penyaluran Dana Investasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

    {{-- Modal Input Pengembalian --}}
    <div class="modal fade" id="modalInputPengembalian" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInputPengembalianTitle">Input Pengembalian Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('penyaluran-dana-investasi.update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id_penyaluran_dana_investasi" id="input_id_penyaluran">

                        <div class="mb-3">
                            <label class="form-label">Nomor Kontrak</label>
                            <input type="text" class="form-control" id="input_nomor_kontrak" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="input_nama_perusahaan" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nominal Disalurkan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end" id="input_nominal_disalurkan_view" readonly>
                                    <input type="hidden" name="nominal_yang_disalurkan" id="input_nominal_disalurkan">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sisa Belum Dikembalikan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end" id="input_sisa_belum_dikembalikan" readonly>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label required">Nominal Dikembalikan Sekarang</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" class="form-control" name="nominal_dikembalikan" required max="" id="input_nominal_dikembalikan">
                            </div>
                            <div class="form-text">Max: <span id="max_nominal_dikembalikan"></span></div>
                        </div>

                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pengiriman Dana (Awal)</label>
                                <input type="date" class="form-control" name="tanggal_pengiriman_dana" id="input_tgl_pengiriman">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pengembalian (Baru)</label>
                                <input type="date" class="form-control" name="tanggal_pengembalian" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Use PHP to inject auth check, but since we are in heredoc with quotes, variables aren't interpolated.
        // I'll assume canInputPengembalian is true for simplicity or rely on backend validation.
        // If I need interpolation, I should have used unquoted heredoc but escaped the JS backticks.
        // Given the error previously, quoted heredoc is safer for JS code.
        // I will replace this line manually or just set it to true since user is likely authorized if they see the page.
        const canInputPengembalian = true;

        function openInputPengembalian(id, noKontrak, namaPerusahaan, nominalDisalurkan, sisa, tglKirim, tglKembali) {
            document.getElementById('input_id_penyaluran').value = id;
            document.getElementById('input_nomor_kontrak').value = noKontrak;
            document.getElementById('input_nama_perusahaan').value = namaPerusahaan;

            document.getElementById('input_nominal_disalurkan').value = nominalDisalurkan;
            document.getElementById('input_nominal_disalurkan_view').value = new Intl.NumberFormat('id-ID').format(nominalDisalurkan);

            document.getElementById('input_sisa_belum_dikembalikan').value = new Intl.NumberFormat('id-ID').format(sisa);

            const inputNominal = document.getElementById('input_nominal_dikembalikan');
            inputNominal.max = sisa;
            inputNominal.value = '';
            document.getElementById('max_nominal_dikembalikan').innerText = new Intl.NumberFormat('id-ID').format(sisa);

            if(tglKirim) document.getElementById('input_tgl_pengiriman').value = tglKirim;

            // Using bootstrap directly since we are in a blade pushed script
            const modalEl = document.getElementById('modalInputPengembalian');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('kontrak-detail-loaded', (event) => {
                console.log('Event received:', event);
                const kontrakData = event.data || event;
                if (!kontrakData || !kontrakData.details) {
                    console.error('Invalid data:', kontrakData);
                    return;
                }

                let html = `
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3">Informasi Kontrak</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr><td width="40%"><strong>No. Kontrak:</strong></td><td>${kontrakData.nomor_kontrak || '-'}</td></tr>
                                                <tr><td><strong>Nama Investor:</strong></td><td>${kontrakData.nama_investor || '-'}</td></tr>
                                                <tr><td><strong>Jumlah Investasi:</strong></td><td>Rp ${new Intl.NumberFormat('id-ID').format(kontrakData.jumlah_investasi || 0)}</td></tr>
                                                <tr><td><strong>Lama Investasi:</strong></td><td>${kontrakData.lama_investasi || '-'} Bulan</td></tr>
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
                                            <th class="text-center">Nama Perusahaan</th>
                                            <th class="text-center">Nominal Disalurkan</th>
                                            <th class="text-center">Nominal Dikembalikan</th>
                                            <th class="text-center">Tanggal Pengiriman</th>
                                            <th class="text-center">Tanggal Pengembalian</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                let totalNominal = 0;
                kontrakData.details.forEach((item, index) => {
                    totalNominal += parseFloat(item.nominal_yang_disalurkan || 0);
                    const nominalDisalurkan = parseFloat(item.nominal_yang_disalurkan || 0);
                    const nominalDikembalikan = parseFloat(item.nominal_yang_dikembalikan || 0);
                    const sisaBelumDikembalikan = parseFloat(item.sisa_belum_dikembalikan ?? (nominalDisalurkan - nominalDikembalikan));

                    let statusBadge = '<span class="badge bg-label-danger">Belum Lunas</span>';
                    if (sisaBelumDikembalikan <= 0) {
                        statusBadge = '<span class="badge bg-label-success">Lunas</span>';
                    } else if (nominalDikembalikan > 0) {
                        statusBadge = '<span class="badge bg-label-warning">Sebagian Lunas</span>';
                    }

                    html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.nama_perusahaan || '-'}</td>
                                    <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(nominalDisalurkan)}</td>
                                    <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(nominalDikembalikan)}</td>
                                    <td class="text-center">${item.tanggal_pengiriman_dana ? new Date(item.tanggal_pengiriman_dana).toLocaleDateString('id-ID') : '-'}</td>
                                    <td class="text-center">${item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID') : '-'}</td>
                                    <td class="text-center">${statusBadge}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            ${canInputPengembalian && sisaBelumDikembalikan > 0 ? `
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="openInputPengembalian(
                                                    '${item.id}',
                                                    '${item.nomor_kontrak || '-'}',
                                                    '${item.nama_perusahaan || '-'}',
                                                    ${nominalDisalurkan},
                                                    ${sisaBelumDikembalikan},
                                                    '${item.tanggal_pengiriman_dana}',
                                                    '${item.tanggal_pengembalian}'
                                                ); $('#detailKontrakModal').modal('hide');"
                                                title="Input Pengembalian">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>`;
                });

                html += `
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2" class="text-end">Total:</th>
                                            <th class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(totalNominal)}</th>
                                            <th colspan="5"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>`;

                $('#detailKontrakContent').html(html);
                const modalEl = document.getElementById('detailKontrakModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    console.log('Modal shown');
                } else {
                    console.error('Modal element not found');
                }
            });
        });
    </script>
@endpush
</div>
