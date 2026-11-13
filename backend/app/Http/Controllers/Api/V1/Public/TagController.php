<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name', 'asc')->get();

        return response()->json(['data' => $tags]);
    }

    public function show($slug)
    {
        $tag = Tag::where('slug', $slug)->first();

        if (! $tag) {
            return response()->json(['data' => null], 404);
        }

        $tag->load(['articles' => function ($query) {
            $query->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->with('writer', 'category')
                ->orderBy('published_at', 'desc')
                ->limit(20);
        }]);

        return response()->json(['data' => $tag]);
    }
}
