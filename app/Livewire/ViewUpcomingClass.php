<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\NewClass;
use App\Models\Course;
use App\Models\Teacher;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Events\ClassMaterialUploadedNotification;
use App\Events\ClassDeletedNotification;
use App\Models\StudentInformation;
use Illuminate\Support\Carbon;

class ViewUpcomingClass extends Component
{
    use WithFileUploads;
    public $search = '';

    public $upcomingClasses;

    // For edit modal
    public $selectedClassId;
    public $class_name;
    public $course_id;

    public $isEditable = true;

    #[Validate('required|date|after_or_equal:today')]
    public $selected_date;

    #[Validate('required|date_format:H:i')]
    public $class_time;

    #[Validate('required|exists:teacher,id')]
    public $teacher_id;

    #[Validate('nullable|file|mimes:mp4,avi|max:20480')]
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

            // Notify students about the class deletion
            $users = StudentInformation::where('status', 'approved')
                ->where('course', 'LIKE', "%{$class->course->name}%")
                ->get();
            $teacherName = Teacher::find($class->teacher_id)->name ?? '';
            foreach ($users as $user) {
                try {
                    $message = [
                        'type' => 'classCancelled',
                        'title' => "Class Cancelled",
                        'description' => "The '{$class->class_name}' scheduled for {$class->start_date} at {$class->class_time}, taught by {$teacherName}, has been cancelled."
                    ];
                    $userId = $user->id;
                    event(new ClassDeletedNotification($message, $userId));
                } catch (\Exception $e) {
                    session()->flash('error', 'Failed to send notification: ' . $e->getMessage());
                }
            }

            // Log the activity
            $authUser = auth()->guard('admin')->id();
            $className = $class->class_name;
            $classDate = $class->start_date;
            logActivity('admin', (int) $authUser, 'Class Deleted', "'{$className}' scheduled on {$classDate} has been deleted.");

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

        // Check if editable: allow until day before class date
        $today = now()->startOfDay();
        $classDate = \Carbon\Carbon::parse($class->start_date)->startOfDay();

        $this->isEditable = $today->lessThan($classDate);

        $this->dispatch('openEditClassModal', teacherId: $this->teacher_id);
    }

    public function updatedvideo(){
        $this->validateOnly('video');

    }

    public function updateClass()
    {
        try{

            $this->validate();
    
            $class = NewClass::find($this->selectedClassId);
            $materialsUpdated = false;
    
            if (!$class) {
                session()->flash('error', 'Class not found.');
                return;
            }

            $class->start_date = $this->selected_date;
            $class->class_time = $this->class_time;
            $class->teacher_id = $this->teacher_id;

            // Optional file updates
            if ($this->video || $this->notes || $this->youtubeUrl) {
                $classStart = Carbon::parse($class->start_date . ' ' . $class->class_time);
                $uploadWindowStart = $classStart->copy()->subMinutes(15);
                $uploadWindowEnd = $classStart->copy()->endOfDay();

                $now = now();

                if ($now->lt($uploadWindowStart) || $now->gt($uploadWindowEnd)) {
                    session()->flash('error', 'You can only upload class materials between '
                        . $uploadWindowStart->format('d M Y h:i A') . ' and '
                        . $uploadWindowEnd->format('d M Y h:i A'));
                    return;
                }
                     
                if ($this->video) {
                    $class->video = $this->video->store('class_videos', 'public');
                    $this->video = null; // Reset after storing
                    $materialsUpdated = true;
                }
        
                if ($this->notes) {
                    $class->notes = $this->notes->store('class_notes', 'public');
                    $this->notes = null;
                    $materialsUpdated = true;
                }
        
                if($this->youtubeUrl){
                    $class->youtube_url = $this->youtubeUrl;
                    $this->youtubeUrl = null;
                    $materialsUpdated = true;
                }
            }
    
            $class->save();

            if ($materialsUpdated) {
                // Notify students about the new materials
                $className = $class->class_name;
                $time = $class->class_time;

                $users = StudentInformation::where('status', 'approved')
                    ->where('course', 'LIKE', "%{$class->course->name}%")
                    ->get();

                foreach ($users as $user) {
                    try {
                        $message = [
                            'type' => 'upload', 
                            'title' => "Class Materials Uploaded",
                            'description' => "Learning materials have been uploaded for your upcoming '{$className}'  scheduled today at {$time}."
                        ];
                        $userId = $user->id;

                        event(new ClassMaterialUploadedNotification($message, $userId));
                    } catch (\Exception $e) {
                        session()->flash('error', 'Failed to send email: ' . $e->getMessage());
                    }
                }
            }

            session()->flash('success', 'Class updated successfully!');
            $this->dispatch('closeEditClassModal');
            $this->refreshUpcomingClasses();
            // Log the activity
            $authUser = auth()->guard('admin')->id();
            $courseName = Course::find($this->course_id)->name;
            $teacher = Teacher::find($this->teacher_id);
            logActivity('admin', (int) $authUser, 'Class Updated', "'{$this->class_name}' scheduled on {$this->selected_date} has been updated. Course name {$courseName}, asigned teacher is {$teacher->name}.");
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

