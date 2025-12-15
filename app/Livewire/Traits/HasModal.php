<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use App\Attributes\FieldInput;
use Livewire\Attributes\Renderless;

trait HasModal
{
    #[On('close-modal')]
    #[Renderless]
    public function resetForm()
    {
        foreach ($this->getModalFieldInputs() as $field) {
            $this->reset($field);
        }
        $this->resetValidation();
    }

    private function getModalFieldInputs(): array
    {
        $reflection = new \ReflectionClass($this);

        return collect($reflection->getProperties())
            ->filter(fn($p) => $p->getAttributes(FieldInput::class))
            ->map(fn($p) => $p->getName())
            ->values()
            ->all();
    }
}