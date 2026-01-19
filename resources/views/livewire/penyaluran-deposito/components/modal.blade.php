{{-- Modal Tambah/Edit Penyaluran Deposito --}}
<div class="modal fade" id="modalPenyaluranDeposito">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenyaluranDepositoLabel">Tambah Penyaluran Deposito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPenyaluranDeposito" wire:submit="{{ $urlAction['store_penyaluran_deposito'] }}">
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
        // Helper function - check if not already declared
        if (typeof window.formatRupiah === 'undefined') {
            window.formatRupiah = function(value) {
                return value ? 'Rp ' + new Intl.NumberFormat('id-ID').format(value) : '';
            };
        }
        
        // Global function untuk modal input pengembalian
        window.openInputPengembalian = function(id, no_kontrak, nama_perusahaan, nominal_disalurkan, nominal_dikembalikan, tgl_pengiriman, tgl_pengembalian) {
            $('#pengembalian_id').val(id);
            $('#pengembalian_no_kontrak').val(no_kontrak || '-');
            $('#pengembalian_nama_perusahaan').val(nama_perusahaan || '-');
            $('#pengembalian_tgl_pengiriman').val(tgl_pengiriman ? new Date(tgl_pengiriman).toLocaleDateString('id-ID') : '-');
            $('#pengembalian_tgl_pengembalian').val(tgl_pengembalian ? new Date(tgl_pengembalian).toLocaleDateString('id-ID') : '-');
            $('#pengembalian_nominal_disalurkan_raw').val(nominal_disalurkan);
            $('#pengembalian_nominal_disalurkan').val(window.formatRupiah(nominal_disalurkan));
            
            const currentNominal = parseFloat(nominal_dikembalikan) || 0;
            $('#pengembalian_nominal_dikembalikan_raw').val(currentNominal);
            $('#pengembalian_nominal_dikembalikan').val(currentNominal > 0 ? window.formatRupiah(currentNominal) : '');
            
            updatePengembalianCalculation();
            new bootstrap.Modal($('#modalInputPengembalian')[0]).show();
        };

        function updatePengembalianCalculation() {
            const disalurkan = parseFloat($('#pengembalian_nominal_disalurkan_raw').val()) || 0;
            const dikembalikan = parseFloat($('#pengembalian_nominal_dikembalikan_raw').val()) || 0;
            const sisa = Math.max(0, disalurkan - dikembalikan);

            $('#pengembalian_sisa').val(window.formatRupiah(sisa));

            const input = $('#pengembalian_nominal_dikembalikan');
            if (dikembalikan > disalurkan) {
                input.addClass('is-invalid');
                $('#pengembalian_validation_error').text('Nominal yang dikembalikan tidak boleh lebih besar dari nominal yang disalurkan!');
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
                const disalurkan = parseFloat($('#pengembalian_nominal_disalurkan_raw').val()) || 0;
                const dikembalikan = parseFloat($('#pengembalian_nominal_dikembalikan_raw').val()) || 0;

                if (dikembalikan < 0 || dikembalikan > disalurkan) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: dikembalikan < 0 ? 'Nominal tidak boleh negatif!' : 'Nominal melebihi yang disalurkan!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                $(this).prop('disabled', true);
                $('#pengembalian_spinner').removeClass('d-none');

                @this.call('updateNominalPengembalian', id, dikembalikan)
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
    </script>
@endpush