<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request){

        $credentials = $request->validated();

        if(!Auth::attempt($credentials)){
            return response()->json(['message' => 'Your account is not valid'], 401);
        }

        $user = $request->user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => 'Bearer ' . $token,
            'data' => $user,
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
