<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StudentInformation;

class StudentList extends Component
{
    public $search = '';
    public $status = 'approved';

    public function render()
    {
        $studentData = StudentInformation::where('status', $this->status)
        ->where(function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email_address', 'like', '%'.$this->search.'%')
                ->orWhere('phone_number', 'like', '%'.$this->search.'%')
                ->orWhere('course', 'like', '%'.$this->search.'%');
        })->get();
        return view('livewire.student-list', ['data' => $studentData])->layout('layouts.app');
    }
}
