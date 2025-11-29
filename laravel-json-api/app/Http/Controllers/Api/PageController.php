<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::where('is_published', true)->get();
        return response()->json(['success' => true, 'data' => $pages]);
    }

    public function adminIndex()
    {
        $pages = Page::orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'data' => $pages]);
    }

    public function show($idOrSlug)
    {
        $page = Page::where('slug', $idOrSlug)->orWhere('id', $idOrSlug)->firstOrFail();
        return response()->json(['success' => true, 'data' => $page]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'slug' => 'required|string|unique:pages,slug',
        ]);

        $page = Page::create($request->only(['title','slug','content','featured_image','is_published']));
        return response()->json(['success' => true, 'data' => $page], 201);
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $page->update($request->only(['title','slug','content','featured_image','is_published']));
        return response()->json(['success' => true, 'data' => $page]);
    }

    public function destroy($id)
    {
        Page::destroy($id);
        return response()->json(['success' => true]);
    }
}
