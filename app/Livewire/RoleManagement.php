<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagement extends Component
{
    use WithPagination;

    public $name = '';
    public $permissions = [];
    public $selectedRole = null;
    public $showModal = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'permissions' => 'array',
    ];

    public function render()
    {
        $roles = Role::where('guard_name', 'web')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10);

        $allPermissions = Permission::where('guard_name', 'web')->get();

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
        $role = Role::findOrFail($roleId);
        $this->selectedRole = $role;
        $this->name = $role->name;
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
            $role->update(['name' => $this->name]);
            
            // Get permission names from IDs and sync with web guard
            $permissionNames = Permission::where('guard_name', 'web')
                ->whereIn('id', $this->permissions)
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);
            
            session()->flash('message', 'Role updated successfully!');
        } else {
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web'
            ]);
            
            // Get permission names from IDs and sync with web guard
            $permissionNames = Permission::where('guard_name', 'web')
                ->whereIn('id', $this->permissions)
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
        $this->permissions = [];
        $this->selectedRole = null;
        $this->resetErrorBag();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
