<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\NewClass;

class ViewUpcomingClass extends Component
{
    public $upcomingClasses;

    protected $listeners = [
        'classAdded' => 'refreshUpcomingClasses',
    ];

    public function mount()
    {
        $this->refreshUpcomingClasses();
    }

    public function refreshUpcomingClasses()
    {
        $this->upcomingClasses = NewClass::with(['course', 'teacher', 'interestedStudents'])->latest()->get();
        // $this->upcomingClasses = NewClass::with(['course', 'teacher', 'interestedStudents'])->latest('start_date')->get();
    }

    public function deleteClass($classId)
    {
        $class = NewClass::find($classId);
        if ($class) {
            $class->delete();
            // $this->refreshUpcomingClasses();
            session()->flash('success', 'Class deleted successfully!');
        } else {
            session()->flash('error', 'Class not found.');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $classes = $this->upcomingClasses;

        return view('livewire.view-upcoming-class', compact('classes'));
    }
}

