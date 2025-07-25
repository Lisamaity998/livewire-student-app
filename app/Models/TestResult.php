<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;
    protected $table = 'test_results';
    protected $fillable = [
        'student_id',
        'course_id',
        'test_date',
        'start_time',
        'total_questions',
        'score',
    ];      

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(StudentInformation::class, 'student_id');
    }
}