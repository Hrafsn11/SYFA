<div wire:ignore>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Penyaluran Dana Investasi</h4>

                    @can('penyaluran_dana_investasi.add')
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahPenyaluran" data-bs-toggle="modal" data-bs-target="#modalPenyaluranDanaInvestasi">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Penyaluran</span>
                    </button>
                    @endcan

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body mt-3">
                <livewire:penyaluran-dana-investasi.penyaluran-dana-investasi-table />
            </div>
        </div>

        @include('livewire.penyaluran-dana-investasi.components.modal')

        <!-- Modal Detail Kontrak -->
        <div wire:ignore.self class="modal fade" id="detailKontrakModal" tabindex="-1"
            aria-labelledby="detailKontrakModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-2 p-2" style="background:#e6f7f5;">
                                <i class="ti ti-building-bank fs-5" style="color:#0d9488;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="detailKontrakModalLabel">Detail Penyaluran Dana Investasi</h5>
                                <small class="text-muted" id="detailKontrakSubtitle"></small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="detailKontrakContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let nilaiInvestasiMax = 0;
    const canInputPengembalian = @json(auth() -> user() -> can('penyaluran_dana_investasi.input_pengembalian'));

    function afterAction(payload) {
        Livewire.dispatch('refreshPenyaluranDanaInvestasiTable');
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

    $(document).ready(function() {
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
            dropdownParent: $('#modalPenyaluranDanaInvestasi'),
            width: '100%',
            placeholder: 'Pilih No Kontrak',
            allowClear: true
        }).on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const sisaDana = parseFloat(selectedOption.data('sisa-dana')) || 0;
            const nilaiInvestasi = selectedOption.data('nilai-investasi');

            nilaiInvestasiMax = sisaDana;

            if (sisaDana && nilaiInvestasi) {
                $('#input-nilai-investasi').val('Rp ' + parseFloat(nilaiInvestasi).toLocaleString('id-ID'));
                $('#input-sisa-dana').val('Rp ' + sisaDana.toLocaleString('id-ID'));
                $('#info-investasi-fields').show();
                $('#nilai-investasi-info').html('');
            } else {
                $('#info-investasi-fields').hide();
                $('#input-nilai-investasi, #input-sisa-dana').val('');
                $('#nilai-investasi-info').html('');
            }
            @this.set('id_pengajuan_investasi', $(this).val());
        });

        $('#id_debitur').select2({
            dropdownParent: $('#modalPenyaluranDanaInvestasi'),
            width: '100%',
            placeholder: 'Pilih Nama Perusahaan',
            allowClear: true
        }).on('change', function() {
            @this.set('id_debitur', $(this).val());
        });

        // Handle nominal input
        $('#nominal_yang_disalurkan').on('input', function() {
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
                $('#nilai-investasi-info').html('');
            }
        });

        // Modal reset on hide
        $('#modalPenyaluranDanaInvestasi').on('hidden.bs.modal', function() {
            $(this).find('form').attr('wire:submit', `{!! $urlAction['store_penyaluran_dana_investasi'] !!}`);
            $(this).find('.modal-title').text('Tambah Penyaluran Dana Investasi');
            $(this).find('#btnHapusData').hide();

            $('#id_pengajuan_investasi, #id_debitur').val('').trigger('change');
            $('#nominal_yang_disalurkan, #nominal_raw').val('');
            flatpickrPengiriman.clear();
            flatpickrPengembalian.clear();
            $('#info-investasi-fields').hide();
            $('#input-nilai-investasi, #input-sisa-dana').val('');
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
        }).on('keyup change', '.form-control, .form-select', function() {
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
                            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:0.05em;">Informasi Kontrak</p>
                            <div class="row g-2 mb-4">
                                <div class="col-6 col-md-3">
                                    <div class="rounded-2 p-3 h-100" style="background:#f8fffe;border:1px solid #e0f2f0;">
                                        <p class="text-muted small mb-1">No. Kontrak</p>
                                        <p class="fw-semibold mb-0 small" style="color:#0d9488;">${kontrakData.nomor_kontrak || '-'}</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="rounded-2 p-3 h-100" style="background:#f8fffe;border:1px solid #e0f2f0;">
                                        <p class="text-muted small mb-1">Nama Investor</p>
                                        <p class="fw-semibold mb-0 small text-dark">${kontrakData.nama_investor || '-'}</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="rounded-2 p-3 h-100" style="background:#f8fffe;border:1px solid #e0f2f0;">
                                        <p class="text-muted small mb-1">Jumlah Investasi</p>
                                        <p class="fw-semibold mb-0" style="color:#0d9488;font-size:0.9rem;">Rp ${new Intl.NumberFormat('id-ID').format(kontrakData.jumlah_investasi || 0)}</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="rounded-2 p-3 h-100" style="background:#f8fffe;border:1px solid #e0f2f0;">
                                        <p class="text-muted small mb-1">Lama Investasi</p>
                                        <p class="fw-semibold mb-0 small text-dark">${kontrakData.lama_investasi || '-'} Bulan</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:0.05em;">Riwayat Penyaluran Dana</p>
                            <div class="table-responsive rounded-2" style="border:1px solid #e9ecef;">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                                    <thead style="background:#f8f9fa;">
                                        <tr>
                                            <th class="px-3 py-2 text-muted fw-semibold" style="width:40px;">NO</th>
                                            <th class="px-3 py-2 text-muted fw-semibold">NAMA PERUSAHAAN</th>
                                            <th class="px-3 py-2 text-muted fw-semibold text-end">NOMINAL DISALURKAN</th>
                                            <th class="px-3 py-2 text-muted fw-semibold text-end">NOMINAL DIKEMBALIKAN</th>
                                            <th class="px-3 py-2 text-muted fw-semibold">TGL PENGIRIMAN</th>
                                            <th class="px-3 py-2 text-muted fw-semibold">TGL PENGEMBALIAN</th>
                                            <th class="px-3 py-2 text-muted fw-semibold text-center">STATUS</th>
                                            <th class="px-3 py-2 text-muted fw-semibold text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

            let totalNominal = 0;
            kontrakData.details.forEach((item, index) => {
                totalNominal += parseFloat(item.nominal_yang_disalurkan || 0);
                const nominalDisalurkan = parseFloat(item.nominal_yang_disalurkan || 0);
                const nominalDikembalikan = parseFloat(item.nominal_yang_dikembalikan || 0);
                const sisaBelumDikembalikan = parseFloat(item.sisa_belum_dikembalikan ?? (nominalDisalurkan - nominalDikembalikan));

                let statusBadge;
                if (sisaBelumDikembalikan <= 0) {
                    statusBadge = '<span class="badge rounded-pill px-3 py-1" style="background:#e6f7f0;color:#198754;font-size:0.75rem;">Lunas</span>';
                } else if (nominalDikembalikan > 0) {
                    statusBadge = '<span class="badge rounded-pill px-3 py-1" style="background:#fff8e6;color:#b45309;font-size:0.75rem;">Sebagian Lunas</span>';
                } else {
                    statusBadge = '<span class="badge rounded-pill px-3 py-1" style="background:#fff0f0;color:#dc3545;font-size:0.75rem;">Belum Lunas</span>';
                }

                html += `
                                <tr>
                                    <td class="px-3 text-center">${index + 1}</td>
                                    <td class="px-3 fw-medium">${item.nama_perusahaan || '-'}</td>
                                    <td class="px-3 text-end fw-medium" style="color:#0d9488;">Rp ${new Intl.NumberFormat('id-ID').format(nominalDisalurkan)}</td>
                                    <td class="px-3 text-end">Rp ${new Intl.NumberFormat('id-ID').format(nominalDikembalikan)}</td>
                                    <td class="px-3 text-muted small">${item.tanggal_pengiriman_dana ? new Date(item.tanggal_pengiriman_dana).toLocaleDateString('id-ID') : '-'}</td>
                                    <td class="px-3 text-muted small">${item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID') : '-'}</td>
                                    <td class="px-3 text-center">${statusBadge}</td>
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

            if (kontrakData.details.length === 0) {
                html += `<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data penyaluran</td></tr>`;
            }

            html += `
                                    </tbody>
                                    <tfoot style="background:#f8f9fa;border-top:2px solid #dee2e6;">
                                        <tr>
                                            <td colspan="2" class="px-3 py-2 fw-bold text-end">TOTAL:</td>
                                            <td class="px-3 py-2 fw-bold text-end" style="color:#0d9488;">Rp ${new Intl.NumberFormat('id-ID').format(totalNominal)}</td>
                                            <td colspan="5"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>`;

            $('#detailKontrakContent').html(html);
            $('#detailKontrakSubtitle').text(kontrakData.nomor_kontrak || '');
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
    $(document).on('click', '#btnHapusData', function(e) {
        e.preventDefault();
        $('#modalPenyaluranDanaInvestasi').modal('hide');
        $('#modalConfirmDelete').modal('show');
    });

    $(document).on('click', '#btnConfirmDelete', function(e) {
        e.preventDefault();
        const currentIdForDelete = $(this).data('id');
        if (!currentIdForDelete) return;

        $('#deleteSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: `{{ route('penyaluran-dana-investasi.destroy', ':id') }}`.replace(':id', currentIdForDelete),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                $('#modalConfirmDelete').modal('hide');
                Livewire.dispatch('refreshPenyaluranDanaInvestasiTable');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data berhasil dihapus'
                });
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            },
            complete: () => {
                $('#deleteSpinner').addClass('d-none');
                $('#btnConfirmDelete').prop('disabled', false);
            }
        });
    });
</script>
@endpush