<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InternActivityReportController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $reports = $user->intern->activityReports;

        return response()->json(['data' => $reports], 200);
    }


    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->intern) {
            return response()->json(['message' => 'Unauthorized. User is not an intern.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'report_type' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'report_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $report = $user->intern->activityReports()->create($request->all());

        return response()->json(['message' => 'Activity report created successfully.', 'data' => $report], 201);
    }


    public function update(Request $request, ActivityReport $report)
    {
        if (Auth::user()->intern->id !== $report->intern_id) {
            return response()->json(['message' => 'Forbidden: You do not own this report.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'report_type' => 'sometimes|required|string|max:100',
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'report_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $report->update($request->all());

        return response()->json(['message' => 'Activity report updated successfully.', 'data' => $report], 200);
    }


    public function destroy(ActivityReport $report)
    {
        if (Auth::user()->intern->id !== $report->intern_id) {
            return response()->json(['message' => 'Forbidden: You do not own this report.'], 403);
        }

        $report->delete();

        return response()->json(['message' => 'Activity report deleted successfully.'], 200);
    }
}
