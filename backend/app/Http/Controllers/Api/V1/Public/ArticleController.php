<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Category;
use App\Models\Identity\Tag;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::where('status', 'published')
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

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('writer', 'category', 'tags', 'likes')
            ->first();

        if (! $article) {
            return response()->json(['data' => null], 404);
        }

        return response()->json(['data' => $article->append('like_count')]);
    }

    public function comments($article)
    {
        $comments = \App\Models\Identity\Comment::where('article_id', $article->id)
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $comments]);
    }

    public function addComment($article)
    {
        return response()->json(['message' => 'comment added']);
    }

    public function report($article)
    {
        return response()->json(['message' => 'reported']);
    }

    public function related($article)
    {
        $related = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->with('writer', 'category')
            ->limit(5)
            ->get();

        return response()->json(['data' => $related]);
    }
}
