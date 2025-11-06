<?php

namespace App\Livewire\Traits;

use Illuminate\Validation\ValidationException;

trait HasValidate
{
    protected function rules()
    {
        $validatorClass = $this->validateClass;

        $validate = new $validatorClass();
        $validate = $validate->rules();

        foreach ($validate as $key => $rule) {
            $validate["form_data.$key"] = $rule;
            unset($validate[$key]);
        }

        return $validate;
    }

    protected function messages() 
    {
        $validatorClass = $this->validateClass;

        $validate = new $validatorClass();
        $validateMessage = $validate->messages();

        foreach ($validateMessage as $key => $rule) {
            $validateMessage["form_data.$key"] = $rule;
            unset($validateMessage[$key]);
        }

        return $validateMessage;
    }


    public function exception($e, $stopPropagation) {
        // ngurus validation
        if ($e instanceof ValidationException) {
            $errorBag = $e->validator->errors()->toArray();
            $this->dispatch('fail-validation', $errorBag);
        }
    }
}