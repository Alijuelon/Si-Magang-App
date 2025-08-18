<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InternTaskController;
use App\Http\Controllers\Api\InternActivityReportController;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\InternLearningProgressController;
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

        // Rute untuk Tasks
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

        // Rute untuk melihat submissions intern
        Route::get('/supervisor/submissions', [SupervisorSubmissionController::class, 'index']);
        Route::get('/supervisor/submissions/{submission}', [SupervisorSubmissionController::class, 'show']);
        Route::get('/supervisor/interns/{intern}/submissions', [SupervisorSubmissionController::class, 'getInternSubmissions']);

        // Rute untuk melihat learning progress intern
        Route::get('/supervisor/learning-progress', [SupervisorLearningProgress::class, 'index']);
        Route::get('/supersivor/learning-progress/{learningProgress}', [SupervisorLearningProgress::class, 'show']);
        Route::get('/supervisor/interns/{intern}/learning-progress', [SupervisorLearningProgress::class, 'getInternProgress']);
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

        // Rute Intern Task
        Route::post('/intern/tasks/{task}/submit', [InternTaskController::class, 'submitTask']);
        Route::get('/intern/submissions', [InternTaskController::class, 'submissions']);
        Route::get('/intern/submissions/{submission}', [InternTaskController::class, 'show']);
        Route::put('/intern/submissions/{submission}', [InternTaskController::class, 'update']);
        Route::delete('/intern/submissions/{submission}', [InternTaskController::class, 'destroy']);

    });
});
