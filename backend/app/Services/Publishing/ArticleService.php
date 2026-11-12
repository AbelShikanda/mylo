<?php

namespace App\Services\Publishing;

use App\Models\Identity\Article;
use App\Models\Identity\ArticleRevision;
use App\Models\Identity\Category;
use App\Models\Identity\Tag;
use App\Models\Identity\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ArticleService extends \App\Services\Shared\BaseService
{
    public function createArticle(array $data, Writer $writer): Article
    {
        $article = Article::create([
            'id' => (string) Str::uuid(),
            'writer_id' => $writer->id,
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? [],
            'featured_image' => $data['featured_image'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'reading_time' => $data['reading_time'] ?? 0,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'published_at' => $data['status'] === 'published' ? now() : null,
            'is_featured' => false,
            'is_reported' => false,
        ]);

        $this->createRevision($article, $writer->id, $data['content'] ?? [], 'Initial version');

        if (isset($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }

        return $article->load('writer', 'category', 'tags');
    }

    public function updateArticle(Article $article, array $data, Writer $writer): Article
    {
        $oldContent = $article->content;

        $article->update([
            'category_id' => $data['category_id'] ?? $article->category_id,
            'subcategory_id' => $data['subcategory_id'] ?? $article->subcategory_id,
            'title' => $data['title'] ?? $article->title,
            'slug' => $data['slug'] ?? $article->slug,
            'excerpt' => $data['excerpt'] ?? $article->excerpt,
            'content' => $data['content'] ?? $article->content,
            'featured_image' => $data['featured_image'] ?? $article->featured_image,
            'status' => $data['status'] ?? $article->status,
            'seo_title' => $data['seo_title'] ?? $article->seo_title,
            'seo_description' => $data['seo_description'] ?? $article->seo_description,
            'reading_time' => $data['reading_time'] ?? $article->reading_time,
            'scheduled_at' => $data['scheduled_at'] ?? $article->scheduled_at,
            'published_at' => $data['status'] === 'published' && !$article->published_at
                ? now()
                : $article->published_at,
        ]);

        if (isset($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }

        if (isset($data['content']) && $data['content'] !== $oldContent) {
            $this->createRevision($article, $writer->id, $data['content'], 'Updated content');
        }

        return $article->fresh()->load('writer', 'category', 'tags');
    }

    public function publishArticle(Article $article, Writer $writer): Article
    {
        $article->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->createRevision($article, $writer->id, $article->content, 'Published');

        return $article->fresh();
    }

    public function scheduleArticle(Article $article, string $scheduledAt, Writer $writer): Article
    {
        $article->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        $this->createRevision($article, $writer->id, $article->content, 'Scheduled');

        return $article->fresh();
    }

    public function autosaveArticle(Article $article, array $data, Writer $writer): Article
    {
        $article->update([
            'content' => $data['content'] ?? $article->content,
            'title' => $data['title'] ?? $article->title,
            'excerpt' => $data['excerpt'] ?? $article->excerpt,
            'reading_time' => $data['reading_time'] ?? $article->reading_time,
        ]);

        $this->createRevision($article, $writer->id, $data['content'] ?? $article->content, 'Autosave');

        return $article->fresh();
    }

    public function deleteArticle(Article $article, Writer $writer): void
    {
        $article->delete();
    }

    public function getArticlesByWriter(Writer $writer, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Article::where('writer_id', $writer->id)
            ->with('category', 'tags');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getPublishedArticles(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('writer', 'category', 'tags');

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('id', $filters['tag_id']);
            });
        }

        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        return $query->orderBy('published_at', 'desc')->paginate(20);
    }

    public function getArticleBySlug(string $slug): ?Article
    {
        return Article::where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('writer', 'category', 'tags')
            ->first();
    }

    public function featureArticle(Article $article): void
    {
        $article->update(['is_featured' => true]);
    }

    public function unfeatureArticle(Article $article): void
    {
        $article->update(['is_featured' => false]);
    }

    private function createRevision(Article $article, ?string $changedBy, array $content, string $summary): void
    {
        ArticleRevision::create([
            'id' => (string) Str::uuid(),
            'article_id' => $article->id,
            'content' => $content,
            'changed_by' => $changedBy,
            'change_summary' => $summary,
        ]);
    }
}
