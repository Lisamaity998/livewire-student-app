<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Questions;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ViewQuestions extends Component
{
    use WithFileUploads;

    public $search = '';

    public $csv_file;

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

    public function uploadCsv()
    {
        $this->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = fopen($this->csv_file->getRealPath(), 'r');
        $header = fgetcsv($file); // skip first row

        $inserted = 0;
        $skipped = 0;

        // Map course names to IDs
        $courses = Course::pluck('id', 'name')->mapWithKeys(
            function($id, $name) {
                return [strtolower(trim($name)) => $id];
            }
        );

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) !== 7) {
                    $skipped++;
                    continue;
                }

                // Extract data
                $courseName    = isset($row[0]) ? trim($row[0]) : null;
                $question_name = isset($row[1]) ? trim($row[1]) : null;
                $a1            = isset($row[2]) ? trim($row[2]) : null;
                $a2            = isset($row[3]) ? trim($row[3]) : null;
                $a3            = isset($row[4]) ? trim($row[4]) : null;
                $a4            = isset($row[5]) ? trim($row[5]) : null;
                $correct       = isset($row[6]) ? trim($row[6]) : null;

                $courseNameKey = strtolower(trim($courseName));
                $course_id = $courses[$courseNameKey] ?? null;

                if (!$course_id) {
                    $skipped++;
                    continue;
                }

                // Check for duplicate question in the same course
                $exists = Questions::where('course_id', $course_id)
                    ->where('question_name', $question_name)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $data = [
                    'course_id' => $course_id,
                    'question_name' => $question_name,
                    'answer1' => $a1,
                    'answer2' => $a2,
                    'answer3' => $a3,
                    'answer4' => $a4,
                    'correct_answer' => $correct,
                ];

                $validator = Validator::make($data, [
                    'course_id' => 'required|exists:course,id',
                    'question_name' => 'required|string|max:255',
                    'answer1' => 'required|string|max:255',
                    'answer2' => 'required|string|max:255',
                    'answer3' => 'required|string|max:255',
                    'answer4' => 'required|string|max:255',
                    'correct_answer' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    $skipped++;
                    continue;
                }

                Questions::create($data);
                $inserted++;
            }

            DB::commit();
            session()->flash('success', "Upload successful! Inserted: $inserted, Skipped: $skipped");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', "Upload failed: " . $e->getMessage());
        }

        fclose($file);
        $this->reset('csv_file');

        $this->dispatch('closeBulkAddQuestionModal');
        $this->fatchQuestions();
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
                $this->refreshQuestions();

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
        $searchTerm = '%' . $this->search . '%';

        $this->questions = Questions::with('course')
            ->where(function ($query) use ($searchTerm) {
                $query->where('question_name', 'like', $searchTerm)
                    ->orWhere('answer1', 'like', $searchTerm)
                    ->orWhere('answer2', 'like', $searchTerm)
                    ->orWhere('answer3', 'like', $searchTerm)
                    ->orWhere('answer4', 'like', $searchTerm)
                    ->orWhere('correct_answer', 'like', $searchTerm)
                    ->orWhereHas('course', function ($courseQuery) use ($searchTerm) {
                        $courseQuery->where('name', 'like', $searchTerm);
                    });
            })
            ->latest()
            ->get();

        return view('livewire.view-questions');
    }
}
