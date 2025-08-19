<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionAttempt;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InternTaskController extends Controller
{
    public function submitTask(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // Max 10MB per file
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $intern = $user->intern;

        // Check if the task is assigned to this intern via the pivot table
        if (!$intern->tasks->contains($task)) {
             return response()->json(['message' => 'Forbidden: This task is not assigned to you.'], 403);
        }

        return DB::transaction(function () use ($request, $user, $task) {
            $submission = Submission::firstOrCreate(
                [
                    'task_id' => $task->id,
                    'intern_id' => $user->intern->id,
                ],
                [
                    'status' => 'submitted',
                    'submission_date' => now(),
                ]
            );

            if (!$submission->wasRecentlyCreated) {
                $submission->update(['status' => 'submitted', 'submission_date' => now()]);
            }

            foreach ($request->file('files') as $file) {
                $path = Storage::disk('public')->putFile('submissions', $file);
                $submission->attempts()->create([
                    'file_path' => $path,
                ]);
            }

            return response()->json([
                'message' => 'Submission successful.',
                'submission' => $submission->load('attempts')
            ], 201);
        });
    }
}
