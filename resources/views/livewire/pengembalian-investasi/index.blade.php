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
        <div class="card-body mt-3">
            @livewire('pengembalian-investasi-table')
        </div>
    </div>

    @include('livewire.pengembalian-investasi.components.modal')
</div>

@push('scripts')
<script>
    let select2Kontrak;

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

        Livewire.on('fail-validation', (payload) => {
            const errors = payload[0];
            if (errors.dana_pokok_dibayar) {
                $('#dana_pokok_dibayar').addClass('is-invalid');
                $('#dana_pokok_dibayar').closest('.form-group').find('.invalid-feedback')
                    .addClass('d-block')
                    .text(errors.dana_pokok_dibayar[0]);
            }
            if (errors.bunga_dibayar) {
                $('#bunga_dibayar').addClass('is-invalid');
                $('#bunga_dibayar').closest('.form-group').find('.invalid-feedback')
                    .addClass('d-block')
                    .text(errors.bunga_dibayar[0]);
            }
        });
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
            title: 'Gagal!',
            text: message,
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
    }

    $('#modalPengembalianInvestasi').on('shown.bs.modal', function() {
        // Destroy previous Select2 instance
        if (select2Kontrak) $('#id_pengajuan_investasi').select2('destroy');

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

                    // Check if sisa_bunga is 0, auto-set bunga_dibayar to 0
                    let sisaBunga = @this.get('sisa_bunga');
                    if (sisaBunga == 0) {
                        $('#bunga_dibayar').val('Rp 0').prop('disabled', true);
                        $('#bunga_raw').val(0);
                        @this.set('bunga_dibayar', 0);
                    } else {
                        $('#bunga_dibayar').prop('disabled', false);
                    }
                });
            } else {
                @this.call('resetCalculatedFields');
                $('#dana_pokok_dibayar').val('').prop('disabled', false);
                $('#bunga_dibayar').val('').prop('disabled', false);
            }
        });

        // Auto-reload if kontrak already selected when modal opens
        let currentValue = $('#id_pengajuan_investasi').val();
        if (currentValue) {
            @this.call('loadDataKontrak', currentValue).then(() => {
                let danaTersedia = @this.get('dana_tersedia');
                if (danaTersedia == 0) {
                    $('#dana_pokok_dibayar').val('Rp 0').prop('disabled', true);
                    $('#dana_pokok_raw').val(0);
                    @this.set('dana_pokok_dibayar', 0);
                }

                let sisaBunga = @this.get('sisa_bunga');
                if (sisaBunga == 0) {
                    $('#bunga_dibayar').val('Rp 0').prop('disabled', true);
                    $('#bunga_raw').val(0);
                    @this.set('bunga_dibayar', 0);
                }
            });
        }

        // Init Bootstrap Datepicker (konsisten dengan halaman lain)
        const $tanggal = $('#tanggal_pengembalian');
        const hiddenTanggal = document.getElementById('tanggal_pengembalian_hidden');

        if ($tanggal.data('datepicker')) {
            $tanggal.off('changeDate');
            $tanggal.datepicker('destroy');
        }

        $tanggal.datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'top auto'
        }).on('hide', function(e) {
            e.stopPropagation();
        }).on('changeDate', function() {
            const dateValue = $tanggal.val();
            if (hiddenTanggal && dateValue) {
                hiddenTanggal.value = dateValue;
                hiddenTanggal.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        // Set nilai awal dari Livewire ke datepicker
        const initialDate = @this.get('tanggal_pengembalian');
        if (initialDate) {
            $tanggal.datepicker('setDate', initialDate);
        }

        function validateDanaPokok(rawValue) {
            const danaTersedia = parseFloat(@this.get('dana_tersedia')) || 0;
            const $el = $('#dana_pokok_dibayar');
            const $fb = $el.closest('.form-group').find('.invalid-feedback');

            if (rawValue > danaTersedia) {
                $el.addClass('is-invalid');
                $fb.addClass('d-block').text('Dana pokok yang dibayarkan tidak boleh melebihi dana tersedia (Maksimal: ' + formatRupiah(danaTersedia) + ')');
                return false;
            } else {
                $el.removeClass('is-invalid');
                $fb.removeClass('d-block').text('');
                return true;
            }
        }

        function validateBunga(rawValue) {
            const sisaBunga = parseFloat(@this.get('sisa_bunga')) || 0;
            const $el = $('#bunga_dibayar');
            const $fb = $el.closest('.form-group').find('.invalid-feedback');

            if (rawValue > sisaBunga) {
                $el.addClass('is-invalid');
                $fb.addClass('d-block').text('Bunga yang dibayarkan tidak boleh melebihi sisa bunga (Maksimal: ' + formatRupiah(sisaBunga) + ')');
                return false;
            } else {
                $el.removeClass('is-invalid');
                $fb.removeClass('d-block').text('');
                return true;
            }
        }

        $('#dana_pokok_dibayar').off('input').on('input', function() {
            const rawValue = parseFloat(unformatRupiah($(this).val())) || 0;
            $(this).val(formatRupiah(rawValue));
            $('#dana_pokok_raw').val(rawValue);
            @this.set('dana_pokok_dibayar', rawValue);
            validateDanaPokok(rawValue);
        });

        $('#bunga_dibayar').off('input').on('input', function() {
            const rawValue = parseFloat(unformatRupiah($(this).val())) || 0;
            $(this).val(formatRupiah(rawValue));
            $('#bunga_raw').val(rawValue);
            @this.set('bunga_dibayar', rawValue);
            validateBunga(rawValue);
        });

        $('#formPengembalianInvestasi').off('submit').on('submit', function(e) {
            const danaPokokVal = parseFloat(unformatRupiah($('#dana_pokok_dibayar').val())) || 0;
            const bungaVal = parseFloat(unformatRupiah($('#bunga_dibayar').val())) || 0;
            
            const isDanaPokokValid = validateDanaPokok(danaPokokVal);
            const isBungaValid = validateBunga(bungaVal);
            
            if (!isDanaPokokValid || !isBungaValid) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

    }).on('hidden.bs.modal', function() {
        // Destroy datepicker saat modal ditutup
        const $tanggal = $('#tanggal_pengembalian');
        if ($tanggal.data('datepicker')) {
            $tanggal.off('changeDate');
            $tanggal.datepicker('destroy');
        }

        // Reset Select2
        if (select2Kontrak) {
            $('#id_pengajuan_investasi').val(null).trigger('change');
        }

        // Clear formatted inputs
        $('#dana_pokok_dibayar').val('');
        $('#bunga_dibayar').val('');

        @this.call('resetForm');
    });
</script>
@endpush