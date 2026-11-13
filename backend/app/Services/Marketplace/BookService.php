<?php

namespace App\Services\Marketplace;

use App\Models\Identity\Book;
use App\Models\Identity\DigitalAccess;
use App\Models\Identity\Order;
use App\Models\Identity\OrderItem;
use App\Models\Identity\ProductCategory;
use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookService extends \App\Services\Shared\BaseService
{
    public function createBook(array $data, Writer $writer): Book
    {
        return Book::create([
            'id' => (string) Str::uuid(),
            'writer_id' => $writer->id,
            'product_category_id' => $data['product_category_id'] ?? null,
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'author' => $data['author'] ?? $writer->profile?->display_name ?? $writer->email,
            'cover_image' => $data['cover_image'] ?? null,
            'price' => $data['price'],
            'product_type' => $data['product_type'] ?? 'ebook',
            'is_available' => $data['is_available'] ?? true,
            'preview_file' => $data['preview_file'] ?? null,
            'digital_file' => $data['digital_file'] ?? null,
            'inventory_count' => $data['inventory_count'] ?? 0,
        ]);
    }

    public function updateBook(Book $book, array $data, Writer $writer): Book
    {
        $book->update([
            'product_category_id' => $data['product_category_id'] ?? $book->product_category_id,
            'title' => $data['title'] ?? $book->title,
            'slug' => $data['slug'] ?? $book->slug,
            'description' => $data['description'] ?? $book->description,
            'author' => $data['author'] ?? $book->author,
            'cover_image' => $data['cover_image'] ?? $book->cover_image,
            'price' => $data['price'] ?? $book->price,
            'product_type' => $data['product_type'] ?? $book->product_type,
            'is_available' => $data['is_available'] ?? $book->is_available,
            'preview_file' => $data['preview_file'] ?? $book->preview_file,
            'digital_file' => $data['digital_file'] ?? $book->digital_file,
            'inventory_count' => $data['inventory_count'] ?? $book->inventory_count,
        ]);

        return $book->fresh();
    }

    public function deleteBook(Book $book, Writer $writer): void
    {
        $book->delete();
    }

    public function getBooksByWriter(Writer $writer, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Book::where('writer_id', $writer->id)
            ->with('productCategory');

        if (isset($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getAllBooks(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Book::where('is_available', true)
            ->with('writer', 'productCategory');

        if (isset($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['category_id'])) {
            $query->where('product_category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getBookBySlug(string $slug): ?Book
    {
        return Book::where('slug', $slug)
            ->where('is_available', true)
            ->with('writer', 'productCategory')
            ->first();
    }

    public function getBookById(string $id): ?Book
    {
        return Book::where('id', $id)
            ->with('writer', 'productCategory')
            ->first();
    }
}
