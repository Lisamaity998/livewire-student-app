<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class StudentDashboard extends Component
{
    #[Layout('layouts.student-app')]
    public function render()
    {
        return view('livewire.student-dashboard');
    }
}
