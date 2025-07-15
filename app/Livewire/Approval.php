<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StudentInformation;
use Livewire\Attributes\Layout;

class Approval extends Component
{
    public $search = '';

    public function approve($id)
    {
        try {
            $item = StudentInformation::find($id);
            $item->status = 'approved';
            $item->save();
            session()->flash('success', 'Student approved successfully!');
        } catch (\Exception $e) { 
            session()->flash('error', 'There was an error approving the student.');
            return;
        }
    }
    
    public function reject($id)
    {
        try {
            $item = StudentInformation::find($id);
            $item->status = 'rejected';
            $item->save();
            session()->flash('success', 'Student rejected successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'There was an error rejecting the student.');
            return;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $status = 'pending';
        $studentData = StudentInformation::latest()
        ->where('status', $status)
        ->where(function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('address', 'like', '%'.$this->search.'%')
                ->orWhere('email_address', 'like', '%'.$this->search.'%')
                ->orWhere('phone_number', 'like', '%'.$this->search.'%')
                ->orWhere('course', 'like', '%'.$this->search.'%');
        })
        ->get();
        return view('livewire.approval', ['data' => $studentData]);
    }
}
