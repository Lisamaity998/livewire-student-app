<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Questions;
use App\Models\Course;
use App\Models\StudentInformation;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Events\MockTestNotification;

class QuestionEntry extends Component
{

    #[Validate('required|exists:course,id')]
    public $course_id;

    #[Validate('required|string|max:255')]
    public string $question_name = '';
    
    #[Validate('required|string|max:255')]
    public string $answer1 = '';
    
    #[Validate('required|string|max:255')]
    public string $answer2 = '';
    
    #[Validate('required|string|max:255')]
    public string $answer3 = '';
    
    #[Validate('required|string|max:255')]
    public string $answer4 = '';
    
    #[Validate('required|string|max:255')]
    public string $correct_answer = '';

    public $courses;

    public function mount()
    {
        $this->courses = Course::all();
    }

    public function addQuestion()
    {
        try {
            $this->validate();

            // Check for duplicate question in the same course
            $exists = Questions::where('course_id', $this->course_id)
                ->where('question_name', $this->question_name)
                ->exists();

            if ($exists) {
                session()->flash('error', 'This question already exists in the selected course.');
                return;
            }
            
            Questions::create([
                'course_id' => $this->course_id,
                'question_name' => $this->question_name,
                'answer1' => $this->answer1,
                'answer2' => $this->answer2,
                'answer3' => $this->answer3,
                'answer4' => $this->answer4,
                'correct_answer' => $this->correct_answer,
            ]);
            $courseName = Course::find($this->course_id)->name;

            // Notify the user
            $questionCount = Questions::where('course_id', $this->course_id)->count();
            if($questionCount == 10) {
                $users = StudentInformation::where('status', 'approved')->where('course', 'LIKE', "%{$courseName}%")->get();
                foreach ($users as $user) {
                    try {
                        // Send notification to the student using websockets
                        $message = [
                            'type' => 'mockTest',
                            'title' => "New Mock Test Available",
                            'description' => "A mock test for '{$courseName}' is now available. Take the test whenever you are free."
                        ];
                        $userId = $user->id;
                        event(new MockTestNotification($message, $userId));
                    } catch (\Exception $e) {
                        session()->flash('error', 'Failed to send email: ' . $e->getMessage());
                    }
                }
            }
            // Log the activity
            $authUser = auth()->guard('admin')->id();
            logActivity('admin', (int) $authUser, 'Question Added', "A new question '{$this->question_name}' was added to course ID {$courseName}.");
            
            session()->flash('success', 'Question added successfully!');
            
            // Reset form immediately
            $this->resetForm();
            
            // Close modal
            $this->dispatch('closeAddQuestionModal');
            
            // Notify parent component
            $this->dispatch('questionAdded');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add question: ' . $e->getMessage());
            return;
        }
    }

    #[On('resetAddForm')]
    public function resetForm()
    {
        $this->reset([
            'course_id', 
            'question_name', 
            'answer1', 
            'answer2', 
            'answer3', 
            'answer4', 
            'correct_answer'
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.question-entry', [
            'courses' => $this->courses ?? Course::all()
        ]);
    }
}
