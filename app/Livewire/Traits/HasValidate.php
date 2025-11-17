<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use App\Attributes\FieldInput;
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

        $validate = new $validatorClass();
        $validate = $validate->rules();

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
}