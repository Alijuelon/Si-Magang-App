<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Learning_Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LearningModuleController extends Controller
{
    public function index()
    {
        $supervisor = Auth::user()->supervisor;
        $modules = $supervisor->learningModules;

        return response()->json(['data' => $modules], 200);
    }

    public function store(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'intern_id' => [
                'required',
                'uuid',
                Rule::in($supervisor->interns->pluck('id'))
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $module = $supervisor->learningModules()->create([
            'title' => $request->title,
            'description' => $request->description,
            'intern_id' => $request->intern_id,
        ]);

        return response()->json([
            'message' => 'Learning module created successfully.',
            'data' => $module
        ], 201);
    }

    public function update(Request $request, Learning_Module $learningModule)
    {
        if (Auth::user()->supervisor->id !== $learningModule->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this module.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $learningModule->update($request->all());

        return response()->json(['message' => 'Learning module updated successfully.', 'data' => $learningModule], 200);
    }

    public function destroy(Learning_Module $learningModule)
    {
        if (Auth::user()->supervisor->id !== $learningModule->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this module.'], 403);
        }

        $learningModule->delete();

        return response()->json(['message' => 'Learning module deleted successfully.'], 200);
    }
}
