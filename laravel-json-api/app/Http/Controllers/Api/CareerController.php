<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    /**
     * Display a listing of careers.
     */
    public function index()
    {
        // Check if request is from admin (has Authorization header)
        $isAdmin = request()->header('Authorization');
        
        if ($isAdmin) {
            // Admin: return all careers with applications count
            $careers = Career::withCount('applications')
                ->orderBy('deadline', 'desc')
                ->get();
        } else {
            // Public: return only active careers with future deadline
            $careers = Career::where('is_active', true)
                ->where('deadline', '>=', now()->toDateString())
                ->orderBy('deadline', 'asc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $careers,
        ]);
    }

    /**
     * Store a newly created career.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|unique:careers|string',
            'position' => 'required|string',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'department' => 'nullable|string',
            'job_type' => 'nullable|in:Full-time,Part-time,Contract,Internship',
            'location' => 'nullable|string',
            'deadline' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $career = Career::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Career created successfully',
            'data' => $career,
        ], 201);
    }

    /**
     * Display the specified career.
     */
    public function show($id)
    {
        $career = Career::find($id);

        if (!$career) {
            return response()->json([
                'success' => false,
                'message' => 'Career not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $career,
        ]);
    }

    /**
     * Update the specified career.
     */
    public function update(Request $request, $id)
    {
        $career = Career::find($id);

        if (!$career) {
            return response()->json([
                'success' => false,
                'message' => 'Career not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'slug' => 'sometimes|required|unique:careers,slug,' . $id,
            'position' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'department' => 'nullable|string',
            'job_type' => 'nullable|in:Full-time,Part-time,Contract,Internship',
            'location' => 'nullable|string',
            'deadline' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $career->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Career updated successfully',
            'data' => $career,
        ]);
    }

    /**
     * Remove the specified career.
     */
    public function destroy($id)
    {
        $career = Career::find($id);

        if (!$career) {
            return response()->json([
                'success' => false,
                'message' => 'Career not found',
            ], 404);
        }

        $career->delete();

        return response()->json([
            'success' => true,
            'message' => 'Career deleted successfully',
        ]);
    }
}
