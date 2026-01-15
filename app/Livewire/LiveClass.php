<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\StudentInformation;
use App\Models\ClassAttendance;
use Illuminate\Support\Facades\Auth;
use App\Models\NewClass;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class LiveClass extends Component
{
    public $upcomingClasses = [];
    public $selectedClassId;

    public $modalData = [
        'className' => '',
        'topic' => '',
        'teacher' => '',
        'date' => '',
        'time' => '',
        'showAttendanceButtons' => false,
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
                ->whereDate('start_date', '=', now('Asia/Kolkata'))
                ->orderBy('start_date')
                ->orderBy('class_time')
                ->get();
        }

        return view('livewire.live-class');
    }

    public function hasMarkedAttendance($classId)
    {
        $studentId = Auth::id();
        return ClassAttendance::where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('attended', '1')
            ->exists();
    }

    public function joinClass($id)
    {
        $class = NewClass::find($id);

        if (!$class) {
            session()->flash('error', 'Class not found.');
            return;
        }

        $startDateTime = Carbon::parse($class->start_date . ' ' . $class->class_time, 'Asia/Kolkata');
        $now = Carbon::now('Asia/Kolkata');

        if ($now->lt($startDateTime)) {
            session()->flash('warning', 'Class has not started yet. Please wait until the scheduled time.');
            return;
        }

        return redirect()->route('class.view', $id);
    }

    #[On('setSelectedClass')]
    public function setSelectedClass($classId)
    {
        $this->selectedClassId = $classId;
        // Find the class and set modal data
        $class = collect($this->upcomingClasses)->firstWhere('id', $classId);
        if ($class) {
            $startTime = Carbon::parse($class->start_date . ' ' . $class->class_time);
            $now = Carbon::now();

            // Only show buttons if class starts in more than 30 minutes
            $showAttendanceButtons = $now->lt($startTime->subMinutes(30));

            $this->modalData = [
                'className' => $class->class_name,
                'topic' => $class->course->name ?? 'N/A',
                'teacher' => $class->teacher->name ?? 'N/A',
                'date' => Carbon::parse($class->start_date)->format('d M Y'),
                'time' => Carbon::parse($class->class_time)->format('h:i A'),
                'showAttendanceButtons' => $showAttendanceButtons
            ];
        }
    }

    public function attendClass()
    {
        try{
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
            session()->flash('success', 'You’ve successfully marked your interest to attend the class.');
            $this->dispatch('closeClassModal');
        }catch (\Exception $e) {
            session()->flash('error', 'Oops! We couldn’t save your interest to attend the class. Please try again later.');
        }
    }

    public function missClass()
    {
        try{
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
            session()->flash('warning', 'You’ve successfully marked your decision to miss the class.');
            $this->dispatch('closeClassModal');
        }catch (\Exception $e) {
            session()->flash('error', 'Oops! We couldn’t save your decision to miss the class. Please try again later.');
        }
    }
}
