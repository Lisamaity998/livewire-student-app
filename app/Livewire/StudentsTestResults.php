<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TestResult;
use App\Models\Course;


class StudentsTestResults extends Component
{
    public $search = '';

    #[Layout('layouts.app')]
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $testResults = TestResult::with(['course', 'student'])
            ->where(function ($query) use ($searchTerm) {
                $query->whereHas('student', function ($studentQuery) use ($searchTerm) {
                    $studentQuery->where('name', 'like', $searchTerm);
                })->orWhereHas('course', function ($courseQuery) use ($searchTerm) {
                    $courseQuery->where('name', 'like', $searchTerm);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.students-test-results', [
            'testResults' => $testResults,
        ]);
    }
}
