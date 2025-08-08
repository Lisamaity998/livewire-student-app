<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\NewClass;
use App\Models\StudentInformation;
use App\Notifications\ClassCreatedNotification;
use App\Events\NewClassNotification;
use Illuminate\Support\Facades\Auth;

class AddNewClass extends Component
{
    #[Validate('required|string|min:3|max:50')]
    public $class_name = '';
    
    #[Validate('required|date|after_or_equal:today')]
    public $selected_date;

    #[Validate('required|date_format:H:i')]
    public $class_time = '';
    
    #[Validate('required|exists:course,id')]
    public $course_id;
    
    #[Validate('required|exists:teacher,id')]
    public $teacher_id;

    public $courseName = '';
    public $teachers = [];

    public function updatedCourseId($value)
    {
        $course = Course::find($value);
        if ($course) {
            $this->courseName = $course->name;

            $this->teachers = Teacher::where('skills', 'LIKE', "%{$course->name}%")->get();
        } else {
            $this->teachers = [];
        }

        $this->teacher_id = '';
    }

    public function addClass()
    {
        $this->validate();

        $newClassCreated = NewClass::create([
            'class_name' => $this->class_name,
            'start_date' => $this->selected_date,
            'class_time' => $this->class_time,
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
        ]);
        
        $teacher = Teacher::find($this->teacher_id);
        $authUser = auth()->guard('admin')->id();
        logActivity('admin', (int) $authUser , 'Class Created', "A new '{$newClassCreated->class_name}' was created scheduled on {$newClassCreated->selected_date} asigned teacher is {$teacher->name}.");
        
        if ($newClassCreated) {
            $course = Course::find($this->course_id);
            $className = $newClassCreated->class_name;
            $selectedDate = $newClassCreated->start_date;
    
            if ($course && $teacher) {
                $users = StudentInformation::where('status', 'approved')->where('course', 'LIKE', "%{$course->name}%")->get();
                foreach ($users as $user) {
                    try {
                        // Your existing email logic
                        sendEmail($user, $className, $teacher->name, $selectedDate);

                        // New: Send notification to the student
                        $user->notify(new ClassCreatedNotification($newClassCreated));

                        $message = "A new '{$className}' is scheduled on {$selectedDate} at {$this->class_time}, tought by {$teacher->name}";
                        $userId = $user->id;

                        event(new NewClassNotification($message, $userId));

                    } catch (\Exception $e) {
                        session()->flash('error', 'Failed to send email: ' . $e->getMessage());
                    }
                }
            }
        }
        $this->reset();
        $this->dispatch('closeAddClassModal');
        $this->dispatch('classAdded');
        session()->flash('success', 'New class added successfully!');
    }

    public function render()
    {
        return view('livewire.add-new-class', [
            'courses' => Course::all(),
        ]);
    }
}
