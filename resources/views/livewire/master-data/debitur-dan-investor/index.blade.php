<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Debitur dan Investor</h4>
                @can('master_data.add')
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahDebitur" data-bs-toggle="modal" data-bs-target="#modalTambahDebitur">
                    <i class="fa-solid fa-plus"></i>
                    <span>Debitur</span>
                </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <ul class="nav nav-pills mb-4" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                data-bs-target="#tab-debitur" aria-controls="tab-debitur" aria-selected="true" data-tab-type="debitur">
                Debitur
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-investor"
                aria-controls="tab-investor" aria-selected="false" data-tab-type="investor">
                Investor
            </button>
        </li>
    </ul>

    {{-- Tabs Content --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="tab-content">
                {{-- Tab Debitur --}}
                <div class="tab-pane fade show active" id="tab-debitur" role="tabpanel">
                    <div class="card-datatable">
                        <livewire:debitur-table />
                    </div>
                </div>

                {{-- Tab Investor --}}
                <div class="tab-pane fade" id="tab-investor" role="tabpanel">
                    <div class="card-datatable">
                        <livewire:investor-table />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.master-data.debitur-dan-investor.components.modal')
</div>

@push('scripts')
<script>
    var currentTabType = 'debitur';
    var isEditMode = false;
    
    function afterAction(payload) {
        Livewire.dispatch('refreshDebiturTable');
        Livewire.dispatch('refreshInvestorTable');
        $('.modal').modal('hide');
    }

    $('button[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
        currentTabType = $(e.target).data('tab-type');

        if (currentTabType === 'investor') {
            $('#btnTambahDebitur').find('span').html('Investor');
        } else {
            $('#btnTambahDebitur').find('span').html('Debitur');
        }
    });

    $('#modalTambahDebitur').on('show.bs.modal', function(e) {
        $('.debitur-section, .investor-section').addClass('d-none');

        if (currentTabType === 'investor') {
            $('.investor-section').removeClass('d-none');
            $('#label-nama').text('Nama Investor');
            $('#nama').attr('placeholder', 'Masukkan Nama Investor');
            $('#label-alamat').text('Alamat Investor');
            $('#alamat').attr('placeholder', 'Masukkan alamat investor');
            $('#label-ttd').text('Upload Tanda Tangan Investor');
            $('#ttd-required').show();
            $('.modal-title').text('Tambah Investor');
            @this.set('flagging', 'ya');
        } else {
            $('.debitur-section').removeClass('d-none');
            $('#label-nama').text('Nama Perusahaan');
            $('#nama').attr('placeholder', 'Masukkan Nama Perusahaan');
            $('#label-alamat').text('Alamat Perusahaan');
            $('#alamat').attr('placeholder', 'Masukkan alamat perusahaan');
            $('#label-ttd').text('Upload Tanda Tangan Debitur');
            $('#ttd-required').show();
            $('.modal-title').text('Tambah Debitur');
            @this.set('flagging', 'tidak');
        }
    });

    function editData(payload) {
        const data = payload.data;
        isEditMode = true;
        
        const modal = $('#modalTambahDebitur');
        const form = modal.find('form');

        form.attr('wire:submit', `{!! $urlAction["update_master_debitur_dan_investor"] !!}`.replace('id_placeholder', data.id));
        
        // ubah title modal
        modal.find('.modal-title').html('Edit ' + (data.flagging == 'ya' ? 'Investor' : 'Debitur'));

        // tampilkan password section dengan label edit mode
        modal.find('.password-section').removeClass('d-none');
        modal.find('#password-label').html('Password Baru <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>');
        modal.find('#password-confirm-label').html('Konfirmasi Password Baru');
        modal.find('#password-required').addClass('d-none');
        modal.find('#password-confirm-required').addClass('d-none');
        
        // tampilkan modal
        modal.find('.password-section').addClass('d-none');
        // Sembunyikan required asterisk untuk tanda tangan saat edit
        modal.find('#ttd-required').hide();
        modal.modal('show');

        // Set flagging_investor first dan sync dengan Livewire
        if (data.flagging_investor) {
            @this.set('flagging_investor', data.flagging_investor);
            // Juga set radio button manually
            $('input[name="flagging_investor"][value="' + data.flagging_investor + '"]').prop('checked', true);
        }

        // Set fields lainnya
        Object.entries(data).forEach(([key, value]) => {
            if (key === 'flagging_investor') {
                // Already handled above
                return;
            } else if (['nama_bank', 'id_kol'].includes(key)) {
                // Select dropdown handling
                $('#' + key).val(value).trigger('change');
            } else {
                @this.set(key, value);
            }
        });
        
        // Clear password fields for edit mode
        @this.set('password', '');
        @this.set('password_confirmation', '');
    }

    $('.modal').on('hide.bs.modal', function() {
        isEditMode = false;
        $(this).find('form').attr('wire:submit', `{!! $urlAction["store_master_debitur_dan_investor"] !!}`);
        $(this).find('.modal-title').text(currentTabType == 'debitur' ? 'Tambah Debitur' : 'Tambah Investor');
        $(this).find('.password-section').removeClass('d-none');
        
        // Reset password labels for create mode
        $(this).find('#password-label').html('Password <span class="text-danger" id="password-required">*</span>');
        $(this).find('#password-confirm-label').html('Konfirmasi Password <span class="text-danger" id="password-confirm-required">*</span>');
        $(this).find('#password-required').removeClass('d-none');
        $(this).find('#password-confirm-required').removeClass('d-none');
        // Tampilkan kembali required asterisk untuk tanda tangan
        $(this).find('#ttd-required').show();

        if (currentTabType === 'investor') {
            $(this).find('.modal-title').text('Tambah Investor');
        } else {
            $(this).find('.modal-title').text('Tambah Debitur');
        }

        // Reset all form fields
        @this.call('resetFormData');
        $(this).find('form')[0].reset();
        // Reset select2 and other inputs
        $(this).find('select').val('').trigger('change');
        $(this).find('input[type="radio"]').prop('checked', false);
    });

    $(document).on('click', '.debitur-toggle-status-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');

        sweetAlertConfirm({
            title: 'Konfirmasi status',
            text: 'Apakah Anda yakin ingin '+ (status == 'active' ? 'menonaktifkan' : 'mengaktifkan') +' data ini?',
            icon: 'warning',
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal',
        }, () => {
            @this.saveData("master-data.debitur-investor.toggle-status", {"id" : id, "callback" : "afterAction"});
        });
    });

    // Handler untuk unlock button
    $(document).on('click', '.debitur-unlock-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        sweetAlertConfirm({
            title: 'Unlock Akun',
            text: 'Apakah Anda yakin ingin membuka kunci akun ini? Pengguna akan dapat login kembali.',
            icon: 'question',
            confirmButtonText: 'Ya, Unlock',
            cancelButtonText: 'Batal',
        }, () => {
            @this.saveData("master-data.debitur-investor.unlock", {"id" : id, "callback" : "afterAction"});
        });
    });
</script>
@endpush