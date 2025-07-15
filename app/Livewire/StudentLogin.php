<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentInformation;


class StudentLogin extends Component
{
    #[Validate('required|email|exists:student_information,email_address')]
    public string $email = '';

    #[Validate('required|string|min:8|max:15')]
    public string $password = '';

    public function login()
    {
        try {
            $this->validate();

            $student = StudentInformation::where('email_address', $this->email)->first();
            if (!$student) {
                session()->flash('error', 'This email is not registered.');
                return;
            }

            if ($student && Hash::check($this->password, $student->password)) {
                if ($student->status === 'pending') {
                    session()->flash('warning', 'Your application is still pending.');
                    return;
                }
                if ($student->status === 'rejected') {
                    session()->flash('error', 'Your application is rejected. Please wait for 10 days and apply again.');
                    return;
                }
                if ($student->status === 'approved') {
                    auth()->guard('student')->login($student);
                    return redirect()->route('student.dashboard');
                }
            }else {
                session()->flash('error', 'Incorrect password. Please try again.');
                return;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.student-login');
    }
}
