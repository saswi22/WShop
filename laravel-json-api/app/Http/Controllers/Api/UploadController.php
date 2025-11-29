<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Store in public/uploads directory
            $path = $image->move(public_path('uploads'), $filename);
            
            // Return the URL
            $url = url('uploads/' . $filename);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ], 400);
    }

    public function uploadCV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'cv_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store in public/uploads/cv directory
            $file->move(public_path('uploads/cv'), $filename);
            
            // Return the URL
            $url = url('uploads/cv/' . $filename);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => '/uploads/cv/' . $filename,
                'filename' => $filename
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ], 400);
    }
}
