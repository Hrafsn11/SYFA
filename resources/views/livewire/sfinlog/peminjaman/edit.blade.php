<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Edit Peminjaman Dana - <span
                            class="">{{ $peminjaman->nomor_peminjaman }}</span></h4>
                </div>
                <a href="{{ route('sfinlog.peminjaman.detail', ['id' => $id_peminjaman]) }}"
                    class="btn btn-outline-primary">
                    <i class="ti ti-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="formEditPeminjamanDana" novalidate>
                <h5 class="mb-3">Informasi Perusahaan & Project</h5>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span
                                class="text-danger">*</span></label>
                        <input type="text" id="nama_perusahaan" class="form-control" placeholder="Nama Perusahaan"
                            wire:model="nama_perusahaan" readonly required>
                        @error('nama_perusahaan')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="id_cells_project" class="form-label">Pilih Cells Bisnis<span
                                class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="id_cells_project" name="id_cells_project" class="form-select select2"
                                data-placeholder="Pilih Project" required>
                                <option value=""></option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id_cells_project }}"
                                        {{ $id_cells_project == $project->id_cells_project ? 'selected' : '' }}>
                                        {{ $project->nama_cells_bisnis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_cells_project')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6  mb-3">
                        <label for="nama_project" class="form-label">Nama Project <span
                                class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="nama_project" name="nama_project" class="form-select select2"
                                data-placeholder="Pilih Project" required>
                                <option value=""></option>
                                @foreach ($availableProjects as $project)
                                    <option value="{{ $project['nama_project'] }}"
                                        {{ $nama_project == $project['nama_project'] ? 'selected' : '' }}>
                                        {{ $project['nama_project'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('nama_project')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="durasi_project" class="form-label">Durasi Project (Bulan) <span
                                class="text-danger">*</span></label>
                        <input type="number" id="durasi_project" class="form-control"
                            placeholder="Masukkan durasi bulan" wire:model="durasi_project" min="0" required>
                        @error('durasi_project')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="durasi_project_hari" class="form-label">Durasi Project (Hari) <span
                                class="text-danger">*</span></label>
                        <input type="number" id="durasi_project_hari" class="form-control"
                            placeholder="Masukkan durasi hari" wire:model="durasi_project_hari" min="0" required>
                        @error('durasi_project_hari')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nib_perusahaan" class="form-label">NIB Perusahaan <span
                                class="text-danger">*</span></label>
                        <input type="file" id="nib_perusahaan" class="form-control" wire:model.blur="nib_perusahaan"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        @if ($existing_nib_perusahaan)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_nib_perusahaan) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('nib_perusahaan')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Detail Pinjaman -->
                <h5 class="mb-3">Detail Pinjaman</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nilai_pinjaman" class="form-label">Nilai Pinjaman <span
                                class="text-danger">*</span></label>
                        <div wire:ignore>
                            <input type="text" id="nilai_pinjaman" class="form-control input-rupiah"
                                placeholder="Rp 0" required data-initial="{{ $nilai_pinjaman }}">
                        </div>
                        @error('nilai_pinjaman')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6  mb-3">
                        <label for="presentase_bagi_hasil" class="form-label">Persentase Bagi Hasil <span
                                class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="presentase_bagi_hasil" name="presentase_bagi_hasil"
                                class="form-select select2" data-placeholder="Pilih Persentase Bagi Hasil"
                                data-initial="{{ $presentase_bagi_hasil }}" required>
                                <option value=""></option>
                            </select>
                        </div>
                        @error('presentase_bagi_hasil')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nilai_bagi_hasil" class="form-label">Nilai Bagi Hasil <span
                                class="text-danger">*</span></label>
                        <div wire:ignore>
                            <input type="text" id="nilai_bagi_hasil" class="form-control input-rupiah"
                                placeholder="Rp 0" readonly disabled data-initial="{{ $nilai_bagi_hasil }}">
                        </div>
                        @error('nilai_bagi_hasil')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="total_pinjaman" class="form-label">Total Pinjaman <span
                                class="text-danger">*</span></label>
                        <div wire:ignore>
                            <input type="text" id="total_pinjaman" class="form-control input-rupiah"
                                placeholder="Rp 0" readonly disabled data-initial="{{ $total_pinjaman }}">
                        </div>
                        @error('total_pinjaman')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Jadwal Pencairan & Pengembalian -->
                <h5 class="mb-3">Jadwal Pencairan & Pengembalian</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harapan_tanggal_pencairan" class="form-label">Harapan Tanggal Pencairan <span
                                class="text-danger">*</span></label>
                        <div class="input-group" wire:ignore>
                            <input type="text" class="form-control bs-datepicker" placeholder="yyyy-mm-dd"
                                id="harapan_tanggal_pencairan" name="harapan_tanggal_pencairan"
                                value="{{ $harapan_tanggal_pencairan }}" required />
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                        </div>
                        @error('harapan_tanggal_pencairan')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="top" class="form-label">TOP (Term of Payment) <span
                                class="text-danger">*</span></label>
                        <input type="number" id="top" class="form-control" placeholder="Masukkan TOP (hari)"
                            wire:model="top" required>
                        @error('top')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg mb-3">
                        <label for="rencana_tanggal_pengembalian" class="form-label">Rencana Tanggal Pengembalian
                            <span class="text-danger">*</span></label>
                        <div class="input-group" wire:ignore>
                            <input type="text" class="form-control bs-datepicker" placeholder="yyyy-mm-dd"
                                id="rencana_tanggal_pengembalian" name="rencana_tanggal_pengembalian" disabled
                                value="{{ $rencana_tgl_pengembalian }}" required
                                wire:model.blur="rencana_tgl_pengembalian" />
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                        </div>
                        @error('rencana_tgl_pengembalian')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Dokumen Persyaratan -->
                <h5 class="mb-3">Dokumen Persyaratan</h5>
                <div class="alert alert-info mb-3">
                    <i class="ti ti-info-circle me-2"></i>
                    Upload file baru hanya jika ingin mengganti dokumen yang sudah ada.
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dokumen_mitra" class="form-label">Dokumen Mitra/Vendor yang Diusulkan
                            (Kontrak/PO/Invoice/PKS)</label>
                        <input type="file" id="dokumen_mitra" class="form-control"
                            wire:model.blur="dokumen_mitra" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                        @if ($existing_dokumen_mitra)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_dokumen_mitra) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('dokumen_mitra')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="form_new_customer" class="form-label">Form New Customer <span
                                class="text-danger">*</span></label>
                        <input type="file" id="form_new_customer" class="form-control"
                            wire:model.blur="form_new_customer" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                        @if ($existing_form_new_customer)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_form_new_customer) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('form_new_customer')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="dokumen_kerja_sama" class="form-label">Dokumen Kerja Sama <span
                                class="text-danger">*</span></label>
                        <input type="file" id="dokumen_kerja_sama" class="form-control"
                            wire:model.blur="dokumen_kerja_sama" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Format: PDF, DOC, DOCX (Max: 2MB)</small>
                        @if ($existing_dokumen_kerja_sama)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_dokumen_kerja_sama) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('dokumen_kerja_sama')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="dokumen_npa" class="form-label">Dokumen NPA <span
                                class="text-danger">*</span></label>
                        <input type="file" id="dokumen_npa" class="form-control" wire:model.blur="dokumen_npa"
                            accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <small class="text-muted">Format: PDF, DOC, DOCX, XLS, XLSX (Max: 2MB)</small>
                        @if ($existing_dokumen_npa)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_dokumen_npa) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('dokumen_npa')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="akta_perusahaan" class="form-label">Akta Perusahaan</label>
                        <input type="file" id="akta_perusahaan" class="form-control"
                            wire:model.blur="akta_perusahaan" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        @if ($existing_akta_perusahaan)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_akta_perusahaan) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('akta_perusahaan')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ktp_owner" class="form-label">KTP Owner Perusahaan</label>
                        <input type="file" id="ktp_owner" class="form-control" wire:model.blur="ktp_owner"
                            accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                        @if ($existing_ktp_owner)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_ktp_owner) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('ktp_owner')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ktp_pic" class="form-label">KTP PIC <span class="text-danger">*</span></label>
                        <input type="file" id="ktp_pic" class="form-control" wire:model.blur="ktp_pic"
                            accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                        @if ($existing_ktp_pic)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_ktp_pic) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('ktp_pic')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="surat_izin_usaha" class="form-label">Surat Izin Lokasi Usaha</label>
                        <input type="file" id="surat_izin_usaha" class="form-control"
                            wire:model.blur="surat_izin_usaha" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        @if ($existing_surat_izin_usaha)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $existing_surat_izin_usaha) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="ti ti-eye me-1"></i> Lihat File Saat Ini
                                </a>
                            </div>
                        @endif
                        @error('surat_izin_usaha')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Catatan -->
                <h5 class="mb-3">Catatan</h5>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="catatan" class="form-label">Catatan Lainnya</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4"
                            placeholder="Masukkan catatan tambahan (opsional)" wire:model.blur="catatan">{{ $catatan }}</textarea>
                        @error('catatan')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('sfinlog.peminjaman.detail', ['id' => $id_peminjaman]) }}"
                            class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                            <i class="ti ti-device-floppy me-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let cleaveInstances = {};
            const cleaveConfig = {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: 0,
                prefix: 'Rp ',
                rawValueTrimPrefix: true,
                noImmediatePrefix: false
            };

            // Helper functions
            const initCleave = () => ['nilai_pinjaman', 'nilai_bagi_hasil', 'total_pinjaman'].forEach(id => {
                if (cleaveInstances[id]) cleaveInstances[id].destroy();
                cleaveInstances[id] = new Cleave(`#${id}`, cleaveConfig);

                // Set initial value if exists
                const initialValue = $(`#${id}`).data('initial');
                if (initialValue) {
                    cleaveInstances[id].setRawValue(initialValue);
                }
            });
            const getCleave = (id) => parseInt(cleaveInstances[id]?.getRawValue()) || 0;
            const setCleave = (id, val) => cleaveInstances[id]?.setRawValue(val);
            const syncCalculatedFields = () => {
                @this.set('nilai_bagi_hasil', getCleave('nilai_bagi_hasil'));
                @this.set('total_pinjaman', getCleave('total_pinjaman'));
            };

            // Initialize
            initCleave();
            $('.bs-datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto'
            });

            $('#id_cells_project').select2({
                    placeholder: 'Pilih Cells Bisnis',
                    allowClear: true
                })
                .on('change', async function() {
                    const cellsProjectId = $(this).val();

                    // Reset nama_project dropdown
                    $('#nama_project').val('').trigger('change');

                    // Update Livewire
                    await @this.set('id_cells_project', cellsProjectId);

                    // Get updated availableProjects from Livewire
                    const projects = @this.availableProjects || [];

                    // Update nama_project dropdown
                    const $namaProject = $('#nama_project');
                    $namaProject.empty().append('<option value=""></option>');

                    projects.forEach(project => {
                        $namaProject.append(new Option(project.nama_project, project.nama_project,
                            false, false));
                    });

                    $namaProject.trigger('change');
                });

            $('#nama_project').select2({
                placeholder: 'Pilih Project',
                allowClear: true
            }).on('change', function() {
                @this.set('nama_project', $(this).val());
            });

            // Initialize presentase_bagi_hasil with options and set initial value
            const initialPresentase = $('#presentase_bagi_hasil').data('initial');
            $('#presentase_bagi_hasil').select2({
                placeholder: 'Pilih Persentase Bagi Hasil',
                allowClear: true,
                data: [{
                    id: '1',
                    text: '1%'
                }, {
                    id: '2',
                    text: '2%'
                }, {
                    id: '3',
                    text: '3%'
                }]
            }).on('change', function() {
                @this.set('presentase_bagi_hasil', $(this).val());
            });

            if (initialPresentase) {
                $('#presentase_bagi_hasil').val(initialPresentase).trigger('change');
            }

            // Event handlers
            $('#harapan_tanggal_pencairan').on('changeDate', function() {
                @this.set('harapan_tanggal_pencairan', $(this).val());
                hitungTanggalPengembalian();
            });
            $('#rencana_tanggal_pengembalian').on('changeDate', function() {
                @this.set('rencana_tgl_pengembalian', $(this).val());
            });
            $('#nilai_pinjaman').on('blur', function() {
                @this.set('nilai_pinjaman', getCleave('nilai_pinjaman'));
            });

            // Calculations
            const hitungBagiHasil = () => {
                const nilaiPinjaman = getCleave('nilai_pinjaman');
                const persentaseBagiHasil = parseFloat($('#presentase_bagi_hasil').val()) || 0;

                if (nilaiPinjaman > 0 && persentaseBagiHasil > 0) {
                    const bagiHasilTotal = Math.round(nilaiPinjaman * persentaseBagiHasil / 100);
                    const totalPinjaman = nilaiPinjaman + bagiHasilTotal;
                    setCleave('nilai_bagi_hasil', bagiHasilTotal);
                    setCleave('total_pinjaman', totalPinjaman);
                    syncCalculatedFields();
                } else {
                    setCleave('nilai_bagi_hasil', 0);
                    setCleave('total_pinjaman', 0);
                    syncCalculatedFields();
                }
            };

            const hitungTanggalPengembalian = () => {
                const tanggalPencairan = $('#harapan_tanggal_pencairan').val();
                const top = parseInt($('#top').val()) || 0;

                if (tanggalPencairan && top > 0) {
                    const datePencairan = new Date(tanggalPencairan);
                    datePencairan.setDate(datePencairan.getDate() + top);
                    const tanggalPengembalian = datePencairan.toISOString().split('T')[0];
                    $('#rencana_tanggal_pengembalian').datepicker('setDate', tanggalPengembalian);
                    @this.set('rencana_tgl_pengembalian', tanggalPengembalian);
                } else {
                    $('#rencana_tanggal_pengembalian').datepicker('clearDates');
                    @this.set('rencana_tgl_pengembalian', '');
                }
            };

            $('#nilai_pinjaman, #durasi_project').on('input', hitungBagiHasil);
            $('#presentase_bagi_hasil').on('change', hitungBagiHasil);
            $('#top').on('input', hitungTanggalPengembalian);

            // Form submit
            $('#formEditPeminjamanDana').on('submit', function(e) {
                e.preventDefault();

                // Sync all Cleave values before submit
                @this.set('nilai_pinjaman', getCleave('nilai_pinjaman'));
                @this.set('nilai_bagi_hasil', getCleave('nilai_bagi_hasil'));
                @this.set('total_pinjaman', getCleave('total_pinjaman'));

                // Show confirmation
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Apakah Anda yakin ingin menyimpan perubahan peminjaman dana ini?',
                    icon: 'question',
                    showConfirmButton: true,
                    showCancelButton: true,
                    showDenyButton: false,
                    showCloseButton: false,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('saveData', 'sfinlog.peminjaman.update', {
                            callback: 'afterAction',
                            id: '{{ $id_peminjaman }}'
                        });
                    }
                });
            });
        });

        function afterAction(payload) {
            if (payload.error === false) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: payload.message || 'Perubahan peminjaman dana berhasil disimpan.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "{{ route('sfinlog.peminjaman.detail', ['id' => $id_peminjaman]) }}";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: payload.message || 'Terjadi kesalahan saat menyimpan data.'
                });
            }
        }
    </script>
@endpush
