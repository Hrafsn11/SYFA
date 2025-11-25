<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Penyaluran Deposito</h4>
                
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahPenyaluran" data-bs-toggle="modal" data-bs-target="#modalPenyaluranDeposito">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Penyaluran</span>
                </button>
            
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="card-datatable">
                <livewire:penyaluran-deposito.penyaluran-deposito-table />
            </div>
        </div>
    </div>

    @include('livewire.penyaluran-deposito.components.modal')
</div>

@push('scripts')
<script>
    let cleaveNominal;
    let flatpickrPengiriman;
    let flatpickrPengembalian;
    let currentIdForUpload;
    let currentIdForDelete;
    let nilaiInvestasiMax = 0;

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

    function afterAction(payload) {
        Livewire.dispatch('refreshPenyaluranDepositoTable');
        $('.modal').modal('hide');
    }

    function editDataDirect(button) {
        // Ambil data langsung dari data-attribute (instant, no AJAX!)
        const data = JSON.parse($(button).attr('data-item'));
        
        const modal = $('#modalPenyaluranDeposito');
        const form = modal.find('form');

        // Clear validation errors
        modal.find('.form-control').removeClass('is-invalid');
        modal.find('.invalid-feedback').text('').hide();
        modal.find('.form-group').removeClass('is-invalid');

        // Set form action untuk update
        form.attr('wire:submit', `{!! $urlAction["update_penyaluran_deposito"] !!}`.replace('id_placeholder', data.id));
        
        // Update modal title dan button
        modal.find('.modal-title').html('Edit Penyaluran Deposito');
        modal.find('#btnHapusData').show();

        // Set ID untuk delete
        currentIdForDelete = data.id;

        $('#id_pengajuan_investasi').val(data.id_pengajuan_investasi).trigger('change');
        $('#id_debitur').val(data.id_debitur).trigger('change');

        flatpickrPengiriman.setDate(data.tanggal_pengiriman_dana);
        flatpickrPengembalian.setDate(data.tanggal_pengembalian);

        $('#nominal_yang_disalurkan').val(formatRupiah(data.nominal_yang_disalurkan));
        $('#nominal_raw').val(data.nominal_yang_disalurkan);

        // Update Livewire properties
        @this.set('id', data.id);
        @this.set('id_pengajuan_investasi', data.id_pengajuan_investasi);
        @this.set('id_debitur', data.id_debitur);
        @this.set('nominal_yang_disalurkan', data.nominal_yang_disalurkan);
        @this.set('tanggal_pengiriman_dana', data.tanggal_pengiriman_dana);
        @this.set('tanggal_pengembalian', data.tanggal_pengembalian);

        modal.modal('show');
    }

    function uploadBukti(id) {
        currentIdForUpload = id;
        $('#formUploadBukti')[0].reset();
        $('#modalUploadBukti').modal('show');
    }

    function previewBukti(id, filePath, isImage) {
        const fullPath = '/storage/' + filePath;
        let content = '';
        
        if (isImage) {
            content = `<img src="${fullPath}" class="img-fluid" alt="Bukti Pengembalian">`;
        } else {
            content = `<iframe src="${fullPath}" style="width: 100%; height: 500px;" frameborder="0"></iframe>`;
        }
        
        $('#previewContent').html(content);
        $('#modalPreviewBukti').modal('show');
    }

    function formatRupiah(angka) {
        if (!angka) return '';
        const number = angka.toString().replace(/[^0-9]/g, '');
        return 'Rp ' + number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiah(rupiah) {
        return rupiah.replace(/[^0-9]/g, '');
    }

    $(document).ready(function() {
    
        $('#nominal_yang_disalurkan').on('input', function() {
            const rawValue = unformatRupiah($(this).val());
            $(this).val(formatRupiah(rawValue));
            $('#nominal_raw').val(rawValue);
            @this.set('nominal_yang_disalurkan', rawValue);
            
            // Validasi
            if (nilaiInvestasiMax > 0 && parseFloat(rawValue) > nilaiInvestasiMax) {
                $('#nilai-investasi-info').html('<span class="text-danger">⚠ Nominal tidak boleh lebih dari nilai investasi (Rp ' + nilaiInvestasiMax.toLocaleString('id-ID') + ')</span>');
            } else {
                $('#nilai-investasi-info').html('');
            }
        });

        flatpickrPengiriman = flatpickr("#tanggal_pengiriman_dana", {
            dateFormat: "Y-m-d",
            allowInput: true,
            onChange: function(selectedDates, dateStr) {
                @this.set('tanggal_pengiriman_dana', dateStr);
            }
        });

        flatpickrPengembalian = flatpickr("#tanggal_pengembalian", {
            dateFormat: "Y-m-d",
            allowInput: true,
            onChange: function(selectedDates, dateStr) {
                @this.set('tanggal_pengembalian', dateStr);
            }
        });

        $('#id_pengajuan_investasi').select2({
            dropdownParent: $('#modalPenyaluranDeposito'),
            width: '100%',
            placeholder: 'Pilih No Kontrak',
            allowClear: true
        });

        $('#id_debitur').select2({
            dropdownParent: $('#modalPenyaluranDeposito'),
            width: '100%',
            placeholder: 'Pilih Nama Perusahaan',
            allowClear: true
        });

        $('#id_pengajuan_investasi').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const nilaiInvestasi = selectedOption.data('nilai-investasi');
            
            if (nilaiInvestasi) {
                nilaiInvestasiMax = parseFloat(nilaiInvestasi);
                $('#nilai-investasi-info').html('<small class="text-muted">Nilai investasi: Rp ' + nilaiInvestasiMax.toLocaleString('id-ID') + '</small>');
            } else {
                nilaiInvestasiMax = 0;
                $('#nilai-investasi-info').html('');
            }
            
            @this.set('id_pengajuan_investasi', $(this).val());
        });

        $('#id_debitur').on('change', function() {
            @this.set('id_debitur', $(this).val());
        });
    });

    $('#modalPenyaluranDeposito').on('shown.bs.modal', function() {
        $('#id_pengajuan_investasi').focus();
    });

    $('#modalPenyaluranDeposito').on('hidden.bs.modal', function() {
        // Reset form ke mode tambah
        $(this).find('form').attr('wire:submit', `{!! $urlAction["store_penyaluran_deposito"] !!}`);
        $(this).find('.modal-title').text('Tambah Penyaluran Deposito');
        $(this).find('#btnHapusData').hide();
        
        // Clear all fields
        $('#id_pengajuan_investasi').val('').trigger('change');
        $('#id_debitur').val('').trigger('change');
        $('#nominal_yang_disalurkan').val('');
        $('#nominal_raw').val('');
        
        flatpickrPengiriman.clear();
        flatpickrPengembalian.clear();
        $('#nilai-investasi-info').html('');
        nilaiInvestasiMax = 0;
        currentIdForDelete = null;
        
        // Clear validation errors
        $('#modalPenyaluranDeposito .form-control').removeClass('is-invalid');
        $('#modalPenyaluranDeposito .invalid-feedback').text('').hide();
        $('#modalPenyaluranDeposito .form-group').removeClass('is-invalid');
        
        // Reset Livewire state
        @this.set('id', null);
        @this.set('id_pengajuan_investasi', null);
        @this.set('id_debitur', null);
        @this.set('nominal_yang_disalurkan', null);
        @this.set('tanggal_pengiriman_dana', null);
        @this.set('tanggal_pengembalian', null);
    });

    $('#modalPenyaluranDeposito').on('keyup change', '.form-control, .form-select', function() {
        $(this).removeClass('is-invalid');
        $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
    });

    $('#btnHapusData').on('click', function(e) {
        e.preventDefault();
        $('#modalPenyaluranDeposito').modal('hide');
        $('#modalConfirmDelete').modal('show');
    });

    $('#btnConfirmDelete').on('click', function(e) {
        e.preventDefault();
        
        if (!currentIdForDelete) return;
        
        $('#deleteSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: '{{ route("penyaluran-deposito.destroy", ":id") }}'.replace(':id', currentIdForDelete),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalConfirmDelete').modal('hide');
                Livewire.dispatch('refreshPenyaluranDepositoTable');
                
                showSuccessAlert(response.message || 'Data berhasil dihapus');
            },
            error: function(xhr) {
                showErrorAlert(xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data');
            },
            complete: function() {
                $('#deleteSpinner').addClass('d-none');
                $('#btnConfirmDelete').prop('disabled', false);
                currentIdForDelete = null;
            }
        });
    });

    $('#formUploadBukti').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentIdForUpload) return;
        
        const formData = new FormData(this);
        
        $('#uploadSpinner').removeClass('d-none');
        $(this).find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: '{{ route("penyaluran-deposito.upload-bukti", ":id") }}'.replace(':id', currentIdForUpload),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalUploadBukti').modal('hide');
                Livewire.dispatch('refreshPenyaluranDepositoTable');
                
                showSuccessAlert(response.message || 'Bukti berhasil diupload');
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessages = Object.values(errors).flat().join('\n');
                    showErrorAlert(errorMessages);
                } else {
                    showErrorAlert(xhr.responseJSON?.message || 'Terjadi kesalahan saat upload');
                }
            },
            complete: function() {
                $('#uploadSpinner').addClass('d-none');
                $('#formUploadBukti button[type="submit"]').prop('disabled', false);
                currentIdForUpload = null;
            }
        });
    });
</script>
@endpush
