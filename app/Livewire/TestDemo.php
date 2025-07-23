<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\Questions;
use App\Models\TestResult;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TestDemo extends Component
{
    public $subjects = [];
    public $selectedSubjectId = null;
    public $hasQuestions = false;
    public $testStarted = false;
    public $questions = [];

    public $currentIndex = 0;
    public $answers = [];
    public $testSubmitted = false;
    public $score = 0;
    public $startTime;
    public $remainingTime = 600;

    protected $listeners = [
        'syncTime' => 'syncTime',
        'autoSubmit' => 'submit',
    ];

    public function mount()
    {
        $student = StudentInformation::find(Auth::id());

        if ($student) {
            $studentCourses = array_map('trim', explode(',', $student->course));
            $this->subjects = Course::whereIn('name', $studentCourses)->get();
        }

        if (session('mock_test_started')) {
            $this->testStarted = true;
            $this->startTime = session('mock_start_time');

            $latestTest = TestResult::where('student_id', Auth::id())
                ->where('status', 'in_progress')
                ->latest()
                ->first();

            if ($latestTest) {
                $this->selectedSubjectId = $latestTest->course_id;
                $this->questions = session('session_questions', []);
                $this->answers = session('session_answers', []);

                // Auto-submit due to reload
                $this->submit();
                $this->testSubmitted = false;
                $this->testStarted = false;
            }
        }
    }

    public function updatedSelectedSubjectId($value)
    {
        if ($value) {
            $questionCount = Questions::where('course_id', $value)->count();
            $this->hasQuestions = $questionCount >= 10;

            // Check if student has already passed this subject with 80% or more
            $maxScore = TestResult::where('student_id', Auth::id())
                ->where('course_id', $value)
                ->where('status', 'submitted')
                ->max('score');

            if ($maxScore >= 8) {
                session()->flash('success', 'You have already passed this subject with 80% or more.');
                $this->hasQuestions = false;
            }
        } else {
            $this->hasQuestions = false;
        }
    }

    public function startTest()
    {
        $this->validate([
            'selectedSubjectId' => 'required|exists:course,id',
        ]);

        $this->questions = Questions::where('course_id', $this->selectedSubjectId)
            ->inRandomOrder()
            ->take(10)
            ->get()
            ->values();

        $this->startTime = now('Asia/Kolkata');
        $this->testStarted = true;
        $this->currentIndex = 0;
        $this->answers = [];
        $this->testSubmitted = false;

        TestResult::create([
            'student_id' => Auth::id(),
            'course_id' => $this->selectedSubjectId,
            'test_date' => now('Asia/Kolkata')->toDateString(),
            'start_time' => $this->startTime,
            'total_questions' => count($this->questions),
            'score' => 0,
            'status' => 'in_progress',
        ]);

        session(['mock_test_started' => true, 'mock_start_time' => $this->startTime, 'session_questions' => $this->questions, 'session_answers' => $this->answers]);
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
        session(['session_answers' => $this->answers]);
    }

    public function previous()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
        session(['session_answers' => $this->answers]);
    }

    public function syncTime()
    {
        if (!$this->testStarted || $this->testSubmitted) return;

        $start = Carbon::parse($this->startTime);
        $now = now('Asia/Kolkata');
        $duration = $now->diffInSeconds($start);

        $totalAllowed = 10 * 60;
        $this->remainingTime = max($totalAllowed - $duration, 0);

        if ($this->remainingTime <= 0) {
            $this->submit();
        }

        $this->dispatch('updateRemainingTime', $this->remainingTime);
    }

    public function submit()
    {
        if ($this->testSubmitted) return;

        $this->score = 0;

        foreach ($this->questions as $index => $question) {
            $userAnswer = $this->answers[$index] ?? '';

            if ($userAnswer === $question->correct_answer) {
                $this->score++;
            }
        }

        TestResult::where('student_id', Auth::id())
            ->where('course_id', $this->selectedSubjectId)
            ->where('status', 'in_progress')
            ->latest()
            ->first()
            ->update([
                'score' => $this->score,
                'status' => 'submitted',
            ]);

        $this->testSubmitted = true;
        $this->testStarted = false;

        session()->forget(['mock_test_started', 'mock_start_time', 'session_questions', 'session_answers']);
        session()->flash('success', "Test Submitted Successfully!");
    }

    #[Layout('layouts.student-app')]
    public function render()
    {
        return view('livewire.test-demo');
    }
}
