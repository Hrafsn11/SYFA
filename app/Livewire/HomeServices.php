<?php

namespace App\Livewire;

use Livewire\Component;

class HomeServices extends Component
{
    public function render()
    {
        return view('livewire.home-services')
            ->layout('layouts.app', [
                'showSidebar' => false,
                'showNavbar'  => false,
            ]);
    }
}


