<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Book;
use App\Models\Identity\ProductCategory;
use App\Models\Identity\Reader;
use App\Services\Marketplace\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('name', 'asc')->get();

        return response()->json(['data' => $categories]);
    }

    public function show($slug)
    {
        $category = ProductCategory::where('slug', $slug)->first();

        if (! $category) {
            return response()->json(['data' => null], 404);
        }

        return response()->json(['data' => $category]);
    }

    public function articles($category)
    {
        return response()->json(['data' => []]);
    }
}

class TagController extends Controller
{
    public function index()
    {
        return response()->json(['data' => []]);
    }

    public function show($slug)
    {
        return response()->json(['data' => null]);
    }
}

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $type = $request->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'article') {
            $articles = Article::where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where('title', 'like', "%{$q}%")
                ->with('writer', 'category')
                ->limit(10)
                ->get();

            foreach ($articles as $article) {
                $results[] = [
                    'type' => 'article',
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'url' => '/articles/' . $article->slug,
                    'author' => $article->writer->display_name ?? $article->writer->email,
                    'category' => $article->category?->name,
                ];
            }
        }

        if ($type === 'all' || $type === 'book') {
            $books = Book::where('is_available', true)
                ->where('title', 'like', "%{$q}%")
                ->with('writer')
                ->limit(10)
                ->get();

            foreach ($books as $book) {
                $results[] = [
                    'type' => 'book',
                    'id' => $book->id,
                    'title' => $book->title,
                    'slug' => $book->slug,
                    'url' => '/books/' . $book->slug,
                    'author' => $book->author,
                    'price' => $book->price,
                    'product_type' => $book->product_type,
                ];
            }
        }

        return response()->json(['data' => $results]);
    }
}

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = session('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'data' => [
                'items' => $cart,
                'total' => $total,
            ],
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'book_id' => 'required|uuid|exists:books,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $book = Book::find($request->book_id);

        $cart = session('cart', []);

        $existingIndex = array_search($request->book_id, array_column($cart, 'book_id'));

        if ($existingIndex !== false) {
            $cart[$existingIndex]['quantity'] += $request->quantity ?? 1;
        } else {
            $cart[] = [
                'book_id' => $book->id,
                'title' => $book->title,
                'slug' => $book->slug,
                'price' => $book->price,
                'product_type' => $book->product_type,
                'cover_image' => $book->cover_image,
                'quantity' => $request->quantity ?? 1,
            ];
        }

        session(['cart' => $cart]);

        return response()->json(['message' => 'Item added to cart', 'data' => $cart]);
    }

    public function removeItem(Request $request, $item)
    {
        $cart = session('cart', []);
        $cart = array_values(array_filter($cart, function ($i) use ($item) {
            return $i['book_id'] !== $item;
        }));

        session(['cart' => $cart]);

        return response()->json(['message' => 'Item removed from cart']);
    }
}

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.book_id' => 'required|uuid|exists:books,id',
            'items.*.quantity' => 'sometimes|integer|min:1',
            'items.*.price' => 'required|numeric',
            'total' => 'required|numeric',
            'currency' => 'sometimes|string|size:3',
            'shipping_address' => 'sometimes|array',
        ]);

        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $order = $this->orderService->createOrder([
            'total' => $request->total,
            'currency' => $request->currency ?? 'KES',
            'shipping_address' => $request->shipping_address,
            'items' => $request->items,
        ], $reader);

        session(['cart' => []]);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }
}
