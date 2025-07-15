<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\StudentInformation;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use App\Models\Course;

class StudentDataEntry extends Component
{
    use WithFileUploads; //trait
    #[Validate('required|string|min:5')]
    public string $name = '';

    #[Validate('required|string|min:10')]
    public string $address = '';

    #[Validate('required|email')]
    public string $email_address = '';

    #[Validate('required|string|min:8|max:15')]
    public string $password = '';

    #[Validate('required|numeric|digits:10')]
    public string $phone_number = '';

    #[Validate('required|array|min:1')]
    public array $course = [];

    #[Validate('required|image|mimes:jpeg,png')]
    public $image; //mixed


    public function submitForm()
    {
        $this->validate();

        $existingStudent = StudentInformation::where('email_address', $this->email_address)->first();
        if ($existingStudent) {
            if ($existingStudent->status === 'rejected') {
                $rejectedDate = $existingStudent->updated_at;

                if (now()->diffInDays($rejectedDate) < 10) {
                    session()->flash('error', 'You can reapply after 10 days.');
                    return;
                }

                try {
                    $existingStudent->update([
                        'name' => $this->name,
                        'address' => $this->address,
                        'password' => Hash::make($this->password),
                        'phone_number' => $this->phone_number,
                        'course' => implode(',', $this->course),
                        'image' => $this->image ? $this->image->store('images', 'public') : $existingStudent->image,
                        'status' => 'pending', 
                    ]);

                    session()->flash('success', 'Re-application submitted successfully!');
                    $this->reset(['name', 'address', 'email_address', 'password', 'phone_number', 'course', 'image']);
                    return;
                } catch (\Exception $e) {
                    session()->flash('error', 'There was an error while reapplying.');
                    return;
                }
            }

            session()->flash('error', 'You are already registered or your application is under review.');
            return;
        }

        try {
            StudentInformation::create([
                'name' => $this->name,
                'address' => $this->address,
                'email_address' => $this->email_address,
                'password' => Hash::make($this->password),
                'phone_number' => $this->phone_number,
                'course' => implode(',', $this->course),
                'image' => $this->image ? $this->image->store('images', 'public') : null,  // Store the image in the 'storage/app/public/images'
                'status' => 'pending',
            ]);

            $this->reset(['name', 'address', 'email_address', 'password', 'phone_number', 'course', 'image']);
            session()->flash('success', 'Student data submitted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'There was an error submitting the student data.');
        }
    }

    public function render()
    {
        return view('livewire.student-data-entry', ['courses' => Course::all()]);
    }
}
