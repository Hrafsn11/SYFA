<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait HandleComponentEvent
{
    #[On('select2-updated')]
    #[On('select2-changed')]
    #[On('datepicker-updated')]
    #[On('currency-updated')]
    public function handleComponentEvent($value, $modelName)
    {
        if (property_exists($this, $modelName)) {
            $this->{$modelName} = $value;

            $methodName = 'updated' . \Illuminate\Support\Str::studly($modelName);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        }
    }
}

