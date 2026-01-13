<div class="d-flex gap-2 justify-content-center">
    @can('pengajuan_investasi_finlog.view')
    <a href="{{ route('sfinlog.pengajuan-investasi.detail', ['id' => $id]) }}" 
        class="btn btn-sm btn-icon btn-text-info rounded-pill waves-effect investor-detail-btn" title="Lihat Detail">
        <i class="ti ti-file-text"></i>
    </a>
    @endcan

    @can('pengajuan_investasi_finlog.edit')
        @if ($status === 'Draft' || $status === 'Menunggu Validasi Finance SKI' || ($status === 'Ditolak Finance SKI' && $current_step == 2))
            <button class="btn btn-sm btn-icon btn-text-warning rounded-pill waves-effect investor-edit-btn" type="button"
                data-id="{{ $id }}" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
        @else
            <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" type="button" disabled
                title="Tidak dapat edit (status: {{ $status }})">
                <i class="ti ti-edit"></i>
            </button>
        @endif
    @endcan
    
    @if($status === 'Selesai')
    <a href="{{ route('sfinlog.pengajuan-investasi.download-sertifikat', ['id' => $id]) }}" 
        class="btn btn-sm btn-icon btn-text-success rounded-pill waves-effect" 
        target="_blank"
        title="Download Sertifikat">
        <i class="ti ti-certificate"></i>
    </a>
    @endif
</div>
