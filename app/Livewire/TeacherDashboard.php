<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class TeacherDashboard extends Component
{
    #[Layout('layouts.teacher-app')]
    public function render()
    {
        return view('livewire.teacher-dashboard');
    }
}
