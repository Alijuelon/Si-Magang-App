<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningProgress;
use App\Models\Intern;

class SupervisorLearningProgress extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        $internIds = $user->supervisor->interns()->pluck('id');
        $allProgress = LearningProgress::with(['intern.user', 'module'])
            ->whereIn('intern_id', $internIds)
            ->get();

        return response()->json(['data' => $allProgress], 200);
    }


    public function show(LearningProgress $learningProgress)
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        $internIds = $user->supervisor->interns()->pluck('id')->toArray();
        if (!in_array($learningProgress->intern_id, $internIds)) {
            return response()->json(['message' => 'Forbidden: You do not have access to this progress record.'], 403);
        }

        $learningProgress->load(['intern.user', 'module']);
        return response()->json(['data' => $learningProgress], 200);
    }

    public function getInternProgress(Intern $intern)
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        // Check if the intern is connected to the authenticated supervisor
        if ($intern->supervisor_id !== $user->supervisor->id) {
            return response()->json(['message' => 'Forbidden: This intern is not under your supervision.'], 403);
        }

        $progress = $intern->learningProgress()->whereHas('module', function ($query) use ($user) {
            $query->where('supervisor_id', $user->supervisor->id);
        })->with('module')->get();

        return response()->json(['data' => $progress], 200);
    }

    public function getModuleProgress($moduleId)
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        $supervisorId = $user->supervisor->id;

        // Pastikan module milik supervisor
        $progress = LearningProgress::with(['intern.user', 'module'])
            ->whereHas('module', function ($query) use ($moduleId, $supervisorId) {
                $query->where('id', $moduleId)
                    ->where('supervisor_id', $supervisorId);
            })
            ->get();

        if ($progress->isEmpty()) {
            return response()->json(['message' => 'No progress found for this module.'], 404);
        }

        return response()->json(['data' => $progress], 200);
    }


}
