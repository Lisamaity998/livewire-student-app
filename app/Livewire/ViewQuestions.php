<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Questions;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

class ViewQuestions extends Component
{
    public $questions;
    protected $listeners = ['questionAdded' => 'fatchQuestions'];

    #[Validate('required|exists:course,id')]
    public $course_id;

    #[Validate('required|string|max:255')]
    public string $question_name;

    #[Validate('required|string|max:255')]
    public string $answer1;

    #[Validate('required|string|max:255')]
    public string $answer2;

    #[Validate('required|string|max:255')]
    public string $answer3;

    #[Validate('required|string|max:255')]
    public string $answer4;

    #[Validate('required|string|max:255')]
    public string $correct_answer;

    public $question_id;
    public $courses;

    public function mount()
    {
        $this->fatchQuestions();
        $this->courses = Course::all();
    }

    public function fatchQuestions()
    {
        $this->questions = Questions::with('course')->get();
    }

    public function deleteQuestion($id)
    {
        $question = Questions::find($id);
        if ($question) {
            $question->delete();
            session()->flash('success', 'Question deleted successfully!');
            $this->fatchQuestions();
        } else {
            session()->flash('error', 'Question not found.');
        }
    }

    public function editQuestion($id)
    {
        $question = Questions::find($id);
        $course = Course::all();
        $this->courses = $course;
        $this->question_id = $id;
        if ($question) {
            $this->course_id = $question->course_id;
            $this->question_name = $question->question_name;
            $this->answer1 = $question->answer1;
            $this->answer2 = $question->answer2;
            $this->answer3 = $question->answer3;
            $this->answer4 = $question->answer4;
            $this->correct_answer = $question->correct_answer;
            logger('Edit clicked: ' . $id);
            $this->dispatch('openUpdateQuestionModal');
            // dd($this->course_id, $this->question_name, $this->answer1, $this->answer2, $this->answer3, $this->answer4, $this->correct_answer);
        } else {
            session()->flash('error', 'Question not found.');
        }
    }

    public function updateQuestion()
    {
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
            session()->flash('success', 'Question updated successfully!');
            $this->reset('question_id', 'course_id', 'question_name', 'answer1', 'answer2', 'answer3', 'answer4', 'correct_answer');
            $this->dispatch('closeUpdateQuestionModal');
            $this->fatchQuestions();
        } else {
            session()->flash('error', 'Question not found.');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.view-questions');
    }
}
