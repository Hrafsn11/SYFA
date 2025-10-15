<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Debitur dan Investor</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    wire:click="create">
                    <i class="fa-solid fa-plus"></i>
                    Debitur dan Investor
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
                                            <select class="form-select rounded-md">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                            <span class="me-2">Entries</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div
                                    class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                    <div class="dataTables_filter">
                                        <label>
                                            <input type="search" class="form-control rounded-md"
                                                placeholder="Cari..." />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <table class="datatables-basic table border-top">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Flagging</th>
                                    <th class="text-center">Nama ceo</th>
                                    <th class="text-center">alamat perusahaan</th>
                                    <th class="text-center">email</th>
                                    <th class="text-center">KOL Perusahaan</th>
                                    <th class="text-center">Nama bank</th>
                                    <th class="text-center">no.rek</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item['nama_perusahaan'] }}</td>
                                        <td class="text-center">{{ $item['Flagging'] }}</td>
                                        <td class="text-center">{{ $item['nama_ceo'] }}</td>
                                        <td class="text-center">{{ $item['alamat_perusahaan'] }}</td>
                                        <td class="text-center">{{ $item['email'] }}</td>
                                        <td class="text-center">{{ $item['kol_perusahaan'] }}</td>
                                        <td class="text-center">{{ $item['nama_bank'] }}</td>
                                        <td class="text-center">{{ $item['no_rek'] }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                                                wire:click="edit({{ $item['id'] }})">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="row mx-2 mt-3 mb-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_info">
                                    Menampilkan data {{ count($data) }} dari {{ count($data) }}
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    <ul class="pagination">
                                        <li class="paginate_button page-item previous disabled">
                                            <a href="#" class="page-link">Sebelumnya</a>
                                        </li>
                                        <li class="paginate_button page-item active">
                                            <a href="#" class="page-link">1</a>
                                        </li>
                                        <li class="paginate_button page-item">
                                            <a href="#" class="page-link">2</a>
                                        </li>
                                        <li class="paginate_button page-item next">
                                            <a href="#" class="page-link">Selanjutnya</a>
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


    <!-- Modal Tambah/Edit Debitur dan Investor -->
    <div class="modal fade @if($showModal) show @endif" id="modalTambahDebiturInvestor" tabindex="-1" aria-hidden="true" style="@if($showModal) display: block; @endif" @if($showModal) aria-modal="true" role="dialog" @endif>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Edit Debitur dan Investor' : 'Tambah Debitur dan Investor' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Nama Perusahaan -->
                            <div class="col-12 mb-3">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="nama_perusahaan" placeholder="Masukkan Nama Perusahaan" required>
                                @error('nama_perusahaan') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Flagging -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Flagging <span class="text-danger">*</span></label>
                                <p class="text-muted small mb-2">Apakah anda termasuk investor?</p>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input name="flagging" class="form-check-input" type="radio" value="investor" wire:model.defer="flagging" id="flaggingYa" required>
                                        <label class="form-check-label" for="flaggingYa">Ya</label>
                                    </div>
                                    <div class="form-check">
                                        <input name="flagging" class="form-check-input" type="radio" value="debitur" wire:model.defer="flagging" id="flaggingTidak" required>
                                        <label class="form-check-label" for="flaggingTidak">Tidak</label>
                                    </div>
                                </div>
                                @error('flagging') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nama CEO -->
                            <div class="col-12 mb-3">
                                <label for="nama_ceo" class="form-label">Nama CEO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="nama_ceo" placeholder="Masukkan Nama CEO" required>
                                @error('nama_ceo') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Alamat Perusahaan -->
                            <div class="col-12 mb-3">
                                <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan <span class="text-danger">*</span></label>
                                <textarea class="form-control" wire:model.defer="alamat_perusahaan" rows="2" placeholder="Masukkan alamat perusahaan" required></textarea>
                                @error('alamat_perusahaan') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model.defer="email" placeholder="Masukkan email" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- KOL Perusahaan -->
                            <div class="col-md-6 mb-3" wire:ignore>
                                <label for="kol" class="form-label">KOL Perusahaan <span class="text-danger">*</span></label>
                                <select id="kol" class="form-select" data-placeholder="Pilih KOL" required>
                                    <option value="">Pilih KOL</option>
                                    @foreach ($kol as $kol_item)
                                        <option value="{{ $kol_item['id'] }}">{{ $kol_item['kol'] }}</option>
                                    @endforeach
                                </select>
                                @error('kol_perusahaan') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nama Bank -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="nama_bank" placeholder="Masukkan nama bank" required>
                                @error('nama_bank') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- No. Rekening -->
                            <div class="col-md-6 mb-3">
                                <label for="no_rek" class="form-label">No. Rekening <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model.defer="no_rek" placeholder="Masukkan no rekening" required>
                                @error('no_rek') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" wire:click="closeModal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditMode ? 'Update Data' : 'Simpan Data' }}
                            <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
        @if($showModal)
            <div class="modal-backdrop fade show"></div>
        @endif
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            const modalElement = document.getElementById('modalTambahDebiturInvestor');
            const $selectKol = $('#kol');
    
            const initSelect2 = () => {
                $selectKol.select2({
                    dropdownParent: $('#modalTambahDebiturInvestor'),
                    placeholder: 'Pilih KOL Perusahaan',
                    allowClear: true
                });
            };
    
            $selectKol.on('change', function (e) {
                let data = $(this).val();
                @this.set('kol_perusahaan', data);
            });
    
            Livewire.on('init-select2', () => {
                initSelect2();
                // Set the value based on the Livewire component's property
                $selectKol.val(@this.get('kol_perusahaan')).trigger('change');
            });
        });
    </script>
    @endpush
    