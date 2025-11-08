<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use App\Livewire\UniversalFormAction;

trait HasUniversalFormAction
{       
    protected $form_data = [];

    public function __get($name)
    {
        // Jika properti tidak ada tapi disimpan di form_data
        if (array_key_exists($name, $this->form_data)) {
            return $this->form_data[$name];
        }

        // Kalau properti betulan ada, gunakan bawaan
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        // Jika bukan properti internal Livewire, simpan di form_data
        if (!property_exists($this, $name)) {
            $this->form_data[$name] = $value;
            // Trigger re-render manual
            $this->dispatch('$refresh');
            return;
        }

        parent::__set($name, $value);
    }

    public function saveData(string $routeName, array $params = [])
    {
        (new UniversalFormAction($this))->saveData([
            'route' => $routeName,
            'params' => $params,
            'formData' => $this->form_data
        ]);
    }

    public function loadDataForm(string $routeName, array $params = [])
    {
        (new UniversalFormAction($this))->loadData([
            'route' => $routeName,
            'params' => $params,
        ]);
    }
}