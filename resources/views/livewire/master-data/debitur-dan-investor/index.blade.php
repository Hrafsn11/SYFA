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
            @this.set('flagging', 'ya');
        } else {
            $('.debitur-section').removeClass('d-none');
            $('#label-nama').text('Nama Perusahaan');
            $('#nama').attr('placeholder', 'Masukkan Nama Perusahaan');
            $('#label-alamat').text('Alamat Perusahaan');
            $('#alamat').attr('placeholder', 'Masukkan alamat perusahaan');
            $('#label-ttd').text('Upload Tanda Tangan Debitur');
            @this.set('flagging', 'tidak');
        }
    });

    function editData(payload) {
        const data = payload.data;
        
        const modal = $('#modalTambahDebitur');
        const form = modal.find('form');

        form.attr('wire:submit', `{!! $urlAction["update_master_debitur_dan_investor"] !!}`.replace('id_placeholder', data.id));
        
        // ubah title modal
        modal.find('.modal-title').html('Edit ' + (data.flagging == 'ya' ? 'Investor' : 'Debitur'));

        // tampilkan modal
        modal.find('.password-section').addClass('d-none');
        modal.modal('show');

        Object.entries(data).forEach(([key, value]) => {
            if (['nama_bank', 'id_kol'].includes(key)) {
                $('#' + key).val(value).trigger('change');
            } else {
                @this.set(key, value);
            }
        });
    }

    $('.modal').on('hide.bs.modal', function() {
        $(this).find('form').attr('wire:submit', `{!! $urlAction["store_master_debitur_dan_investor"] !!}`);
        $(this).find('.modal-title').text(currentTabType == 'debitur' ? 'Tambah Debitur' : 'Tambah Investor');
        $(this).find('.password-section').removeClass('d-none');

        if (currentTabType === 'investor') {
            $(this).find('.modal-title').text('Tambah Investor');
        } else {
            $(this).find('.modal-title').text('Tambah Debitur');
        }
    });

    $(document).on('click', '.debitur-toggle-status-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');

        sweetAlertConfirm({
            title: 'Konfirmasi status',
            text: 'Apakah Anda yakin ingin '+ (status == 'active' ? 'menonaktifkan' : 'mengaktifkan') +' data ini?',
            icon: 'warning',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }, () => {
            @this.saveData("master-data.debitur-investor.toggle-status", {"id" : id, "callback" : "afterAction"});
        });
    });
</script>
@endpush