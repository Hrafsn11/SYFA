<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionManagement extends Component
{
    use WithPagination;

    public $name = '';
    public $selectedPermission = null;
    public $showModal = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:permissions,name',
    ];

    public function render()
    {
        $permissions = Permission::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->paginate(10);

        return view('livewire.permission-management', compact('permissions'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        $this->selectedPermission = $permission;
        $this->name = $permission->name;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->selectedPermission) {
            $this->rules['name'] = 'required|string|max:255|unique:permissions,name,' . $this->selectedPermission->id;
        }

        $this->validate();

        if ($this->selectedPermission) {
            $permission = $this->selectedPermission;
            $permission->update(['name' => $this->name]);
            session()->flash('message', 'Permission updated successfully!');
        } else {
            Permission::create(['name' => $this->name]);
            session()->flash('message', 'Permission created successfully!');
        }

        $this->closeModal();
    }

    public function delete($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        $permission->delete();
        session()->flash('message', 'Permission deleted successfully!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->selectedPermission = null;
        $this->resetErrorBag();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
