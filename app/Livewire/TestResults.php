<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentInformation;
use App\Models\Course;


#[Layout('layouts.student-app')]
class TestResults extends Component
{
    public function render()
    {
        $student = StudentInformation::where('id', Auth::id())->first();
        if ($student) {
            $studentCourses = array_map('trim', explode(',', $student->course));
            $courseIds = Course::whereIn('name', $studentCourses)->pluck('id');

            $testResults = TestResult::with(['course', 'student'])
                ->whereIn('course_id', $courseIds)
                ->where('student_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $testResults = collect();
        }
        return view('livewire.test-results', [
            'testResults' => $testResults,
        ]);
    }
}
