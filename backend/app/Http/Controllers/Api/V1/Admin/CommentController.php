<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Comment;
use App\Models\Identity\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        $query = Comment::with('article', 'writer', 'reader');

        if ($search = $request->get('search')) {
            $query->where('content', 'like', "%{$search}%");
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);

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

    public function destroy(Request $request, Comment $comment)
    {
        abort_unless($request->user()->hasPermission('comments.moderate'), 403);

        AuditLog::create([
            'administrator_id' => $request->user()->id,
            'action' => 'remove_comment',
            'subject_type' => Comment::class,
            'subject_id' => $comment->id,
            'old_values' => $comment->getAttributes(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $comment->delete();

        return response()->json(['message' => 'Comment removed']);
    }
}
