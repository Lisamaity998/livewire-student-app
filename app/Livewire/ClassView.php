<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NewClass;
use App\Models\ClassAttendance;
use App\Models\StudentInformation;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ClassView extends Component
{
    public $class;

    public function mount($id)
    {
        $student = StudentInformation::where('id', Auth::id())->first();
        if ($student) {
            $studentCourses = array_map('trim', explode(',', $student->course));
        }

        $this->class = NewClass::findOrFail($id);
        $classCourseName = $this->class->course->name ?? null;
        // Check if the course name exists in the student's course list
        $isEnrolled = in_array($classCourseName, $studentCourses);

        // Check if attendance is marked
        $hasMarkedAttendance = $student->id && ClassAttendance::where('class_id', $this->class->id)
            ->where('student_id', $student->id)
            ->where('attended', '1')
            ->exists();

        if (!($isEnrolled && $hasMarkedAttendance)) {
            session()->flash('error', 'You are not authorized to access this class.');
            return redirect()->route('student.dashboard');
        }
        
        $startDateTime = Carbon::parse($this->class->start_date . ' ' . $this->class->class_time, 'Asia/Kolkata');
        $now = Carbon::now('Asia/Kolkata');
    
        if ($now->lt($startDateTime)) {
            session()->flash('warning', 'Class has not started yet. Please wait until the scheduled time.');
            return redirect()->route('live.class');
        }
    }

    #[Layout('layouts.student-app')]
    public function render()
    {
        return view('livewire.class-view');
    }
}


