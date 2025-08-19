<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function interns()
    {
        $interns = Intern::with(['supervisor'])->get();
        return response()->json(['data' => $interns], 200);
    }

    public function supervisors()
    {
        $supervisors = Supervisor::with(['interns'])->get();
        return response()->json(['data' => $supervisors], 200);
    }

    public function index()
    {
        $users = User::all();

        $users->each(function ($user) {
            if ($user->role === 'supervisor') {
                $user->load(['supervisor.interns']);
            } elseif ($user->role === 'intern') {
                $user->load(['intern.supervisor']);
            }
        });

        return response()->json(['users' => $users], 200);
    }

    public function store(Request $request)
    {
        $baseRules = [
            'name'     => 'required|string|max:100',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|string|in:admin,supervisor,intern',
        ];

        if ($request->role === 'supervisor') {
            $extraRules = [
                'full_name' => 'required|string|max:255',
                'division'  => 'required|string|max:100',
            ];
        } elseif ($request->role === 'intern') {
            $extraRules = [
                'full_name'     => 'required|string|max:255',
                'division'      => 'required|string|max:100',
                'school_origin' => 'required|string|max:150',
                'major'         => 'required|string|max:100',
                'gender'        => 'required|string|max:20',
                'phone_number'  => 'required|string|max:20',
                'birth_date'    => 'required|date',
                'start_date'    => 'required|date',
                'end_date'      => 'required|date',
                'intern_type'   => 'required|in:SMK,College,General',
                'supervisor_id' => 'required|exists:supervisors,id',
            ];
        } else {
            $extraRules = [];
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $extraRules));

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            if ($request->role === 'supervisor') {
                Supervisor::create([
                    'full_name' => $request->full_name ?? $request->name,
                    'division'  => $request->division,
                    'user_id'   => $user->id,
                ]);
            } elseif ($request->role === 'intern') {
                Intern::create([
                    'full_name'     => $request->full_name ?? $request->name,
                    'division'      => $request->division,
                    'school_origin' => $request->school_origin,
                    'major'         => $request->major,
                    'gender'        => $request->gender,
                    'phone_number'  => $request->phone_number,
                    'birth_date'    => $request->birth_date,
                    'start_date'    => $request->start_date,
                    'end_date'      => $request->end_date,
                    'intern_type'   => $request->intern_type,
                    'user_id'       => $user->id,
                    'supervisor_id' => $request->supervisor_id,
                ]);
            }

            return response()->json(['message' => 'User created successfully.', 'user' => $user], 201);
        });
    }

    public function update(Request $request, User $user)
    {
        $baseRules = [
            'name'  => 'sometimes|required|string|max:100',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        $extraRules = [];
        $dataToUpdate = [];

        if ($user->role === 'supervisor') {
            $extraRules = [
                'full_name' => 'sometimes|required|string|max:255',
                'division'  => 'sometimes|required|string|max:100',
            ];
            $dataToUpdate = $request->only(['full_name', 'division']);
        } elseif ($user->role === 'intern') {
            $extraRules = [
                'full_name'     => 'sometimes|required|string|max:255',
                'division'      => 'sometimes|required|string|max:100',
                'school_origin' => 'sometimes|required|string|max:150',
                'major'         => 'sometimes|required|string|max:100',
                'gender'        => 'sometimes|required|string|max:20',
                'phone_number'  => 'sometimes|required|string|max:20',
                'birth_date'    => 'sometimes|required|date',
                'start_date'    => 'sometimes|required|date',
                'end_date'      => 'sometimes|required|date',
                'intern_type'   => 'sometimes|required|in:SMK,College,General',
                'supervisor_id' => 'sometimes|required|exists:supervisors,id',
            ];
            $dataToUpdate = $request->only([
                'full_name', 'division', 'school_origin', 'major',
                'gender', 'phone_number', 'birth_date',
                'start_date', 'end_date', 'intern_type', 'supervisor_id'
            ]);
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $extraRules));

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return DB::transaction(function () use ($request, $user, $dataToUpdate) {
            $user->update($request->only(['name', 'email']));

            if ($user->role === 'supervisor' && $user->supervisor) {
                $user->supervisor->update($dataToUpdate);
            } elseif ($user->role === 'intern' && $user->intern) {
                $user->intern->update($dataToUpdate);
            }

            $user->load(['supervisor', 'intern']);

            return response()->json([
                'message' => 'User updated successfully.',
                'user'    => $user
            ], 200);
        });
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'], 200);
    }
}
