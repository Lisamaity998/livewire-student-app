<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\NewClass;
use App\Models\StudentInformation;

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

        if ($newClassCreated) {
            $course = Course::find($this->course_id);
            $teacher = Teacher::find($this->teacher_id);
            $className = $newClassCreated->class_name;
            $selectedDate = $newClassCreated->start_date;
    
            if ($course && $teacher) {
                $users = StudentInformation::where('status', 'approved')->where('course', 'LIKE', "%{$course->name}%")->get();
                foreach ($users as $user) {
                    try {
                        sendEmail($user, $className, $teacher->name, $selectedDate);
                    } catch (\Exception $e) {
                        session()->flash('error', 'Failed to send email: ' . $e->getMessage());
                    }
                }
            }
        }

        session()->flash('success', 'New class added successfully!');
        $this->reset();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.add-new-class', [
            'courses' => Course::all(),
        ]);
    }
}
