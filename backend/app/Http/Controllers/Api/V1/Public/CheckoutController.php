<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Cart;
use App\Services\Marketplace\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function checkout(Request $request)
    {
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $validated = $request->validate([
            'currency' => 'sometimes|string|size:3',
            'shipping_address' => 'sometimes|array',
        ]);

        $cart = Cart::where('reader_id', $reader->id)->with('items.book')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        $items = $cart->items->map(function ($item) {
            return [
                'book_id' => $item->book_id,
                'quantity' => $item->quantity,
                'price' => $item->book->price,
            ];
        })->values()->all();

        $total = $cart->items->reduce(
            fn ($carry, $item) => $carry + ($item->book->price * $item->quantity),
            0
        );

        $order = $this->orderService->createOrder([
            'total' => number_format((float) $total, 2, '.', ''),
            'currency' => $validated['currency'] ?? 'KES',
            'shipping_address' => $validated['shipping_address'] ?? null,
            'items' => $items,
        ], $reader);

        $cart->items()->delete();
        $cart->delete();

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }
}
