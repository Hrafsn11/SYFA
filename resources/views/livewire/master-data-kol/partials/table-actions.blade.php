
<div class="d-flex justify-content-center align-items-center gap-2">
    <button class="btn btn-sm btn-icon btn-text-primary rounded-pill"
            wire:click='loadDataForm("master-data.kol.edit", @json(["id" => $id, "callback" => "editData"]))'
            type="button" 
            title="Edit">

        <i class="ti ti-edit" wire:loading.remove wire:target='loadDataForm("master-data.kol.edit", @json(["id" => $id, "callback" => "editData"]))'></i>
        <span class="spinner-border spinner-border-sm" wire:loading wire:target='loadDataForm("master-data.kol.edit", @json(["id" => $id, "callback" => "editData"]))'></span>
    </button>
    
    <button class="btn btn-sm btn-icon btn-text-danger rounded-pill kol-delete-btn" 
            type="button" 
            data-id="{{ $id }}" 
            title="Hapus">
        <i class="ti ti-trash" wire:loading.remove wire:target='saveData("master-data.kol.destroy", @json(["id" => $id, "callback" => "afterAction"]))'></i>
        <span class="spinner-border spinner-border-sm" wire:loading wire:target='saveData("master-data.kol.destroy", @json(["id" => $id, "callback" => "afterAction"]))'></span>
    </button>
</div>

