<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Reader;
use App\Services\Reader\ReaderService;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected ReaderService $readerService;

    public function __construct(ReaderService $readerService)
    {
        $this->readerService = $readerService;
    }

    public function toggle(Request $request, Article $article, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $result = $readerService->toggleLike($reader, $article->id);

        return response()->json(['data' => $result]);
    }

    public function isLiked(Request $request, Article $article, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $liked = $readerService->isLiked($reader, $article->id);

        return response()->json(['data' => ['liked' => $liked]]);
    }

    public function getLiked(Request $request, ReaderService $readerService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $likes = $readerService->getLikedArticles($reader);

        return response()->json(['data' => $likes]);
    }
}
