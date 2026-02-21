<div>
{{-- Modal Tambah/Edit Penyaluran Dana Investasi --}}
<div class="modal fade" id="modalPenyaluranDanaInvestasi">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenyaluranDanaInvestasiLabel">Tambah Penyaluran Dana Investasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPenyaluranDanaInvestasi" wire:submit="{{ $urlAction['store_penyaluran_dana_investasi'] }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- No Kontrak -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_pengajuan_investasi" class="form-label">
                                No Kontrak <span class="text-danger">*</span>
                            </label>
                            <select id="id_pengajuan_investasi" class="form-select select2"
                                wire:model="id_pengajuan_investasi" data-placeholder="Pilih No Kontrak">
                                <option value=""></option>
                                @foreach ($pengajuanInvestasi as $item)
                                    <option value="{{ $item->id_pengajuan_investasi }}"
                                        data-nilai-investasi="{{ $item->jumlah_investasi }}"
                                        data-sisa-dana="{{ $item->sisa_dana ?? 0 }}">
                                        {{ $item->nomor_kontrak }} - {{ $item->nama_investor }} (Sisa Dana: Rp
                                        {{ number_format($item->sisa_dana ?? 0, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Nama Perusahaan (Debitur) -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_debitur" class="form-label">
                                Nama Perusahaan <span class="text-danger">*</span>
                            </label>
                            <select id="id_debitur" class="form-select select2" wire:model="id_debitur"
                                data-placeholder="Pilih Nama Perusahaan">
                                <option value=""></option>
                                @foreach ($debitur as $item)
                                    <option value="{{ $item->id_debitur }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
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
                    <!-- No. Kontrak -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Kontrak</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_no_kontrak" readonly>
                    </div>

                    <!-- Nama Perusahaan -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control bg-light" id="pengembalian_nama_perusahaan" readonly>
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

                    <!-- Bukti Pengembalian (Opsional) -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Bukti Pengembalian <small class="text-muted">(Opsional)</small></label>
                        <input type="file" class="form-control" wire:model="bukti_input_pengembalian" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Max 2MB)</small>
                        <div wire:loading wire:target="bukti_input_pengembalian" class="text-info mt-1">
                            <span class="spinner-border spinner-border-sm"></span> Uploading...
                        </div>
                    </div>

                    <!-- Catatan (Opsional) -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Catatan <small class="text-muted">(Opsional)</small></label>
                        <textarea class="form-control" wire:model="catatan_pengembalian" rows="2" placeholder="Tulis catatan..."></textarea>
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

{{-- Modal Riwayat Pengembalian --}}
<div wire:ignore.self class="modal fade" id="modalRiwayatPengembalian" tabindex="-1"
    aria-labelledby="modalRiwayatPengembalianLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRiwayatPengembalianLabel">Riwayat Pengembalian Dana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Info Summary -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Informasi Penyaluran</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="40%"><strong>No. Kontrak:</strong></td>
                                                <td id="riwayat_no_kontrak">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nama Perusahaan:</strong></td>
                                                <td id="riwayat_nama_perusahaan">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Nominal Disalurkan:</strong></td>
                                                <td id="riwayat_nominal_disalurkan">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Dikembalikan:</strong></td>
                                                <td id="riwayat_total_dikembalikan" class="text-success fw-bold">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sisa:</strong></td>
                                                <td id="riwayat_sisa" class="text-danger fw-bold">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat -->
                <h6 class="fw-bold mb-3">Daftar Riwayat Pengembalian</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableRiwayatPengembalian">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Nominal</th>
                                <th class="text-center">Bukti</th>
                                <th class="text-center">Catatan</th>
                                <th class="text-center">Diinput Oleh</th>
                                <th class="text-center">Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat_tbody">
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
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
        // Helper function - check if not already declared
        if (typeof window.formatRupiah === 'undefined') {
            window.formatRupiah = function(value) {
                return value ? 'Rp ' + new Intl.NumberFormat('id-ID').format(value) : '';
            };
        }
        
        // Global function untuk modal input pengembalian
        window.openInputPengembalian = function(id, no_kontrak, nama_perusahaan, nominal_disalurkan, sisa_belum_dikembalikan, tgl_pengiriman, tgl_pengembalian) {
            $('#pengembalian_id').val(id);
            $('#pengembalian_no_kontrak').val(no_kontrak || '-');
            $('#pengembalian_nama_perusahaan').val(nama_perusahaan || '-');
            $('#pengembalian_tgl_pengiriman').val(tgl_pengiriman ? new Date(tgl_pengiriman).toLocaleDateString('id-ID') : '-');
            $('#pengembalian_tgl_pengembalian').val(tgl_pengembalian ? new Date(tgl_pengembalian).toLocaleDateString('id-ID') : '-');
            $('#pengembalian_nominal_disalurkan_raw').val(nominal_disalurkan);
            $('#pengembalian_nominal_disalurkan').val(window.formatRupiah(nominal_disalurkan));
            
            // Set sisa yang belum dikembalikan
            const sisaBelumDikembalikan = parseFloat(sisa_belum_dikembalikan) || 0;
            $('#pengembalian_nominal_dikembalikan_raw').val(0);
            $('#pengembalian_nominal_dikembalikan').val('');
            $('#pengembalian_sisa').val(window.formatRupiah(sisaBelumDikembalikan));
            
            // Store max value untuk validation
            $('#pengembalian_max_input').remove();
            $('#modalInputPengembalian .modal-body').append(`<input type="hidden" id="pengembalian_max_input" value="${sisaBelumDikembalikan}">`);
            
            // Reset Livewire properties untuk bukti dan catatan
            @this.set('bukti_input_pengembalian', null);
            @this.set('catatan_pengembalian', '');
            
            updatePengembalianCalculation();
            new bootstrap.Modal($('#modalInputPengembalian')[0]).show();
        };

        function updatePengembalianCalculation() {
            const maxInput = parseFloat($('#pengembalian_max_input').val()) || 0;
            const inputNominal = parseFloat($('#pengembalian_nominal_dikembalikan_raw').val()) || 0;
            const sisa = Math.max(0, maxInput - inputNominal);

            $('#pengembalian_sisa').val(window.formatRupiah(sisa));

            const input = $('#pengembalian_nominal_dikembalikan');
            if (inputNominal > maxInput) {
                input.addClass('is-invalid');
                $('#pengembalian_validation_error').text('Nominal yang dikembalikan tidak boleh lebih besar dari sisa yang belum dikembalikan (Rp ' + new Intl.NumberFormat('id-ID').format(maxInput) + ')!');
            } else if (inputNominal < 0) {
                input.addClass('is-invalid');
                $('#pengembalian_validation_error').text('Nominal tidak boleh negatif!');
            } else {
                input.removeClass('is-invalid');
                $('#pengembalian_validation_error').text('');
            }
        }

        $(document).ready(() => {
            $('#pengembalian_nominal_dikembalikan').on('input', function() {
                const value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value ? window.formatRupiah(value) : '');
                $('#pengembalian_nominal_dikembalikan_raw').val(value);
                updatePengembalianCalculation();
            });

            $('#btnSimpanPengembalian').on('click', function() {
                const id = $('#pengembalian_id').val();
                const maxInput = parseFloat($('#pengembalian_max_input').val()) || 0;
                const dikembalikan = parseFloat($('#pengembalian_nominal_dikembalikan_raw').val()) || 0;

                if (dikembalikan <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nominal harus lebih dari 0!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (dikembalikan > maxInput) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nominal melebihi sisa yang belum dikembalikan!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                $(this).prop('disabled', true);
                $('#pengembalian_spinner').removeClass('d-none');

                @this.call('simpanPengembalian', id, dikembalikan)
                    .then(() => bootstrap.Modal.getInstance($('#modalInputPengembalian')[0])?.hide())
                    .finally(() => {
                        $(this).prop('disabled', false);
                        $('#pengembalian_spinner').addClass('d-none');
                    });
            });
        });

        window.addEventListener('pengembalian-success', (e) => {
            Swal.fire({icon: 'success', title: 'Berhasil!', text: e.detail.message, confirmButtonText: 'OK'});
        });

        window.addEventListener('showAlert', (e) => {
            const {type, message} = e.detail;
            Swal.fire({
                icon: type || 'info',
                title: type === 'success' ? 'Berhasil!' : 'Gagal!',
                text: message,
                confirmButtonText: 'OK'
            });
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-riwayat-modal', (event) => {
                const data = event[0] || event;
                
                // Close detail modal first
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailKontrakModal'));
                if (detailModal) {
                    detailModal.hide();
                }
                
                // Populate info summary
                $('#riwayat_no_kontrak').text(data.no_kontrak || '-');
                $('#riwayat_nama_perusahaan').text(data.nama_perusahaan || '-');
                $('#riwayat_nominal_disalurkan').text(data.nominal_disalurkan || '-');
                $('#riwayat_total_dikembalikan').text(data.total_dikembalikan || '-');
                $('#riwayat_sisa').text(data.sisa || '-');
                
                // Populate table
                const riwayatList = @this.riwayat_list || [];
                let html = '';
                
                if (riwayatList.length === 0) {
                    html = '<tr><td colspan="7" class="text-center">Belum ada riwayat pengembalian</td></tr>';
                } else {
                    riwayatList.forEach((item, index) => {
                        html += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${item.tanggal || '-'}</td>
                                <td class="text-end">${item.nominal || '-'}</td>
                                <td class="text-center">${item.bukti ? '<a href="' + item.bukti + '" target="_blank" class="btn btn-sm btn-info"><i class="ti ti-file"></i> Lihat</a>' : '-'}</td>
                                <td>${item.catatan || '-'}</td>
                                <td class="text-center">${item.user || '-'}</td>
                                <td class="text-center">${item.created_at || '-'}</td>
                            </tr>
                        `;
                    });
                }
                
                $('#riwayat_tbody').html(html);
                
                const modalEl = document.getElementById('modalRiwayatPengembalian');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });
        });
    </script>
@endpush
