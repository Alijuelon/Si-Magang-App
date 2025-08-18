<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use App\Models\Intern;
use App\Models\LearningProgress;
use App\Models\Submission;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getActivityReports()
    {
        $reports = ActivityReport::with('intern.user')->get();
        return response()->json(['data' => $reports], 200);
    }

    public function getLearningProgress()
    {
        $progress = LearningProgress::with(['intern.user', 'module.supervisor.user'])->get();
        return response()->json(['data' => $progress], 200);
    }

    public function getSubmissions()
    {
        $submissions = Submission::with(['intern.user', 'task.supervisor.user'])->get();
        return response()->json(['data' => $submissions], 200);
    }

    public function getInternReports(Intern $intern)
    {
        $activityReports = $intern->activityReports;
        $learningProgress = $intern->learningProgress;
        $submissions = $intern->submissions()->with(['task.supervisor.user'])->get();

        return response()->json([
            'intern_info' => $intern->load('user'),
            'activity_reports' => $activityReports,
            'learning_progress' => $learningProgress,
            'submissions' => $submissions
        ], 200);
    }

    public function getAllInternReports()
    {
        $interns = Intern::with([
            'user',
            'activityReports',
            'learningProgress',
            'submissions.task.supervisor.user'
        ])->get();

        return response()->json(['data' => $interns], 200);
    }
}
