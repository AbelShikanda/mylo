<?php

namespace App\Services\Marketplace;

use App\Models\Identity\DigitalAccess;
use App\Models\Identity\Order;
use App\Models\Identity\OrderItem;
use App\Models\Identity\Reader;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderService extends \App\Services\Shared\BaseService
{
    public function createOrder(array $data, Reader $reader): Order
    {
        return DB::transaction(function () use ($data, $reader) {
            $order = Order::create([
                'id' => (string) Str::uuid(),
                'reader_id' => $reader->id,
                'status' => 'pending',
                'total' => $data['total'],
                'currency' => $data['currency'] ?? 'KES',
                'shipping_address' => $data['shipping_address'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'id' => (string) Str::uuid(),
                    'order_id' => $order->id,
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Decrease inventory
                $book = \App\Models\Identity\Book::find($item['book_id']);
                if ($book && $book->inventory_count > 0) {
                    $book->decrement('inventory_count', $item['quantity']);
                }

                // Create digital access for ebooks
                if ($book && $book->product_type === 'ebook') {
                    DigitalAccess::create([
                        'id' => (string) Str::uuid(),
                        'order_item_id' => $orderItem->id ?? (string) Str::uuid(),
                        'reader_id' => $reader->id,
                        'book_id' => $book->id,
                        'access_token' => (string) Str::uuid(),
                        'expires_at' => Carbon::now()->addYear(),
                    ]);
                }
            }

            return $order->fresh()->load('items.book');
        });
    }

    public function getOrdersByReader(Reader $reader, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Order::where('reader_id', $reader->id)
            ->with('items.book');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getOrderById(string $orderId, Reader $reader): ?Order
    {
        return Order::where('id', $orderId)
            ->where('reader_id', $reader->id)
            ->with('items.book')
            ->first();
    }

    public function getAllOrders(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Order::with('reader', 'items.book');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['reader_id'])) {
            $query->where('reader_id', $filters['reader_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->fresh();
    }

    public function getDigitalAccess(Reader $reader, string $bookId): ?DigitalAccess
    {
        return DigitalAccess::where('reader_id', $reader->id)
            ->where('book_id', $bookId)
            ->where('expires_at', '>', now())
            ->first();
    }
}
