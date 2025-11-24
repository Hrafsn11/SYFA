<?php

namespace App\Livewire;

use App\Models\Permission as ModelsPermission;
use App\Models\Role as ModelsRole;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagement extends Component
{
    use WithPagination;

    public $name = '';
    public $restriction = '';
    public $permissions = [];
    public $selectedRole = null;
    public $showModal = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'restriction' => 'nullable|in:0,1',
        'permissions' => 'array',
    ];

    public function render()
    {
        $roles = ModelsRole::where('guard_name', 'web')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10);

        $allPermissions = ModelsPermission::all()->mapToGroups(function ($permission) {
            [$group, $action] = explode('.', $permission->name);
            return [$group => ['id' => $permission->id, 'name' => $action]];
        });

        return view('livewire.role-management', compact('roles', 'allPermissions'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($roleId)
    {
        $role = ModelsRole::findOrFail($roleId);
        $this->selectedRole = $role;
        $this->name = $role->name;
        $this->restriction = $role->restriction ?? 1;
        $this->permissions = $role->permissions->pluck('id')->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->selectedRole) {
            $this->rules['name'] = 'required|string|max:255|unique:roles,name,' . $this->selectedRole->id;
        }

        $this->validate();

        if ($this->selectedRole) {
            $role = $this->selectedRole;
            $role->update([
                'name' => $this->name,
                'restriction' => $this->restriction
            ]);
            
            // Convert permission IDs to names for syncing
            $permissionNames = Permission::whereIn('id', $this->permissions)
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);
            
            session()->flash('message', 'Role updated successfully!');
        } else {
            $role = ModelsRole::create([
                'name' => $this->name,
                'restriction' => $this->restriction,
                'guard_name' => 'web'
            ]);
            
            // Convert permission IDs to names for syncing
            $permissionNames = ModelsPermission::whereIn('id', $this->permissions)
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);
            
            session()->flash('message', 'Role created successfully!');
        }

        $this->closeModal();
    }

    public function delete($roleId)
    {
        $role = Role::findOrFail($roleId);

        // Don't allow deletion of super-admin role
        if ($role->name === 'super-admin') {
            session()->flash('error', 'Cannot delete super-admin role!');
            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->restriction = '';
        $this->permissions = [];
        $this->selectedRole = null;
        $this->resetErrorBag();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
