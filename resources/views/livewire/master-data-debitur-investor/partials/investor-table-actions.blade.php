<div class="d-flex justify-content-center align-items-center gap-2">
    {{-- Button Edit --}}
    <button class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect debitur-edit-btn" 
            type="button"
            data-id="{{ $id }}" 
            title="Edit">
        <i class="ti ti-edit"></i>
    </button>

    {{-- Button Toggle Status --}}
    @if($status === 'active')
        <button class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect debitur-toggle-status-btn" 
                type="button"
                data-id="{{ $id }}"
                data-status="{{ $status }}"
                title="Nonaktifkan">
            <i class="ti ti-circle-x"></i>
        </button>
    @else
        <button class="btn btn-sm btn-icon btn-text-success rounded-pill waves-effect debitur-toggle-status-btn" 
                type="button"
                data-id="{{ $id }}"
                data-status="{{ $status }}"
                title="Aktifkan">
            <i class="ti ti-circle-check"></i>
        </button>
    @endif
</div>
