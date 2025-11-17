<div class="d-flex justify-content-center align-items-center gap-2">
    <button class="btn btn-sm btn-icon btn-text-primary rounded-pill" type="button"
        wire:click='{{ $this->urlAction['get_data_' . $id] }}'
        data-id="{{ $id }}" title="Edit">
        <i class="ti ti-edit" wire:loading.remove wire:target='{{ $this->urlAction['get_data_' . $id] }}'></i>
        <span class="spinner-border spinner-border-sm" wire:loading wire:target='{{ $this->urlAction['get_data_' . $id] }}'></span>
    </button>

    <button class="btn btn-sm btn-icon btn-text-danger rounded-pill config-delete-btn" type="button"
        data-id="{{ $id }}" title="Hapus">
        <i class="ti ti-trash"></i>
    </button>
</div>
