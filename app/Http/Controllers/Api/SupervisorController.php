<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{


    // Iterns & submissions
    public function interns()
    {
        $user = Auth::user();
        if (!$user->supervisor) {
            return response()->json(['message' => 'Unauthorized. User is not a supervisor.'], 403);
        }

        $supervisor = $user->supervisor;
        return response()->json(['data' => $supervisor->interns], 200);
    }

}
