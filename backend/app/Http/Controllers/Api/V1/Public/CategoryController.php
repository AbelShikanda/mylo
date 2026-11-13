<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $category) {
            return response()->json(['data' => null], 404);
        }

        return response()->json(['data' => $category]);
    }

    public function articles($category)
    {
        $model = Category::where('slug', $category)
            ->where('is_active', true)
            ->first();

        if (! $model) {
            return response()->json(['data' => []], 404);
        }

        $articles = Article::where('category_id', $model->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('writer', 'category', 'tags')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $articles->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                ],
            ],
        ]);
    }
}
