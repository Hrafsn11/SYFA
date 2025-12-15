<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Select2 extends Component
{
    public $list_data;
    public $value_name;
    public $value_label;
    public $data_placeholder;
    public $model_name;
    public $value = null;
    public $allow_clear = true;
    public $tags = false;
    private $previousValue = null;

    public function mount(
        $list_data = [],
        $value_name = 'id',
        $value_label = 'name',
        $data_placeholder = 'Pilih Data',
        $model_name = 'data',
        $value = null,
        $allow_clear = true,
        $tags = false
    ){
        $this->list_data = $list_data;
        $this->value_name = $value_name;
        $this->value_label = $value_label;
        $this->data_placeholder = $data_placeholder;
        $this->model_name = $model_name;
        $this->value = $value;
        $this->allow_clear = $allow_clear;
        $this->tags = $tags;
    }

    public function render()
    {
        return view('livewire.components.select2');
    }

    public function updatedValue($value)
    {
        if ($this->previousValue !== $value) {
            $this->previousValue = $value;
            $this->skipRender();
            $this->dispatch('select2-updated', value: $value, modelName: $this->model_name);
        }
    }
}
