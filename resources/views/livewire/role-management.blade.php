<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Role Management</h5>
                <div class="text-muted small">Atur akses pengguna berdasarkan menu dan proses bisnis.</div>
            </div>
            @can('roles.add')
                <button wire:click="create" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create Role
                </button>
            @endcan
        </div>

        <div class="card-body">
            <!-- Search -->
            <div class="role-toolbar">
                <div class="role-search">
                    <i class="ti ti-search"></i>
                    <input wire:model.live="search" type="text" placeholder="Cari role..." class="form-control">
                </div>
                <div class="text-muted small">
                    Total: {{ $roles->total() }} role
                </div>
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

            <!-- Roles Grid -->
            <div class="row g-3">
                @forelse($roles as $role)
                    <div class="col-12 col-lg-6">
                        <div class="role-card">
                            <div class="role-card-header">
                                <div>
                                    <div class="role-title">{{ $role->name }}</div>
                                    <div class="role-meta">
                                        <span class="role-chip {{ $role->restriction ? 'chip-open' : 'chip-restrict' }}">
                                            {{ $role->restriction ? 'Open' : 'Restricted' }}
                                        </span>
                                        <span class="text-muted">Created {{ $role->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
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
                            </div>
                            <div class="role-permissions">
                                @php
                                    $permissionPreview = $role->permissions->take(8);
                                    $remaining = max($role->permissions->count() - $permissionPreview->count(), 0);
                                @endphp
                                @foreach ($permissionPreview as $permission)
                                    <span class="role-badge">{{ $permission->name }}</span>
                                @endforeach
                                @if ($remaining > 0)
                                    <span class="role-badge role-badge-more">+{{ $remaining }} more</span>
                                @endif
                                @if ($role->permissions->isEmpty())
                                    <span class="text-muted small">Belum ada permission.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="ti ti-info-circle"></i>
                            <div>No roles found.</div>
                        </div>
                    </div>
                @endforelse
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
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable role-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $selectedRole ? 'Edit Role' : 'Create Role' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row g-4">
                                <div class="col-12 col-lg-4">
                                    <div class="permission-panel">
                                        <div class="panel-title">Detail Role</div>
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

                                        <div class="helper-box">
                                            <div class="fw-semibold">Tips</div>
                                            <div class="text-muted small">Gunakan tab untuk memilih izin per area kerja.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-8">
                                    <div class="permission-panel">
                                        <div class="panel-title">Permissions</div>
                                        <div class="text-muted small mb-3">
                                            Pilih izin per menu dan proses bisnis. Gunakan "Select All" di tiap tab untuk mempercepat.
                                        </div>

                                @php
                                    $tabs = [
                                        'config' => 'Configuration',
                                        'master_data' => 'Master Data',
                                        'peminjaman' => 'Peminjaman',
                                        'restrukturisasi' => 'Restrukturisasi',
                                        'investasi' => 'Investasi',
                                        'menu_sfinance' => 'Menu SFinance',
                                        'lainnya' => 'Lainnya',
                                    ];

                                    $groupPrefixes = [
                                        'config' => ['users', 'roles', 'permissions', 'settings'],
                                        'master_data' => ['master_data'],
                                        'peminjaman' => ['peminjaman_dana', 'pengembalian_pinjaman'],
                                        'restrukturisasi' => ['pengajuan_cicilan', 'penyesuaian_cicilan'],
                                        'investasi' => ['investasi', 'penyaluran_dana_investasi', 'pengembalian_investasi'],
                                        'menu_sfinance' => ['sfinance.menu'],
                                        'lainnya' => ['debitur_piutang', 'riwayat_tagihan'],
                                    ];
                                @endphp

                                <!-- Nav Pills -->
                                <ul class="nav nav-pills permission-tabs" id="permissionTabs" role="tablist">
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
                                            <div class="tab-tools">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="checkAll-{{ $id }}" data-tab="{{ $id }}" />
                                                    <label class="form-check-label" for="checkAll-{{ $id }}">
                                                        Select All in {{ $label }}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="permission-groups">
                                                @foreach ($allPermissions as $group => $perm)
                                                    @if (collect($groupPrefixes[$id] ?? [])->contains(fn($prefix) => Str::startsWith($group, $prefix)))
                                                        <div class="permission-group">
                                                            <div class="permission-group-title">
                                                                @php
                                                                    $groupMap = [
                                                                        'roles' => 'roles',
                                                                        'users' => 'users',
                                                                        'permissions' => 'permissions',
                                                                        'settings' => 'settings',
                                                                        'peminjaman_dana' => 'peminjaman dana',
                                                                        'pengembalian_pinjaman' => 'pengembalian pinjaman',
                                                                        'investasi' => 'investasi',
                                                                        'penyaluran_dana_investasi' => 'penyaluran dana investasi',
                                                                        'pengembalian_investasi' => 'pengembalian investasi',
                                                                        'pengajuan_cicilan' => 'pengajuan cicilan',
                                                                        'penyesuaian_cicilan' => 'penyesuaian cicilan',
                                                                        'master_data' => 'master data',
                                                                        'debitur_piutang' => 'debitur piutang',
                                                                        'riwayat_tagihan' => 'riwayat tagihan',
                                                                        'sfinance.menu' => 'menu sfinance',
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
                                                            </div>
                                                            <div class="permission-items">
                                                                @foreach ($perm as $p)
                                                                    <label class="permission-item">
                                                                        <input wire:model="permissions"
                                                                            class="form-check-input check-{{ $id }}"
                                                                            type="checkbox"
                                                                            id="check-{{ $group }}-{{ $p['name'] }}"
                                                                            value="{{ $p['id'] }}" />
                                                                        <span>{{ ucwords(str_replace(['-', '_'], ' ', $p['name'])) }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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
@push('styles')
    <style>
        .role-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(12, 109, 125, 0.08), rgba(12, 109, 125, 0.02));
            border: 1px solid rgba(12, 109, 125, 0.12);
            margin-bottom: 18px;
        }

        .role-search {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid rgba(30, 41, 59, 0.12);
            min-width: 260px;
            flex: 1;
        }

        .role-search i {
            color: #0c6d7d;
        }

        .role-search .form-control {
            border: 0;
            padding: 0;
            box-shadow: none;
        }

        .role-card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .role-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .role-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .role-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .role-chip {
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .chip-open {
            background: rgba(16, 185, 129, 0.12);
            color: #0f766e;
        }

        .chip-restrict {
            background: rgba(239, 68, 68, 0.12);
            color: #b91c1c;
        }

        .role-permissions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .role-badge {
            background: rgba(14, 116, 144, 0.1);
            color: #0f5a6a;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.75rem;
        }

        .role-badge-more {
            background: rgba(15, 23, 42, 0.08);
            color: #475569;
        }

        .empty-state {
            padding: 40px;
            border-radius: 16px;
            background: #fff;
            border: 1px dashed rgba(30, 41, 59, 0.2);
            text-align: center;
            color: #64748b;
            display: grid;
            gap: 8px;
            justify-items: center;
        }

        .permission-panel {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            padding: 18px;
            background: #fff;
            height: 100%;
        }

        .panel-title {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .helper-box {
            background: rgba(12, 109, 125, 0.08);
            border-radius: 12px;
            padding: 12px;
        }

        .permission-tabs {
            gap: 8px;
            flex-wrap: wrap;
        }

        .permission-tabs .nav-link {
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 600;
            background: rgba(15, 23, 42, 0.04);
            color: #334155;
        }

        .permission-tabs .nav-link.active {
            background: #0c6d7d;
            color: #fff;
            box-shadow: 0 8px 16px rgba(12, 109, 125, 0.2);
        }

        .tab-tools {
            display: flex;
            justify-content: flex-end;
            padding: 8px 0 12px;
            border-bottom: 1px dashed rgba(15, 23, 42, 0.1);
        }

        .permission-groups {
            display: grid;
            gap: 14px;
            margin-top: 12px;
        }

        .permission-group {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 12px;
            padding: 12px;
            background: rgba(248, 250, 252, 0.7);
        }

        .permission-group-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .permission-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            font-size: 0.85rem;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 3.5rem);
        }

        .modal-dialog-scrollable .modal-body {
            max-height: calc(100vh - 14rem);
            overflow-y: auto;
        }

        .modal-dialog-scrollable {
            height: calc(100% - 3.5rem);
        }

        .role-modal {
            max-width: 2000px;
        }

        @media (max-width: 768px) {
            .role-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endpush
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
                if (e.target && e.target.classList.contains('form-check-input')) {
                    const classList = Array.from(e.target.classList);
                    const hasCheckClass = classList.some(cls => cls.startsWith('check-'));
                    if (!hasCheckClass) {
                        return;
                    }
                    // Find which tab this checkbox belongs to
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
