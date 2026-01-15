<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationClassCreated;
use App\Livewire\Approval;
use App\Livewire\StudentList;
use App\Livewire\StudentDashboard;
use App\Livewire\LiveClass;
use App\Livewire\UpcomingClass;
use App\Livewire\ViewUpcomingClass;
use App\Livewire\ViewQuestions;
use App\Livewire\MockTest;
use App\Livewire\TestResults;
use App\Livewire\StudentsTestResults;
use App\Livewire\PreviousClass;
use App\Livewire\ClassView;
use App\Livewire\TeacherDashboard;
use App\Livewire\ViewActivityLogs;

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
    Route::get('student/test_results', TestResults::class)->name('test.results');
    Route::get('student/previous_class', PreviousClass::class)->name('previous.class');
    Route::get('student/class_view/{id}', ClassView::class)->name('class.view');
    
    Route::get('/markasred/{id}', [NotificationClassCreated::class, 'markasread'])->name('markasred');
});

Route::get('/studentLogout', function () {
    $authUser = auth()->guard('student')->user();
    if ($authUser) {
        logActivity('student', (int) $authUser->id, 'Student Logout', "Student name {$authUser->name}, email '{$authUser->email_address}' logged out successfully.");
    }
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
    Route::get('admin/students_test_results', StudentsTestResults::class)->name('students.test.results');
    Route::get('admin/view_activity_logs', ViewActivityLogs::class)->name('activity.logs');
});

Route::get('/logout', function () {
    $authUser = auth()->guard('admin')->user();
    if ($authUser) {
        logActivity('admin', (int) $authUser->id, 'Admin Logout', "Admin with email '{$authUser->email}' logged out successfully."
        );
    }
    auth()->guard('admin')->logout();
    return redirect()->route('admin.login');
})->name('admin.logout');

Route::get('/teacher_login', function () {
    return view('teacherLogin');
})->name('teacher.login');

Route::middleware(['auth:teacher'])->group(function () {
    Route::get('teacher/dashboard', TeacherDashboard::class)->name('teacher.dashboard');
});

Route::get('/teacherLogout', function () {
    $authUser = auth()->guard('teacher')->user();
    if ($authUser) {
        logActivity('teacher', (int) $authUser->id, 'Teacher Logout', "Teacher name {$authUser->name}, email '{$authUser->email}' logged out successfully.");
    }
    auth()->guard('teacher')->logout();
    return redirect()->route('teacher.login');
})->name('teacher.logout');

Route::get('/to_do_list', function () {
    return view('toDoList');
})->name('todo.list');




