<div class="d-flex gap-2 justify-content-center">
    @can('pengajuan_investasi_finlog.view')
    <a href="{{ route('sfinlog.pengajuan-investasi.detail', ['id' => $id]) }}" 
        class="btn btn-sm btn-icon btn-text-info rounded-pill waves-effect investor-detail-btn" title="Lihat Detail">
        <i class="ti ti-file-text"></i>
    </a>
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
