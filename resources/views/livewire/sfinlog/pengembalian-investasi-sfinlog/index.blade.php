<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengembalian Investasi - SFinlog</h4>
                
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    data-bs-toggle="modal" data-bs-target="#modalPengembalianInvestasiSfinlog">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Pengembalian</span>
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="card-datatable">
                <livewire:SFinlog.pengembalian-investasi-finlog-table />
            </div>
        </div>
    </div>

    @include('livewire.sfinlog.pengembalian-investasi-sfinlog.modal')
</div>

@push('scripts')
<script>
    let select2KontrakFinlog;
    let flatpickrTanggalFinlog;

    function afterAction(payload) {
        Livewire.dispatch('refreshPengembalianInvestasiFinlogTable');
        $('.modal').modal('hide');
        
        if (payload && payload.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: payload.message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-success'
                }
            });
        }
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('closeModal', () => $('#modalPengembalianInvestasiSfinlog').modal('hide'));
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        const number = angka.toString().replace(/[^0-9]/g, '');
        return 'Rp ' + number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiah(rupiah) {
        return rupiah.replace(/[^0-9]/g, '');
    }

    $('#modalPengembalianInvestasiSfinlog').on('shown.bs.modal', function () {
        if (select2KontrakFinlog) $('#id_pengajuan_investasi_finlog').select2('destroy');
        if (flatpickrTanggalFinlog) flatpickrTanggalFinlog.destroy();

        select2KontrakFinlog = $('#id_pengajuan_investasi_finlog').select2({
            dropdownParent: $('#modalPengembalianInvestasiSfinlog'),
            placeholder: 'Pilih No Kontrak',
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            let value = $(this).val();
            
            @this.set('id_pengajuan_investasi_finlog', value);
            
            if (value) {
                @this.call('loadDataKontrak', value);
            } else {
                @this.call('resetCalculatedFields');
            }
        });

        flatpickrTanggalFinlog = flatpickr('#tanggal_pengembalian_finlog', {
            dateFormat: 'Y-m-d',
            allowInput: true,
            onChange: function(selectedDates, dateStr) {
                @this.set('tanggal_pengembalian', dateStr);
            }
        });

        $('#dana_pokok_dibayar_finlog').on('input', function() {
            const rawValue = unformatRupiah($(this).val());
            $(this).val(formatRupiah(rawValue));
            $('#dana_pokok_raw_finlog').val(rawValue);
            @this.set('dana_pokok_dibayar', rawValue);
        });

        $('#bagi_hasil_dibayar_finlog').on('input', function() {
            const rawValue = unformatRupiah($(this).val());
            $(this).val(formatRupiah(rawValue));
            $('#bagi_hasil_raw_finlog').val(rawValue);
            @this.set('bagi_hasil_dibayar', rawValue);
        });

    }).on('hidden.bs.modal', function () {
        if (select2KontrakFinlog) {
            $('#id_pengajuan_investasi_finlog').val(null).trigger('change');
        }
        $('#dana_pokok_dibayar_finlog').val('');
        $('#bagi_hasil_dibayar_finlog').val('');
        
        @this.call('resetForm');
    });
</script>
@endpush