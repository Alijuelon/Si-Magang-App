<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InternSubmissionController extends Controller
{

    public function index()
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

        // Pastikan user adalah intern yang punya submission ini
        if (!$user->intern || $submission->intern_id !== $user->intern->id) {
            return response()->json(['message' => 'Forbidden: You do not have access to this submission.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|string|in:submitted,resubmitted',
            'submission_date' => 'sometimes|required|date',
            'files' => 'sometimes|array',
            'files.*' => 'file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request, $submission) {

            // Update status dan submission_date
            $updateData = [];
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }
            if ($request->has('submission_date')) {
                $updateData['submission_date'] = $request->submission_date;
            }
            if (!empty($updateData)) {
                $submission->update($updateData);
            }

            // Update file jika ada
            if ($request->hasFile('files')) {
                // Hapus file lama
                foreach ($submission->attempts as $attempt) {
                    Storage::disk('public')->delete($attempt->file_path);
                }
                $submission->attempts()->delete();

                // Simpan file baru
                foreach ($request->file('files') as $file) {
                    $path = Storage::disk('public')->putFile('submissions', $file);
                    $submission->attempts()->create(['file_path' => $path]);
                }
            }

            // Load relasi attempts agar response lengkap
            $submission->load('attempts');

            return response()->json([
                'message' => 'Submission updated successfully.',
                'data' => $submission
            ], 200);
        });
    }

    public function destroy(Submission $submission)
    {
        $user = Auth::user();
        if ($user->intern && $submission->intern_id === $user->intern->id) {
            return DB::transaction(function () use ($submission) {
                foreach ($submission->attempts as $attempt) {
                    Storage::disk('public')->delete($attempt->file_path);
                }
                $submission->delete();
                return response()->json(['message' => 'Submission and associated files deleted successfully.'], 200);
            });
        }
        return response()->json(['message' => 'Forbidden: You do not have access to this submission.'], 403);
    }
}
