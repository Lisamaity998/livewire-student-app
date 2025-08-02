<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class StudentInformation extends Authenticatable
{
    use HasFactory,Notifiable;

    protected $table = 'student_information';
    protected $fillable = [
        'name',
        'address',
        'email_address',
        'password',
        'phone_number',
        'course',
        'image',
        'status'
    ];
    
}
