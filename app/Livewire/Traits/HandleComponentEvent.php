<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait HandleComponentEvent
{
    #[On('select2-updated')]
    #[On('datepicker-updated')]
    #[On('currency-updated')]
    public function handleComponentEvent($value, $modelName)
    {
        if (property_exists($this, $modelName)) {
            $this->{$modelName} = $value;
        }
    }
}

