<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // Materials
    Route::get('/courses/{courseId}/materials', [MaterialController::class, 'index']);
    Route::post('/courses/{courseId}/materials', [MaterialController::class, 'store']);
    Route::get('/materials/{id}', [MaterialController::class, 'show']);
    Route::delete('/materials/{id}', [MaterialController::class, 'destroy']);

    // Assignments
    Route::get('/courses/{courseId}/assignments', [AssignmentController::class, 'index']);
    Route::post('/courses/{courseId}/assignments', [AssignmentController::class, 'store']);
    Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);

    // Submissions
    Route::post('/assignments/{assignmentId}/submit', [SubmissionController::class, 'store']);
    Route::get('/assignments/{assignmentId}/submissions', [SubmissionController::class, 'index']);
    Route::put('/submissions/{id}/grade', [SubmissionController::class, 'update']);
});
