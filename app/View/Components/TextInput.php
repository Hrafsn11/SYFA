<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TextInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $type = 'text',
        public ?string $name = null,
        public ?string $id = null,
        public ?string $value = null,
        public ?string $class = null,
        public ?string $placeholder = null,
        public bool $required = false,
        public bool $autofocus = false,
        public ?string $autocomplete = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.text-input');
    }
}
