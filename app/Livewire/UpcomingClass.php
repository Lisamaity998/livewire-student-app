<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\StudentInformation;
use App\Models\NewClass;
use App\Models\ClassAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class UpcomingClass extends Component
{
    public $upcomingClasses = [];
    public $selectedClassId;

    #[Layout('layouts.student-app')]
    public function render()
    {
        $student = StudentInformation::where('id', Auth::id())->first();

        if ($student) {
            $studentCourses = array_map('trim', explode(',', $student->course));

            $courseIds = Course::whereIn('name', $studentCourses)->pluck('id');

            $this->upcomingClasses = NewClass::with(['teacher', 'course'])
                ->whereIn('course_id', $courseIds)
                ->whereDate('start_date', '>', now())
                ->orderBy('start_date')
                ->get();
        }

        return view('livewire.upcoming-class');
    }

    public function setSelectedClass($classId)
    {
        $this->selectedClassId = $classId;
    }

    public function attendClass()
    {
        $studentId = Auth::id();
        if (!$this->selectedClassId || !$studentId) return;

        $attendance = ClassAttendance::where('class_id', $this->selectedClassId)
            ->where('student_id', $studentId)
            ->first();

        if (!$attendance) {
            ClassAttendance::create([
                'class_id' => $this->selectedClassId,
                'student_id' => $studentId,
                'attended' => '1',
            ]);
        } elseif ($attendance->attended === '0') {
            $attendance->update(['attended' => '1']);
        }
    }

    public function missClass()
    {
        $studentId = Auth::id();
        if (!$this->selectedClassId || !$studentId) return;

        $attendance = ClassAttendance::where('class_id', $this->selectedClassId)
            ->where('student_id', $studentId)
            ->first();

        if (!$attendance) {
            ClassAttendance::create([
                'class_id' => $this->selectedClassId,
                'student_id' => $studentId,
                'attended' => '0',
            ]);
        } elseif ($attendance->attended === '1') {
            $attendance->update(['attended' => '0']);
        }
    }

}
