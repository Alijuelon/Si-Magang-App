<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Submission_Attempts;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InternTaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $tasks = $user->intern->tasks;

        return response()->json(['data' => $tasks], 200);
    }


    public function submitTask(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // Maksimal 10MB per file
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $intern = $user->intern;

        // Cek apakah tugas diberikan secara personal atau umum oleh supervisor
        $isPersonalTask = $task->intern_id === $intern->id;
        $isGeneralTask = is_null($task->intern_id) && $task->supervisor_id === $intern->supervisor_id;

        if (!$isPersonalTask && !$isGeneralTask) {
             return response()->json(['message' => 'Forbidden: This task is not for you.'], 403);
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

            // Update submission status if it was 'not_submitted'
            if ($submission->wasRecentlyCreated === false) {
                $submission->update(['status' => 'submitted', 'submission_date' => now()]);
            }

            // Store each uploaded file
            $attempts = [];
            foreach ($request->file('files') as $file) {
                $path = Storage::disk('public')->putFile('submissions', $file);
                $attempt = $submission->attempts()->create([
                    'file_path' => $path,
                ]);
                $attempts[] = $attempt;
            }

            return response()->json([
                'message' => 'Submission successful.',
                'submission' => $submission->load('attempts')
            ], 201);
        });
    }

    public function submissions()
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $submissions = $user->intern->submissions()->with(['task', 'attempts'])->get();

        return response()->json(['data' => $submissions], 200);
    }

    public function show(Submission $submission)
    {
        $user = Auth::user();

          if ($user->intern && $submission->intern_id === $user->intern->id) {
            $submission->load(['task', 'attempts']);
            return response()->json(['data' => $submission], 200);
        }
        return response()->json(['message' => 'Forbidden: You do not have access to this submission.'], 403);
    }

    public function update(Request $request, Submission $submission)
    {
        $user = Auth::user();

        if ($user->intern && $submission->intern_id === $user->intern->id) {
            $validator = Validator::make($request->all(), [
                'status' => ['sometimes', 'required', 'string', Rule::in(['submitted', 'resubmitted'])],
                'submission_date' => 'sometimes|required|date',
                'files' => 'array',
                'files.*' => 'file|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            return DB::transaction(function () use ($request, $submission) {
                // Hapus semua file lama
                if ($request->hasFile('files')) {
                    foreach ($submission->attempts as $attempt) {
                        Storage::disk('public')->delete($attempt->file_path);
                    }
                    $submission->attempts()->delete();
                }

                // Update submission status if provided
                if ($request->has('status') || $request->has('submission_date')) {
                    $submission->update($request->only('status', 'submission_date'));
                }

                // Handle file uploads
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $path = Storage::disk('public')->putFile('submissions', $file);
                        $submission->attempts()->create([
                            'file_path' => $path,
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'Submission updated successfully.',
                    'data' => $submission->load('attempts')
                ], 200);
            });
        }

        return response()->json(['message' => 'Forbidden: You do not have access to this submission.'], 403);
    }


    public function destroy(Submission $submission)
    {
        $user = Auth::user();

       if ($user->intern && $submission->intern_id === $user->intern->id) {
            return DB::transaction(function () use ($submission) {
                // Hapus file dari penyimpanan
                foreach ($submission->attempts as $attempt) {
                    Storage::disk('public')->delete($attempt->file_path);
                }

                // Hapus submission dan semua attempts terkait secara otomatis (berkat cascade)
                $submission->delete();

                return response()->json(['message' => 'Submission and associated files deleted successfully.'], 200);
            });
        }
        return response()->json(['message' => 'Forbidden: You do not have access to this submission.'], 403);
    }
}
