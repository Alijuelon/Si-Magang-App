<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InternLearningProgressController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $progress = $user->intern->learningProgress;

        return response()->json(['data' => $progress], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'progress_status' => 'required|string|max:50',
            'module_id' => 'required|uuid|exists:learning_modules,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $progress = $user->intern->learningProgress()->create($request->all());

        return response()->json(['message' => 'Learning progress created successfully.', 'data' => $progress], 201);
    }

    public function update(Request $request, LearningProgress $progress)
    {
        if (Auth::user()->intern->id !== $progress->intern_id) {
            return response()->json(['message' => 'Forbidden: You do not own this progress record.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'progress_status' => 'sometimes|required|string|max:50',
            'module_id' => 'sometimes|required|uuid|exists:learning_modules,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $progress->update($request->all());

        return response()->json(['message' => 'Learning progress updated successfully.', 'data' => $progress], 200);
    }

    public function destroy(LearningProgress $progress)
    {
        if (Auth::user()->intern->id !== $progress->intern_id) {
            return response()->json(['message' => 'Forbidden: You do not own this progress record.'], 403);
        }

        $progress->delete();

        return response()->json(['message' => 'Learning progress deleted successfully.'], 200);
    }
}
