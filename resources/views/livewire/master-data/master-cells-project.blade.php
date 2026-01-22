<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">List Cells Project SFinlog</h4>
                @can('master_data.add')
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        data-bs-toggle="modal" data-bs-target="#modalTambahCellsProject" id="btnTambahCellsProject">
                        <i class="fa-solid fa-plus"></i>
                        Tambah List Cells Project SFinlog
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable">
                    <livewire:cells-project-table />
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Cells Project --}}
    <div class="modal fade" id="modalTambahCellsProject" wire:ignore>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCellsProjectLabel">Tambah List Cells Project SFinlog</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='{{ $urlAction['store_cells_project'] }}'>
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="nama_cells_bisnis" class="form-label">Nama Cells Bisnis <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_cells_bisnis"
                                placeholder="Masukkan Nama Cells Bisnis" wire:model.blur="nama_cells_bisnis">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="nama_pic" class="form-label">Nama PIC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pic" placeholder="Masukkan Nama PIC"
                                wire:model.blur="nama_pic">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="tanda_tangan_pic" class="form-label">Tanda Tangan PIC</label>
                            <input type="file" class="form-control" id="tanda_tangan_pic"
                                wire:model="tanda_tangan_pic" accept="image/*">
                            <div class="text-muted small mt-1">Upload gambar (JPG, PNG) maks 2MB.</div>

                            @if ($tanda_tangan_pic && !is_string($tanda_tangan_pic))
                                <div class="mt-2">
                                    <img src="{{ $tanda_tangan_pic->temporaryUrl() }}" alt="Preview"
                                        class="img-thumbnail" style="max-height: 100px">
                                </div>
                            @elseif ($tanda_tangan_pic && is_string($tanda_tangan_pic))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $tanda_tangan_pic) }}" alt="Current Signature"
                                        class="img-thumbnail" style="max-height: 100px">
                                </div>
                            @endif

                            <div class="invalid-feedback">
                                @error('tanda_tangan_pic')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="alamat" placeholder="Masukkan Alamat"
                                wire:model.blur="alamat">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="deskripsi_bidang" class="form-label">Deskripsi Bidang <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="deskripsi_bidang"
                                placeholder="Masukkan Deskripsi Bidang" wire:model.blur="deskripsi_bidang">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="profile_pict" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_pict" wire:model="profile_pict"
                                accept="image/*">
                            <div class="text-muted small mt-1">Upload gambar profil cells project (JPG, PNG) maks 2MB.
                            </div>

                            @if ($profile_pict && !is_string($profile_pict))
                                <div class="mt-2">
                                    <img src="{{ $profile_pict->temporaryUrl() }}" alt="Preview" class="img-thumbnail"
                                        style="max-height: 150px">
                                </div>
                            @elseif ($profile_pict && is_string($profile_pict))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $profile_pict) }}" alt="Current Profile"
                                        class="img-thumbnail" style="max-height: 150px">
                                </div>
                            @endif

                            <div class="invalid-feedback">
                                @error('profile_pict')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">Nama Project</label>
                                <button type="button" class="btn btn-sm btn-primary" id="btnTambahProject">
                                    <i class="fa-solid fa-plus"></i> Tambah Project
                                </button>
                            </div>
                            <div id="projectsContainer">
                                <!-- Projects will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanCellsProject">
                            <span class="spinner-border spinner-border-sm me-2" wire:loading
                                wire:target="saveData"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let projectCounter = 0;

        function addProjectInput(projectName = '') {
            projectCounter++;
            const projectHtml = `
                <div class="input-group mb-2 project-item" data-index="${projectCounter}">
                    <input type="text" class="form-control project-input" 
                           placeholder="Masukkan Nama Project" 
                           value="${projectName}">
                    <button type="button" class="btn btn-outline-danger btn-remove-project">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            $('#projectsContainer').append(projectHtml);
        }

        function collectProjects() {
            const projects = [];
            $('.project-input').each(function() {
                const value = $(this).val().trim();
                if (value) {
                    projects.push(value);
                }
            });
            return projects;
        }

        function clearProjects() {
            $('#projectsContainer').empty();
            projectCounter = 0;
        }

        $(document).ready(function() {
            // Add first project input on modal open
            $('#modalTambahCellsProject').on('shown.bs.modal', function() {
                if ($('#projectsContainer').children().length === 0) {
                    addProjectInput();
                }
            });

            // Add project button
            $(document).on('click', '#btnTambahProject', function() {
                addProjectInput();
            });

            // Remove project button
            $(document).on('click', '.btn-remove-project', function() {
                if ($('.project-item').length > 1) {
                    $(this).closest('.project-item').remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada satu project!',
                    });
                }
            });

            // Override form submit to include projects
            $('#modalTambahCellsProject form').on('submit', function(e) {
                const projects = collectProjects();
                @this.set('projects', projects);
            });
        });

        function afterAction(payload) {
            Livewire.dispatch('refreshCellsProjectTable');
            $('.modal').modal('hide');
        }

        function editData(payload) {
            const data = payload.data;

            const modal = $('.modal');
            const form = modal.find('form');

            form.attr('wire:submit', `{!! $urlAction['update_cells_project'] !!}`.replace('id_placeholder', data.id_cells_project));

            // ubah title modal
            modal.find('.modal-title').text('Edit List Cells Project SFinlog');

            // Set cells project data
            @this.set('id_cells_project', data.id_cells_project);
            @this.set('nama_cells_bisnis', data.nama_cells_bisnis);
            @this.set('nama_pic', data.nama_pic);
            @this.set('tanda_tangan_pic', data.tanda_tangan_pic); // Populate file path
            @this.set('profile_pict', data.profile_pict); // Populate profile picture path
            @this.set('alamat', data.alamat);
            @this.set('deskripsi_bidang', data.deskripsi_bidang);

            // Clear and populate projects
            clearProjects();
            if (data.projects && data.projects.length > 0) {
                data.projects.forEach(project => {
                    addProjectInput(project.nama_project);
                });
            } else {
                addProjectInput();
            }

            // tampilkan modal
            modal.modal('show');
        }

        $('.modal').on('hide.bs.modal', function() {
            $(this).find('form').attr('wire:submit', `{!! $urlAction['store_cells_project'] !!}`);
            $(this).find('.modal-title').text('Tambah List Cells Project SFinlog');
            @this.set('id_cells_project', null);
            @this.set('nama_cells_bisnis', '');
            @this.set('nama_pic', '');
            @this.set('tanda_tangan_pic', null);
            @this.set('profile_pict', null);
            // Reset input file value
            $('#tanda_tangan_pic').val('');
            $('#profile_pict').val('');
            @this.set('alamat', '');
            @this.set('deskripsi_bidang', '');
            @this.set('projects', []);
            clearProjects();
        });

        $(document).on('click', '.cells-project-delete-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            sweetAlertConfirm({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus Cells Project ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }, () => {
                @this.saveData("master-data.cells-project.destroy", {
                    "id": id,
                    "callback": "afterAction"
                });
            });
        });
    </script>
@endpush
