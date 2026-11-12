<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $transactions = Transaction::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $transactions->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ],
        ]);
    }
}
