<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\StudentInformation;
use App\Models\Teacher;
use Livewire\Attributes\Layout;

class ViewActivityLogs extends Component
{
    public $search = '';

    #[Layout('layouts.app')]
    public function render()
    {
        $logs = ActivityLog::latest()
            ->where(function ($query) {
                $query->where('activity_type', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('user_role', 'like', '%' . $this->search . '%');
            })
            ->get(); // Execute the query to get the collection

        // Attach user_name using Eloquent
        foreach ($logs as $log) {
            if ($log->user_role === 'admin') {
                // $admin = Admin::find($log->user_id);
                // $log->user_name = $admin ? $admin->name : 'Unknown Admin';
                $log->user_name = 'Admin';
            } elseif ($log->user_role === 'student') {
                $student = StudentInformation::find($log->user_id);
                $log->user_name = $student ? $student->name : 'Unknown Student';
            } elseif ($log->user_role === 'teacher') {
                $teacher = Teacher::find($log->user_id);
                $log->user_name = $teacher ? $teacher->name : 'Unknown Teacher';
            } else {
                $log->user_name = 'Unknown User';
            }
        }

        return view('livewire.view-activity-logs', compact('logs'));
    }
}
