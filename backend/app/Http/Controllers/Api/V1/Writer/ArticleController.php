<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Writer;
use App\Services\Publishing\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    protected function checkWriter(Request $request): Writer
    {
        $user = $request->user('writer');
        if (! $user instanceof Writer) {
            abort(404);
        }
        return $user;
    }

    public function index(Request $request)
    {
        $writer = $this->checkWriter($request);

        $filters = $request->only(['status', 'search']);
        $articles = $this->articleService->getArticlesByWriter($writer, $filters);

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

    public function store(Request $request, ArticleService $articleService)
    {
        $writer = $this->checkWriter($request);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'status' => 'nullable|in:draft,published,scheduled',
            'category_id' => 'nullable|uuid|exists:categories,id',
            'subcategory_id' => 'nullable|uuid|exists:categories,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'reading_time' => 'nullable|integer|min:0',
            'scheduled_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'uuid|exists:tags,id',
        ]);

        $article = $articleService->createArticle($validated, $writer);

        return response()->json(['data' => $article], 201);
    }

    public function show(Request $request, Article $article)
    {
        $writer = $this->checkWriter($request);
        abort_unless($article->writer_id === $writer->id, 403);

        return response()->json(['data' => $article->load('writer', 'category', 'tags')]);
    }

    public function update(Request $request, Article $article, ArticleService $articleService)
    {
        $writer = $this->checkWriter($request);
        abort_unless($article->writer_id === $writer->id, 403);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:articles,slug,' . $article->id,
            'excerpt' => 'sometimes|string',
            'content' => 'sometimes|array',
            'featured_image' => 'sometimes|string',
            'status' => 'sometimes|in:draft,published,scheduled',
            'category_id' => 'sometimes|uuid|exists:categories,id',
            'subcategory_id' => 'sometimes|uuid|exists:categories,id',
            'seo_title' => 'sometimes|string|max:255',
            'seo_description' => 'sometimes|string',
            'reading_time' => 'sometimes|integer|min:0',
            'scheduled_at' => 'sometimes|date',
            'tags' => 'sometimes|array',
            'tags.*' => 'uuid|exists:tags,id',
        ]);

        $article = $articleService->updateArticle($article, $validated, $writer);

        return response()->json(['data' => $article]);
    }

    public function destroy(Request $request, Article $article, ArticleService $articleService)
    {
        $writer = $this->checkWriter($request);
        abort_unless($article->writer_id === $writer->id, 403);

        $articleService->deleteArticle($article, $writer);

        return response()->json(['message' => 'Article deleted']);
    }

    public function publish(Request $request, Article $article, ArticleService $articleService)
    {
        $writer = $this->checkWriter($request);
        abort_unless($article->writer_id === $writer->id, 403);

        $article = $articleService->publishArticle($article, $writer);

        return response()->json(['data' => $article, 'message' => 'Article published']);
    }

    public function schedule(Request $request, Article $article, ArticleService $articleService)
    {
        $writer = $this->checkWriter($request);
        abort_unless($article->writer_id === $writer->id, 403);

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $article = $articleService->scheduleArticle($article, $validated['scheduled_at'], $writer);

        return response()->json(['data' => $article, 'message' => 'Article scheduled']);
    }

    public function autosave(Request $request, Article $article, ArticleService $articleService)
    {
        $writer = $this->checkWriter($request);
        abort_unless($article->writer_id === $writer->id, 403);

        $validated = $request->validate([
            'content' => 'sometimes|array',
            'title' => 'sometimes|string|max:255',
            'excerpt' => 'sometimes|string',
            'reading_time' => 'sometimes|integer|min:0',
        ]);

        $article = $articleService->autosaveArticle($article, $validated, $writer);

        return response()->json(['data' => $article, 'message' => 'Article autosaved']);
    }
}
