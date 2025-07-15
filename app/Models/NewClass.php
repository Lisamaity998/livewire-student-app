<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Teacher;

class NewClass extends Model
{
    use HasFactory;
    protected $table = 'class';
    protected $fillable = [
        'class_name',
        'course_id',
        'teacher_id',
        'start_date',
        'class_time',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function interestedStudents()
    {
        return $this->hasMany(ClassAttendance::class, 'class_id')->where('attended', '1');
    }
}
