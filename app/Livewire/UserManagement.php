<?php

namespace App\Livewire;

use App\Models\Role as ModelsRole;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;

    public $name = '';
    public $email = '';
    public $password = '';
    public $roles = [];
    public $selectedUser = null;
    public $showModal = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'roles' => 'array',
    ];

    public function render()
    {
        $users = User::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        })->paginate(10);

        $allRoles = ModelsRole::where('guard_name', 'web')->get();

        return view('livewire.user-management', compact('users', 'allRoles'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->roles = $user->roles->pluck('id')->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->selectedUser) {
            $this->rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->selectedUser->id;
            $this->rules['password'] = 'nullable|string|min:8';
        }

        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = bcrypt($this->password);
        }

        if ($this->selectedUser) {
            $user = $this->selectedUser;
            $user->update($userData);
            // Convert role IDs to role names for syncRoles
            $roleNames = ModelsRole::whereIn('id', $this->roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
            session()->flash('message', 'User updated successfully!');
        } else {
            $user = User::create($userData);
            // Convert role IDs to role names for syncRoles
            $roleNames = ModelsRole::whereIn('id', $this->roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
            session()->flash('message', 'User created successfully!');
        }

        $this->closeModal();
    }

    public function delete($userId)
    {
        $user = User::findOrFail($userId);

        // Don't allow deletion of super admin user
        if ($user->hasRole('super-admin')) {
            session()->flash('error', 'Tidak dapat menghapus user super admin!');
            return;
        }

        $userName = $user->name;
        $user->delete();
        session()->flash('message', 'User ' . $userName . ' berhasil dihapus!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->roles = [];
        $this->selectedUser = null;
        $this->resetErrorBag();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
