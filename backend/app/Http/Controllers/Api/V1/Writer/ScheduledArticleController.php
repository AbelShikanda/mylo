<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use Illuminate\Http\Request;

class ScheduledArticleController extends Controller
{
    public function index(Request $request)
    {
        $writer = $request->user('writer');
        abort_unless($writer, 404);

        $articles = Article::where('writer_id', $writer->id)
            ->where('status', 'scheduled')
            ->with('category', 'tags')
            ->orderBy('scheduled_at', 'asc')
            ->orderBy('published_at', 'asc')
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
