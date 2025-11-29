<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with('career')->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $applications]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'career_id' => 'nullable|exists:careers,id',
            'position' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'education' => 'required|string',
            'cv_url' => 'required|string',
            'cover_letter' => 'required|string',
            'status' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $application = Application::create($request->all());
        return response()->json(['data' => $application, 'message' => 'Lamaran berhasil dikirim'], 201);
    }

    public function show($id)
    {
        $application = Application::with('career')->findOrFail($id);
        return response()->json(['data' => $application]);
    }

    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,interview,accepted,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $application->update($request->all());
        return response()->json(['data' => $application, 'message' => 'Status lamaran berhasil diupdate']);
    }

    public function destroy($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();
        return response()->json(['message' => 'Lamaran berhasil dihapus']);
    }
}
