<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\NewClass;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentInformation;
use App\Models\ClassAttendance;
use Illuminate\Support\Carbon;


class ViewUpcomingClass extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $classes = NewClass::with(['course', 'teacher', 'interestedStudents'])->latest()->get();

        return view('livewire.view-upcoming-class', compact('classes'));
    }
}

