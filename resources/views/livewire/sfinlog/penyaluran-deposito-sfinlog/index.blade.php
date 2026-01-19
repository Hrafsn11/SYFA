<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Penyaluran Deposito - SFinlog</h4>

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
</div>

@push('scripts')
    <script>
        // =============================================
        // STATE & SELECTORS
        // =============================================
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
            
            console.log('updateProjectDropdown called with:', projects, 'selected:', selectedValue);
            
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
                console.log('Projects added:', projects.length);
            } else {
                $select.prop('disabled', true);
                console.log('No projects available');
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
        }

        function editDataDirect(button) {
            const encodedData = $(button).attr('data-item');
            const data = JSON.parse(atob(encodedData));
            const $modal = $(S.modal);
            
            console.log('Edit data:', data);
            
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

        function uploadBukti(id) {
            State.currentIdForUpload = id;
            $('#formUploadBukti')[0].reset();
            $('#modalUploadBukti').modal('show');
        }

        function previewBukti(id, filePath, isImage) {
            const fullPath = '/storage/' + filePath;
            $('#previewContent').html(isImage 
                ? `<img src="${fullPath}" class="img-fluid" alt="Bukti Pengembalian">`
                : `<iframe src="${fullPath}" style="width: 100%; height: 500px;" frameborder="0"></iframe>`
            );
            $('#modalPreviewBukti').modal('show');
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

        const doUpload = (formData) => {
            if (!State.currentIdForUpload) return;
            
            $('#uploadSpinner').removeClass('d-none');
            $('#formUploadBukti button[type="submit"]').prop('disabled', true);
            
            $.ajax({
                url: '{{ route('sfinlog.penyaluran-deposito-sfinlog.upload-bukti', ':id') }}'.replace(':id', State.currentIdForUpload),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: (res) => {
                    $('#modalUploadBukti').modal('hide');
                    Livewire.dispatch('refreshPenyaluranDepositoSfinlogTable');
                    showAlert('success', res.message || 'Bukti berhasil diupload');
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    showAlert('error', errors ? Object.values(errors).flat().join('\n') : (xhr.responseJSON?.message || 'Gagal upload'));
                },
                complete: () => {
                    $('#uploadSpinner').addClass('d-none');
                    $('#formUploadBukti button[type="submit"]').prop('disabled', false);
                    State.currentIdForUpload = null;
                }
            });
        };

        // =============================================
        // INITIALIZATION
        // =============================================
        $(document).ready(function() {
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
            $(S.nominal).on('input', function() {
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
            $(S.kontrak).on('change', function() {
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
            $(S.cellBisnis).on('change', function() {
                const val = $(this).val();
                console.log('Cell Bisnis changed to:', val);
                
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
            $(S.project).on('select2:select select2:clear', function(e) {
                const val = $(this).val();
                console.log('Project changed to:', val);
                @this.set('id_project', val || null);
            });
            
            // Livewire event listener for updateProjects
            Livewire.on('updateProjects', (payload) => {
                console.log('updateProjects event received:', payload);
                
                let projects = [];
                if (Array.isArray(payload)) {
                    projects = payload[0]?.projects || payload[0] || [];
                } else if (payload?.projects) {
                    projects = payload.projects;
                } else if (Array.isArray(payload)) {
                    projects = payload;
                }
                
                console.log('Parsed projects:', projects);
                
                const selectedProjectId = State.pendingProjectId || @this.id_project;
                updateProjectDropdown(projects, selectedProjectId);
                State.pendingProjectId = null;
            });
            
            // Modal events
            $(S.modal).on('hidden.bs.modal', resetModalUI);
            
            $(S.modal).on('keyup change', '.form-control, .form-select', function() {
                $(this).removeClass('is-invalid');
                $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
            });
            
            // Delete & Upload
            $('#btnHapusData').on('click', (e) => {
                e.preventDefault();
                $(S.modal).modal('hide');
                $('#modalConfirmDelete').modal('show');
            });
            
            $('#btnConfirmDelete').on('click', (e) => { e.preventDefault(); doDelete(); });
            
            $('#formUploadBukti').on('submit', function(e) {
                e.preventDefault();
                doUpload(new FormData(this));
            });
        });
    </script>
@endpush
