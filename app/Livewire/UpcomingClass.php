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
use Livewire\Attributes\On;

class UpcomingClass extends Component
{
    public $upcomingClasses = [];
    public $selectedClassId;

    public $modalData = [
        'className' => '',
        'topic' => '',
        'teacher' => '',
        'date' => '',
        'time' => ''
    ];

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

    #[On('setSelectedClass')]
    public function setSelectedClass($classId)
    {
        $this->selectedClassId = $classId;
        // Find the class and set modal data
        $class = collect($this->upcomingClasses)->firstWhere('id', $classId);
        if ($class) {
            $this->modalData = [
                'className' => $class->class_name,
                'topic' => $class->course->name ?? 'N/A',
                'teacher' => $class->teacher->name ?? 'N/A',
                'date' => Carbon::parse($class->start_date)->format('d M Y'),
                'time' => Carbon::parse($class->class_time)->format('H:i')
            ];
        }
        
        logger('Selected class set: '.$classId);
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
        $this->dispatch('closeClassModal');
    }

    public function missClass()
    {
        $studentId = Auth::id();
        // dd($this->selectedClassId, $studentId);
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
        $this->dispatch('closeClassModal');
    }
}
