<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengembalian Investasi</h4>

                @can('pengembalian_investasi.add')
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        data-bs-toggle="modal" data-bs-target="#modalPengembalianInvestasi">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Pengembalian</span>
                    </button>
                @endcan

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="card-datatable">
                @livewire('pengembalian-investasi-table')
            </div>
        </div>
    </div>

    @include('livewire.pengembalian-investasi.components.modal')
</div>

@push('scripts')
    <script>
        let select2Kontrak;
        let flatpickrTanggal;

        // Best Practice: Pattern from PenyaluranDeposito
        function afterAction(payload) {
            Livewire.dispatch('refreshPengembalianInvestasiTable');
            $('.modal').modal('hide');

            if (payload && payload.message) {
                showSuccessAlert(payload.message);
            }
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('closeModal', () => $('#modalPengembalianInvestasi').modal('hide'));
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            const number = angka.toString().replace(/[^0-9]/g, '');
            return 'Rp ' + number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatRupiah(rupiah) {
            return rupiah.replace(/[^0-9]/g, '');
        }

        function showSuccessAlert(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-success'
                }
            });
        }

        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-danger'
                }
            });
        }

        $('#modalPengembalianInvestasi').on('shown.bs.modal', function() {
            // Destroy previous instances
            if (select2Kontrak) $('#id_pengajuan_investasi').select2('destroy');
            if (flatpickrTanggal) flatpickrTanggal.destroy();

            // Init Select2
            select2Kontrak = $('#id_pengajuan_investasi').select2({
                dropdownParent: $('#modalPengembalianInvestasi'),
                placeholder: 'Pilih No Kontrak',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                let value = $(this).val();

                @this.set('id_pengajuan_investasi', value);

                if (value) {
                    @this.call('loadDataKontrak', value).then(() => {
                        // Check if dana_tersedia is 0, auto-set dana_pokok_dibayar to 0
                        let danaTersedia = @this.get('dana_tersedia');
                        if (danaTersedia == 0) {
                            $('#dana_pokok_dibayar').val('Rp 0').prop('disabled', true);
                            $('#dana_pokok_raw').val(0);
                            @this.set('dana_pokok_dibayar', 0);
                        } else {
                            $('#dana_pokok_dibayar').prop('disabled', false);
                        }

                        // Check if sisa_bagi_hasil is 0, auto-set bagi_hasil_dibayar to 0
                        let sisaBagiHasil = @this.get('sisa_bagi_hasil');
                        if (sisaBagiHasil == 0) {
                            $('#bagi_hasil_dibayar').val('Rp 0').prop('disabled', true);
                            $('#bagi_hasil_raw').val(0);
                            @this.set('bagi_hasil_dibayar', 0);
                        } else {
                            $('#bagi_hasil_dibayar').prop('disabled', false);
                        }
                    });
                } else {
                    @this.call('resetCalculatedFields');
                    $('#dana_pokok_dibayar').val('').prop('disabled', false);
                    $('#bagi_hasil_dibayar').val('').prop('disabled', false);
                }
            });

            // ✅ NEW: Auto-reload if kontrak already selected when modal opens
            let currentValue = $('#id_pengajuan_investasi').val();
            if (currentValue) {
                @this.call('loadDataKontrak', currentValue).then(() => {
                    // Handle dana_pokok_dibayar
                    let danaTersedia = @this.get('dana_tersedia');
                    if (danaTersedia == 0) {
                        $('#dana_pokok_dibayar').val('Rp 0').prop('disabled', true);
                        $('#dana_pokok_raw').val(0);
                        @this.set('dana_pokok_dibayar', 0);
                    }

                    // Handle bagi_hasil_dibayar
                    let sisaBagiHasil = @this.get('sisa_bagi_hasil');
                    if (sisaBagiHasil == 0) {
                        $('#bagi_hasil_dibayar').val('Rp 0').prop('disabled', true);
                        $('#bagi_hasil_raw').val(0);
                        @this.set('bagi_hasil_dibayar', 0);
                    }
                });
            }

            // Init Flatpickr
            flatpickrTanggal = flatpickr('#tanggal_pengembalian', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                onChange: function(selectedDates, dateStr) {
                    @this.set('tanggal_pengembalian', dateStr);
                }
            });

            $('#dana_pokok_dibayar').on('input', function() {
                const rawValue = unformatRupiah($(this).val());
                $(this).val(formatRupiah(rawValue));
                $('#dana_pokok_raw').val(rawValue);
                @this.set('dana_pokok_dibayar', rawValue);
            });

            $('#bagi_hasil_dibayar').on('input', function() {
                const rawValue = unformatRupiah($(this).val());
                $(this).val(formatRupiah(rawValue));
                $('#bagi_hasil_raw').val(rawValue);
                @this.set('bagi_hasil_dibayar', rawValue);
            });

        }).on('hidden.bs.modal', function() {
            // Reset form
            if (select2Kontrak) {
                $('#id_pengajuan_investasi').val(null).trigger('change');
            }
            // Clear formatted inputs
            $('#dana_pokok_dibayar').val('');
            $('#bagi_hasil_dibayar').val('');

            @this.call('resetForm');
        });
    </script>
@endpush
