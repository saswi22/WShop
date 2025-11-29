<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of news.
     */
    public function index()
    {
        $news = News::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }

    /**
     * Store a newly created news.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|unique:news|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'category' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['published_at'] = now();

        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'News created successfully',
            'data' => $news->load('user'),
        ], 201);
    }

    /**
     * Display the specified news.
     */
    public function show($id)
    {
        $news = News::with('user')->find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }

    /**
     * Update the specified news.
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'slug' => 'sometimes|required|unique:news,slug,' . $id,
            'content' => 'sometimes|required|string',
            'featured_image' => 'nullable|string',
            'category' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $news->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'News updated successfully',
            'data' => $news->load('user'),
        ]);
    }

    /**
     * Remove the specified news.
     */
    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found',
            ], 404);
        }

        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News deleted successfully',
        ]);
    }
}
