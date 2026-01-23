<div>
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-0">Debitur Piutang Finlog</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <livewire:s-finlog.debitur-piutang-finlog-table />
        </div>
    </div>

    {{-- Modal Edit Debitur Piutang Finlog --}}
    @can('debitur_piutang_finlog.edit')
        <div class="modal fade" id="editDebiturPiutangFinlogModal" tabindex="-1" aria-labelledby="editDebiturPiutangFinlogModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDebiturPiutangFinlogModalLabel">Edit Debitur Piutang Finlog</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editDebiturPiutangFinlogForm">
                        <div class="modal-body">
                            <input type="hidden" id="edit_id_peminjaman_finlog" name="id_peminjaman">
                            <input type="hidden" id="edit_id_pengembalian_finlog" name="id_pengembalian">

                            <div class="mb-3">
                                <label class="form-label">Cells Bisnis</label>
                                <input type="text" class="form-control" id="edit_cells_bisnis_finlog" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Client</label>
                                <input type="text" class="form-control" id="edit_nama_client_finlog" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="edit_nilai_pinjaman_finlog" class="form-label">Nilai Pinjaman (Pokok)</label>
                                <input type="text" class="form-control money-format" id="edit_nilai_pinjaman_finlog" name="nilai_pinjaman" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_presentase_bagi_hasil_finlog" class="form-label">% Bagi Hasil</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="edit_presentase_bagi_hasil_finlog" name="presentase_bagi_hasil" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_nilai_bagi_hasil_finlog" class="form-label">Nilai Bagi Hasil</label>
                                <input type="text" class="form-control money-format" id="edit_nilai_bagi_hasil_finlog" name="nilai_bagi_hasil" required>
                            </div>

                            <hr>
                            <p class="text-muted small">Data Sisa Pembayaran (dari pengembalian terakhir)</p>

                            <div class="mb-3">
                                <label for="edit_sisa_pinjaman_finlog" class="form-label">Sisa Pokok</label>
                                <input type="text" class="form-control money-format" id="edit_sisa_pinjaman_finlog" name="sisa_pinjaman" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_sisa_bagi_hasil_finlog" class="form-label">Sisa Bagi Hasil</label>
                                <input type="text" class="form-control money-format" id="edit_sisa_bagi_hasil_finlog" name="sisa_bagi_hasil" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="saveDebiturPiutangFinlogBtn">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editDebiturPiutangFinlogModal');
            if (!modal) return;

            const bsModal = new bootstrap.Modal(modal);

            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            function parseFormattedNumber(str) {
                return parseFloat(String(str).replace(/\./g, '').replace(/,/g, '')) || 0;
            }

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-debitur-piutang-finlog-btn');
                if (!btn) return;

                const data = JSON.parse(btn.dataset.row);

                document.getElementById('edit_id_peminjaman_finlog').value = data.id_peminjaman || '';
                document.getElementById('edit_id_pengembalian_finlog').value = data.id_pengembalian || '';
                document.getElementById('edit_cells_bisnis_finlog').value = data.cells_bisnis || '-';
                document.getElementById('edit_nama_client_finlog').value = data.nama_client || '-';
                document.getElementById('edit_nilai_pinjaman_finlog').value = formatNumber(data.nilai_pinjaman || 0);
                document.getElementById('edit_presentase_bagi_hasil_finlog').value = data.presentase_bagi_hasil || 0;
                document.getElementById('edit_nilai_bagi_hasil_finlog').value = formatNumber(data.nilai_bagi_hasil || 0);
                document.getElementById('edit_sisa_pinjaman_finlog').value = formatNumber(data.sisa_pinjaman || 0);
                document.getElementById('edit_sisa_bagi_hasil_finlog').value = formatNumber(data.sisa_bagi_hasil || 0);

                bsModal.show();
            });

            ['edit_nilai_pinjaman_finlog', 'edit_nilai_bagi_hasil_finlog', 'edit_sisa_pinjaman_finlog', 'edit_sisa_bagi_hasil_finlog'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        e.target.value = formatNumber(value);
                    });
                }
            });

            // Handle form submit
            document.getElementById('editDebiturPiutangFinlogForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const saveBtn = document.getElementById('saveDebiturPiutangFinlogBtn');
                saveBtn.querySelector('.normal-state').classList.add('d-none');
                saveBtn.querySelector('.loading-state').classList.remove('d-none');
                saveBtn.disabled = true;

                const formData = {
                    id_peminjaman: document.getElementById('edit_id_peminjaman_finlog').value,
                    id_pengembalian: document.getElementById('edit_id_pengembalian_finlog').value,
                    nilai_pinjaman: parseFormattedNumber(document.getElementById('edit_nilai_pinjaman_finlog').value),
                    presentase_bagi_hasil: parseFloat(document.getElementById('edit_presentase_bagi_hasil_finlog').value),
                    nilai_bagi_hasil: parseFormattedNumber(document.getElementById('edit_nilai_bagi_hasil_finlog').value),
                    sisa_pinjaman: parseFormattedNumber(document.getElementById('edit_sisa_pinjaman_finlog').value),
                    sisa_bagi_hasil: parseFormattedNumber(document.getElementById('edit_sisa_bagi_hasil_finlog').value),
                };

                fetch('{{ route("sfinlog.debitur-piutang.update") }}', {
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
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message || 'Data berhasil diperbarui',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
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
