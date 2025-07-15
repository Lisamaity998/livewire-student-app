<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\ClassCreated;

if (!function_exists('sendEmail')) {
    function sendEmail($user, $className, $teacherName, $classDate)
    {
        Mail::to($user->email_address)->queue(
            new ClassCreated($user, $className, $teacherName, $classDate)
        );
    }
}