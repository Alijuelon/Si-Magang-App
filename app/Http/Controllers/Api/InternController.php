<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InternController extends Controller
{
    /**
     * Get all tasks assigned to the authenticated intern.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasks()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. User not authenticated.'], 401);
        }
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not a valid intern.'], 403);
        }

        $tasks = $user->intern->tasks;

        return response()->json(['data' => $tasks], 200);
    }

    /**
     * Get all learning modules from the connected supervisor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLearningModules()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. User not authenticated.'], 401);
        }

        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not a valid intern.'], 403);
        }

        $learningModules = $user->intern->learningModules;

        return response()->json(['data' => $learningModules], 200);
    }
}
