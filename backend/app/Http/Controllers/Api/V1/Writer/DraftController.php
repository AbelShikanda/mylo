<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use Illuminate\Http\Request;

class DraftController extends Controller
{
    public function index(Request $request)
    {
        $writer = $request->user('writer');
        abort_unless($writer, 404);

        $drafts = Article::where('writer_id', $writer->id)
            ->where('status', 'draft')
            ->with('category', 'tags')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $drafts->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $drafts->currentPage(),
                    'last_page' => $drafts->lastPage(),
                    'per_page' => $drafts->perPage(),
                    'total' => $drafts->total(),
                ],
            ],
        ]);
    }
}
