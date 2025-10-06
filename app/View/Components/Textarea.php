<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Textarea extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $id = '',
        public string $class = '',
        public string $placeholder = '',
        public bool $required = false,
        public int $rows = 3,
        public string $value = '',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.textarea');
    }
}
