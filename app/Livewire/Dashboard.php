<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        return view('livewire.dashboard', compact('stats'))
            ->layout('layouts.app');
    }
}
