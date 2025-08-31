<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InternSubmissionController;
use App\Http\Controllers\Api\InternActivityReportController;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\InternLearningProgressController;
use App\Http\Controllers\Api\InternTaskController;
use App\Http\Controllers\Api\LearningModuleController;
use App\Http\Controllers\Api\SupervisorController;
use App\Http\Controllers\Api\SupervisorLearningProgress;
use App\Http\Controllers\Api\SupervisorSubmissionController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin',[AdminController::class, 'index']);

        Route::get('/interns',[UserController::class, 'interns']);
        Route::get('/supervisors',[UserController::class, 'supervisors']);

        Route::get('/users',[UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/interns/link', [UserController::class, 'linkIntern']);

        // Admin Reports Routes
        Route::get('/admin/activity-reports', [AdminController::class, 'getActivityReports']);
        Route::get('/admin/learning-progress', [AdminController::class, 'getLearningProgress']);
        Route::get('/admin/submissions', [AdminController::class, 'getSubmissions']);
        Route::get('/admin/interns/{intern}/reports', [AdminController::class, 'getInternReports']);
        Route::get('/admin/all-intern-reports', [AdminController::class, 'getAllInternReports']);
    });


    Route::middleware(['role:supervisor'])->group(function () {

        Route::get('/supervisor-interns', [SupervisorController::class, 'interns']);

       // Rute untuk Learning Modules
        Route::get('/learning-modules', [LearningModuleController::class, 'index']);
        Route::post('/learning-modules', [LearningModuleController::class, 'store']);
        Route::put('/learning-modules/{learningModule}', [LearningModuleController::class, 'update']);
        Route::delete('/learning-modules/{learningModule}', [LearningModuleController::class, 'destroy']);

        // Rute untuk menugaskan materi
        Route::post('/learning-modules/{learningModule}/assign', [LearningModuleController::class, 'assignModule']);
        // Lihat module yang sudah di-assign ke intern tertentu
        Route::get('interns/{internId}/learning-modules', [LearningModuleController::class, 'assignedModulesByIntern']);

        // Rute untuk Tasks
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

        // Rute untuk menugaskan tugas
        Route::post('/tasks/{task}/assign', [TaskController::class, 'assignTask']);
        Route::get('interns/{internId}/tasks', [TaskController::class, 'assignedTaskByIntern']);

        // Rute untuk melihat submissions intern
        Route::get('/supervisor/submissions', [SupervisorSubmissionController::class, 'index']);
        Route::get('/supervisor/submissions/{submission}', [SupervisorSubmissionController::class, 'show']);
        Route::get('/supervisor/interns/{intern}/submissions', [SupervisorSubmissionController::class, 'getInternSubmissions']);
        Route::get('tasks/{task}/submissions', [SupervisorSubmissionController::class, 'getTaskSubmissions']);
        // Rute untuk melihat learning progress intern
        Route::get('/supervisor/learning-progress', [SupervisorLearningProgress::class, 'index']);
        Route::get('/supersivor/learning-progress/{learningProgress}', [SupervisorLearningProgress::class, 'show']);
        Route::get('/supervisor/interns/{intern}/learning-progress', [SupervisorLearningProgress::class, 'getInternProgress']);
        Route::get('modules/{module}/learning-progress', [SupervisorLearningProgress::class, 'getModuleProgress']);

    });



    Route::middleware('role:intern')->group(function () {

        Route::get('/intern-tasks',[InternController::class, 'getTasks']);
        Route::get('/intern-learning-modules',[InternController::class, 'getLearningModules']);

        // Activity Reports
        Route::controller(InternActivityReportController::class)->group(function () {
            Route::get('intern/activity-reports', 'index');
            Route::post('intern/activity-reports', 'store');
            Route::put('intern/activity-reports/{report}', 'update');
            Route::delete('intern/activity-reports/{report}', 'destroy');
        });

        // Learning Progress
        Route::controller(InternLearningProgressController::class)->group(function () {
            Route::get('intern/learning-progress', 'index');
            Route::post('intern/learning-progress', 'store');
            Route::put('intern/learning-progress/{progress}', 'update');
            Route::delete('intern/learning-progress/{progress}', 'destroy');
        });

        // 🔹 Routes untuk Task
        Route::post('intern/tasks/{task}/submit', [InternTaskController::class, 'submitTask']);

        // 🔹 Routes untuk Submission
        Route::get('intern/submissions', [InternSubmissionController::class, 'index']);
        Route::get('intern/submissions/{submission}', [InternSubmissionController::class, 'show']);
        Route::post('intern/submissions/{submission}', [InternSubmissionController::class, 'update']);
        Route::delete('intern/submissions/{submission}', [InternSubmissionController::class, 'destroy']);

    });
});
