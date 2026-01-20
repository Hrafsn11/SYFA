{{-- Modal Tambah/Edit Penyaluran Deposito SFinlog --}}
<div class="modal fade" id="modalPenyaluranDepositoSfinlog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenyaluranDepositoSfinlogLabel">Tambah Penyaluran Deposito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPenyaluranDepositoSfinlog"
                wire:submit="{{ $urlAction['store_penyaluran_deposito_sfinlog'] }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- No Kontrak -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_pengajuan_investasi_finlog" class="form-label">
                                No Kontrak <span class="text-danger">*</span>
                            </label>
                            <select id="id_pengajuan_investasi_finlog" class="form-select select2"
                                wire:model="id_pengajuan_investasi_finlog" data-placeholder="Pilih No Kontrak">
                                <option value=""></option>
                                @foreach ($pengajuanInvestasiFinlog as $item)
                                    @php
                                        // Hitung sisa dana dari pengajuan investasi finlog
                                        $totalDisalurkan = \App\Models\PenyaluranDepositoSfinlog::where('id_pengajuan_investasi_finlog', $item->id_pengajuan_investasi_finlog)
                                            ->sum('nominal_yang_disalurkan');
                                        $sisaDana = $item->nominal_investasi - $totalDisalurkan;
                                        $firstProject = $item->project && $item->project->projects ? $item->project->projects->first() : null;
                                    @endphp
                                    <option value="{{ $item->id_pengajuan_investasi_finlog }}"
                                        data-nilai-investasi="{{ $item->nominal_investasi }}"
                                        data-sisa-dana="{{ $sisaDana }}"
                                        data-id-cells-project="{{ $item->id_cells_project }}"
                                        data-id-project="{{ $firstProject ? $firstProject->id_project : '' }}">
                                        {{ $item->nomor_kontrak }} - {{ $item->nama_investor }} (Sisa Dana: Rp
                                        {{ number_format($sisaDana, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Cell Bisnis -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_cells_project" class="form-label">
                                Cell Bisnis <span class="text-danger">*</span>
                            </label>
                            <select id="id_cells_project" class="form-select select2" wire:model="id_cells_project"
                                data-placeholder="Pilih Cell Bisnis">
                                <option value=""></option>
                                @foreach ($cellsProject as $item)
                                    <option value="{{ $item->id_cells_project }}">{{ $item->nama_cells_bisnis }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Project -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_project" class="form-label">
                                Project <span class="text-danger">*</span>
                            </label>
                            <div wire:ignore>
                                <select id="id_project" class="form-select select2" data-placeholder="Pilih Project">
                                    <option value=""></option>
                                    @if(!empty($availableProjects))
                                        @foreach ($availableProjects as $project)
                                            <option value="{{ $project['id_project'] }}">{{ $project['nama_project'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Nominal yang Disalurkan -->
                        <div class="col-12 mb-3 form-group">
                            <label for="nominal_yang_disalurkan" class="form-label">
                                Nominal yang Disalurkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_yang_disalurkan"
                                placeholder="Ketik angka saja (contoh: 10000000)" autocomplete="off">
                            <input type="hidden" id="nominal_raw" wire:model="nominal_yang_disalurkan">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tanggal Pengiriman Dana -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="tanggal_pengiriman_dana" class="form-label">
                                Tanggal Pengiriman Dana <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tanggal_pengiriman_dana"
                                placeholder="Pilih tanggal" wire:model="tanggal_pengiriman_dana">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tanggal Pengembalian -->
                        <div class="col-md-6 mb-3 form-group">
                            <label for="tanggal_pengembalian" class="form-label">
                                Tanggal Pengembalian <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tanggal_pengembalian"
                                placeholder="Pilih tanggal" wire:model="tanggal_pengembalian">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapusData" style="display: none;">
                        <i class="ti ti-trash me-1"></i> Hapus Data
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-2" wire:loading wire:target="saveData"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Input Pengembalian --}}
<div wire:ignore.self class="modal fade" id="modalInputPengembalian" tabindex="-1"
    aria-labelledby="modalInputPengembalianLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInputPengembalianLabel">Input Pengembalian Dana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pengembalian_id">
                <input type="hidden" id="pengembalian_nominal_disalurkan_raw">
                <input type="hidden" id="pengembalian_nominal_dikembalikan_raw">

                <div class="row">
                    <!-- Cell Bisnis -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cell Bisnis</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_cell_bisnis" readonly>
                    </div>

                    <!-- Project -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Project</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_project" readonly>
                    </div>

                    <!-- Tanggal Pengiriman -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pengiriman</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_tgl_pengiriman" readonly>
                    </div>

                    <!-- Tanggal Pengembalian -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pengembalian</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_tgl_pengembalian" readonly>
                    </div>

                    <!-- Nominal Disalurkan -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nominal Disalurkan</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_nominal_disalurkan" readonly>
                    </div>

                    <!-- Nominal yang Dikembalikan -->
                    <div class="col-md-6 mb-3 form-group">
                        <label class="form-label">Nominal yang Dikembalikan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pengembalian_nominal_dikembalikan"
                            placeholder="Rp 0" autocomplete="off">
                        <div id="pengembalian_validation_error" class="invalid-feedback"></div>
                    </div>

                    <!-- Sisa yang Belum Dikembalikan -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Sisa yang Belum Dikembalikan</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_sisa" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanPengembalian">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="pengembalian_spinner"></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirm Delete --}}
<div class="modal fade" id="modalConfirmDelete">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="deleteSpinner"></span>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // ===== HANDLER UNTUK INPUT PENGEMBALIAN SFINLOG =====
        let cleavePengembalianNominal;

        /**
         * Open Modal Input Pengembalian
         */
        function openInputPengembalian(id, cellBisnis, projectName, nominal_disalurkan, nominal_dikembalikan, tgl_pengiriman, tgl_pengembalian) {
            console.log('Opening Input Pengembalian modal...', { id, cellBisnis, projectName, nominal_disalurkan });

            // Destroy previous Cleave instance first
            if (cleavePengembalianNominal) {
                cleavePengembalianNominal.destroy();
                cleavePengembalianNominal = null;
            }

            // Clear all inputs FIRST
            document.getElementById('pengembalian_nominal_dikembalikan').value = '';
            document.getElementById('pengembalian_nominal_dikembalikan_raw').value = '';
            document.getElementById('pengembalian_sisa').value = '';

            // Remove validation classes
            document.getElementById('pengembalian_nominal_dikembalikan').classList.remove('is-invalid');
            document.getElementById('pengembalian_validation_error').textContent = '';

            // Set readonly data
            document.getElementById('pengembalian_id').value = id;
            document.getElementById('pengembalian_cell_bisnis').value = cellBisnis || '-';
            document.getElementById('pengembalian_project').value = projectName || '-';
            document.getElementById('pengembalian_tgl_pengiriman').value = tgl_pengiriman ? new Date(tgl_pengiriman).toLocaleDateString('id-ID') : '-';
            document.getElementById('pengembalian_tgl_pengembalian').value = tgl_pengembalian ? new Date(tgl_pengembalian).toLocaleDateString('id-ID') : '-';

            // Set nominal disalurkan
            document.getElementById('pengembalian_nominal_disalurkan_raw').value = nominal_disalurkan;
            document.getElementById('pengembalian_nominal_disalurkan').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(nominal_disalurkan);

            // Initialize Cleave untuk input nominal dikembalikan
            cleavePengembalianNominal = new Cleave('#pengembalian_nominal_dikembalikan', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                prefix: 'Rp ',
                rawValueTrimPrefix: true,
                onValueChanged: function (e) {
                    const rawValue = parseFloat(e.target.rawValue) || 0;
                    document.getElementById('pengembalian_nominal_dikembalikan_raw').value = rawValue;

                    // Calculate sisa
                    updatePengembalianCalculation();
                }
            });

            // Set nominal dikembalikan yang sudah ada (jika ada) - AFTER Cleave initialized
            const currentNominal = parseFloat(nominal_dikembalikan) || 0;
            if (currentNominal > 0) {
                cleavePengembalianNominal.setRawValue(currentNominal);
            }

            // Initial calculation
            updatePengembalianCalculation();

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('modalInputPengembalian'));
            modal.show();
        }

        /**
         * Update calculation untuk sisa
         */
        function updatePengembalianCalculation() {
            const nominalDisalurkan = parseFloat(document.getElementById('pengembalian_nominal_disalurkan_raw').value) || 0;
            const nominalDikembalikan = parseFloat(document.getElementById('pengembalian_nominal_dikembalikan_raw').value) || 0;

            // Calculate sisa
            const sisa = Math.max(0, nominalDisalurkan - nominalDikembalikan);
            document.getElementById('pengembalian_sisa').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisa);

            // Validation
            const input = document.getElementById('pengembalian_nominal_dikembalikan');
            const errorDiv = document.getElementById('pengembalian_validation_error');

            if (nominalDikembalikan > nominalDisalurkan) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'Nominal yang dikembalikan tidak boleh lebih besar dari nominal yang disalurkan!';
            } else {
                input.classList.remove('is-invalid');
                errorDiv.textContent = '';
            }
        }

        /**
         * Save Pengembalian - Event listener
         */
        document.addEventListener('DOMContentLoaded', function () {
            const btnSimpan = document.getElementById('btnSimpanPengembalian');

            if (btnSimpan) {
                btnSimpan.addEventListener('click', function () {
                    const id = document.getElementById('pengembalian_id').value;
                    const nominalDisalurkan = parseFloat(document.getElementById('pengembalian_nominal_disalurkan_raw').value) || 0;
                    const nominalDikembalikan = parseFloat(document.getElementById('pengembalian_nominal_dikembalikan_raw').value) || 0;

                    // Validation
                    if (nominalDikembalikan < 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Nominal yang dikembalikan tidak boleh negatif!',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    if (nominalDikembalikan > nominalDisalurkan) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Nominal yang dikembalikan tidak boleh lebih besar dari nominal yang disalurkan!',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Disable button & show loading
                    btnSimpan.disabled = true;
                    btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

                    // Call Livewire method
                    @this.call('updateNominalPengembalian', id, nominalDikembalikan)
                        .then(() => {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalInputPengembalian'));
                            if (modal) modal.hide();

                            // Reset button
                            btnSimpan.disabled = false;
                            btnSimpan.innerHTML = 'Simpan';
                        })
                        .catch((error) => {
                            console.error('Error saving:', error);
                            btnSimpan.disabled = false;
                            btnSimpan.innerHTML = 'Simpan';
                        });
                });
            }
        });

        // Listen for showAlert event from Livewire
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showAlert', (event) => {
                console.log('showAlert event:', event);
                const data = Array.isArray(event) ? event[0] : event;

                Swal.fire({
                    icon: data.type || 'info',
                    title: data.type === 'success' ? 'Berhasil!' : 'Gagal!',
                    text: data.message,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-' + (data.type === 'success' ? 'success' : 'danger')
                    }
                });
            });
        });
    </script>
@endpush