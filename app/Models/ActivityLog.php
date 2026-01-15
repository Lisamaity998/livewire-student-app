<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';
    protected $fillable = [
        'user_role', // e.g., 'student', 'teacher', 'admin'
        'user_id',
        'activity_type', // e.g., 'login', 'logout', 'class_created', etc.
        'description', // Additional details about the activity
    ];
}
