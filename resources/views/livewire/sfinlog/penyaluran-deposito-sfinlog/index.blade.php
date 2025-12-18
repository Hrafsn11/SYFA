<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Penyaluran Deposito - SFinlog</h4>
                
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahPenyaluran" data-bs-toggle="modal" data-bs-target="#modalPenyaluranDepositoSfinlog">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Penyaluran</span>
                </button>
            
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
</div>

@push('scripts')
<script>
    let cleaveNominal;
    let flatpickrPengiriman;
    let flatpickrPengembalian;
    let currentIdForUpload;
    let currentIdForDelete;
    let nilaiInvestasiMax = 0;

    function showSuccessAlert(message) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-success'
            }
        });
    }

    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
    }

    function afterAction(payload) {
        Livewire.dispatch('refreshPenyaluranDepositoSfinlogTable');
        $('.modal').modal('hide');
    }

    function editDataDirect(button) {
        // Ambil data langsung dari data-attribute (instant, no AJAX!)
        const data = JSON.parse($(button).attr('data-item'));
        
        const modal = $('#modalPenyaluranDepositoSfinlog');
        const form = modal.find('form');

        // Clear validation errors
        modal.find('.form-control').removeClass('is-invalid');
        modal.find('.invalid-feedback').text('').hide();
        modal.find('.form-group').removeClass('is-invalid');

        // Set form action untuk update
        form.attr('wire:submit', `{!! $urlAction["update_penyaluran_deposito_sfinlog"] !!}`.replace('id_placeholder', data.id));
        
        // Update modal title dan button
        modal.find('.modal-title').html('Edit Penyaluran Deposito');
        modal.find('#btnHapusData').show();

        // Set ID untuk delete
        currentIdForDelete = data.id;

        // Set via Livewire terlebih dahulu untuk trigger updated methods
        @this.set('id_pengajuan_investasi_finlog', data.id_pengajuan_investasi_finlog);
        
        // Tunggu sebentar untuk Livewire process, lalu set select2
        setTimeout(async () => {
            $('#id_pengajuan_investasi_finlog').val(data.id_pengajuan_investasi_finlog).trigger('change');
            
            if (data.id_cells_project) {
                await @this.set('id_cells_project', data.id_cells_project);
                
                // Tunggu Livewire update availableProjects
                setTimeout(() => {
                    const projects = @this.availableProjects || [];
                    const $projectSelect = $('#id_project');
                    
                    $projectSelect.empty().append('<option value=""></option>');
                    
                    if (projects && projects.length > 0) {
                        projects.forEach(project => {
                            $projectSelect.append(new Option(project.nama_project, project.id_project, false, false));
                        });
                        $projectSelect.prop('disabled', false);
                        
                        // Set project value jika ada
                        if (data.id_project) {
                            $projectSelect.val(data.id_project).trigger('change');
                            @this.set('id_project', data.id_project);
                        }
                    } else {
                        $projectSelect.prop('disabled', true);
                    }
                    
                    $('#id_cells_project').val(data.id_cells_project).trigger('change');
                }, 400);
            }
            
        }, 300);

        flatpickrPengiriman.setDate(data.tanggal_pengiriman_dana);
        flatpickrPengembalian.setDate(data.tanggal_pengembalian);

        $('#nominal_yang_disalurkan').val(formatRupiah(data.nominal_yang_disalurkan));
        $('#nominal_raw').val(data.nominal_yang_disalurkan);

        // Update Livewire properties
        @this.set('id', data.id);
        @this.set('id_pengajuan_investasi_finlog', data.id_pengajuan_investasi_finlog);
        @this.set('id_cells_project', data.id_cells_project || null);
        @this.set('id_project', data.id_project || null);
        @this.set('nominal_yang_disalurkan', data.nominal_yang_disalurkan);
        @this.set('tanggal_pengiriman_dana', data.tanggal_pengiriman_dana);
        @this.set('tanggal_pengembalian', data.tanggal_pengembalian);

        modal.modal('show');
    }

    function uploadBukti(id) {
        currentIdForUpload = id;
        $('#formUploadBukti')[0].reset();
        $('#modalUploadBukti').modal('show');
    }

    function previewBukti(id, filePath, isImage) {
        const fullPath = '/storage/' + filePath;
        let content = '';
        
        if (isImage) {
            content = `<img src="${fullPath}" class="img-fluid" alt="Bukti Pengembalian">`;
        } else {
            content = `<iframe src="${fullPath}" style="width: 100%; height: 500px;" frameborder="0"></iframe>`;
        }
        
        $('#previewContent').html(content);
        $('#modalPreviewBukti').modal('show');
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
    
        $('#nominal_yang_disalurkan').on('input', function() {
            const rawValue = unformatRupiah($(this).val());
            $(this).val(formatRupiah(rawValue));
            $('#nominal_raw').val(rawValue);
            @this.set('nominal_yang_disalurkan', rawValue);
            
            const selectedOption = $('#id_pengajuan_investasi_finlog option:selected');
            const nilaiInvestasi = parseFloat(selectedOption.data('nilai-investasi')) || 0;
            const sisaDana = parseFloat(selectedOption.data('sisa-dana')) || 0;
            
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
                
                if (sisaDana > 0 && nilaiInvestasi > 0) {
                    $('#nilai-investasi-info').html(`
                        <div class="alert alert-info py-2 mt-2">
                            <small>
                                <strong>Nilai Investasi:</strong> Rp ${nilaiInvestasi.toLocaleString('id-ID')}<br>
                                <strong class="text-success">Sisa Dana Tersedia:</strong> Rp ${sisaDana.toLocaleString('id-ID')}
                            </small>
                        </div>
                    `);
                }
            }
        });

        flatpickrPengiriman = flatpickr("#tanggal_pengiriman_dana", {
            dateFormat: "Y-m-d",
            allowInput: true,
            onChange: function(selectedDates, dateStr) {
                @this.set('tanggal_pengiriman_dana', dateStr);
            }
        });

        flatpickrPengembalian = flatpickr("#tanggal_pengembalian", {
            dateFormat: "Y-m-d",
            allowInput: true,
            onChange: function(selectedDates, dateStr) {
                @this.set('tanggal_pengembalian', dateStr);
            }
        });

        $('#id_pengajuan_investasi_finlog').select2({
            dropdownParent: $('#modalPenyaluranDepositoSfinlog'),
            width: '100%',
            placeholder: 'Pilih No Kontrak',
            allowClear: true
        });

        $('#id_cells_project').select2({
            dropdownParent: $('#modalPenyaluranDepositoSfinlog'),
            width: '100%',
            placeholder: 'Pilih Cell Bisnis',
            allowClear: true
        });

        // Initialize select2 untuk project
        $('#id_project').select2({
            dropdownParent: $('#modalPenyaluranDepositoSfinlog'),
            width: '100%',
            placeholder: 'Pilih Project',
            allowClear: true
        }).on('change', function() {
            @this.set('id_project', $(this).val());
        });


        $('#id_pengajuan_investasi_finlog').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const nilaiInvestasi = selectedOption.data('nilai-investasi');
            const sisaDana = parseFloat(selectedOption.data('sisa-dana')) || 0;
            
            if (sisaDana !== undefined && nilaiInvestasi !== undefined) {
                nilaiInvestasiMax = sisaDana;  
                
                $('#nilai-investasi-info').html(`
                    <div class="alert alert-info py-2 mt-2">
                        <small>
                            <strong>Nilai Investasi:</strong> Rp ${parseFloat(nilaiInvestasi).toLocaleString('id-ID')}<br>
                            <strong class="text-success">Sisa Dana Tersedia:</strong> Rp ${sisaDana.toLocaleString('id-ID')}
                        </small>
                    </div>
                `);
            } else {
                nilaiInvestasiMax = 0;
                $('#nilai-investasi-info').html('');
            }
            
            // Set via Livewire - akan trigger updatedIdPengajuanInvestasiFinlog
            @this.set('id_pengajuan_investasi_finlog', $(this).val());
            
            // Tunggu Livewire update cell bisnis dan project
            setTimeout(async () => {
                const projects = @this.availableProjects || [];
                const $projectSelect = $('#id_project');
                
                $projectSelect.empty().append('<option value=""></option>');
                
                if (projects && projects.length > 0) {
                    projects.forEach(project => {
                        $projectSelect.append(new Option(project.nama_project, project.id_project, false, false));
                    });
                    $projectSelect.prop('disabled', false);
                } else {
                    $projectSelect.prop('disabled', true);
                }
                
                $projectSelect.trigger('change');
            }, 500);
        });

        // Handle perubahan cell bisnis - update dropdown project via Livewire event (backup)
        Livewire.on('updateProjects', (data) => {
            console.log('updateProjects event received:', data);
            const projects = (data && data.projects) ? data.projects : (Array.isArray(data) ? data : []);
            console.log('Projects to add:', projects);
            
            // Update dropdown project
            const $projectSelect = $('#id_project');
            $projectSelect.empty().append('<option value=""></option>');
            
            if (projects && projects.length > 0) {
                projects.forEach(project => {
                    $projectSelect.append(new Option(project.nama_project, project.id_project, false, false));
                });
                $projectSelect.prop('disabled', false);
            } else {
                $projectSelect.prop('disabled', true);
            }
            
            $projectSelect.trigger('change');
        });
        
        // Listen untuk perubahan availableProjects dari Livewire (reactive update)
        Livewire.on('availableProjectsUpdated', () => {
            // Field project akan di-update melalui event updateProjects
            // Tapi kita juga bisa langsung check dari DOM
            const projectSelect = $('#id_project');
            if (projectSelect.find('option').length > 1) {
                projectSelect.prop('disabled', false);
            }
        });

        $('#id_cells_project').on('change', async function() {
            const cellBisnisId = $(this).val();
            
            // Reset project dropdown
            $('#id_project').val('').trigger('change');
            
            // Update Livewire
            await @this.set('id_cells_project', cellBisnisId);
            
            // Tunggu Livewire update availableProjects
            setTimeout(async () => {
                // Get updated availableProjects from Livewire
                const projects = @this.availableProjects || [];
                
                console.log('Available projects from Livewire:', projects);
                
                // Update project dropdown
                const $projectSelect = $('#id_project');
                $projectSelect.empty().append('<option value=""></option>');
                
                if (projects && projects.length > 0) {
                    projects.forEach(project => {
                        $projectSelect.append(new Option(project.nama_project, project.id_project, false, false));
                    });
                    $projectSelect.prop('disabled', false);
                } else {
                    $projectSelect.prop('disabled', true);
                }
                
                $projectSelect.trigger('change');
            }, 300);
        });

        $('#id_project').on('change', function() {
            @this.set('id_project', $(this).val());
        });

    });

    $('#modalPenyaluranDepositoSfinlog').on('shown.bs.modal', function() {
        $('#id_pengajuan_investasi_finlog').focus();
        
        // Check jika cell bisnis sudah terisi, enable project field
        const cellBisnisValue = $('#id_cells_project').val();
        if (cellBisnisValue) {
            // Tunggu sebentar untuk memastikan availableProjects sudah terisi
            setTimeout(() => {
                const projectSelect = $('#id_project');
                if (projectSelect.find('option').length > 1) { // Lebih dari 1 karena ada option kosong
                    projectSelect.prop('disabled', false);
                }
            }, 300);
        }
    });

    $('#modalPenyaluranDepositoSfinlog').on('hidden.bs.modal', function() {
        // Reset form ke mode tambah
        $(this).find('form').attr('wire:submit', `{!! $urlAction["store_penyaluran_deposito_sfinlog"] !!}`);
        $(this).find('.modal-title').text('Tambah Penyaluran Deposito');
        $(this).find('#btnHapusData').hide();
        
        // Clear all fields
        $('#id_pengajuan_investasi_finlog').val('').trigger('change');
        $('#id_cells_project').val('').trigger('change');
        $('#id_project').val('').trigger('change');
        $('#nominal_yang_disalurkan').val('');
        $('#nominal_raw').val('');
        
        flatpickrPengiriman.clear();
        flatpickrPengembalian.clear();
        $('#nilai-investasi-info').html('');
        nilaiInvestasiMax = 0;
        currentIdForDelete = null;
        
        // Reset project dropdown
        $('#id_project').prop('disabled', true).val('').trigger('change');
        
        // Clear validation errors
        $('#modalPenyaluranDepositoSfinlog .form-control').removeClass('is-invalid');
        $('#modalPenyaluranDepositoSfinlog .invalid-feedback').text('').hide();
        $('#modalPenyaluranDepositoSfinlog .form-group').removeClass('is-invalid');
        
        // Reset Livewire state
        @this.set('id', null);
        @this.set('id_pengajuan_investasi_finlog', null);
        @this.set('id_cells_project', null);
        @this.set('id_project', null);
        @this.set('nominal_yang_disalurkan', null);
        @this.set('tanggal_pengiriman_dana', null);
        @this.set('tanggal_pengembalian', null);
    });

    $('#modalPenyaluranDepositoSfinlog').on('keyup change', '.form-control, .form-select', function() {
        $(this).removeClass('is-invalid');
        $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
    });

    $('#btnHapusData').on('click', function(e) {
        e.preventDefault();
        $('#modalPenyaluranDepositoSfinlog').modal('hide');
        $('#modalConfirmDelete').modal('show');
    });

    $('#btnConfirmDelete').on('click', function(e) {
        e.preventDefault();
        
        if (!currentIdForDelete) return;
        
        $('#deleteSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: '{{ route("sfinlog.penyaluran-deposito-sfinlog.destroy", ":id") }}'.replace(':id', currentIdForDelete),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalConfirmDelete').modal('hide');
                Livewire.dispatch('refreshPenyaluranDepositoSfinlogTable');
                
                showSuccessAlert(response.message || 'Data berhasil dihapus');
            },
            error: function(xhr) {
                showErrorAlert(xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data');
            },
            complete: function() {
                $('#deleteSpinner').addClass('d-none');
                $('#btnConfirmDelete').prop('disabled', false);
                currentIdForDelete = null;
            }
        });
    });

    $('#formUploadBukti').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentIdForUpload) return;
        
        const formData = new FormData(this);
        
        $('#uploadSpinner').removeClass('d-none');
        $(this).find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: '{{ route("sfinlog.penyaluran-deposito-sfinlog.upload-bukti", ":id") }}'.replace(':id', currentIdForUpload),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalUploadBukti').modal('hide');
                Livewire.dispatch('refreshPenyaluranDepositoSfinlogTable');
                
                showSuccessAlert(response.message || 'Bukti berhasil diupload');
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessages = Object.values(errors).flat().join('\n');
                    showErrorAlert(errorMessages);
                } else {
                    showErrorAlert(xhr.responseJSON?.message || 'Terjadi kesalahan saat upload');
                }
            },
            complete: function() {
                $('#uploadSpinner').addClass('d-none');
                $('#formUploadBukti button[type="submit"]').prop('disabled', false);
                currentIdForUpload = null;
            }
        });
    });
</script>
@endpush
