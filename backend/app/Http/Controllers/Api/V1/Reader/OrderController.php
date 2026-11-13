<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $orders = $this->orderService->getOrdersByReader($reader);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
            ],
        ]);
    }

    public function show(Request $request, $order)
    {
        $reader = $request->user('reader');
        abort_unless($reader, 404);

        $model = $this->orderService->getOrderById($order, $reader);

        if (! $model) {
            return response()->json(['data' => null], 404);
        }

        return response()->json(['data' => $model]);
    }
}
