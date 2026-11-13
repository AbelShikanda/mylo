<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Book;
use App\Models\Identity\Writer;
use App\Services\Marketplace\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(Request $request)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $filters = $request->only(['product_type', 'search']);
        $books = $this->bookService->getBooksByWriter($writer, $filters);

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

    public function store(Request $request, BookService $bookService)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:books,slug',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'product_type' => 'sometimes|in:physical,ebook',
            'is_available' => 'sometimes|boolean',
            'preview_file' => 'nullable|string',
            'digital_file' => 'nullable|string',
            'inventory_count' => 'sometimes|integer|min:0',
            'product_category_id' => 'nullable|uuid|exists:product_categories,id',
        ]);

        $book = $bookService->createBook($validated, $writer);

        return response()->json(['data' => $book], 201);
    }

    public function show(Request $request, Book $book)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer || $book->writer_id !== $writer->id) {
            abort(403);
        }

        return response()->json(['data' => $book]);
    }

    public function update(Request $request, Book $book, BookService $bookService)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer || $book->writer_id !== $writer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:books,slug,' . $book->id,
            'description' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'cover_image' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'product_type' => 'sometimes|in:physical,ebook',
            'is_available' => 'sometimes|boolean',
            'preview_file' => 'sometimes|string',
            'digital_file' => 'sometimes|string',
            'inventory_count' => 'sometimes|integer|min:0',
            'product_category_id' => 'sometimes|uuid|exists:product_categories,id',
        ]);

        $book = $bookService->updateBook($book, $validated, $writer);

        return response()->json(['data' => $book]);
    }

    public function destroy(Request $request, Book $book, BookService $bookService)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer || $book->writer_id !== $writer->id) {
            abort(403);
        }

        $bookService->deleteBook($book, $writer);

        return response()->json(['message' => 'Book deleted']);
    }
}
