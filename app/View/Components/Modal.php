<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Modal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $id = null,
        public string $title = '',
        public bool $show = false,
    ) {
        // Generate a unique ID if none provided
        $this->id = $this->id ?? uniqid('modal_');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.modal');
    }
}
