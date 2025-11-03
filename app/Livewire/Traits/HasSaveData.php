<?php

namespace App\Livewire\Traits;

trait HasSaveData
{
    public $formData = [];
    public function saveData($routeName, $callback = null)
    {
        $this->dispatch('universal-save-data', [
            'route' => $routeName,
            'callback' => $callback,
            'formData' => $this->formData,
        ]);
    }
}