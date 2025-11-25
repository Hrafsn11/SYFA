<div wire:ignore>
    {{-- Header Section --}}
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengajuan Restrukturisasi</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#modalRestrukturisasi" id="btnTambahRestrukturisasi">
                    <i class="fa-solid fa-plus"></i>
                    <span>Ajukan Restrukturisasi</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th>Nama Debitur</th>
                                    <th>Nomor Kontrak</th>
                                    <th class="text-center">Jenis Pembiayaan</th>
                                    <th class="text-end">Plafon Awal</th>
                                    <th class="text-end">Sisa Pokok</th>
                                    <th>Jenis Restrukturisasi</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <div class="fw-semibold">PT Maju Jaya Sejahtera</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">INV/2024/001</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary">Invoice Financing</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-semibold">Rp 500.000.000</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-warning fw-semibold">Rp 300.000.000</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">Perpanjangan Tenor</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="{{ route('detail-restrukturisasi') }}"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1"
                                                title="Detail">
                                                <i class="ti ti-file"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1"
                                                title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>
                                        <div class="fw-semibold">CV Berkah Mandiri</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">PO/2024/025</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-success">PO Financing</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-semibold">Rp 750.000.000</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-warning fw-semibold">Rp 450.000.000</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-warning">Penurunan Margin</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="{{ route('detail-restrukturisasi') }}"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1"
                                                title="Detail">
                                                <i class="ti ti-file"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1"
                                                title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Restrukturisasi --}}
    @include('livewire.pengajuan-restrukturisasi.partials._modal-pengajuan-restrukturisasi')
</div>

@push('scripts')
    <script>
        let wizardRestrukturisasi;

        $(document).ready(function() {
            initWizardRestrukturisasi();
        });

        function initWizardRestrukturisasi() {
            const wizardElement = document.getElementById('wizardRestrukturisasi');
            if (wizardElement) {
                wizardRestrukturisasi = new Stepper(wizardElement, {
                    linear: false,
                    animation: true
                });

                wizardElement.addEventListener('shown.bs-stepper', function(event) {
                    if (event.detail.indexStep === 3) {
                        populateReviewData();
                    }
                });
            }
        }

        function validateCurrentStep() {
            return true;
        }

        function populateReviewData() {
            $('#review_kode_peminjaman').text($('#nomor_kontrak_pembiayaan option:selected').text() || '-');
            $('#review_nama_perusahaan').text($('#nama_perusahaan').val() || '-');
            $('#review_alasan').text($('#alasan_restrukturisasi').val() || '-');

            const jenisText = $('input[name="customRadioTemp"]:checked').next('.custom-option-header').find('.h6').text();
            $('#review_jenis').text(jenisText || '-');

            const dokumenList = [];
            const fileInputs = [{
                    id: 'fotocopy_ktp_pic',
                    label: 'Fotocopy KTP PIC'
                },
                {
                    id: 'fotokopi_npwp_perusahaan',
                    label: 'Fotokopi NPWP Perusahaan'
                },
                {
                    id: 'laporan_keuangan_terbaru',
                    label: 'Laporan Keuangan Terbaru'
                },
                {
                    id: 'rekap_arus_kas',
                    label: 'Rekap Arus Kas'
                }
            ];

            fileInputs.forEach(function(file) {
                const input = $(`#${file.id}`)[0];
                if (input && input.files.length > 0) {
                    dokumenList.push(
                        `<li><i class="ti ti-file-check text-success me-2"></i>${file.label}: ${input.files[0].name}</li>`
                    );
                }
            });

            if (dokumenList.length > 0) {
                $('#review_dokumen').html(dokumenList.join(''));
            } else {
                $('#review_dokumen').html('<li class="text-muted">Belum ada dokumen yang diupload</li>');
            }
        }

        function handleSubmit() {
            sweetAlertConfirm({
                title: 'Konfirmasi Pengajuan',
                text: 'Apakah Anda yakin ingin mengajukan restrukturisasi ini?',
                icon: 'warning',
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Batal',
            }, () => {
                // TODO: Integrate with Livewire
                // @this.saveData("route.name", {"data": formData, "callback": "afterAction"});

                showSweetAlert({
                    title: 'Berhasil!',
                    text: 'Pengajuan restrukturisasi berhasil disubmit',
                    icon: 'success'
                }).then(() => {
                    $('#modalRestrukturisasi').modal('hide');
                });
            });
        }

        function resetWizard() {
            // $('#formRestrukturisasi')[0].reset();

            if (wizardRestrukturisasi) {
                // wizardRestrukturisasi.reset();
                wizardRestrukturisasi.to(1);
            }
        }

        // Livewire callback function
        function afterAction(payload) {
            // Refresh datatable jika ada
            // Livewire.dispatch('refreshRestrukturisasiTable');
            $('#modalRestrukturisasi').modal('hide');
        }

        // Event handlers menggunakan event delegation
        $(document).on('click', '.btn-next', function() {
            if (validateCurrentStep()) {
                wizardRestrukturisasi.next();
            }
        });

        $(document).on('click', '.btn-prev', function() {
            wizardRestrukturisasi.previous();
        });

        $(document).on('click', '#btnSubmitRestrukturisasi', function(e) {
            e.preventDefault();
            handleSubmit();
        });

        $('#modalRestrukturisasi').on('shown.bs.modal', function() {
            initAllComponents();

            if (typeof window.initCleaveRupiah === 'function') {
                window.initCleaveRupiah();
            }

            // Initialize Bootstrap Datepicker
            $('.bs-datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto'
            });
        });

        $('#modalRestrukturisasi').on('hide.bs.modal', function() {
            resetWizard();
        });

        $(document).on('change', '#checkLainnya', function() {
            $('#inputLainnya').prop('disabled', !this.checked);
        });
    </script>
@endpush
