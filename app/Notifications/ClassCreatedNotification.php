<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassCreatedNotification extends Notification
{
    use Queueable;

    protected $class;

    /**
     * Create a new notification instance.
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Class Scheduled',
            'message' => "A '{$this->class->class_name}' is scheduled for {$this->class->start_date} at {$this->class->class_time}, tought by {$this->class->teacher->name}.",
        ];
    }
}
