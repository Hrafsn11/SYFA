@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Master Karyawan SKI</h4>
                    <button type="button" class="btn btn-primary d-flex justify-center align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalKaryawan" onclick="openModalCreate()">
                        <i class="fa-solid fa-plus"></i>
                        Karyawan SKI
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <!-- Search and Filter -->
                            <div class="row mx-2 mt-3">
                                <div class="col-md-2">
                                    <div class="me-3">
                                        <div class="dataTables_length">
                                            <label>
                                                <span class="me-2">Show</span>
                                                <select class="form-select rounded-md" id="entriesSelect">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <span class="ms-2">Entries</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div
                                        class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                        <div class="dataTables_filter">
                                            <label>
                                                <input type="search" class="form-control rounded-md" id="searchInput"
                                                    placeholder="Search (Ctrl+/)" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Table -->
                            <table class="table table-bordered border-top" id="tableKaryawan">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">NO</th>
                                        <th class="text-center">NAMA</th>
                                        <th class="text-center">JABATAN</th>
                                        <th class="text-center">EMAIL</th>
                                        <th class="text-center">ROLE</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($karyawan as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_karyawan }}</td>
                                            <td>{{ $item->jabatan }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->role ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($item->status === 'Active')
                                                    <span class="badge bg-label-success">Active</span>
                                                @else
                                                    <span class="badge bg-label-danger">Non Active</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-sm btn-icon btn-label-primary"
                                                        onclick="editKaryawan({{ $item->id }})" title="Edit">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-sm btn-icon btn-label-{{ $item->status === 'Active' ? 'danger' : 'success' }}"
                                                        onclick="toggleStatus({{ $item->id }})"
                                                        title="{{ $item->status === 'Active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <i
                                                            class="ti ti-{{ $item->status === 'Active' ? 'x' : 'check' }}"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="row mx-2 mb-3">
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_info">
                                        Showing 1 to {{ count($karyawan) }} of {{ count($karyawan) }} entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <ul class="pagination justify-content-end">
                                            <li class="paginate_button page-item previous disabled">
                                                <a href="#" class="page-link">Previous</a>
                                            </li>
                                            <li class="paginate_button page-item active">
                                                <a href="#" class="page-link">1</a>
                                            </li>
                                            <li class="paginate_button page-item next disabled">
                                                <a href="#" class="page-link">Next</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Karyawan SKI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formKaryawan">
                    @csrf
                    <input type="hidden" id="karyawan_id" name="karyawan_id">
                    <input type="hidden" id="form_method" value="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
                            <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan"
                                placeholder="Masukkan Nama Karyawan" required>
                            <div class="invalid-feedback" id="error_nama_karyawan"></div>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                placeholder="Masukkan Jabatan" required>
                            <div class="invalid-feedback" id="error_jabatan"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Masukkan Email" required>
                                <div class="invalid-feedback" id="error_email"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" name="role"
                                    placeholder="Masukkan Role">
                                <div class="invalid-feedback" id="error_role"></div>
                            </div>
                        </div>
                        <div class="row" id="passwordFields">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan Password Anda">
                                <div class="invalid-feedback" id="error_password"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Masukkan Password Anda">
                                <div class="invalid-feedback" id="error_password_confirmation"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Hapus Data</button>
                        <button type="submit" class="btn btn-primary">
                            Simpan Data <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let editMode = false;
        let editId = null;

        function openModalCreate() {
            editMode = false;
            editId = null;
            $('#modalTitle').text('Tambah Karyawan SKI');
            $('#formKaryawan')[0].reset();
            $('#karyawan_id').val('');
            $('#form_method').val('POST');
            $('#password').prop('required', true);
            $('#password_confirmation').prop('required', true);
            $('#passwordFields label').html('Password <span class="text-danger">*</span>');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function editKaryawan(id) {
            editMode = true;
            editId = id;
            $('#modalTitle').text('Edit Karyawan SKI');
            $('#form_method').val('PUT');
            $('#karyawan_id').val(id);
            $('#password').prop('required', false);
            $('#password_confirmation').prop('required', false);
            $('#passwordFields label').html('Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>');

            // Fetch data
            $.ajax({
                url: `/master-data/karyawan-ski/${id}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#nama_karyawan').val(response.data.nama_karyawan);
                        $('#jabatan').val(response.data.jabatan);
                        $('#email').val(response.data.email);
                        $('#role').val(response.data.role);
                        $('#password').val('');
                        $('#password_confirmation').val('');
                        $('#modalKaryawan').modal('show');
                    }
                },
                error: function() {
                    alert('Gagal mengambil data karyawan');
                }
            });
        }

        function toggleStatus(id) {
            if (!confirm('Yakin ingin mengubah status karyawan ini?')) return;

            $.ajax({
                url: `/master-data/karyawan-ski/${id}/toggle-status`,
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Gagal mengubah status: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        }

        $('#formKaryawan').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            const formData = new FormData(this);
            const method = $('#form_method').val();
            const id = $('#karyawan_id').val();
            let url = '/master-data/karyawan-ski';

            if (method === 'PUT') {
                url += `/${id}`;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#modalKaryawan').modal('hide');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#error_${key}`).text(errors[key][0]);
                        });
                    } else {
                        alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                }
            });
        });

        // Search functionality
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#tableKaryawan tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Entries select
        $('#entriesSelect').on('change', function() {
            // Would implement pagination logic here
            console.log('Show entries:', $(this).val());
        });
    </script>
@endpush
