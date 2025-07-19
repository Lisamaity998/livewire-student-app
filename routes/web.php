<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Approval;
use App\Livewire\StudentList;
use App\Livewire\StudentDashboard;
use App\Livewire\LiveClass;
use App\Livewire\UpcomingClass;
use App\Livewire\ViewUpcomingClass;
use App\Livewire\ViewQuestions;
use App\Livewire\MockTest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/student_login', function () {
    return view('studentLogin');
})->name('student.login');

Route::middleware(['auth:student'])->group(function () {
    Route::get('student/dashboard', StudentDashboard::class)->name('student.dashboard');
    Route::get('student/live_class', LiveClass::class)->name('live.class');
    Route::get('student/upcoming_class', UpcomingClass::class)->name('upcoming.class');
    Route::get('student/mock_test', MockTest::class)->name('mock.test');
});

Route::get('/studentLogout', function () {
    auth()->guard('student')->logout();
    return redirect()->route('student.login');
})->name('student.logout');

Route::get('/admin_login', function () {
    return view('adminLogin');
})->name('admin.login');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('admin/approval', Approval::class)->name('approval');
    Route::get('admin/student_list', StudentList::class)->name('student.list');
    Route::get('admin/view_upcoming_class', ViewUpcomingClass::class)->name('view.upcoming.class');
    Route::get('admin/view_question', ViewQuestions::class)->name('view.question');
});

Route::get('/logout', function () {
    auth()->guard('admin')->logout();
    return redirect()->route('admin.login');
})->name('admin.logout');

Route::get('/to_do_list', function () {
    return view('toDoList');
})->name('todo.list');




