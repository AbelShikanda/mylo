<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::where('is_available', true)
            ->with('writer', 'productCategory');

        if ($productType = $request->get('product_type')) {
            $query->where('product_type', $productType);
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('product_category_id', $categoryId);
        }

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

    public function show($slug)
    {
        $book = Book::where('slug', $slug)
            ->where('is_available', true)
            ->with('writer', 'productCategory')
            ->first();

        if (! $book) {
            return response()->json(['data' => null], 404);
        }

        return response()->json(['data' => $book]);
    }
}

