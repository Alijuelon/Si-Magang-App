<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index()
    {
        $supervisor = Auth::user()->supervisor;
        $tasks = $supervisor->tasks;
        
        return response()->json(['data' => $tasks], 200);
    }

    public function store(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'intern_id' => [
                'required',
                'uuid',
                Rule::in($supervisor->interns->pluck('id'))
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = $supervisor->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'intern_id' => $request->intern_id,
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => $task
        ], 201);
    }

    public function update(Request $request, Task $task)
    {
        if (Auth::user()->supervisor->id !== $task->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this task.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task->update($request->all());

        return response()->json(['message' => 'Task updated successfully.', 'data' => $task], 200);
    }

    public function destroy(Task $task)
    {
        if (Auth::user()->supervisor->id !== $task->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this task.'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }
}
