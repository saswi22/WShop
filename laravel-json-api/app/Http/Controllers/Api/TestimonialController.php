<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $items = Testimonial::where('is_approved', true)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function all()
    {
        $items = Testimonial::orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function approve($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_approved = true;
        $testimonial->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Testimoni berhasil disetujui',
            'data' => $testimonial
        ]);
    }

    public function storePublic(Request $request)
    {
        $this->validate($request, [
            'patient_name' => 'required|string|max:255',
            'message' => 'required|string|min:20',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $testimonial = Testimonial::create([
            'patient_name' => $request->patient_name,
            'message' => $request->message,
            'rating' => $request->rating,
            'photo' => null,
            'is_approved' => false // Menunggu approval admin
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Testimoni berhasil dikirim dan menunggu persetujuan',
            'data' => $testimonial
        ], 201);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'patient_name' => 'required|string',
            'message' => 'required|string',
        ]);

        $t = Testimonial::create($request->only(['patient_name','message','photo','is_approved']));
        return response()->json(['success' => true, 'data' => $t], 201);
    }

    public function destroy($id)
    {
        Testimonial::destroy($id);
        return response()->json(['success' => true]);
    }
}
