<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TestResult;
use App\Models\Course;


class StudentsTestResults extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $testResults = TestResult::with(['course', 'student'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('livewire.students-test-results', [
            'testResults' => $testResults,
        ]);
    }
}
