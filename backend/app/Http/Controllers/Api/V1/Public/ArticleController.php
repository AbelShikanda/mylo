<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Category;
use App\Models\Identity\Comment;
use App\Models\Identity\Report;
use App\Models\Identity\Tag;
use App\Services\Reader\ReaderService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    protected function resolveArticle($article): Article
    {
        return Article::where('slug', $article)
            ->when(\Illuminate\Support\Str::isUuid($article), function ($query) use ($article) {
                $query->orWhere('id', $article);
            })
            ->firstOrFail();
    }

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
        $resolved = $this->resolveArticle($article);

        $comments = Comment::where('article_id', $resolved->id)
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->with('replies.reader')
            ->with('reader')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $comments]);
    }

    public function addComment(Request $request, $article)
    {
        $resolved = $this->resolveArticle($article);
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        $comment = Comment::create([
            'article_id' => $resolved->id,
            'reader_id' => $reader->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'is_approved' => true,
        ]);

        return response()->json(['message' => 'Comment added', 'data' => $comment], 201);
    }

    public function report(Request $request, $article)
    {
        $resolved = $this->resolveArticle($article);
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        Report::create([
            'reportable_type' => 'article',
            'reportable_id' => $resolved->id,
            'reporter_type' => 'reader',
            'reporter_id' => $reader->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        $resolved->update(['is_reported' => true]);

        return response()->json(['message' => 'Article reported'], 201);
    }

    public function related($article)
    {
        $resolved = $this->resolveArticle($article);

        $related = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('category_id', $resolved->category_id)
            ->where('id', '!=', $resolved->id)
            ->with('writer', 'category')
            ->limit(5)
            ->get();

        return response()->json(['data' => $related]);
    }

    public function like(Request $request, $article)
    {
        $resolved = $this->resolveArticle($article);
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $result = $this->readerService->toggleLike($reader, $resolved->id);

        return response()->json(['message' => 'Like toggled', 'data' => $result]);
    }

    public function unlike(Request $request, $article)
    {
        $resolved = $this->resolveArticle($article);
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        if ($this->readerService->isLiked($reader, $resolved->id)) {
            $this->readerService->toggleLike($reader, $resolved->id);
        }

        return response()->json(['message' => 'Article unliked', 'data' => ['liked' => false]]);
    }
}
