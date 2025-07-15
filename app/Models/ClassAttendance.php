<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    use HasFactory;
    protected $table = 'class_attendance';
    protected $fillable = [
        'class_id',
        'student_id',
        'attended',
    ];

    public function class()
    {
        return $this->belongsTo(NewClass::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(StudentInformation::class, 'student_id');
    }
}
