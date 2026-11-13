<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $writer = $request->user('writer');
        abort_unless($writer, 404);

        $comments = Comment::whereHas('article', function ($query) use ($writer) {
                $query->where('writer_id', $writer->id);
            })
            ->with('article:id,title,slug', 'reader:id,email', 'reader.profile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $comments->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ],
            ],
        ]);
    }

    public function update(Request $request, $comment)
    {
        $writer = $request->user('writer');
        abort_unless($writer, 404);

        $model = Comment::whereHas('article', function ($query) use ($writer) {
                $query->where('writer_id', $writer->id);
            })
            ->where('id', $comment)
            ->firstOrFail();

        $validated = $request->validate([
            'is_approved' => 'sometimes|boolean',
        ]);

        $model->update($validated);

        return response()->json(['message' => 'Comment updated', 'data' => $model]);
    }
}
