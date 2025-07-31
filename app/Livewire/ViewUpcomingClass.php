<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\NewClass;
use App\Models\Course;
use App\Models\Teacher;
use Livewire\Attributes\Validate;
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

    #[Validate('required|date|after_or_equal:today')]
    public $selected_date;

    #[Validate('required|date_format:H:i')]
    public $class_time;

    #[Validate('required|exists:teacher,id')]
    public $teacher_id;

    #[Validate('required|file|mimes:mp4,avi|max:20480')]
    public $video;

    #[Validate('nullable|file|mimes:pdf|max:10240')]
    public $notes;

    #[Validate('nullable|url|starts_with:https://youtu.be')]
    public $youtubeUrl;

    public $courses = [];
    public $teachers = [];

    protected $listeners = [
        'classAdded' => 'refreshUpcomingClasses',
    ];

    public function mount()
    {
        $this->refreshUpcomingClasses();
        $this->courses = Course::all();
        // dd(phpinfo());
        // dd([
        //     'post_max_size' => ini_get('post_max_size'),
        //     'upload_max_filesize' => ini_get('upload_max_filesize'),
        //     'max_file_uploads' => ini_get('max_file_uploads'),
        // ]);
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
            $this->refreshUpcomingClasses();
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

        // Clear everything first
        $this->reset(['selectedClassId', 'class_name', 'course_id', 'selected_date', 'class_time', 'teacher_id', 'video', 'notes', 'youtubeUrl']);
        $this->teachers = [];

        $this->selectedClassId = $class->id;
        $this->class_name = $class->class_name;
        $this->course_id = $class->course_id;
        $this->selected_date = $class->start_date;
        $this->class_time = date('H:i', strtotime($class->class_time));
        
        // Load teachers for the course
        $this->teachers = Teacher::where('skills', 'LIKE', "%{$class->course->name}%")->get();
        $this->teacher_id = $class->teacher_id;

        $this->dispatch('openEditClassModal', teacherId: $this->teacher_id);
    }

    public function updatedvideo(){
        $this->validateOnly('video');

    }

    public function updateClass()
    {
        // dd($this->video);
        try{

            $this->validate();
    
            // if ($this->video && $this->video->getSize() > 20480) { 
            //     session()->flash('error', 'Video File too large. Maximum size is 20MB.');
            //     return;
            // }
    
            // if ($this->notes && $this->notes->getSize() > 10240) {
            //     session()->flash('error', 'Notes File too large. Maximum size is 10MB.');
            //     return;
            // }
    
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
                $this->video = null; // Reset after storing
            }
    
            if ($this->notes) {
                $class->notes = $this->notes->store('class_notes', 'public');
                $this->notes = null;
            }
    
            if($this->youtubeUrl){
                $class->youtube_url = $this->youtubeUrl;
                $this->youtubeUrl = null;
            }
    
            $class->save();
    
            session()->flash('success', 'Class updated successfully!');
            $this->dispatch('closeEditClassModal');
            $this->refreshUpcomingClasses();
            $this->reset(['selectedClassId', 'class_name', 'course_id', 'selected_date', 'class_time', 'teacher_id', 'video', 'notes', 'youtubeUrl']);
        }catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Failed to update class: ' . $e->getMessage());
            throw $e; // Re-throw to show validation errors in the UI
        }
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
            })
            ->orderBy('start_date', 'desc')
            ->orderBy('class_time', 'desc')
            ->get();

        return view('livewire.view-upcoming-class', compact('classes'));
    }
}

