<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('class')->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $facilities]);
    }

    public function show($id)
    {
        $facility = Facility::findOrFail($id);
        return response()->json(['success' => true, 'data' => $facility]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'class' => 'required|in:kelas_3,kelas_2,kelas_1,vip',
        ]);

        $facility = Facility::create($request->only(['name', 'class', 'capacity', 'image', 'facilities', 'description']));
        return response()->json(['success' => true, 'data' => $facility], 201);
    }

    public function update(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string',
            'class' => 'required|in:kelas_3,kelas_2,kelas_1,vip',
        ]);
        
        $facility->update($request->only(['name', 'class', 'capacity', 'image', 'facilities', 'description']));
        return response()->json(['success' => true, 'data' => $facility]);
    }

    public function destroy($id)
    {
        Facility::destroy($id);
        return response()->json(['success' => true]);
    }
}
