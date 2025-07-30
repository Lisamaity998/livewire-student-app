<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\StudentInformation;
use App\Models\ClassAttendance;
use Illuminate\Support\Facades\Auth;
use App\Models\NewClass;

class PreviousClass extends Component
{
    public $upcomingClasses = [];

    #[Layout('layouts.student-app')]
    public function render()
    {
        $student = StudentInformation::where('id', Auth::id())->first();

        if ($student) {
            $studentCourses = array_map('trim', explode(',', $student->course));

            $courseIds = Course::whereIn('name', $studentCourses)->pluck('id');

            $this->upcomingClasses = NewClass::with(['teacher', 'course'])
                ->whereIn('course_id', $courseIds)
                ->whereDate('start_date', '<', now('Asia/Kolkata'))
                ->orderBy('start_date', 'desc')
                ->orderBy('class_time', 'desc')
                ->get();
        }

        return view('livewire.previous-class');
    }

    public function hasMarkedAttendance($classId)
    {
        $studentId = Auth::id();
        return ClassAttendance::where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('attended', '1')
            ->exists();
    }
}
