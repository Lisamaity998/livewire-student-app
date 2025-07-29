<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

class PreviousClass extends Component
{
    #[Layout('layouts.student-app')]
    public function render()
    {
        return view('livewire.previous-class');
    }
}
