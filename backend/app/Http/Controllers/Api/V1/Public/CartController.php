<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Book;
use App\Models\Identity\Cart;
use App\Models\Identity\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function getCart(Request $request): Cart
    {
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        return Cart::firstOrCreate(['reader_id' => $reader->id]);
    }

    protected function serializeCart(Cart $cart): array
    {
        $cart->load('items.book');

        $items = $cart->items->map(function (CartItem $item) {
            $book = $item->book;

            return [
                'id' => $item->id,
                'book_id' => $book->id,
                'title' => $book->title,
                'slug' => $book->slug,
                'price' => $book->price,
                'product_type' => $book->product_type,
                'cover_image' => $book->cover_image,
                'quantity' => $item->quantity,
            ];
        })->values();

        $total = $items->reduce(fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);

        return [
            'items' => $items,
            'total' => number_format((float) $total, 2, '.', ''),
        ];
    }

    public function show(Request $request)
    {
        $cart = $this->getCart($request);

        return response()->json(['data' => $this->serializeCart($cart)]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|uuid|exists:books,id',
            'quantity' => 'sometimes|integer|min:1|max:99',
        ]);

        $cart = $this->getCart($request);

        $book = Book::findOrFail($validated['book_id']);
        abort_unless($book->is_available, 422, 'This book is not available.');

        $quantity = $validated['quantity'] ?? 1;

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'book_id' => $book->id,
        ]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->save();

        return response()->json([
            'message' => 'Item added to cart',
            'data' => $this->serializeCart($cart),
        ], 201);
    }

    public function removeItem(Request $request, $item)
    {
        $cart = $this->getCart($request);

        CartItem::where('cart_id', $cart->id)
            ->where(function ($query) use ($item) {
                $query->where('id', $item)->orWhere('book_id', $item);
            })
            ->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
