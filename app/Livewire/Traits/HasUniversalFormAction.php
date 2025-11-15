<?php

namespace App\Livewire\Traits;

use App\Attributes\FieldInput;
use Illuminate\Http\UploadedFile;
use App\Livewire\UniversalFormAction;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HasUniversalFormAction
{       
    public $form_data = [];
    public $urlAction = [];

    public function setUrlSaveData($nameVariable, $routeName, array $params = [])
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->urlAction[$nameVariable] = 'saveData("'.$routeName.'", '.$paramsJson.')';
        return $this->urlAction[$nameVariable];
    }

    public function setUrlLoadData($nameVariable, $routeName, array $params = [])
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->urlAction[$nameVariable] = 'loadDataForm("'.$routeName.'", '.$paramsJson.')';
        return $this->urlAction[$nameVariable];
    }

    public function saveData(string $routeName, array $params = [])
    {
        if (method_exists($this, 'beforeSave')) {
            $this->beforeSave();
        }

        if (method_exists($this, 'setterFormData')) {
            $this->setterFormData();
        } else {
            foreach ($this->getUniversalFieldInputs() as $key => $value) {
                $this->form_data[$value] = $this->{$value};
            }
        }

        foreach ($this->form_data as $key => $value) {
            if ($value instanceof TemporaryUploadedFile) {
                $file = new UploadedFile(
                    $this->form_data[$key]->getRealPath(),
                    $this->form_data[$key]->getClientOriginalName(),
                    $this->form_data[$key]->getMimeType(),
                    null,
                    true // penting! tandai sebagai "test file" agar laravel menerimanya
                );
                $this->form_data[$key] = $file;
            }
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

    private function getUniversalFieldInputs(): array
    {
        $reflection = new \ReflectionClass($this);

        return collect($reflection->getProperties())
            ->filter(fn($p) => $p->getAttributes(FieldInput::class))
            ->map(fn($p) => $p->getName())
            ->values()
            ->all();
    }
}