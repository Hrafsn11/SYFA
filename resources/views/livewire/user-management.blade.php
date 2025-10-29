<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Management</h5>
            @can('create users')
            <button wire:click="create" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Create User
            </button>
            @endcan
        </div>

        <div class="card-body">
            <!-- Search -->
            <div class="mb-3">
                <input wire:model.live="search" type="text" placeholder="Search users..." 
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

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>
                                    {{ $user->email }}
                                </td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-label-primary me-1 mb-1">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('edit users')
                                        <button wire:click="edit({{ $user->id }})" 
                                                class="btn btn-sm btn-icon btn-primary" 
                                                title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endcan
                                        @can('delete users')
                                        @if(!$user->hasRole('super-admin'))
                                        <button wire:click="delete({{ $user->id }})" 
                                                onclick="return confirm('Are you sure you want to delete this user?')"
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
                                <td colspan="5" class="text-center">
                                    <div class="py-4 text-muted">
                                        <i class="ti ti-info-circle me-1"></i> No users found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->links() }}
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
                        {{ $selectedUser ? 'Edit User' : 'Create User' }}
                    </h5>
                    <button type="button" wire:click="closeModal" class="btn-close"></button>
                </div>
                
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">
                                Name
                            </label>
                            <input wire:model="name" type="text" id="name" class="form-control" 
                                   placeholder="Enter user name">
                            @error('name') 
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">
                                Email
                            </label>
                            <input wire:model="email" type="email" id="email" class="form-control" 
                                   placeholder="Enter email address">
                            @error('email') 
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">
                                Password {{ $selectedUser ? '(leave blank to keep current)' : '' }}
                            </label>
                            <input wire:model="password" type="password" id="password" class="form-control" 
                                   placeholder="Enter password">
                            @error('password') 
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Roles
                            </label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($allRoles as $role)
                                    <div class="form-check mb-2">
                                        <input wire:model="roles" type="checkbox" value="{{ $role->id }}"
                                               class="form-check-input" id="role_{{ $role->id }}">
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->name }}
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
                            {{ $selectedUser ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
