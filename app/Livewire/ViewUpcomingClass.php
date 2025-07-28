<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\NewClass;
use App\Models\Course;
use App\Models\Teacher;
use Livewire\WithFileUploads;

class ViewUpcomingClass extends Component
{
    use WithFileUploads;
    public $search = '';

    public $upcomingClasses;

    // For edit modal
    public $selectedClassId;
    public $class_name;
    public $course_id;
    public $selected_date;
    public $class_time;
    public $teacher_id;

    public $video;
    public $notes;

    public $courses = [];
    public $teachers = [];

    protected $listeners = [
        'classAdded' => 'refreshUpcomingClasses',
    ];

    public function mount()
    {
        $this->refreshUpcomingClasses();
        $this->courses = Course::all();
    }

    public function refreshUpcomingClasses()
    {
        $this->upcomingClasses = NewClass::with(['course', 'teacher', 'interestedStudents'])->latest()->get();
        // $this->upcomingClasses = NewClass::with(['course', 'teacher', 'interestedStudents'])->latest('start_date')->get();
    }

    public function deleteClass($classId)
    {
        $class = NewClass::find($classId);
        if ($class) {
            $class->delete();
            // $this->refreshUpcomingClasses();
            session()->flash('success', 'Class deleted successfully!');
        } else {
            session()->flash('error', 'Class not found.');
        }
    }

    public function editClass($classId)
    {
        $class = NewClass::with('course', 'teacher')->find($classId);

        if (!$class) {
            session()->flash('error', 'Class not found.');
            return;
        }

        $this->selectedClassId = $class->id;
        $this->class_name = $class->class_name;
        $this->course_id = $class->course_id;
        $this->selected_date = $class->start_date;
        $this->class_time = $class->class_time;
        $this->teacher_id = $class->teacher_id;

        // Load teachers for the course
        $this->teachers = Teacher::where('skills', 'LIKE', "%{$class->course->name}%")->get();

        $this->dispatch('openEditClassModal');
    }

    public function updateClass()
    {
        $this->validate([
            'selected_date' => 'required|date|after_or_equal:today',
            'class_time' => 'required|date_format:H:i',
            'teacher_id' => 'required|exists:teacher,id',
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:20480',
            'notes' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $class = NewClass::find($this->selectedClassId);

        if (!$class) {
            session()->flash('error', 'Class not found.');
            return;
        }

        $class->start_date = $this->selected_date;
        $class->class_time = $this->class_time;
        $class->teacher_id = $this->teacher_id;

        // Optional file updates
        if ($this->video) {
            $class->video = $this->video->store('class_videos', 'public');
        }

        if ($this->notes) {
            $class->notes = $this->notes->store('class_notes', 'public');
        }

        $class->save();

        session()->flash('success', 'Class updated successfully!');
        $this->dispatch('closeEditClassModal');
        $this->refreshUpcomingClasses();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $classes = NewClass::with(['course', 'teacher', 'interestedStudents'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('class_name', 'like', $searchTerm)
                    ->orWhereHas('course', function ($courseQuery) use ($searchTerm) {
                        $courseQuery->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($searchTerm) {
                        $teacherQuery->where('name', 'like', $searchTerm);
                    });
                    // ->orWhereDate('start_date', 'like', $searchTerm)
                    // ->orWhere('class_time', 'like', $searchTerm);
            })
            ->latest()
            ->get();

        return view('livewire.view-upcoming-class', compact('classes'));
    }
}

