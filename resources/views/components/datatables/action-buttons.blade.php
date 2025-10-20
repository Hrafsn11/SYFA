<div class="d-flex justify-content-center">
    <button type="button" class="btn btn-sm btn-primary me-2" wire:click="$emit('edit', {{ $id }} )">Edit</button>
    <button type="button" class="btn btn-sm btn-danger" wire:click="$emit('delete', {{ $id }} )">Delete</button>
</div>
