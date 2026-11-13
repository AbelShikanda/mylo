<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\ArticleLike;
use App\Models\Identity\Comment;
use App\Models\Identity\ReadingHistory;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $writer = $request->user('writer');
        abort_unless($writer, 404);

        $articleIds = Article::where('writer_id', $writer->id)->pluck('id');

        $data = [
            'total_articles' => $articleIds->count(),
            'published_articles' => Article::where('writer_id', $writer->id)
                ->where('status', 'published')->count(),
            'total_likes' => ArticleLike::whereIn('article_id', $articleIds)->count(),
            'total_comments' => Comment::whereIn('article_id', $articleIds)->count(),
            'total_reads' => ReadingHistory::whereIn('article_id', $articleIds)->count(),
            'reading_time_minutes' => (int) Article::where('writer_id', $writer->id)
                ->sum('reading_time'),
        ];

        return response()->json(['data' => $data]);
    }
}
