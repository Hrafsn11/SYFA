<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CurrencyField extends Component
{
    public $model_name = 'currency';
    public $value = null;
    public $placeholder = 'Rp 0';
    public $prefix = 'Rp ';

    private $previousValue = null;

    public function mount(
        $model_name = 'currency',
        $value = null,
        $placeholder = 'Rp 0',
        $prefix = 'Rp '
    ) {
        $this->model_name = $model_name;
        $this->value = $value;
        $this->previousValue = $value;
        $this->placeholder = $placeholder;
        $this->prefix = $prefix;
    }

    public function render()
    {
        return view('livewire.components.currency-field');
    }

    public function updatedValue($value)
    {
        if ($this->previousValue === $value) {
            return;
        }

        $this->previousValue = $value;

        $this->skipRender();
        $this->dispatch(
            'currency-updated',
            value: $value,
            modelName: $this->model_name
        );
    }
}
