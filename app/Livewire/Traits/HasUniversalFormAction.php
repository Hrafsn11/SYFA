<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use App\Livewire\UniversalFormAction;

trait HasUniversalFormAction
{       
    public $form_data = [];

    public function saveData(string $routeName, array $params = [])
    {
        if (method_exists($this, 'setterFormData')) {
            $this->setterFormData();
        }

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