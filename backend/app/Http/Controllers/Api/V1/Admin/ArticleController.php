<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('articles.view'), 403);

        $query = Article::with('writer', 'category');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(20);

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

    public function feature(Request $request, Article $article)
    {
        abort_unless($request->user()->hasPermission('articles.feature'), 403);

        $article->update(['is_featured' => true]);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'feature_article',
            'subject_type' => Article::class,
            'subject_id' => $article->id,
            'new_values' => ['is_featured' => true],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Article featured']);
    }

    public function unfeature(Request $request, Article $article)
    {
        abort_unless($request->user()->hasPermission('articles.unfeature'), 403);

        $article->update(['is_featured' => false]);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'unfeature_article',
            'subject_type' => Article::class,
            'subject_id' => $article->id,
            'new_values' => ['is_featured' => false],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Article unfeatured']);
    }

    public function destroy(Request $request, Article $article)
    {
        abort_unless($request->user()->hasPermission('articles.remove'), 403);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'remove_article',
            'subject_type' => Article::class,
            'subject_id' => $article->id,
            'old_values' => $article->getAttributes(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $article->delete();

        return response()->json(['message' => 'Article removed']);
    }
}
