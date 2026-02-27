<div class="d-flex justify-content-center align-items-center gap-2">
    @can('master_data.edit')
        <button class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                wire:click='{{ $this->urlAction['get_data_' . $id] }}'
                type="button" 
                title="Edit">

            <i class="ti ti-edit" wire:loading.remove wire:target='{{ $this->urlAction['get_data_' . $id] }}'></i>
            <span class="spinner-border spinner-border-sm" wire:loading wire:target='{{ $this->urlAction['get_data_' . $id] }}'></span>
        </button>
    @endcan
    
    @can('master_data.delete')
        <button class="btn btn-sm btn-icon btn-text-danger rounded-pill kol-delete-btn" 
                type="button" 
                data-id="{{ $id }}" 
                title="Hapus">
            <i class="ti ti-trash"></i>
        </button>
    @endcan
</div>

