<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Peminjaman Dana</h4>
                @can('peminjaman_finlog.add')
                    <a href="{{ route('sfinlog.peminjaman.create') }}"
                        class="btn btn-primary d-flex justify-center align-items-center gap-3">
                        <i class="fa-solid fa-plus"></i>
                        Peminjaman Dana
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <livewire:SFinlog.peminjaman-finlog-table />
        </div>
    </div>

    {{-- Modal Template NPA - Hanya untuk Debitur --}}
    @role('Debitur')
        <div class="modal fade" id="modalTemplateNPA" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Template Dokumen NPA</h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-4" role="alert">
                            <i class="ti ti-info-circle me-2"></i>
                            Silakan download template dokumen NPA terlebih dahulu, isi dengan lengkap, kemudian upload saat
                            pengajuan peminjaman dana.
                        </div>

                        <div class="d-flex justify-content-center mb-4">
                            <a href="{{ asset('templates/Format_NPA-SKI.xlsx') }}" class="btn btn-primary" download>
                                <i class="ti ti-download me-2"></i>
                                Template NPA
                            </a>
                        </div>

                        <hr class="my-4">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkboxNPA" required>
                            <label class="form-check-label" for="checkboxNPA">
                                Saya telah mendownload dan mengisi dokumen NPA dengan lengkap
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnOkeNPA" disabled>
                            <i class="ti ti-check me-1"></i>
                            Setuju dan Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endrole
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            @role('Debitur')
                // Check if user has NPA status - Only for Debitur
                const userNpaStatus = {{ auth()->user()->debitur->npa ?? 'false' }};

                // Show modal if NPA is false
                if (!userNpaStatus) {
                    $('#modalTemplateNPA').modal('show');
                }

                // Enable/disable OK button based on checkbox
                $('#checkboxNPA').on('change', function() {
                    $('#btnOkeNPA').prop('disabled', !this.checked);
                });

                // Handle OK button click
                $('#btnOkeNPA').on('click', function() {
                    const $btn = $(this);
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

                    // Send AJAX request to update NPA status
                    $.ajax({
                        url: '{{ route('sfinlog.peminjaman.update-npa-status') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            npa_confirmed: true
                        },
                        success: function(response) {
                            $('#modalTemplateNPA').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Status NPA berhasil diperbarui.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menyimpan status NPA.'
                            });
                            $btn.prop('disabled', false).html(
                                '<i class="ti ti-check me-1"></i>Oke, Paham');
                        }
                    });
                });
            @endrole

            // Initialize DataTable
            $('#tablePeminjamanDana').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('sfinlog.peminjaman.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_project',
                        name: 'nama_project'
                    },
                    {
                        data: 'tanggal_peminjaman',
                        name: 'tanggal_peminjaman'
                    },
                    {
                        data: 'lama_peminjaman',
                        name: 'lama_peminjaman'
                    },
                    {
                        data: 'nominal_peminjaman',
                        name: 'nominal_peminjaman'
                    },
                    {
                        data: 'persentase_bagi_hasil',
                        name: 'persentase_bagi_hasil'
                    },
                    {
                        data: 'nominal_bagi_hasil',
                        name: 'nominal_bagi_hasil'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });
        });
    </script>
@endpush
