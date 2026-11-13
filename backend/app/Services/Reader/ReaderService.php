<?php

namespace App\Services\Reader;

use App\Models\Identity\Article;
use App\Models\Identity\ArticleLike;
use App\Models\Identity\Bookmark;
use App\Models\Identity\Category;
use App\Models\Identity\Notification;
use App\Models\Identity\Reader;
use App\Models\Identity\ReaderCategoryPreference;
use App\Models\Identity\ReadingHistory;
use App\Models\Identity\Report;
use Illuminate\Support\Str;

class ReaderService extends \App\Services\Shared\BaseService
{
    public function getPreferences(Reader $reader): array
    {
        $preferences = ReaderCategoryPreference::where('reader_id', $reader->id)
            ->with('category')
            ->get();

        return $preferences->map(function ($pref) {
            return [
                'id' => $pref->id,
                'category_id' => $pref->category_id,
                'category_name' => $pref->category->name,
                'category_slug' => $pref->category->slug,
            ];
        })->toArray();
    }

    public function updatePreferences(Reader $reader, array $categoryIds): void
    {
        ReaderCategoryPreference::where('reader_id', $reader->id)->delete();

        foreach ($categoryIds as $categoryId) {
            ReaderCategoryPreference::create([
                'reader_id' => $reader->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    public function getAllCategories(): array
    {
        return Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'is_featured' => $cat->is_featured,
                ];
            })->toArray();
    }

    public function toggleLike(Reader $reader, string $articleId): array
    {
        $like = ArticleLike::withTrashed()
            ->where('reader_id', $reader->id)
            ->where('article_id', $articleId)
            ->first();

        if ($like && ! $like->trashed()) {
            $like->delete();
            return ['liked' => false];
        }

        if ($like) {
            $like->restore();
            return ['liked' => true];
        }

        ArticleLike::create([
            'reader_id' => $reader->id,
            'article_id' => $articleId,
        ]);

        return ['liked' => true];
    }

    public function isLiked(Reader $reader, string $articleId): bool
    {
        return ArticleLike::where('reader_id', $reader->id)
            ->where('article_id', $articleId)
            ->exists();
    }

    public function toggleBookmark(Reader $reader, string $articleId): array
    {
        $bookmark = Bookmark::withTrashed()
            ->where('reader_id', $reader->id)
            ->where('article_id', $articleId)
            ->first();

        if ($bookmark && ! $bookmark->trashed()) {
            $bookmark->delete();
            return ['bookmarked' => false];
        }

        if ($bookmark) {
            $bookmark->restore();
            return ['bookmarked' => true];
        }

        Bookmark::create([
            'reader_id' => $reader->id,
            'article_id' => $articleId,
        ]);

        return ['bookmarked' => true];
    }

    public function isBookmarked(Reader $reader, string $articleId): bool
    {
        return Bookmark::where('reader_id', $reader->id)
            ->where('article_id', $articleId)
            ->exists();
    }

    public function getBookmarks(Reader $reader): \Illuminate\Database\Eloquent\Collection
    {
        return Bookmark::where('reader_id', $reader->id)
            ->with('article.writer', 'article.category')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLikedArticles(Reader $reader): \Illuminate\Database\Eloquent\Collection
    {
        return ArticleLike::where('reader_id', $reader->id)
            ->with('article.writer', 'article.category')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function recordReadingHistory(Reader $reader, string $articleId, int $durationSeconds = 0): void
    {
        ReadingHistory::updateOrCreate(
            ['reader_id' => $reader->id, 'article_id' => $articleId],
            [
                'duration_seconds' => $durationSeconds,
                'read_at' => now(),
            ]
        );
    }

    public function getReadingHistory(Reader $reader): \Illuminate\Database\Eloquent\Collection
    {
        return ReadingHistory::where('reader_id', $reader->id)
            ->with('article.writer', 'article.category')
            ->orderBy('read_at', 'desc')
            ->get();
    }

    public function getNotifications(Reader $reader): \Illuminate\Database\Eloquent\Collection
    {
        return Notification::where('recipient_type', 'reader')
            ->where('recipient_id', $reader->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markNotificationAsRead(Reader $reader, string $notificationId): void
    {
        Notification::where('recipient_type', 'reader')
            ->where('recipient_id', $reader->id)
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
    }

    public function getPersonalizedArticles(Reader $reader, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $preferenceCategoryIds = ReaderCategoryPreference::where('reader_id', $reader->id)
            ->pluck('category_id');

        $query = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('writer', 'category', 'tags');

        if ($preferenceCategoryIds->isNotEmpty()) {
            $query->whereIn('category_id', $preferenceCategoryIds);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('published_at', 'desc')->paginate(20);
    }

    public function getFeaturedArticles(): \Illuminate\Database\Eloquent\Collection
    {
        return Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('is_featured', true)
            ->with('writer', 'category', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();
    }

    public function getRecentArticles(): \Illuminate\Database\Eloquent\Collection
    {
        return Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('writer', 'category', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function reportContent(string $reportableType, string $reportableId, string $reporterType, string $reporterId, string $reason): void
    {
        Report::create([
            'reportable_type' => $reportableType,
            'reportable_id' => $reportableId,
            'reporter_type' => $reporterType,
            'reporter_id' => $reporterId,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }
}
