<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Role Management</h5>
            @can('roles.add')
                <button wire:click="create" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create Role
                </button>
            @endcan
        </div>

        <div class="card-body">
            <!-- Search -->
            <div class="mb-3">
                <input wire:model.live="search" type="text" placeholder="Search roles..." class="form-control">
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
                            <th>Restricted</th>
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
                                    @foreach ($role->permissions as $permission)
                                        <span class="badge bg-label-success me-1 mb-1">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $role->restriction ? 'No' : 'Yes' }}
                                </td>
                                <td>
                                    {{ $role->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('roles.edit')
                                            <button wire:click="edit('{{ $role->id }}')"
                                                class="btn btn-sm btn-icon btn-primary" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        @endcan
                                        @can('roles.delete')
                                            @if ($role->name !== 'super-admin')
                                                <button wire:click="delete('{{ $role->id }}')"
                                                    onclick="return confirm('Are you sure you want to delete this role?')"
                                                    class="btn btn-sm btn-icon btn-danger" title="Delete">
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
                {{ $roles->links('pagination::bootstrap-5') }}
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
                                <label class="form-label" for="restriction">
                                    Restriction
                                </label>
                                <select wire:model="restriction" id="restriction" class="form-control">
                                    <option value="" selected disabled>Select Restriction</option>
                                    <option value="0">Yes</option>
                                    <option value="1">No</option>
                                </select>
                                @error('restriction')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="permissions">
                                    Permissions
                                </label>

                                @php
                                    $tabs = [
                                        'config' => 'Configuration',
                                        'master_data' => 'Master Data',
                                        'peminjaman' => 'Peminjaman',
                                        'investasi' => 'Investasi',
                                        'restrukturisasi' => 'Restrukturisasi',
                                        'sfinlog' => 'S-Finlog',
                                        'menu_sfinance' => 'Menu SFinance',
                                        'menu_sfinlog' => 'Menu S-Finlog',
                                        'lainnya' => 'Lainnya',
                                    ];

                                    $groupPrefixes = [
                                        'config' => ['users', 'roles', 'permissions', 'settings'],
                                        'master_data' => ['master_data'],
                                        'peminjaman' => ['peminjaman', 'peminjaman_dana', 'peminjaman_finlog', 'pengembalian_pinjaman', 'pengembalian_pinjaman_finlog'],
                                        'investasi' => ['investasi', 'penyaluran_jenis investasi', 'penyaluran_jenis investasi_finlog', 'pengembalian_investasi', 'pengembalian_investasi_finlog'],
                                        'restrukturisasi' => ['pengajuan_restrukturisasi', 'program_restrukturisasi'],
                                        'sfinlog' => ['pengajuan_investasi_finlog'],
                                        'menu_sfinance' => ['sfinance.menu'],
                                        'menu_sfinlog' => ['sfinlog.menu'],
                                        'lainnya' => ['debitur_tagihan pinjaman', 'debitur_tagihan pinjaman_finlog'],
                                    ];
                                @endphp

                                <!-- Nav Pills -->
                                <ul class="nav nav-pills mb-3 flex-nowrap gap-2" id="permissionTabs" role="tablist"
                                    style="overflow-x: auto; white-space: nowrap;">
                                    @foreach ($tabs as $id => $label)
                                        <li class="nav-item flex-fill text-center" role="presentation">
                                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                id="tab-{{ $id }}-tab" data-bs-toggle="pill"
                                                data-bs-target="#tab-{{ $id }}" type="button" role="tab"
                                                aria-controls="tab-{{ $id }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                {{ $label }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" id="permissionTabsContent">
                                    @foreach ($tabs as $id => $label)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                            id="tab-{{ $id }}" role="tabpanel"
                                            aria-labelledby="tab-{{ $id }}-tab">
                                            <div class="table-responsive mt-3">
                                                <table class="table table-flush-spacing">
                                                    <tbody>
                                                        <!-- Administrator Access Row Per Tab -->
                                                        <tr>
                                                            <td class="text-nowrap fw-medium text-heading">
                                                                Administrator Access
                                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="Allows a full access to the system"></i>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-end mt-4">
                                                                    <div class="form-check mb-0 me-4 me-lg-12">
                                                                        <input class="form-check-input"
                                                                            type="checkbox"
                                                                            id="checkAll-{{ $id }}"
                                                                            data-tab="{{ $id }}" />
                                                                        <label class="form-check-label"
                                                                            for="checkAll-{{ $id }}"> Select
                                                                            All </label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <!-- Permission List -->
                                                        @foreach ($allPermissions as $group => $perm)
                                                            @if (collect($groupPrefixes[$id] ?? [])->contains(fn($prefix) => Str::startsWith($group, $prefix)))
                                                                <tr>
                                                                    <td class="text-nowrap fw-medium text-heading">
                                                                        @php
                                                                            $groupMap = [
                                                                                'roles' => 'roles',
                                                                                'users' => 'users',
                                                                                'permissions' => 'permissions',
                                                                                'settings' => 'settings',
                                                                                'peminjaman' => 'peminjaman',
                                                                                'peminjaman_dana' => 'peminjaman dana',
                                                                                'peminjaman_finlog' => 'peminjaman finlog',
                                                                                'pengembalian_pinjaman' => 'pengembalian pinjaman',
                                                                                'pengembalian_pinjaman_finlog' => 'pengembalian pinjaman finlog',
                                                                                'investasi' => 'investasi',
                                                                                'penyaluran_jenis investasi' => 'penyaluran jenis investasi',
                                                                                'penyaluran_jenis investasi_finlog' => 'penyaluran jenis investasi finlog',
                                                                                'pengembalian_investasi' => 'pengembalian investasi',
                                                                                'pengembalian_investasi_finlog' => 'pengembalian investasi finlog',
                                                                                'pengajuan_restrukturisasi' => 'pengajuan cicilan',
                                                                                'program_restrukturisasi' => 'penyesuaian cicilan',
                                                                                'master_data' => 'master data',
                                                                                'pengajuan_investasi_finlog' => 'pengajuan investasi finlog',
                                                                                'debitur_tagihan pinjaman' => 'debitur tagihan pinjaman',
                                                                                'debitur_tagihan pinjaman_finlog' => 'debitur tagihan pinjaman finlog',
                                                                                'sfinance.menu' => 'menu sfinance',
                                                                                'sfinlog.menu' => 'menu sfinlog',
                                                                            ];

                                                                            $name_group = $groupMap[$group] ?? $group;
                                                                            $alwaysUppercase = ['isps', 'ism'];
                                                                            foreach ($alwaysUppercase as $word) {
                                                                                $name_group = preg_replace_callback(
                                                                                    "/\\b$word\\b/i",
                                                                                    function ($matches) {
                                                                                        return strtoupper($matches[0]);
                                                                                    },
                                                                                    $name_group,
                                                                                );
                                                                            }
                                                                        @endphp

                                                                        {{ ucwords(str_replace(['-', '_'], ' ', $name_group)) }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex justify-content-end mt-4">
                                                                            @foreach ($perm as $p)
                                                                                <div
                                                                                    class="form-check mb-0 me-4 me-lg-12">
                                                                                    <input wire:model="permissions"
                                                                                        class="form-check-input check-{{ $id }}"
                                                                                        type="checkbox"
                                                                                        id="check-{{ $group }}-{{ $p['name'] }}"
                                                                                        value="{{ $p['id'] }}" />
                                                                                    <label class="form-check-label"
                                                                                        for="check-{{ $group }}-{{ $p['name'] }}">
                                                                                        {{ ucwords(str_replace(['-', '_'], ' ', $p['name'])) }}
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle checkAll functionality for each tab
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id.startsWith('checkAll-')) {
                    const tabId = e.target.getAttribute('data-tab');
                    const isChecked = e.target.checked;
                    const checkboxes = document.querySelectorAll('.check-' + tabId);

                    checkboxes.forEach(checkbox => {
                        if (checkbox.checked !== isChecked) {
                            checkbox.checked = isChecked;
                            // Trigger Livewire change event
                            checkbox.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                            checkbox.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    });
                }
            });

            // Update checkAll state when individual checkboxes change
            document.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('form-check-input') && e.target.classList
                    .contains('check-')) {
                    // Find which tab this checkbox belongs to
                    const classList = Array.from(e.target.classList);
                    const tabClass = classList.find(cls => cls.startsWith('check-'));
                    if (tabClass) {
                        const tabId = tabClass.replace('check-', '');
                        const checkAllBox = document.getElementById('checkAll-' + tabId);
                        const tabCheckboxes = document.querySelectorAll('.check-' + tabId);
                        const checkedTabCheckboxes = document.querySelectorAll('.check-' + tabId +
                            ':checked');

                        if (checkAllBox) {
                            if (checkedTabCheckboxes.length === tabCheckboxes.length) {
                                checkAllBox.checked = true;
                                checkAllBox.indeterminate = false;
                            } else if (checkedTabCheckboxes.length === 0) {
                                checkAllBox.checked = false;
                                checkAllBox.indeterminate = false;
                            } else {
                                checkAllBox.checked = false;
                                checkAllBox.indeterminate = true;
                            }
                        }
                    }
                }
            });

            // Initialize checkAll state when modal opens or Livewire updates
            function initializeCheckAllStates() {
                document.querySelectorAll('[id^="checkAll-"]').forEach(checkAllBox => {
                    const tabId = checkAllBox.getAttribute('data-tab');
                    const tabCheckboxes = document.querySelectorAll('.check-' + tabId);
                    const checkedTabCheckboxes = document.querySelectorAll('.check-' + tabId + ':checked');

                    if (tabCheckboxes.length > 0) {
                        if (checkedTabCheckboxes.length === tabCheckboxes.length) {
                            checkAllBox.checked = true;
                            checkAllBox.indeterminate = false;
                        } else if (checkedTabCheckboxes.length === 0) {
                            checkAllBox.checked = false;
                            checkAllBox.indeterminate = false;
                        } else {
                            checkAllBox.checked = false;
                            checkAllBox.indeterminate = true;
                        }
                    }
                });
            }

            // Initialize on load
            setTimeout(initializeCheckAllStates, 100);

            // Re-initialize when Livewire updates
            document.addEventListener('livewire:load', function() {
                Livewire.hook('message.processed', function() {
                    setTimeout(initializeCheckAllStates, 100);
                });
            });
        });
    </script>
@endpush
