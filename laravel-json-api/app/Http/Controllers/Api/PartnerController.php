<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $partners
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|string',
            'website' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $partner = Partner::create($validated);

        return response()->json([
            'success' => true,
            'data' => $partner
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'logo' => 'sometimes|required|string',
            'website' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $partner->update($validated);

        return response()->json([
            'success' => true,
            'data' => $partner
        ]);
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Partner deleted successfully'
        ]);
    }
}
