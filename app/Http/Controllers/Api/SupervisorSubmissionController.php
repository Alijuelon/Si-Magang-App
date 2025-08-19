<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\Submission;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class SupervisorSubmissionController extends Controller
{

   public function index()
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        $supervisor = $user->supervisor;
        $interns = $supervisor->interns()->with(['submissions.task', 'submissions.attempts'])->get();

        return response()->json(['data' => $interns], 200);
    }


    public function show(Submission $submission)
    {
        $user = Auth::user();

        if ($user->supervisor && $submission->task->supervisor_id === $user->supervisor->id) {
            $submission->load(['task', 'intern.user', 'attempts']);
            return response()->json(['data' => $submission], 200);
        }

        return response()->json(['message' => 'Forbidden: You do not have access to this submission.'], 403);
    }

    public function getInternSubmissions(Intern $intern)
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        // Check if the intern is connected to the authenticated supervisor
        if ($intern->supervisor_id !== $user->supervisor->id) {
            return response()->json(['message' => 'Forbidden: This intern is not under your supervision.'], 403);
        }

        $submissions = $intern->submissions()->whereHas('task', function ($query) use ($user) {
            $query->where('supervisor_id', $user->supervisor->id);
        })->with(['task', 'attempts'])->get();

        return response()->json(['data' => $submissions], 200);
    }

    public function getTaskSubmissions(Task $task)
    {
        $user = Auth::user();

        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        // Pastikan task ini milik supervisor
        if ($task->supervisor_id !== $user->supervisor->id) {
            return response()->json(['message' => 'Forbidden: You do not own this task.'], 403);
        }

        $submissions = Submission::where('task_id', $task->id)
            ->with(['intern.user', 'attempts'])
            ->get();

        return response()->json(['data' => $submissions], 200);
    }
}
