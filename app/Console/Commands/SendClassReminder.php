<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\ClassReminderNotification;
use App\Models\NewClass;
use App\Models\StudentInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendClassReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'class:send-class-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send class start reminders 15 minutes before';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $now = Carbon::now();
            $start = $now->copy()->addMinutes(15)->format('H:i:s');
            $date = $now->copy()->format('Y-m-d');

            $classes = NewClass::where('start_date', $date)
                ->where('class_time', $start)
                ->get();

            foreach ($classes as $class) {
                $users = StudentInformation::where('status', 'approved')
                    ->where('course', 'LIKE', "%{$class->course->name}%")
                    ->get();

                foreach ($users as $user) {

                    $userId = $user->id;
                    $message = "Reminder: Your '{$class->class_name}' class starts at {$class->class_time}";

                    event(new ClassReminderNotification($message, $userId));
                }
            }
            $this->info('Class reminders sent successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending class reminders: ' . $e->getMessage());
            return;
        }
    }
}
