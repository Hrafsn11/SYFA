<?php

namespace App\Livewire\Traits;

trait HasSaveData
{
    public $form_data = [];
    public function saveData($routeName, $callback = null)
    {
        $this->validate();

        $this->dispatch('universal-save-data', [
            'route' => $routeName,
            'callback' => $callback,
            'formData' => $this->form_data,
        ]);
    }
}