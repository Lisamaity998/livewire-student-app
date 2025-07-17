<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Questions;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class ViewQuestions extends Component
{
    public $questions;

    protected $listeners = [
        'questionAdded' => 'refreshQuestions',
        'resetAddForm' => 'handleResetAddForm'
    ];

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

    public $question_id;
    public $courses;

    public function mount()
    {
        $this->refreshQuestions();
        $this->courses = Course::all();
    }

    public function refreshQuestions()
    {
        $this->questions = Questions::with('course')->get();
    }

    public function fatchQuestions()
    {
        $this->refreshQuestions();
    }

    public function deleteQuestion($id)
    {
        try {
            $question = Questions::find($id);
            if ($question) {
                $question->delete();
                $this->refreshQuestions();
                session()->flash('success', 'Question deleted successfully!');
            } else {
                session()->flash('error', 'Question not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    public function editQuestion($id)
    {
        try {
            $question = Questions::find($id);
            
            if ($question) {
                $this->question_id = $id;
                $this->course_id = $question->course_id;
                $this->question_name = $question->question_name;
                $this->answer1 = $question->answer1;
                $this->answer2 = $question->answer2;
                $this->answer3 = $question->answer3;
                $this->answer4 = $question->answer4;
                $this->correct_answer = $question->correct_answer;
                
                // Clear any previous errors
                $this->resetErrorBag();
                $this->resetValidation();
                
                $this->dispatch('openUpdateQuestionModal');
            } else {
                session()->flash('error', 'Question not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load question: ' . $e->getMessage());
        }
    }

    public function updateQuestion()
    {
        try {
            $this->validate();
            
            $question = Questions::find($this->question_id);
            
            if ($question) {
                $question->update([
                    'course_id' => $this->course_id,
                    'question_name' => $this->question_name,
                    'answer1' => $this->answer1,
                    'answer2' => $this->answer2,
                    'answer3' => $this->answer3,
                    'answer4' => $this->answer4,
                    'correct_answer' => $this->correct_answer,
                ]);
                
                // Reset form
                $this->resetUpdateForm();

                // Close modal
                $this->dispatch('closeUpdateQuestionModal');

                // Refresh questions list
                // $this->refreshQuestions();

                session()->flash('success', 'Question updated successfully!');
            } else {
                session()->flash('error', 'Question not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    #[On('resetUpdateForm')]
    public function resetUpdateForm()
    {
        $this->reset([
            'question_id', 
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

    public function handleResetAddForm()
    {
        // This method handles the reset from the child component
        $this->refreshQuestions();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.view-questions');
    }
}
