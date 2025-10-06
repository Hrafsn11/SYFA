<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Checkbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $value = '',
        public bool $checked = false,
        public string $id = '',
        public string $class = '',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.checkbox');
    }
}
