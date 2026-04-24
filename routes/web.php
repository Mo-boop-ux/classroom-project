<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| AUTH MIDDLEWARE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD (MAIN FIX)
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [ClassroomController::class, 'dashboard'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | CLASSROOMS
    |--------------------------------------------------------------------------
    */
    Route::get('/classrooms', [ClassroomController::class, 'index'])
        ->name('classrooms.index');

    Route::get('/classrooms/create', [ClassroomController::class, 'create'])
        ->name('classrooms.create');

    Route::post('/classrooms', [ClassroomController::class, 'store'])
        ->name('classrooms.store');

    Route::get('/classrooms/join', [ClassroomController::class, 'joinPage'])
        ->name('classrooms.joinPage');

    Route::post('/classrooms/join', [ClassroomController::class, 'joinByCode'])
        ->name('classrooms.joinByCode');

    Route::get('/classrooms/join/{code}', [ClassroomController::class, 'joinByLink'])
        ->name('classrooms.joinByLink');

    Route::get('/classrooms/{id}', [ClassroomController::class, 'show'])
        ->name('classrooms.show');

    Route::get('/classrooms/{id}/classwork', [ClassroomController::class, 'classwork'])
        ->name('classrooms.classwork');

    Route::get('/classrooms/{id}/assignments', [ClassroomController::class, 'assignments'])
        ->name('classrooms.assignments');

    Route::get('/classrooms/{id}/people', [ClassroomController::class, 'people'])
        ->name('classrooms.people');

    /*
    |--------------------------------------------------------------------------
    | POSTS
    |--------------------------------------------------------------------------
    */
    Route::post('/posts', [PostController::class, 'store'])
        ->name('posts.store');

    /*
    |--------------------------------------------------------------------------
    | COMMENTS
    |--------------------------------------------------------------------------
    */
    Route::post('/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/classrooms/{id}/assignments/create', [AssignmentController::class, 'create'])
        ->name('assignments.create');

    Route::post('/assignments', [AssignmentController::class, 'store'])
        ->name('assignments.store');

    Route::get('/assignments/{id}', [AssignmentController::class, 'show'])
        ->name('assignments.show');

    /*
    |--------------------------------------------------------------------------
    | SUBMISSIONS
    |--------------------------------------------------------------------------
    */
    Route::post('/submissions', [SubmissionController::class, 'store'])
        ->name('submissions.store');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});