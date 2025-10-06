<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class InputLabel extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $value = null,
        public ?string $for = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.input-label');
    }
}
