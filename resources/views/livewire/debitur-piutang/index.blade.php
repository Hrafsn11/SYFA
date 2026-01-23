<div>
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold mb-0">Debitur Piutang</h4>
            <p class="text-muted mb-0">Data piutang debitur dan riwayat pembayaran</p>
        </div>
        <div class="col-md-6 text-end">
            <button wire:click="export" class="btn btn-success" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export" class="d-flex align-items-center">
                    <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                </span>
                <span wire:loading wire:target="export">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Generating...
                </span>
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <livewire:debitur-piutang-sfinance />
        </div>
    </div>

    {{-- Modal Edit Debitur Piutang --}}
    @can('debitur_piutang.edit')
        <div class="modal fade" id="editDebiturPiutangModal" tabindex="-1" aria-labelledby="editDebiturPiutangModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDebiturPiutangModalLabel">Edit Debitur Piutang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editDebiturPiutangForm">
                        <div class="modal-body">
                            <input type="hidden" id="edit_id_pengajuan" name="id_pengajuan">
                            <input type="hidden" id="edit_id_bukti" name="id_bukti">
                            <input type="hidden" id="edit_id_history" name="id_history">
                            <input type="hidden" id="edit_id_pengembalian" name="id_pengembalian">

                            <div class="mb-3">
                                <label for="edit_objek_jaminan" class="form-label">Objek Jaminan</label>
                                <input type="text" class="form-control" id="edit_objek_jaminan" name="objek_jaminan"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_nilai_dicairkan" class="form-label">Nilai Yang Dicairkan</label>
                                <input type="text" class="form-control money-format" id="edit_nilai_dicairkan"
                                    name="nilai_dicairkan" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_persentase_bagi_hasil" class="form-label">% Bagi Hasil</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control"
                                        id="edit_persentase_bagi_hasil" name="persentase_bagi_hasil" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_kurang_bayar_bagi_hasil" class="form-label">Total Kurang Bayar Bagi
                                    Hasil</label>
                                <input type="text" class="form-control money-format" id="edit_kurang_bayar_bagi_hasil"
                                    name="kurang_bayar_bagi_hasil" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="saveDebiturPiutangBtn">
                                <span class="normal-state">Simpan</span>
                                <span class="loading-state d-none">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</div>

@push('styles')
    <style>
        .table-container {
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
            white-space: normal;
            min-width: 250px;
        }

        .table-container table {
            width: auto;
        }

        .table-container table th,
        .table-container table td {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table-container {
                display: block;
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editDebiturPiutangModal');
            if (!modal) return;

            const bsModal = new bootstrap.Modal(modal);

            // Handle click on edit button
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-debitur-piutang-btn');
                if (!btn) return;

                const data = JSON.parse(btn.dataset.row);

                document.getElementById('edit_id_pengajuan').value = data.id_pengajuan || '';
                document.getElementById('edit_id_bukti').value = data.id_bukti || '';
                document.getElementById('edit_id_history').value = data.id_history || '';
                document.getElementById('edit_id_pengembalian').value = data.id_pengembalian || '';
                document.getElementById('edit_objek_jaminan').value = data.objek_jaminan || '';
                document.getElementById('edit_nilai_dicairkan').value = formatNumber(data.nilai_dicairkan ||
                    0);
                document.getElementById('edit_persentase_bagi_hasil').value = data.persentase_bagi_hasil ||
                    0;
                document.getElementById('edit_kurang_bayar_bagi_hasil').value = formatNumber(data
                    .kurang_bayar_bagi_hasil ||
                    0);

                bsModal.show();
            });

            // Format number with thousand separator
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            // Parse formatted number back to integer
            function parseFormattedNumber(str) {
                return parseInt(str.replace(/\./g, '').replace(/,/g, '')) || 0;
            }

            // Auto format money input
            document.getElementById('edit_nilai_dicairkan').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = formatNumber(value);
            });

            // Auto format money input for kurang_bayar_bagi_hasil
            document.getElementById('edit_kurang_bayar_bagi_hasil').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = formatNumber(value);
            });

            // Handle form submit
            document.getElementById('editDebiturPiutangForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const saveBtn = document.getElementById('saveDebiturPiutangBtn');
                saveBtn.querySelector('.normal-state').classList.add('d-none');
                saveBtn.querySelector('.loading-state').classList.remove('d-none');
                saveBtn.disabled = true;

                const formData = {
                    id_pengajuan: document.getElementById('edit_id_pengajuan').value,
                    id_bukti: document.getElementById('edit_id_bukti').value,
                    id_history: document.getElementById('edit_id_history').value,
                    id_pengembalian: document.getElementById('edit_id_pengembalian').value,
                    objek_jaminan: document.getElementById('edit_objek_jaminan').value,
                    nilai_dicairkan: parseFormattedNumber(document.getElementById(
                        'edit_nilai_dicairkan').value),
                    persentase_bagi_hasil: parseFloat(document.getElementById(
                        'edit_persentase_bagi_hasil').value),
                    kurang_bayar_bagi_hasil: parseFormattedNumber(document.getElementById(
                        'edit_kurang_bayar_bagi_hasil').value),
                };

                fetch('{{ route('debitur-piutang.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(formData),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            bsModal.hide();
                            // Refresh table
                            Livewire.dispatch('$refresh');
                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message || 'Data berhasil diperbarui',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                alert(data.message || 'Data berhasil diperbarui');
                            }
                            // Reload page to refresh data
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: error.message || 'Terjadi kesalahan saat menyimpan data',
                            });
                        } else {
                            alert(error.message || 'Terjadi kesalahan saat menyimpan data');
                        }
                    })
                    .finally(() => {
                        saveBtn.querySelector('.normal-state').classList.remove('d-none');
                        saveBtn.querySelector('.loading-state').classList.add('d-none');
                        saveBtn.disabled = false;
                    });
            });
        });
    </script>
@endpush
