<div class="d-flex justify-content-center align-items-center gap-2">
    <button class="btn btn-sm btn-icon btn-text-info rounded-pill waves-effect investor-detail-btn" 
            type="button"
            data-id="{{ $id }}" 
            title="Detail">
        <i class="ti ti-file-text"></i>
    </button>
    
    @if($status === 'Draft' || ($status === 'Ditolak' && $current_step == 1))
    <button class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect investor-edit-btn" 
            type="button"
            data-id="{{ $id }}" 
            title="Edit">
        <i class="ti ti-edit"></i>
    </button>
    @else
    <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" 
            type="button"
            disabled
            title="Tidak dapat edit (status: {{ $status }})">
        <i class="ti ti-edit"></i>
    </button>
    @endif
</div>
