<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Reader;
use App\Services\Reader\ReaderService;
use Illuminate\Http\Request;

class ReadingHistoryController extends Controller
{
    protected ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    public function index(Request $request, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $history = $readerService->getReadingHistory($reader);

        return response()->json(['data' => $history]);
    }

    public function record(Request $request, Article $article, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $validated = $request->validate([
            'duration_seconds' => 'nullable|integer|min:0',
        ]);

        $readerService->recordReadingHistory(
            $reader,
            $article->id,
            $validated['duration_seconds'] ?? 0
        );

        return response()->json(['message' => 'Reading history recorded']);
    }
}
