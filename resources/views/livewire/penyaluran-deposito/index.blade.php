<div>
    <div wire:ignore>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Aset Investasi</h4>

                    @can('penyaluran_deposito.add')
                        <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                            id="btnTambahPenyaluran" data-bs-toggle="modal" data-bs-target="#modalPenyaluranDeposito">
                            <i class="fa-solid fa-plus"></i>
                            <span>Tambah Penyaluran</span>
                        </button>
                    @endcan

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="card-datatable">
                    <livewire:penyaluran-deposito.penyaluran-deposito-table />
                </div>
            </div>
        </div>

        @include('livewire.penyaluran-deposito.components.modal')

        <!-- Modal Detail Kontrak -->
        <div wire:ignore.self class="modal fade" id="detailKontrakModal" tabindex="-1"
            aria-labelledby="detailKontrakModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailKontrakModalLabel">Detail Aset Investasi</h5>
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
    </div>
</div>

@push('scripts')
    <script>
    let nilaiInvestasiMax = 0;
        const canInputPengembalian = @json(auth()->user()->can('penyaluran_deposito.input_pengembalian'));

        function afterAction(payload) {
            Livewire.dispatch('refreshPenyaluranDepositoTable');
            $('.modal').modal('hide');
        }

        function formatRupiah(angka) {
            if (!angka) return '';
            const number = angka.toString().replace(/[^0-9]/g, '');
            return 'Rp ' + number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatRupiah(rupiah) {
            return rupiah.replace(/[^0-9]/g, '');
        }

        $(document).ready(function () {
            // Initialize flatpickr
            const flatpickrPengiriman = flatpickr("#tanggal_pengiriman_dana", {
                dateFormat: "Y-m-d",
                allowInput: true,
                minDate: "today",
                onChange: (selectedDates, dateStr) => @this.set('tanggal_pengiriman_dana', dateStr)
                });

            const flatpickrPengembalian = flatpickr("#tanggal_pengembalian", {
                dateFormat: "Y-m-d",
                allowInput: true,
                minDate: "today",
                onChange: (selectedDates, dateStr) => @this.set('tanggal_pengembalian', dateStr)
                });

            // Initialize select2
            $('#id_pengajuan_investasi').select2({
                dropdownParent: $('#modalPenyaluranDeposito'),
                width: '100%',
                placeholder: 'Pilih No Kontrak',
                allowClear: true
            }).on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const sisaDana = parseFloat(selectedOption.data('sisa-dana')) || 0;
                const nilaiInvestasi = selectedOption.data('nilai-investasi');

                nilaiInvestasiMax = sisaDana;

                if (sisaDana && nilaiInvestasi) {
                    $('#nilai-investasi-info').html(`
                                <div class="alert alert-info py-2 mt-2">
                                    <small>
                                        <strong>Nilai Investasi:</strong> Rp ${parseFloat(nilaiInvestasi).toLocaleString('id-ID')}<br>
                                        <strong class="text-success">Sisa Dana Tersedia:</strong> Rp ${sisaDana.toLocaleString('id-ID')}
                                    </small>
                                </div>
                            `);
                } else {
                    $('#nilai-investasi-info').html('');
                }
                @this.set('id_pengajuan_investasi', $(this).val());
            });

            $('#id_debitur').select2({
                dropdownParent: $('#modalPenyaluranDeposito'),
                width: '100%',
                placeholder: 'Pilih Nama Perusahaan',
                allowClear: true
            }).on('change', function () {
                @this.set('id_debitur', $(this).val());
            });

            // Handle nominal input
            $('#nominal_yang_disalurkan').on('input', function () {
                const rawValue = unformatRupiah($(this).val());
                $(this).val(formatRupiah(rawValue));
                $('#nominal_raw').val(rawValue);
                @this.set('nominal_yang_disalurkan', rawValue);

                if (nilaiInvestasiMax > 0 && parseFloat(rawValue) > nilaiInvestasiMax) {
                    $('#nilai-investasi-info').html(`
                                <div class="alert alert-danger py-2 mt-2">
                                    <small>
                                        <i class="ti ti-alert-circle me-1"></i>
                                        <strong>Perhatian!</strong> Nominal melebihi sisa dana yang tersedia 
                                        (Rp ${nilaiInvestasiMax.toLocaleString('id-ID')})
                                    </small>
                                </div>
                            `);
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Modal reset on hide
            $('#modalPenyaluranDeposito').on('hidden.bs.modal', function () {
                $(this).find('form').attr('wire:submit', `{!! $urlAction['store_penyaluran_deposito'] !!}`);
                $(this).find('.modal-title').text('Tambah Penyaluran Deposito');
                $(this).find('#btnHapusData').hide();

                $('#id_pengajuan_investasi, #id_debitur').val('').trigger('change');
                $('#nominal_yang_disalurkan, #nominal_raw').val('');
                flatpickrPengiriman.clear();
                flatpickrPengembalian.clear();
                $('#nilai-investasi-info').html('');
                nilaiInvestasiMax = 0;

                $(this).find('.form-control').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('').hide();

                @this.set('id', null);
                @this.set('id_pengajuan_investasi', null);
                @this.set('id_debitur', null);
                @this.set('nominal_yang_disalurkan', null);
                @this.set('tanggal_pengiriman_dana', null);
                @this.set('tanggal_pengembalian', null);
            }).on('keyup change', '.form-control, .form-select', function () {
                $(this).removeClass('is-invalid').closest('.form-group').find('.invalid-feedback').text('').hide();
            });
        });

        // Livewire event untuk detail kontrak
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
                                            <button type="button" class="btn btn-sm btn-info" 
                                                wire:click="lihatRiwayat('${item.id}')"
                                                title="Lihat History">
                                                <i class="ti ti-history"></i>
                                            </button>
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

        // Delete confirmation
        $(document).on('click', '#btnHapusData', function (e) {
            e.preventDefault();
            $('#modalPenyaluranDeposito').modal('hide');
            $('#modalConfirmDelete').modal('show');
        });

        $(document).on('click', '#btnConfirmDelete', function (e) {
            e.preventDefault();
            const currentIdForDelete = $(this).data('id');
            if (!currentIdForDelete) return;

            $('#deleteSpinner').removeClass('d-none');
            $(this).prop('disabled', true);

            $.ajax({
                url: '{{ route('penyaluran-deposito.destroy', ':id') }}'.replace(':id', currentIdForDelete),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: (response) => {
                    $('#modalConfirmDelete').modal('hide');
                    Livewire.dispatch('refreshPenyaluranDepositoTable');
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Data berhasil dihapus' });
                },
                error: (xhr) => {
                    Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan' });
                },
                complete: () => {
                    $('#deleteSpinner').addClass('d-none');
                    $('#btnConfirmDelete').prop('disabled', false);
                }
            });
        });
    </script>
@endpush