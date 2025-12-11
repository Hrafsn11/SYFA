<?php

namespace App\Livewire\Components;

use Livewire\Component;

class DatepickerBootstrap extends Component
{
    public $model_name;
    public $value = null;
    public $data_placeholder = 'DD/MM/YYYY';
    public $format = 'dd/mm/yyyy';
    public $autoclose = true;
    public $today_highlight = true;
    public $start_date = null;
    public $end_date = null;
    
    private $previousValue = null;

    public function mount(
        $model_name = 'date',
        $value = null,
        $data_placeholder = 'DD/MM/YYYY',
        $format = 'dd/mm/yyyy',
        $autoclose = true,
        $today_highlight = true,
        $start_date = null,
        $end_date = null
    ){
        $this->model_name = $model_name;
        $this->value = $value;
        $this->previousValue = $value; // Initialize previousValue dengan value awal
        $this->data_placeholder = $data_placeholder;
        $this->format = $format;
        $this->autoclose = $autoclose;
        $this->today_highlight = $today_highlight;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function render()
    {
        return view('livewire.components.datepicker-bootstrap');
    }

    public function updatedValue($value)
    {
        if ($this->previousValue === $value) {
            return;
        }

        $this->previousValue = $value;
        $this->skipRender();
        $this->dispatch('datepicker-updated', value: $value, modelName: $this->model_name);
    }
}
