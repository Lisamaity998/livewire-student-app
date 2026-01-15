<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

class TeacherLogin extends Component
{
    #[Validate('required|email|exists:teacher,email')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    public function login()
    {
        $this->validate();
        // Attempt teacher login using the custom guard
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];
    
        if (auth()->guard('teacher')->attempt($credentials)) {
            $authUser = auth()->guard('teacher')->id();
            logActivity('teacher', (int) $authUser, 'Teacher Login', "Teacher with email '{$this->email}' logged in successfully.");
            return redirect()->route('teacher.dashboard');
        } else {
            session()->flash('error', 'Invalid email or password.');
        }
    }

    #[Layout('layouts.teacher-app')]
    public function render()
    {
        return view('livewire.teacher-login');
    }
}
