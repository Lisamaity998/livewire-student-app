<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\Questions;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MockTest extends Component
{
    public $subjects = [];
    public $selectedSubjectId = null;
    public $testStarted = false;
    public $questions = [];

    public $currentIndex = 0;
    public $answers = [];
    public $testSubmitted = false;
    public $score = 0;
    public $startTime;

    public function mount()
    {
        $this->subjects = Course::all();
    }

    public function startTest()
    {
        if (!$this->selectedSubjectId) {
            session()->flash('error', 'Please select a subject first.');
            return;
        }
        $this->validate([
            'selectedSubjectId' => 'required|exists:course,id',
        ]);
        $this->startTime = now()->format('H:i:s');
        $this->questions = Questions::where('course_id', $this->selectedSubjectId)->inRandomOrder()->take(10)->get()->values();
        $this->testStarted = true;
        $this->currentIndex = 0;
        $this->answers = [];
        $this->testSubmitted = false;
    }

    public function next()
    {
        if (!isset($this->answers[$this->currentIndex])) {
            session()->flash('error', 'Please select an answer before proceeding.');
            return;
        }

        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;
        }
    }

    public function previous()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function submit()
    {
        if (!isset($this->answers[$this->currentIndex])) {
            session()->flash('error', 'Please select an answer before submitting.');
            return;
        }

        $this->score = 0;
        foreach ($this->questions as $index => $question) {
            if (($this->answers[$index]) == $question->correct_answer) {
                $this->score++;
            }
        }

        // Save result
        TestResult::create([
            'student_id' => Auth::id(),
            'course_id' => $this->selectedSubjectId,
            'test_date' => now()->toDateString(),
            'start_time' => $this->startTime,
            'score' => $this->score,
        ]);

        $this->testSubmitted = true;
        session()->flash('success', "Test Submitted Successfully!");
    }

    #[Layout('layouts.student-app')]
    public function render()
    {
        return view('livewire.mock-test');
    }
}
