<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    use HasFactory;
    protected $table = 'question';
    protected $fillable = [
        'course_id',
        'question_name',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'correct_answer'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
