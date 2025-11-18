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
    });

    $('#modalTambahDebitur').on('show.bs.modal', function(e) {
        $('.debitur-section, .investor-section').addClass('d-none');

        if (currentTabType === 'investor') {
            $('#btnTambahText').text('Investor');
            $('.investor-section').removeClass('d-none');
            $('#hiddenFlagging').val('ya');
            $('#label-nama').text('Nama Investor');
            $('#nama').attr('placeholder', 'Masukkan Nama Investor');
            $('#label-alamat').text('Alamat Investor');
            $('#alamat').attr('placeholder', 'Masukkan alamat investor');
            $('#label-ttd').text('Upload Tanda Tangan Investor');
            @this.set('flagging', 'ya');
        } else {
            $('#btnTambahText').text('Debitur');
            $('.debitur-section').removeClass('d-none');
            $('#label-nama').text('Nama Perusahaan');
            $('#nama').attr('placeholder', 'Masukkan Nama Perusahaan');
            $('#label-alamat').text('Alamat Perusahaan');
            $('#alamat').attr('placeholder', 'Masukkan alamat perusahaan');
            $('#label-ttd').text('Upload Tanda Tangan Debitur');
            @this.set('flagging', 'tidak');
        }
    });
</script>
@endpush