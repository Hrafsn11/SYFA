<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Permission Management</h5>
            @can('create permissions')
                <button wire:click="create" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create Permission
                </button>
            @endcan
        </div>

        <div class="card-body">
            <!-- Search -->
            <div class="mb-3">
                <input wire:model.live="search" type="text" placeholder="Search permissions..." class="form-control">
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Permissions Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>
                                    <strong>{{ $permission->name }}</strong>
                                </td>
                                <td>
                                    {{ $permission->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('edit permissions')
                                            <button wire:click="edit({{ $permission->id }})"
                                                class="btn btn-sm btn-icon btn-primary" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        @endcan
                                        @can('delete permissions')
                                            <button wire:click="delete({{ $permission->id }})"
                                                onclick="return confirm('Are you sure you want to delete this permission?')"
                                                class="btn btn-sm btn-icon btn-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="py-4 text-muted">
                                        <i class="ti ti-info-circle me-1"></i> No permissions found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $permissions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $selectedPermission ? 'Edit Permission' : 'Create Permission' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="name">
                                    Permission Name
                                </label>
                                <input wire:model="name" type="text" id="name" class="form-control"
                                    placeholder="Enter permission name">
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ $selectedPermission ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
