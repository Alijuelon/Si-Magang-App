<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get all tasks of the logged-in supervisor.
     */
    public function index()
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor) {
            return response()->json(['message' => 'Supervisor not found.'], 404);
        }

        $tasks = $supervisor->tasks()->get();

        return response()->json(['data' => $tasks], 200);
    }

    /**
     * Store a new task.
     */
    public function store(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor) {
            return response()->json(['message' => 'Supervisor not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = $supervisor->tasks()->create($validator->validated());

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => $task
        ], 201);
    }

    /**
     * Assign interns to a task.
     */
    public function assignTask(Request $request, Task $task)
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor || $supervisor->id !== $task->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this task.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'intern_ids'   => 'required|array',
            'intern_ids.*' => 'uuid|exists:interns,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // pastikan semua intern id memang milik supervisor ini
        $validInterns = $supervisor->interns->pluck('id')->toArray();
        foreach ($request->intern_ids as $internId) {
            if (!in_array($internId, $validInterns)) {
                return response()->json([
                    'message' => "Intern {$internId} does not belong to this supervisor."
                ], 403);
            }
        }

        $task->interns()->sync($request->intern_ids);

        return response()->json(['message' => 'Task assigned successfully.'], 200);
    }
    
    public function assignedTaskByIntern($internId)
    {
        $supervisor = Auth::user()->supervisor;

        // Pastikan intern memang milik supervisor ini
        $intern = $supervisor->interns()->where('id', $internId)->first();
        if (!$intern) {
            return response()->json(['message' => 'Intern not found or not under your supervision.'], 404);
        }

        // Ambil semua modul yang sudah di-assign ke intern tersebut
        $tasks = $intern->tasks()->get();

        return response()->json([
            'intern' => $intern,
            'assigned_tasks' => $tasks
        ], 200);
    }

    /**
     * Update an existing task.
     */
    public function update(Request $request, Task $task)
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor || $supervisor->id !== $task->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this task.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'due_date'    => 'sometimes|required|date',
            'intern_ids'   => 'sometimes|array',
            'intern_ids.*' => 'uuid|exists:interns,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->update($request->except('intern_ids'));

        if ($request->has('intern_ids')) {
            // cek apakah semua intern memang milik supervisor ini
            $validInterns = $supervisor->interns->pluck('id')->toArray();
            foreach ($request->intern_ids as $internId) {
                if (!in_array($internId, $validInterns)) {
                    return response()->json([
                        'message' => "Intern {$internId} does not belong to this supervisor."
                    ], 403);
                }
            }

            $task->interns()->sync($request->intern_ids);
        }

        return response()->json([
            'message' => 'Task updated successfully.',
            'data'    => $task->load('interns')
        ], 200);
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor || $supervisor->id !== $task->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this task.'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }
}
