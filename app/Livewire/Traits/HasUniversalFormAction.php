<?php

namespace App\Livewire\Traits;

use App\Livewire\UniversalFormAction;

trait HasUniversalFormAction
{       
    public $form_data = [];
    public $urlAction = [];

    public function setUrlSaveData($nameVariable, $routeName, array $params = [])
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->urlAction[$nameVariable] = "saveData('{$routeName}', {$paramsJson})";
        return $this->urlAction[$nameVariable];
    }

    public function setUrlLoadData($nameVariable, $routeName, array $params = [])
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->urlAction[$nameVariable] = "loadDataForm('{$routeName}', {$paramsJson})";
        return $this->urlAction[$nameVariable];
    }

    public function saveData(string $routeName, array $params = [])
    {
        if (method_exists($this, 'beforeSave')) {
            $this->beforeSave();
        }

        if (method_exists($this, 'setterFormData')) {
            $this->setterFormData();
        }

        $payload = (new UniversalFormAction($this))->saveData([
            'route' => $routeName,
            'params' => $params,
            'formData' => $this->form_data
        ]);

        if (method_exists($this, 'afterSave')) {
            $this->afterSave($payload);
        }
    }

    public function loadDataForm(string $routeName, array $params = [])
    {
        if (method_exists($this, 'beforeLoadData')) {
            $this->beforeLoadData();
        }

        $payload = (new UniversalFormAction($this))->loadData([
            'route' => $routeName,
            'params' => $params,
        ]);
        
        if (method_exists($this, 'afterLoadData')) {
            $this->afterLoadData($payload);
        }
    }
}