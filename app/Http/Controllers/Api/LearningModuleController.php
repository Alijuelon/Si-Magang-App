<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningModule; // Diperbaiki: Hapus underscore
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LearningModuleController extends Controller
{
    public function index()
    {
        $supervisor = Auth::user()->supervisor;
        $modules = $supervisor->learningModules()->get();

        return response()->json(['data' => $modules], 200);
    }

    public function store(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $module = $supervisor->learningModules()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Learning module created successfully.',
            'data' => $module
        ], 201);
    }

    public function assignModule(Request $request, LearningModule $learningModule)
    {
        if (Auth::user()->supervisor->id !== $learningModule->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this module.'], 403);
        }

        $supervisor = Auth::user()->supervisor;

        $validator = Validator::make($request->all(), [
            'intern_ids' => [
                'required',
                'array',
                Rule::in($supervisor->interns->pluck('id'))
            ],
            'intern_ids.*' => 'uuid|exists:interns,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $learningModule->interns()->sync($request->intern_ids);

        return response()->json(['message' => 'Learning module assigned successfully.'], 200);
    }

    public function assignedModulesByIntern($internId)
    {
        $supervisor = Auth::user()->supervisor;

        // Pastikan intern memang milik supervisor ini
        $intern = $supervisor->interns()->where('id', $internId)->first();
        if (!$intern) {
            return response()->json(['message' => 'Intern not found or not under your supervision.'], 404);
        }

        // Ambil semua modul yang sudah di-assign ke intern tersebut
        $modules = $intern->learningModules()->get();

        return response()->json([
            'intern' => $intern,
            'assigned_modules' => $modules
        ], 200);
    }

    public function update(Request $request, LearningModule $learningModule)
    {
        if (Auth::user()->supervisor->id !== $learningModule->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this module.'], 403);
        }

        $supervisor = Auth::user()->supervisor;

        // 🔹 Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'intern_ids' => [
                'nullable',
                'array',
                Rule::in($supervisor->interns->pluck('id')) // Pastikan intern milik supervisor ini
            ],
            'intern_ids.*' => 'uuid|exists:interns,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 🔹 Update modul (judul / deskripsi)
        $learningModule->update($request->only('title', 'description'));

        // 🔹 Jika ada daftar intern baru, lakukan assign ulang
        if ($request->has('intern_ids')) {
            $learningModule->interns()->sync($request->intern_ids);
        }

        return response()->json([
            'message' => 'Learning module updated successfully.',
            'data' => [
                'module' => $learningModule,
                'assigned_interns' => $learningModule->interns()->get()
            ]
        ], 200);
    }



    public function destroy(LearningModule $learningModule)
    {
        if (Auth::user()->supervisor->id !== $learningModule->supervisor_id) {
            return response()->json(['message' => 'Forbidden: You do not own this module.'], 403);
        }

        $learningModule->delete();

        return response()->json(['message' => 'Learning module deleted successfully.'], 200);
    }
}
