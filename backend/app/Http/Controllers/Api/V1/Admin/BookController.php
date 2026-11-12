<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('books.manage'), 403);

        $query = Book::with('writer');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $books->currentPage(),
                    'last_page' => $books->lastPage(),
                    'per_page' => $books->perPage(),
                    'total' => $books->total(),
                ],
            ],
        ]);
    }
}
