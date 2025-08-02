<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentInformation;
use App\Notifications\ClassCreatedNotification;

class NotificationClassCreated extends Controller
{
    public function markasread($id)
    {
       $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($id) {
            $notification = $user->unreadNotifications()->where('id', $id)->first();

            if ($notification) {
                $notification->markAsRead();
            }
        }

        return back();
    }

}
