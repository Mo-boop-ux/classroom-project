<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\PostAttachmentController;
use App\Http\Controllers\AssignmentAttachmentController;

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
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [ClassroomController::class, 'dashboard'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | CLASSROOMS
    |--------------------------------------------------------------------------
    */
    Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::get('/classrooms/create', [ClassroomController::class, 'create'])->name('classrooms.create');
    Route::post('/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');

    Route::get('/classrooms/join', [ClassroomController::class, 'joinPage'])->name('classrooms.joinPage');
    Route::post('/classrooms/join', [ClassroomController::class, 'joinByCode'])->name('classrooms.joinByCode');
    Route::get('/classrooms/join/{code}', [ClassroomController::class, 'joinByLink'])->name('classrooms.joinByLink');

    Route::get('/classrooms/{id}', [ClassroomController::class, 'show'])->name('classrooms.show');
    Route::get('/classrooms/{id}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
    Route::put('/classrooms/{id}', [ClassroomController::class, 'update'])->name('classrooms.update');
    Route::delete('/classrooms/{id}', [ClassroomController::class, 'destroy'])->name('classrooms.destroy');

    Route::post('/classrooms/{id}/leave', [ClassroomController::class, 'leave'])->name('classrooms.leave');

    Route::get('/classrooms/{id}/classwork', [ClassroomController::class, 'classwork'])->name('classrooms.classwork');
    Route::get('/classrooms/{id}/assignments', [ClassroomController::class, 'assignments'])->name('classrooms.assignments');

    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

    Route::get('/classrooms/{id}/people', [ClassroomController::class, 'people'])->name('classrooms.people');

    Route::delete('/classrooms/{classroom}/students/{student}', [ClassroomController::class, 'removeStudent'])->name('classrooms.removeStudent');

    /*
    |--------------------------------------------------------------------------
    | POSTS
    |--------------------------------------------------------------------------
    */
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

    /*
    |--------------------------------------------------------------------------
    | COMMENTS
    |--------------------------------------------------------------------------
    */
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{id}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/classrooms/{id}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');

    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');

    Route::get('/assignments/{id}', [AssignmentController::class, 'show'])->name('assignments.show');

    Route::get('/assignments/{id}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');

    Route::put('/assignments/{id}', [AssignmentController::class, 'update'])->name('assignments.update');

    /*
    |--------------------------------------------------------------------------
    | SUBMISSIONS
    |--------------------------------------------------------------------------
    */
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');

    /*
    |--------------------------------------------------------------------------
    | ATTACHMENTS (FIXED - ONLY ONE SYSTEM)
    |--------------------------------------------------------------------------
    */

    // Post attachments
    Route::delete('/attachments/{id}', [PostAttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Assignment attachments (FIXED VERSION - NO MORE /{id} ONLY)
    Route::delete('/assignment-attachments/{id}',[AssignmentAttachmentController::class, 'destroy'])->name('assignment.attachments.destroy');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});