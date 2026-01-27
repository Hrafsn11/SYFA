<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Aset Investasi - SFinlog</h4>

                @can('penyaluran_deposito_finlog.add')
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahPenyaluran" data-bs-toggle="modal" data-bs-target="#modalPenyaluranDepositoSfinlog">
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
                <livewire:SFinlog.penyaluran-deposito-sfinlog-table />
            </div>
        </div>
    </div>

    @include('livewire.sfinlog.penyaluran-deposito-sfinlog.component.modal')

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

    <!-- Modal Riwayat Pengembalian -->
    <div wire:ignore.self class="modal fade" id="modalRiwayatPengembalian" tabindex="-1"
        aria-labelledby="modalRiwayatPengembalianLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRiwayatPengembalianLabel">Riwayat Pengembalian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="riwayatContent">
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

@push('scripts')
    <script>
        // ===== ========================================
        // STATE & SELECTORS
        // =============================================
        const canInputPengembalian = @json(auth()->user()->can('penyaluran_deposito_finlog.input_pengembalian'));

        const State = {
            flatpickrPengiriman: null,
            flatpickrPengembalian: null,
            currentIdForUpload: null,
            currentIdForDelete: null,
            nilaiInvestasiMax: 0,
            isUpdatingProject: false,
            pendingProjectId: null
        };

        const S = {
            modal: '#modalPenyaluranDepositoSfinlog',
            kontrak: '#id_pengajuan_investasi_finlog',
            cellBisnis: '#id_cells_project',
            project: '#id_project',
            nominal: '#nominal_yang_disalurkan',
            nominalRaw: '#nominal_raw',
            infoNilai: '#nilai-investasi-info'
        };

        // =============================================
        // UTILITIES
        // =============================================
        const formatRupiah = (angka) => {
            if (!angka) return '';
            return 'Rp ' + angka.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        const unformatRupiah = (val) => val.replace(/[^0-9]/g, '');

        const formatNumber = (num) => parseFloat(num).toLocaleString('id-ID');

        const showAlert = (type, message) => Swal.fire({
            icon: type,
            title: type === 'success' ? 'Berhasil!' : 'Error!',
            text: message,
            confirmButtonText: 'OK',
            customClass: { confirmButton: `btn btn-${type === 'success' ? 'success' : 'danger'}` }
        });

        // =============================================
        // PROJECT DROPDOWN - IMPROVED VERSION
        // =============================================
        const initProjectSelect2 = () => {
            const $select = $(S.project);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                dropdownParent: $(S.modal),
                width: '100%',
                placeholder: 'Pilih Project',
                allowClear: true
            });
        };

        const updateProjectDropdown = (projects = [], selectedValue = null) => {
            if (State.isUpdatingProject) return;
            State.isUpdatingProject = true;


            const $select = $(S.project);

            // Destroy select2 first
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            // Clear and rebuild options
            $select.empty().append('<option value=""></option>');

            if (Array.isArray(projects) && projects.length > 0) {
                projects.forEach(p => {
                    const isSelected = String(p.id_project) === String(selectedValue);
                    const option = new Option(p.nama_project, p.id_project, isSelected, isSelected);
                    $select.append(option);
                });
                $select.prop('disabled', false);
            } else {
                $select.prop('disabled', true);
            }

            // Reinitialize select2
            $select.select2({
                dropdownParent: $(S.modal),
                width: '100%',
                placeholder: 'Pilih Project',
                allowClear: true
            });

            // Set value after reinitialize
            if (selectedValue) {
                $select.val(selectedValue).trigger('change.select2');
            }

            State.isUpdatingProject = false;
        };

        // =============================================
        // INFO DISPLAY
        // =============================================
        const showNilaiInfo = (nilaiInvestasi, sisaDana) => {
            $(S.infoNilai).html(`
                                                            <div class="alert alert-info py-2 mt-2">
                                                                <small>
                                                                    <strong>Nilai Investasi:</strong> Rp ${formatNumber(nilaiInvestasi)}<br>
                                                                    <strong class="text-success">Sisa Dana Tersedia:</strong> Rp ${formatNumber(sisaDana)}
                                                                </small>
                                                            </div>
                                                        `);
        };

        const showNilaiError = (max) => {
            $(S.infoNilai).html(`
                                                            <div class="alert alert-danger py-2 mt-2">
                                                                <small><i class="ti ti-alert-circle me-1"></i>
                                                                    <strong>Perhatian!</strong> Nominal melebihi sisa dana (Rp ${formatNumber(max)})
                                                                </small>
                                                            </div>
                                                        `);
        };

        // =============================================
        // MODAL RESET
        // =============================================
        const resetModalUI = () => {
            const $modal = $(S.modal);

            $modal.find('form').attr('wire:submit', `{!! $urlAction['store_penyaluran_deposito_sfinlog'] !!}`);
            $modal.find('.modal-title').text('Tambah Penyaluran Deposito');
            $modal.find('#btnHapusData').hide();

            $(S.kontrak).val(null).trigger('change.select2');
            $(S.cellBisnis).val(null).trigger('change.select2');

            updateProjectDropdown([], null);
            $(S.project).prop('disabled', true);

            $(S.nominal).val('');
            $(S.nominalRaw).val('');
            State.flatpickrPengiriman?.clear();
            State.flatpickrPengembalian?.clear();

            $(S.infoNilai).html('');
            State.nilaiInvestasiMax = 0;
            State.currentIdForDelete = null;
            State.pendingProjectId = null;

            $modal.find('.form-control, .form-group').removeClass('is-invalid');
            $modal.find('.invalid-feedback').text('').hide();
        };

        // =============================================
        // GLOBAL FUNCTIONS
        // =============================================
        function afterAction() {
            Livewire.dispatch('refreshPenyaluranDepositoSfinlogTable');
            $('.modal').modal('hide');

            // Reload page to update dropdown sisa dana
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }

        function editDataDirect(button) {
            const encodedData = $(button).attr('data-item');
            const data = JSON.parse(atob(encodedData));
            const $modal = $(S.modal);


            $modal.find('.form-control, .form-group').removeClass('is-invalid');
            $modal.find('.invalid-feedback').text('').hide();

            $modal.find('form').attr('wire:submit',
                `{!! $urlAction['update_penyaluran_deposito_sfinlog'] !!}`.replace('id_placeholder', data.id));
            $modal.find('.modal-title').html('Edit Penyaluran Deposito');
            $modal.find('#btnHapusData').show();

            State.currentIdForDelete = data.id;

            // Set UI values
            $(S.nominal).val(formatRupiah(data.nominal_yang_disalurkan));
            $(S.nominalRaw).val(data.nominal_yang_disalurkan);
            State.flatpickrPengiriman?.setDate(data.tanggal_pengiriman_dana);
            State.flatpickrPengembalian?.setDate(data.tanggal_pengembalian);

            // Set Livewire properties
            @this.set('id', data.id);
            @this.set('id_pengajuan_investasi_finlog', data.id_pengajuan_investasi_finlog);
            @this.set('nominal_yang_disalurkan', data.nominal_yang_disalurkan);
            @this.set('tanggal_pengiriman_dana', data.tanggal_pengiriman_dana);
            @this.set('tanggal_pengembalian', data.tanggal_pengembalian);

            // Set kontrak select2
            $(S.kontrak).val(data.id_pengajuan_investasi_finlog).trigger('change.select2');

            // Store pending project ID for when dropdown updates
            State.pendingProjectId = data.id_project || null;

            // Load projects by setting cell bisnis - this triggers updatedIdCellsProject on server
            // The updateProjects event will be dispatched automatically
            if (data.id_cells_project) {
                $(S.cellBisnis).val(data.id_cells_project).trigger('change.select2');
                @this.set('id_cells_project', data.id_cells_project);

                // Set id_project after a delay to ensure Livewire has processed
                if (data.id_project) {
                    @this.set('id_project', data.id_project);
                }
            }

            $modal.modal('show');
        }

        // =============================================
        // AJAX HANDLERS
        // =============================================
        const doDelete = () => {
            if (!State.currentIdForDelete) return;

            $('#deleteSpinner').removeClass('d-none');
            $('#btnConfirmDelete').prop('disabled', true);

            $.ajax({
                url: '{{ route('sfinlog.penyaluran-deposito-sfinlog.destroy', ':id') }}'.replace(':id', State.currentIdForDelete),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: (res) => {
                    $('#modalConfirmDelete').modal('hide');
                    Livewire.dispatch('refreshPenyaluranDepositoSfinlogTable');
                    showAlert('success', res.message || 'Data berhasil dihapus');
                },
                error: (xhr) => showAlert('error', xhr.responseJSON?.message || 'Gagal menghapus data'),
                complete: () => {
                    $('#deleteSpinner').addClass('d-none');
                    $('#btnConfirmDelete').prop('disabled', false);
                    State.currentIdForDelete = null;
                }
            });
        };

        // =============================================
        // INITIALIZATION
        // =============================================
        $(document).ready(function () {
            // Flatpickr
            State.flatpickrPengiriman = flatpickr("#tanggal_pengiriman_dana", {
                dateFormat: "Y-m-d",
                allowInput: true,
                onChange: (_, dateStr) => @this.set('tanggal_pengiriman_dana', dateStr)
                });

            State.flatpickrPengembalian = flatpickr("#tanggal_pengembalian", {
                dateFormat: "Y-m-d",
                allowInput: true,
                onChange: (_, dateStr) => @this.set('tanggal_pengembalian', dateStr)
                });

            // Select2 Config
            const select2Cfg = (placeholder) => ({
                dropdownParent: $(S.modal), width: '100%', placeholder, allowClear: true
            });

            // Initialize Select2
            $(S.kontrak).select2(select2Cfg('Pilih No Kontrak'));
            $(S.cellBisnis).select2(select2Cfg('Pilih Cell Bisnis'));
            initProjectSelect2();

            // Event: Nominal input
            $(S.nominal).on('input', function () {
                const rawValue = unformatRupiah($(this).val());
                $(this).val(formatRupiah(rawValue));
                $(S.nominalRaw).val(rawValue);
                @this.set('nominal_yang_disalurkan', rawValue);

                const $opt = $(S.kontrak).find('option:selected');
                const nilaiInvestasi = parseFloat($opt.data('nilai-investasi')) || 0;
                const sisaDana = parseFloat($opt.data('sisa-dana')) || 0;

                if (State.nilaiInvestasiMax > 0 && parseFloat(rawValue) > State.nilaiInvestasiMax) {
                    showNilaiError(State.nilaiInvestasiMax);
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                    if (sisaDana > 0 && nilaiInvestasi > 0) showNilaiInfo(nilaiInvestasi, sisaDana);
                }
            });

            // Event: Kontrak change
            $(S.kontrak).on('change', function () {
                const $opt = $(this).find('option:selected');
                const nilaiInvestasi = $opt.data('nilai-investasi');
                const sisaDana = parseFloat($opt.data('sisa-dana')) || 0;

                if (nilaiInvestasi !== undefined) {
                    State.nilaiInvestasiMax = sisaDana;
                    showNilaiInfo(nilaiInvestasi, sisaDana);
                } else {
                    State.nilaiInvestasiMax = 0;
                    $(S.infoNilai).html('');
                }

                @this.set('id_pengajuan_investasi_finlog', $(this).val());
            });

            // Event: Cell Bisnis change - Load projects via Livewire
            $(S.cellBisnis).on('change', function () {
                const val = $(this).val();

                // Reset project first
                updateProjectDropdown([], null);
                @this.set('id_project', null);

                if (val) {
                    // Set cell bisnis - Livewire will dispatch updateProjects event automatically
                    @this.set('id_cells_project', val);
                } else {
                    @this.set('id_cells_project', null);
                }
            });

            // Event: Project change
            $(S.project).on('select2:select select2:clear', function (e) {
                const val = $(this).val();
                @this.set('id_project', val || null);
            });

            // Livewire event listener for updateProjects
            Livewire.on('updateProjects', (payload) => {

                let projects = [];
                if (Array.isArray(payload)) {
                    projects = payload[0]?.projects || payload[0] || [];
                } else if (payload?.projects) {
                    projects = payload.projects;
                } else if (Array.isArray(payload)) {
                    projects = payload;
                }


                const selectedProjectId = State.pendingProjectId || @this.id_project;
                updateProjectDropdown(projects, selectedProjectId);
                State.pendingProjectId = null;
            });

            // Modal events
            $(S.modal).on('hidden.bs.modal', resetModalUI);

            $(S.modal).on('keyup change', '.form-control, .form-select', function () {
                $(this).removeClass('is-invalid');
                $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
            });

            // Delete confirmation
            $('#btnHapusData').on('click', (e) => {
                e.preventDefault();
                $(S.modal).modal('hide');
                $('#modalConfirmDelete').modal('show');
            });

            $('#btnConfirmDelete').on('click', (e) => { e.preventDefault(); doDelete(); });
        });

        // Listen for detail kontrak event
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('kontrak-detail-loaded', (event) => {
                const kontrakData = event.data;

                if (!kontrakData) return;

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
                                                                    <td><strong>Nominal Investasi:</strong></td>
                                                                    <td>Rp ${new Intl.NumberFormat('id-ID').format(kontrakData.nominal_investasi || 0)}</td>
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
                                                            <th class="text-center">Cell Bisnis</th>
                                                            <th class="text-center">Project</th>
                                                            <th class="text-center">Nominal Disalurkan</th>
                                                            <th class="text-center">Nominal Dikembalikan</th>
                                                            <th class="text-center">Tgl Pengiriman</th>
                                                            <th class="text-center">Tgl Pengembalian</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                        `;

                let totalNominal = 0;
                let totalDikembalikan = 0;

                kontrakData.details.forEach((item, index) => {
                    totalNominal += parseFloat(item.nominal_yang_disalurkan || 0);
                    totalDikembalikan += parseFloat(item.nominal_yang_dikembalikan || 0);

                    const tglPengiriman = item.tanggal_pengiriman_dana ? new Date(item.tanggal_pengiriman_dana).toLocaleDateString('id-ID') : '-';
                    const tglPengembalian = item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID') : '-';
                    const nominalDisalurkan = parseFloat(item.nominal_yang_disalurkan || 0);
                    const sisaBelumDikembalikan = parseFloat(item.sisa_belum_dikembalikan ?? nominalDisalurkan);

                    // Status badge based on sisa
                    let statusBadge = '<span class="badge bg-label-danger">Belum Lunas</span>';
                    if (sisaBelumDikembalikan <= 0) {
                        statusBadge = '<span class="badge bg-label-success">Lunas</span>';
                    } else if (item.nominal_yang_dikembalikan > 0) {
                        statusBadge = '<span class="badge bg-label-warning">Sebagian Lunas</span>';
                    }

                    html += `
                                                <tr>
                                                    <td class="text-center">${index + 1}</td>
                                                    <td>${item.cell_bisnis || '-'}</td>
                                                    <td>${item.project || '-'}</td>
                                                    <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal_yang_disalurkan || 0)}</td>
                                                    <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal_yang_dikembalikan || 0)}</td>
                                                    <td class="text-center">${tglPengiriman}</td>
                                                    <td class="text-center">${tglPengembalian}</td>
                                                    <td class="text-center">${statusBadge}</td>
                                                    <td class="text-center">
                                                        <div class="d-flex gap-1 justify-content-center">
                                                            ${canInputPengembalian && sisaBelumDikembalikan > 0 ? `
                                                            <button type="button" class="btn btn-sm btn-primary" 
                                                                onclick="openInputPengembalian('${item.id}', '${item.cell_bisnis}', '${item.project}', ${item.nominal_yang_disalurkan}, ${sisaBelumDikembalikan}, '${item.tanggal_pengiriman_dana}', '${item.tanggal_pengembalian}'); $('#detailKontrakModal').modal('hide');"
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
                                                </tr>
                                            `;
                });

                html += `
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th colspan="3" class="text-end">Total:</th>
                                                            <th class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(totalNominal)}</th>
                                                            <th class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(totalDikembalikan)}</th>
                                                            <th colspan="4"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        `;

                document.getElementById('detailKontrakContent').innerHTML = html;

                const modal = new bootstrap.Modal(document.getElementById('detailKontrakModal'));
                modal.show();
            });

            // Event listener untuk riwayat pengembalian
            Livewire.on('riwayat-loaded', (event) => {
                const data = event.data;

                if (!data) return;

                // Close detail modal first
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailKontrakModal'));
                if (detailModal) detailModal.hide();

                let html = `
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Cell Bisnis:</strong> ${data.cell_bisnis}</p>
                                <p class="mb-1"><strong>Project:</strong> ${data.project}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="mb-1"><strong>Nominal Disalurkan:</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.nominal_disalurkan)}</p>
                                <p class="mb-1"><strong>Total Dikembalikan:</strong> <span class="text-success">Rp ${new Intl.NumberFormat('id-ID').format(data.total_dikembalikan)}</span></p>
                                <p class="mb-1"><strong>Sisa:</strong> <span class="${data.sisa_belum_dikembalikan > 0 ? 'text-danger' : 'text-success'}">Rp ${new Intl.NumberFormat('id-ID').format(data.sisa_belum_dikembalikan)}</span></p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Riwayat Pengembalian</h6>`;

                if (data.riwayat && data.riwayat.length > 0) {
                    html += `
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Nominal</th>
                                        <th class="text-center">Bukti</th>
                                        <th class="text-center">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    data.riwayat.forEach((item, index) => {
                        html += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${item.tanggal || '-'}</td>
                                <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal)}</td>
                                <td class="text-center">
                                    ${item.bukti ? `<a href="${item.bukti}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-file me-1"></i>Lihat</a>` : '-'}
                                </td>
                                <td>${item.catatan || '-'}</td>
                            </tr>`;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>`;
                } else {
                    html += `<div class="alert alert-info">Belum ada riwayat pengembalian.</div>`;
                }

                document.getElementById('riwayatContent').innerHTML = html;

                setTimeout(() => {
                    const riwayatModal = new bootstrap.Modal(document.getElementById('modalRiwayatPengembalian'));
                    riwayatModal.show();
                }, 300);
            });
        });
    </script>
@endpush