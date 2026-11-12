<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Article;
use App\Models\Identity\Administrator;
use App\Models\Identity\AdminRole;
use App\Models\Identity\AdminPermission;
use App\Models\Identity\Category;
use App\Models\Identity\Comment;
use App\Models\Identity\MediaAsset;
use App\Models\Identity\Book;
use App\Models\Identity\Order;
use App\Models\Identity\Contribution;
use App\Models\Identity\Transaction;
use App\Models\Identity\PayoutRequest;
use App\Models\Identity\Refund;
use App\Models\Identity\AuditLog;
use App\Models\Identity\PlatformConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof Administrator) {
            abort(403);
        }

        $range = $request->get('range', '30d');
        $start = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };

        return response()->json([
            'data' => [
                'users' => [
                    'total_readers' => Reader::count(),
                    'total_writers' => Writer::count(),
                    'new_registrations' => Reader::where('created_at', '>=', $start)->count() +
                        Writer::where('created_at', '>=', $start)->count(),
                    'active_accounts' => Reader::where('updated_at', '>=', $start)->count() +
                        Writer::where('updated_at', '>=', $start)->count(),
                    'suspended_accounts' => Reader::whereNotNull('deleted_at')->count() +
                        Writer::whereNotNull('deleted_at')->count(),
                ],
                'publishing' => [
                    'total_articles' => Article::count(),
                    'published_articles' => Article::where('status', 'published')->count(),
                    'drafts' => Article::where('status', 'draft')->count(),
                    'scheduled_articles' => Article::where('status', 'scheduled')->count(),
                    'recently_published' => Article::where('status', 'published')
                        ->where('published_at', '>=', $start)->count(),
                    'reported_articles' => Article::where('is_reported', true)->count(),
                ],
                'engagement' => [
                    'article_views' => 0,
                    'likes' => 0,
                    'comments' => Comment::count(),
                    'popular_articles' => [],
                    'popular_categories' => [],
                ],
                'marketplace' => [
                    'total_books' => Book::count(),
                    'ebooks' => Book::where('product_type', 'ebook')->count(),
                    'physical_books' => Book::where('product_type', 'physical')->count(),
                    'orders' => Order::count(),
                    'completed_sales' => Order::where('status', 'completed')->count(),
                    'pending_orders' => Order::where('status', 'pending')->count(),
                ],
                'finance' => [
                    'contributions' => Contribution::count(),
                    'book_sales' => Order::where('status', 'completed')->count(),
                    'platform_revenue' => 0,
                    'writer_earnings' => 0,
                    'pending_payouts' => PayoutRequest::where('status', 'pending')->count(),
                    'refunds' => Refund::count(),
                    'failed_transactions' => Transaction::where('status', 'failed')->count(),
                ],
            ],
        ]);
    }
}
