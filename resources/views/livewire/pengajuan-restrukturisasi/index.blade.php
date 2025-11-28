@extends('layouts.app')

@section('title', 'Pengajuan Restrukturisasi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Pengajuan Restrukturisasi
                </h4>
            </div>
            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                data-bs-target="#modalRestrukturisasi" id="btnTambahRestrukturisasi">
                <i class="ti ti-plus me-1"></i>
                <span>Ajukan Restrukturisasi</span>
            </button>
        </div>

        {{-- DataTable Card --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"></i>Daftar Pengajuan
                </h5>
            </div>
            <div class="card-body">
                <livewire:pengajuan-restrukturisasi-table />
            </div>
        </div>

        {{-- Modal Restrukturisasi --}}
        @include('livewire.pengajuan-restrukturisasi.partials._modal-pengajuan-restrukturisasi')
    </div>
@endsection

@push('scripts')
    <script>
        'use strict';

        let wizardRestrukturisasi;
        let currentStepIndex = 0;
        let editMode = false;
        let editId = null;

        $(document).ready(function() {
            initWizardRestrukturisasi();
            setupEventListeners();
        });

        function initWizardRestrukturisasi() {
            const wizardElement = document.getElementById('wizardRestrukturisasi');
            if (!wizardElement) return;

            wizardRestrukturisasi = new Stepper(wizardElement, {
                linear: false,
                animation: true
            });

            const steps = wizardElement.querySelectorAll('.bs-stepper-content .content');
            steps.forEach((step, index) => {
                const observer = new MutationObserver(() => {
                    if (step.classList.contains('active')) {
                        currentStepIndex = index;
                    }
                });
                observer.observe(step, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            });
        }

        function resetWizard() {
            if (wizardRestrukturisasi) {
                wizardRestrukturisasi.to(1);
            }

            const form = document.getElementById('formRestrukturisasi');
            if (form) form.reset();

            editMode = false;
            editId = null;

            const $select2 = $('#nomor_kontrak_pembiayaan');
            if ($select2.length && $select2.hasClass('select2-hidden-accessible')) {
                $select2.val(null).trigger('change');
            }

            $('#id_pengajuan_peminjaman, #nomor_kontrak_pembiayaan_value').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false);
            $('.jenis-pembiayaan-radio').closest('.custom-option').removeClass('checked');
            $('.input-rupiah').val('Rp 0');
        }

        // ========================================
        // FORM SUBMISSION
        // ========================================
        function validateCurrentStep() {
            return true;
        }

        function handleSubmit() {
            sweetAlertConfirm({
                title: 'Konfirmasi Pengajuan',
                text: 'Apakah Anda yakin ingin mengajukan restrukturisasi ini?',
                icon: 'warning',
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Batal',
            }, () => {
                submitForm();
            });
        }

        function submitForm() {
            const form = document.getElementById('formRestrukturisasi');
            if (!form) return;

            const formData = new FormData(form);

            formData.delete('jenis_restrukturisasi[]');
            $('input[name="jenis_restrukturisasi[]"]:checked').each(function() {
                formData.append('jenis_restrukturisasi[]', $(this).val());
            });

            const jenisPembiayaan = $('input[name="jenis_pembiayaan_radio"]:checked').val();
            if (jenisPembiayaan) {
                formData.append('jenis_pembiayaan', jenisPembiayaan);
            }

            const rupiahFields = ['jumlah_plafon_awal', 'sisa_pokok_belum_dibayar', 'tunggakan_pokok',
                'tunggakan_margin_bunga'
            ];
            rupiahFields.forEach(field => {
                const value = $('#' + field).val();
                if (value && value !== 'Rp 0' && value.trim() !== 'Rp' && value.trim() !== '') {
                    let cleanValue = value.replace(/Rp\s*/g, '');
                    cleanValue = cleanValue.replace(/\./g, '');
                    cleanValue = cleanValue.replace(',', '.');
                    cleanValue = cleanValue.replace(/[^\d.]/g, '');
                    formData.set(field, cleanValue);
                }
            });

            const url = editMode ?
                "{{ route('pengajuan-restrukturisasi.update', ':id') }}".replace(':id', editId) :
                "{{ route('pengajuan-restrukturisasi.store') }}";

            if (editMode) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showSweetAlert({
                        title: 'Berhasil!',
                        text: response.message || 'Pengajuan restrukturisasi berhasil disimpan',
                        icon: 'success'
                    });

                    $('#modalRestrukturisasi').modal('hide');

                    setTimeout(() => window.location.reload(), 1500);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};
                        displayValidationErrors(errors);

                        showSweetAlert({
                            title: 'Error Validasi',
                            text: 'Mohon periksa kembali data yang Anda masukkan',
                            icon: 'error'
                        });
                    } else {
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                        showSweetAlert({
                            title: 'Error',
                            text: message,
                            icon: 'error'
                        });
                    }
                }
            });
        }

        function displayValidationErrors(errors) {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.each(errors, function(field, messages) {
                const errorMessage = Array.isArray(messages) ? messages[0] : messages;
                const $field = $('#' + field);

                if ($field.length) {
                    $field.addClass('is-invalid');
                    $field.closest('.form-group').find('.invalid-feedback').remove();
                    const $errorSpan = $('<span class="invalid-feedback d-block"></span>').text(errorMessage);
                    $field.after($errorSpan);
                }
            });

            const $firstError = $('.is-invalid').first();
            if ($firstError.length) {
                $firstError[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        $('#modalRestrukturisasi').on('click', '.btn-next', function(e) {
            e.preventDefault();
            if (validateCurrentStep()) {
                wizardRestrukturisasi.next();
            }
        });

        $('#modalRestrukturisasi').on('click', '.btn-prev', function(e) {
            e.preventDefault();
            wizardRestrukturisasi.previous();
        });

        $('#modalRestrukturisasi').on('click', '#btnSubmitRestrukturisasi', function(e) {
            e.preventDefault();
            handleSubmit();
        });

        // Helper function to format rupiah
        function formatRupiah(number) {
            if (!number) return '';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function initializeModalPlugins() {
            const $select2 = $('#nomor_kontrak_pembiayaan');

            if ($select2.hasClass('select2-hidden-accessible')) {
                $select2.select2('destroy');
            }

            $select2.select2({
                dropdownParent: $('#modalRestrukturisasi'),
                placeholder: 'Pilih Nomor Kontrak Pembiayaan'
            });

            $select2.off('change.restrukturisasi').on('change.restrukturisasi', function(e) {
                const value = $(this).val();
                const selectedText = $(this).find('option:selected').text();

                $('#id_pengajuan_peminjaman').val(value);
                $('#nomor_kontrak_pembiayaan_value').val(selectedText.trim());

                if (value) {
                    loadPengajuanData(value);
                } else {
                    $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false);
                    $('.jenis-pembiayaan-radio').closest('.custom-option').removeClass('checked');
                    $('#jumlah_plafon_awal, #sisa_pokok_belum_dibayar').val('Rp 0');
                    $('#id_pengajuan_peminjaman').val('');
                    $('#nomor_kontrak_pembiayaan_value').val('');
                }
            });

            if (typeof window.initFlatpickr === 'function') {
                window.initFlatpickr();
            } else if (typeof flatpickr !== 'undefined') {
                $('.flatpickr').each(function() {
                    const $input = $(this);
                    if (!$input[0]._flatpickr) {
                        flatpickr($input[0], {
                            altInput: true,
                            altFormat: 'j F Y',
                            dateFormat: 'Y-m-d',
                            locale: 'id',
                        });
                    }
                });
            }

            if (typeof window.initCleaveRupiah === 'function') {
                window.initCleaveRupiah();
            }
        }

        function loadPengajuanData(idPengajuan) {
            const url = "{{ route('pengajuan-restrukturisasi.detail-pengajuan', ':id') }}".replace(':id', idPengajuan);

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const data = response.data;
                    if (!data) return;

                    $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false);
                    $('.jenis-pembiayaan-radio').each(function() {
                        if ($(this).val() === data.jenis_pembiayaan) {
                            $(this).prop('disabled', false).prop('checked', true);
                            $(this).closest('.custom-option').addClass('checked');
                        } else {
                            $(this).closest('.custom-option').removeClass('checked');
                        }
                    });

                    $('#jumlah_plafon_awal').val(formatRupiah(data.jumlah_plafon_awal));
                    $('#sisa_pokok_belum_dibayar').val(formatRupiah(data.sisa_pokok_belum_dibayar));

                    if (typeof window.initCleaveRupiah === 'function') {
                        window.initCleaveRupiah();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading pengajuan data:', xhr);
                }
            });
        }

        function setupEventListeners() {
            $('#modalRestrukturisasi').on('shown.bs.modal', function() {
                initializeModalPlugins();
            });

            $('#modalRestrukturisasi').on('hide.bs.modal', function() {
                resetWizard();

                if ($('#nomor_kontrak_pembiayaan').hasClass('select2-hidden-accessible')) {
                    $('#nomor_kontrak_pembiayaan').select2('destroy');
                }

                $('.bs-datepicker').each(function() {
                    if ($(this).data('datepicker')) {
                        $(this).datepicker('destroy');
                    }
                });
            });

            $(document).on('change', '#checkLainnya', function() {
                const isChecked = $(this).is(':checked');
                const $inputLainnya = $('#inputLainnya');

                if (isChecked) {
                    $inputLainnya.prop('disabled', false);
                    setTimeout(() => $inputLainnya.focus(), 100);
                } else {
                    $inputLainnya.prop('disabled', true).val('');
                }
            });
        }

        function editPengajuan(id) {
            editMode = true;
            editId = id;

            $.ajax({
                url: `/pengajuan-restrukturisasi/${id}`,
                method: 'GET',
                success: function(response) {
                    const data = response.data;

                    wizardRestrukturisasi.reset();

                    $('#id_debitur').val(data.id_debitur).trigger('change');

                    setTimeout(() => {
                        $('#id_pengajuan_peminjaman').val(data.id_pengajuan_peminjaman).trigger(
                            'change');
                    }, 500);

                    $('#nama_perusahaan').val(data.nama_perusahaan);
                    $('#npwp').val(data.npwp);
                    $('#nama_pic').val(data.nama_pic);
                    $('#no_hp_pic').val(data.no_hp_pic);
                    $('#jabatan_pic').val(data.jabatan_pic);
                    $('#alamat').val(data.alamat);
                    $('#nomor_kontrak_pembiayaan_value').val(data.nomor_kontrak_pembiayaan);
                    $('#jenis_pembiayaan').val(data.jenis_pembiayaan);
                    $('#tanggal_akad').val(data.tanggal_akad);
                    $('#jatuh_tempo_terakhir').val(data.jatuh_tempo_terakhir);
                    $('#jumlah_plafon_awal').val(data.jumlah_plafon_awal);
                    $('#sisa_pokok_belum_dibayar').val(data.sisa_pokok_belum_dibayar);
                    $('#tunggakan_pokok').val(data.tunggakan_pokok);
                    $('#tunggakan_margin_bunga').val(data.tunggakan_margin_bunga);
                    $('#status_dpd').val(data.status_dpd);
                    $('#alasan_restrukturisasi').val(data.alasan_restrukturisasi);
                    $('#rencana_pemulihan_usaha').val(data.rencana_pemulihan_usaha);
                    $('#tanggal').val(data.tanggal);

                    if (data.jenis_restrukturisasi) {
                        const jenisArray = JSON.parse(data.jenis_restrukturisasi);
                        jenisArray.forEach(jenis => {
                            $(`input[name="jenis_restrukturisasi[]"][value="${jenis}"]`).prop('checked',
                                true);
                            if (jenis === 'Lainnya' && data.jenis_restrukturisasi_lainnya) {
                                $('#jenis_restrukturisasi_lainnya').val(data
                                    .jenis_restrukturisasi_lainnya).prop('disabled', false);
                            }
                        });
                    }

                    $('#modalRestrukturisasi').modal('show');
                    $('#modalRestrukturisasiLabel').text('Edit Pengajuan Restrukturisasi');
                },
                error: function(xhr) {
                    showNotification('error', 'Gagal memuat data', xhr.responseJSON?.message ||
                        'Terjadi kesalahan');
                }
            });
        }
    </script>
@endpush
