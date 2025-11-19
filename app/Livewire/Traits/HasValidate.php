<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use Illuminate\Http\Request;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use Livewire\Attributes\Renderless;
use Illuminate\Validation\ValidationException;

trait HasValidate
{
    public function updated($name, $value)
    {
        $this->validateOnly($name); 
    }

    protected function rules()
    {
        $validatorClass = $this->validateClass;

        $formData = [];
        foreach ($this->getValidateFieldInputs() as $field) {
            $formData[$field] = $this->{$field};
        }
        $primaryKey = $this->getValidatePrimaryKey();

        if ($primaryKey) {
            $formData[$primaryKey] = $this->{$primaryKey};
        }

        $baseRequest = Request::create('/', 'POST', $formData);
        $formRequest = $validatorClass::createFrom(
            $baseRequest,
            new $validatorClass
        );

        $validate = $formRequest->rules();

        return $validate;
    }

    protected function messages() 
    {
        $validatorClass = $this->validateClass;

        $validate = new $validatorClass();
        $validateMessage = $validate->messages();

        return $validateMessage;
    }

    public function exception($e, $stopPropagation) {
        // ngurus validation
        if ($e instanceof ValidationException) {
            $errorBag = $e->validator->errors()->toArray();
            $this->dispatch('fail-validation', $errorBag);
        }
    }

    #[On('close-modal')]
    #[Renderless]
    public function resetForm()
    {
        foreach ($this->getValidateFieldInputs() as $field) {
            $this->reset($field);
        }
        $this->resetValidation();
    }

    private function getValidateFieldInputs(): array
    {
        $reflection = new \ReflectionClass($this);

        return collect($reflection->getProperties())
            ->filter(fn($p) => $p->getAttributes(FieldInput::class))
            ->map(fn($p) => $p->getName())
            ->values()
            ->all();
    }

    private function getValidatePrimaryKey()
    {
        $reflection = new \ReflectionClass($this);

        return collect($reflection->getProperties())
            ->filter(fn($p) => $p->getAttributes(ParameterIDRoute::class))
            ->map(fn($p) => $p->getName())
            ->values()
            ->first();
    }
}