<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminLogin extends Component
{
    #[Validate('required|email|exists:admin,email')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    public function login()
    {
        $this->validate();
        // Attempt admin login using the custom guard
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];
    
        if (auth()->guard('admin')->attempt($credentials)) {
            return redirect()->route('approval');
        } else {
            session()->flash('error', 'Invalid email or password.');
        }
    }

    public function render()
    {
        return view('livewire.admin-login');
    }
}
