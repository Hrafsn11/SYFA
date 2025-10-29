<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Role Management</h5>
            @can('create roles')
            <button wire:click="create" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Create Role
            </button>
            @endcan
        </div>

        <div class="card-body">
            <!-- Search -->
            <div class="mb-3">
                <input wire:model.live="search" type="text" placeholder="Search roles..." 
                       class="form-control">
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Roles Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                </td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-label-success me-1 mb-1">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $role->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('edit roles')
                                        <button wire:click="edit({{ $role->id }})" 
                                                class="btn btn-sm btn-icon btn-primary" 
                                                title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endcan
                                        @can('delete roles')
                                        @if($role->name !== 'super-admin')
                                        <button wire:click="delete({{ $role->id }})" 
                                                onclick="return confirm('Are you sure you want to delete this role?')"
                                                class="btn btn-sm btn-icon btn-danger" 
                                                title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="py-4 text-muted">
                                        <i class="ti ti-info-circle me-1"></i> No roles found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $selectedRole ? 'Edit Role' : 'Create Role' }}
                    </h5>
                    <button type="button" wire:click="closeModal" class="btn-close"></button>
                </div>
                
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">
                                Role Name
                            </label>
                            <input wire:model="name" type="text" id="name" class="form-control" 
                                   placeholder="Enter role name">
                            @error('name') 
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Permissions
                            </label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($allPermissions as $permission)
                                    <div class="form-check mb-2">
                                        <input wire:model="permissions" type="checkbox" value="{{ $permission->id }}"
                                               class="form-check-input" id="permission_{{ $permission->id }}">
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ $selectedRole ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
