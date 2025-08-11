<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles // Menggunakan parameter variadic untuk menerima banyak peran
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(!Auth::check()){
            return response()->json(['message'=>'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Cek apakah peran user ada di dalam array $roles
        if(!in_array($user->role, $roles)){
            return response()->json(['message' => 'Forbidden: You do not have required role'], 403);
        }

        return $next($request);
    }
}
