<?php

namespace App\Services\Administration;

use App\Models\Identity\Administrator;
use App\Models\Identity\Article;
use App\Models\Identity\AuditLog;
use App\Models\Identity\Book;
use App\Models\Identity\Comment;
use App\Models\Identity\Contribution;
use App\Models\Identity\ModerationRecord;
use App\Models\Identity\Order;
use App\Models\Identity\Reader;
use App\Models\Identity\Transaction;
use App\Models\Identity\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedAnalyticsService extends \App\Services\Shared\BaseService
{
    public function getRegistrationTrends(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $readers = Reader::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $writers = Writer::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $admins = Administrator::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'readers' => $readers,
            'writers' => $writers,
            'administrators' => $admins,
        ];
    }

    public function getReaderEngagement(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $articleViews = Article::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $likes = \App\Models\Identity\ArticleLike::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $comments = Comment::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $bookmarks = \App\Models\Identity\Bookmark::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'views' => $articleViews,
            'likes' => $likes,
            'comments' => $comments,
            'bookmarks' => $bookmarks,
        ];
    }

    public function getWriterActivity(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $articlesPublished = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', $start)
            ->select(DB::raw('DATE(published_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $articlesDraft = Article::where('status', 'draft')
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'published' => $articlesPublished,
            'drafts' => $articlesDraft,
        ];
    }

    public function getArticlePerformance(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $topArticles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', $start)
            ->with('writer')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return $topArticles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'writer' => $article->writer->display_name ?? $article->writer->email,
                'category' => $article->category?->name,
                'likes' => $article->likes()->count(),
                'comments' => $article->comments()->count(),
                'published_at' => $article->published_at,
            ];
        })->toArray();
    }

    public function getCategoryPopularity(): array
    {
        $categories = \App\Models\Identity\Category::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(20)
            ->get();

        return $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'article_count' => $cat->articles_count,
            ];
        })->toArray();
    }

    public function getSearchActivity(string $range = '30d'): array
    {
        $topTags = DB::table('article_tags')
            ->join('tags', 'tags.id', '=', 'article_tags.tag_id')
            ->select('tags.name', DB::raw('COUNT(*) as total'))
            ->groupBy('tags.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => ['query' => $row->name, 'count' => (int) $row->total])
            ->values()
            ->all();

        return [
            'total_searches' => Article::where('status', 'published')->count(),
            'top_queries' => $topTags,
        ];
    }

    public function getBookSales(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $sales = Order::where('status', 'completed')
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topBooks = Book::where('is_available', true)
            ->with('writer')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get()->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'price' => $book->price,
                    'product_type' => $book->product_type,
                ];
            })->toArray();

        return [
            'sales_over_time' => $sales,
            'top_books' => $topBooks,
        ];
    }

    public function getFinancialContributions(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $contributions = Contribution::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $contributions->toArray();
    }

    public function getPayouts(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $payouts = \App\Models\Identity\PayoutRequest::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $payouts->toArray();
    }

    public function getFailedTransactions(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $failed = Transaction::where('status', 'failed')
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $failed->toArray();
    }

    public function getModerationActivity(string $range = '30d'): array
    {
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        $reports = \App\Models\Identity\Report::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $moderations = ModerationRecord::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'reports' => $reports,
            'moderations' => $moderations,
        ];
    }

    public function getPlatformOverview(): array
    {
        return [
            'total_readers' => Reader::count(),
            'total_writers' => Writer::count(),
            'total_administrators' => Administrator::count(),
            'total_articles' => Article::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'total_books' => Book::count(),
            'total_orders' => Order::count(),
            'total_contributions' => Contribution::count(),
            'total_transactions' => Transaction::count(),
            'total_audit_logs' => AuditLog::count(),
        ];
    }
}
