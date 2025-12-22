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
        }

        function initCheckboxLainnya() {
            const $checkbox = $('#checkLainnya');
            const $input = $('#inputLainnya');

            if ($checkbox.length && $input.length) {
                $checkbox.off('change.lainnya').on('change.lainnya', function() {
                    const isChecked = this.checked;
                    $input.prop('disabled', !isChecked);

                    if (isChecked) {
                        $input.removeClass('disabled').focus();
                    } else {
                        $input.addClass('disabled').val('');
                    }
                });

                if ($checkbox.is(':checked')) {
                    $input.prop('disabled', false);
                }
            }
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

            $('#id_pengajuan_peminjaman, #nomor_kontrak_pembiayaan_value, #jatuh_tempo_terakhir_value').val('');
            $('#jatuh_tempo_terakhir').val('-');
            $('#jumlah_plafon_awal, #sisa_pokok_belum_dibayar, #tunggakan_margin_bunga, .input-rupiah').val('Rp 0');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false).closest('.custom-option')
                .removeClass('checked');
            $('#checkLainnya').prop('checked', false);
            $('#inputLainnya').prop('disabled', true).addClass('disabled').val('');
            $('input[name="jenis_restrukturisasi[]"]').prop('checked', false);
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

            // Handle jenis_restrukturisasi checkboxes
            formData.delete('jenis_restrukturisasi[]');
            $('input[name="jenis_restrukturisasi[]"]:checked').each(function() {
                formData.append('jenis_restrukturisasi[]', $(this).val());
            });

            // Handle jenis_pembiayaan radio button
            const jenisPembiayaan = $('input[name="jenis_pembiayaan_radio"]:checked').val();
            if (jenisPembiayaan) {
                formData.append('jenis_pembiayaan', jenisPembiayaan);
            }

            // Clean rupiah format fields (readonly fields - just extract numbers)
            const rupiahFields = ['jumlah_plafon_awal', 'sisa_pokok_belum_dibayar', 'tunggakan_margin_bunga'];
            rupiahFields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    const rawValue = element.value.replace(/[^0-9]/g, '');

                    if (rawValue && rawValue !== 0 && rawValue !== '0') {
                        formData.set(field, rawValue.toString());
                    }
                }
            });

            // Handle jatuh_tempo_terakhir - use hidden value (Y-m-d format for database)
            const jatuhTempoValue = document.getElementById('jatuh_tempo_terakhir_value');
            if (jatuhTempoValue && jatuhTempoValue.value) {
                formData.set('jatuh_tempo_terakhir', jatuhTempoValue.value);
            }

            const url = editMode ?
                "{{ route('pengajuan-restrukturisasi.update', ':id') }}".replace(':id', editId) :
                "{{ route('pengajuan-restrukturisasi.store') }}";

            if (editMode) {
                formData.append('_method', 'PUT');
            }

            // Show loading state
            const $submitBtn = $('#btnSubmitRestrukturisasi');
            const originalBtnText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

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
                    // Reset button state
                    $submitBtn.prop('disabled', false).html(originalBtnText);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};
                        displayValidationErrors(errors);
                        showValidationErrorSummary(errors);
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
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let errorCount = 0;
            $.each(errors, function(field, messages) {
                const errorMessage = Array.isArray(messages) ? messages[0] : messages;
                const $field = $('#' + field);

                if ($field.length) {
                    $field.addClass('is-invalid');
                    errorCount++;

                    // Handle file inputs with wrapper
                    const $wrapper = $field.closest('.file-upload-wrapper');
                    if ($wrapper.length) {
                        $wrapper.find('.invalid-feedback').remove();
                        const $errorSpan = $('<span class="invalid-feedback d-block"></span>').text(errorMessage);
                        $wrapper.append($errorSpan);
                    } else {
                        // Handle regular inputs
                        $field.closest('.form-group').find('.invalid-feedback').remove();
                        const $errorSpan = $('<span class="invalid-feedback d-block"></span>').text(errorMessage);

                        // For input-group, place error after the group
                        const $inputGroup = $field.closest('.input-group');
                        if ($inputGroup.length) {
                            $inputGroup.after($errorSpan);
                        } else {
                            $field.after($errorSpan);
                        }
                    }
                }
            });

            // Scroll to first error with wizard step navigation
            if (errorCount > 0) {
                const $firstError = $('.is-invalid').first();
                if ($firstError.length) {
                    // Find which step contains the error
                    const $errorStep = $firstError.closest('.content');
                    if ($errorStep.length) {
                        const stepIndex = $errorStep.index();
                        if (wizardRestrukturisasi && stepIndex >= 0) {
                            wizardRestrukturisasi.to(stepIndex + 1);
                        }
                    }

                    setTimeout(() => {
                        $firstError[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        $firstError.focus();
                    }, 300);
                }
            }
        }

        function showValidationErrorSummary(errors) {
            const errorCount = Object.keys(errors).length;
            let errorList = '<div class="text-start"><ul class="mb-0">';

            let count = 0;
            $.each(errors, function(field, messages) {
                if (count < 5) { // Show max 5 errors in summary
                    const errorMessage = Array.isArray(messages) ? messages[0] : messages;
                    const fieldLabel = getFieldLabel(field);
                    errorList += `<li class="mb-1"><strong>${fieldLabel}:</strong> ${errorMessage}</li>`;
                    count++;
                }
            });

            if (errorCount > 5) {
                errorList += `<li class="text-muted">...dan ${errorCount - 5} error lainnya</li>`;
            }

            errorList += '</ul></div>';

            showSweetAlert({
                title: `Terdapat ${errorCount} Kesalahan Validasi`,
                text: errorList,
                icon: 'error'
            });
        }

        function getFieldLabel(field) {
            const labels = {
                'id_debitur': 'Debitur',
                'id_pengajuan_peminjaman': 'Nomor Kontrak',
                'nama_perusahaan': 'Nama Perusahaan',
                'nama_pic': 'Nama PIC',
                'jabatan_pic': 'Jabatan PIC',
                'nomor_kontrak_pembiayaan': 'Nomor Kontrak Pembiayaan',
                'tanggal_akad': 'Tanggal Akad',
                'jenis_pembiayaan': 'Jenis Pembiayaan',
                'alasan_restrukturisasi': 'Alasan Restrukturisasi',
                'jenis_restrukturisasi': 'Jenis Restrukturisasi',
                'rencana_pemulihan_usaha': 'Rencana Pemulihan Usaha',
                'dokumen_ktp_pic': 'KTP PIC',
                'dokumen_npwp_perusahaan': 'NPWP Perusahaan',
                'dokumen_laporan_keuangan': 'Laporan Keuangan',
                'dokumen_arus_kas': 'Arus Kas',
                'dokumen_kontrak_pembiayaan': 'Kontrak Pembiayaan',
                'dokumen_tanda_tangan': 'Tanda Tangan'
            };
            return labels[field] || field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }



        $('#modalRestrukturisasi').on('click', '.btn-next', function(e) {
            e.preventDefault();
            wizardRestrukturisasi.next();
        }).on('click', '.btn-prev', function(e) {
            e.preventDefault();
            wizardRestrukturisasi.previous();
        }).on('click', '#btnSubmitRestrukturisasi', function(e) {
            e.preventDefault();
            handleSubmit();
        });

        // Helper function to format rupiah
        function formatRupiah(number) {
            if (!number) return '';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function initializeModalPlugins() {
            // Initialize Select2
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

                // Only load data if NOT in edit mode
                if (value && !editMode) {
                    loadPengajuanData(value);
                } else if (!value && !editMode) {
                    // Reset jenis pembiayaan and auto-fill fields only if not edit mode
                    $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false);
                    $('.custom-option').addClass('disabled').removeClass('checked');
                    $('#jumlah_plafon_awal, #sisa_pokok_belum_dibayar, #tunggakan_margin_bunga').val('Rp 0');
                    $('#jatuh_tempo_terakhir').val('-');
                    $('#jatuh_tempo_terakhir_value').val('');
                    $('#id_pengajuan_peminjaman').val('');
                    $('#nomor_kontrak_pembiayaan_value').val('');
                }
            });

            // Initialize Flatpickr
            if (typeof window.initFlatpickr === 'function') {
                window.initFlatpickr();
            }

            // Initialize Cleave.js for Rupiah format
            if (typeof window.initCleaveRupiah === 'function') {
                $('.input-rupiah').each(function() {
                    this.dataset.cleaveInitialized = 'false';
                });
                window.initCleaveRupiah();
            }

            // Initialize checkbox Lainnya handler
            initCheckboxLainnya();
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

                    // Reset all radio buttons and mark as disabled
                    $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false);
                    $('.custom-option').addClass('disabled').removeClass('checked');

                    // Find and check the matching jenis_pembiayaan (but keep it disabled)
                    if (data.jenis_pembiayaan) {
                        const $matchedRadio = $('input[name="jenis_pembiayaan_radio"][value="' + data
                            .jenis_pembiayaan + '"]');
                        if ($matchedRadio.length) {
                            $matchedRadio.prop('checked', true);
                            $matchedRadio.closest('.custom-option').addClass('checked').css('opacity', '1');
                        }
                    }

                    // Set plafon and sisa pokok values (readonly fields - use manual formatting)
                    $('#jumlah_plafon_awal').val(formatRupiah(data.jumlah_plafon_awal));
                    $('#sisa_pokok_belum_dibayar').val(formatRupiah(data.sisa_pokok_belum_dibayar));

                    // Set tunggakan margin/bunga (readonly field - use manual formatting)
                    $('#tunggakan_margin_bunga').val(formatRupiah(data.tunggakan_margin_bunga));

                    // Set jatuh tempo terakhir (readonly field - display formatted, store actual date)
                    if (data.jatuh_tempo_terakhir_formatted) {
                        $('#jatuh_tempo_terakhir').val(data.jatuh_tempo_terakhir_formatted);
                        $('#jatuh_tempo_terakhir_value').val(data.jatuh_tempo_terakhir);
                    } else {
                        $('#jatuh_tempo_terakhir').val('-');
                        $('#jatuh_tempo_terakhir_value').val('');
                    }

                    // Set status DPD (auto-calculated)
                    if (data.status_dpd !== undefined) {
                        $('#status_dpd').val(data.status_dpd);
                    } else {
                        $('#status_dpd').val(0);
                    }
                },
                error: function(xhr) {
                    showSweetAlert({
                        title: 'Error',
                        text: 'Gagal memuat data pengajuan',
                        icon: 'error'
                    });
                }
            });
        }

        function setupEventListeners() {
            // Modal shown event
            $('#modalRestrukturisasi').on('shown.bs.modal', function() {
                initializeModalPlugins();
            });

            // Modal hide event
            $('#modalRestrukturisasi').on('hide.bs.modal', function() {
                resetWizard();

                const $select2 = $('#nomor_kontrak_pembiayaan');
                if ($select2.hasClass('select2-hidden-accessible')) {
                    $select2.select2('destroy');
                }

                $('.input-rupiah').each(function() {
                    this.dataset.cleaveInitialized = 'false';
                });
            });
        }

        function editPengajuan(id) {
            editMode = true;
            editId = id;

            $.ajax({
                url: `/pengajuan-restrukturisasi/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    const data = response.data;

                    // Reset wizard first
                    wizardRestrukturisasi.reset();

                    // Step 1: Identitas Debitur
                    $('#nama_perusahaan').val(data.nama_perusahaan);
                    $('#npwp').val(data.npwp);
                    $('#nama_pic').val(data.nama_pic);
                    $('#no_hp_pic').val(data.no_hp_pic);
                    $('#jabatan_pic').val(data.jabatan_pic);
                    $('#alamat').val(data.alamat);

                    // Step 2: Data Pembiayaan
                    // Set nomor kontrak pembiayaan to select2
                    if (data.id_pengajuan_peminjaman) {
                        $('#nomor_kontrak_pembiayaan').val(data.id_pengajuan_peminjaman).trigger('change');
                    }

                    $('#nomor_kontrak_pembiayaan_value').val(data.nomor_kontrak_pembiayaan);
                    $('#id_pengajuan_peminjaman').val(data.id_pengajuan_peminjaman);

                    // Set tanggal akad
                    $('#tanggal_akad').val(data.tanggal_akad);

                    // Set jenis pembiayaan radio (disabled state)
                    $('.jenis-pembiayaan-radio').prop('disabled', true).prop('checked', false);
                    $('.custom-option').addClass('disabled').removeClass('checked');

                    if (data.jenis_pembiayaan) {
                        $('.jenis-pembiayaan-radio').each(function() {
                            const $radio = $(this);
                            const $option = $radio.closest('.custom-option');
                            if ($radio.val() === data.jenis_pembiayaan) {
                                $radio.prop('checked', true);
                                $option.addClass('checked').css('opacity', '1');
                            }
                        });
                    }

                    // Format rupiah fields (readonly)
                    $('#jumlah_plafon_awal').val(formatRupiah(data.jumlah_plafon_awal));
                    $('#sisa_pokok_belum_dibayar').val(formatRupiah(data.sisa_pokok_belum_dibayar));
                    $('#tunggakan_margin_bunga').val(formatRupiah(data.tunggakan_margin_bunga));

                    // Set jatuh tempo terakhir (convert to Indonesian format)
                    if (data.jatuh_tempo_terakhir) {
                        const jatuhTempoDate = new Date(data.jatuh_tempo_terakhir);
                        const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        const formatted = jatuhTempoDate.getDate() + ' ' +
                            bulanIndo[jatuhTempoDate.getMonth()] + ' ' +
                            jatuhTempoDate.getFullYear();
                        $('#jatuh_tempo_terakhir').val(formatted);
                        $('#jatuh_tempo_terakhir_value').val(data.jatuh_tempo_terakhir);
                    }

                    $('#status_dpd').val(data.status_dpd);
                    $('#alasan_restrukturisasi').val(data.alasan_restrukturisasi);

                    // Step 3: Permohonan Restrukturisasi
                    if (data.jenis_restrukturisasi) {
                        // Data sudah berupa array dari Laravel cast, tidak perlu JSON.parse()
                        const jenisArray = Array.isArray(data.jenis_restrukturisasi) ?
                            data.jenis_restrukturisasi :
                            JSON.parse(data.jenis_restrukturisasi);

                        jenisArray.forEach(jenis => {
                            $(`input[name="jenis_restrukturisasi[]"][value="${jenis}"]`).prop('checked',
                                true);
                        });

                        // Handle "Lainnya" checkbox
                        if (jenisArray.includes('Lainnya') && data.jenis_restrukturisasi_lainnya) {
                            $('#checkLainnya').prop('checked', true);
                            $('#inputLainnya').val(data.jenis_restrukturisasi_lainnya)
                                .prop('disabled', false)
                                .removeClass('disabled');
                        }
                    }

                    $('#rencana_pemulihan_usaha').val(data.rencana_pemulihan_usaha);

                    // Step 4: Dokumen Pendukung
                    $('#tanggal').val(data.tanggal);
                    $('#tempat').val(data.tempat);

                    // Show modal and initialize plugins
                    $('#modalRestrukturisasi').modal('show');
                    $('#modalRestrukturisasiTitle').text('Edit Pengajuan Restrukturisasi');

                    // Initialize modal plugins after modal is shown
                    setTimeout(function() {
                        initializeModalPlugins();

                        // Re-set select2 value after initialization
                        if (data.id_pengajuan_peminjaman) {
                            $('#nomor_kontrak_pembiayaan').val(data.id_pengajuan_peminjaman).trigger(
                                'change');
                        }
                    }, 300);
                },
                error: function(xhr) {
                    showSweetAlert({
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Gagal memuat data pengajuan',
                        icon: 'error'
                    });
                }
            });
        }
    </script>
@endpush
