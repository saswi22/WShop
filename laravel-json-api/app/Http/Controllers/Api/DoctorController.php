<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors.
     */
    public function index()
    {
        $doctors = Doctor::where('is_active', true)
            ->with('schedules')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $doctors,
        ]);
    }

    /**
     * Store a newly created doctor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'specialization' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'bio' => 'nullable|string',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'license_number' => 'nullable|string',
            'photo' => 'nullable|string',
        ]);

        $doctor = Doctor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Doctor created successfully',
            'data' => $doctor,
        ], 201);
    }

    /**
     * Display the specified doctor.
     */
    public function show($id)
    {
        $doctor = Doctor::with('schedules')->find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $doctor,
        ]);
    }

    /**
     * Update the specified doctor.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'specialization' => 'sometimes|required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'bio' => 'nullable|string',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'license_number' => 'nullable|string',
            'photo' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $doctor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Doctor updated successfully',
            'data' => $doctor,
        ]);
    }

    /**
     * Remove the specified doctor.
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
            ], 404);
        }

        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully',
        ]);
    }
}
