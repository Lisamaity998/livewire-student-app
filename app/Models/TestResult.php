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
        'score',
    ];      
}
