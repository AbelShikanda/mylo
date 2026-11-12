<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Transaction;
use App\Models\Identity\Contribution;
use App\Models\Identity\PayoutRequest;
use App\Models\Identity\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function overview(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        return response()->json([
            'data' => [
                'transaction_volume' => Transaction::sum('amount'),
                'platform_commissions' => 0,
                'book_sales' => 0,
                'writer_contributions' => Contribution::sum('amount'),
                'payment_fees' => 0,
                'refunds' => Refund::sum('amount'),
                'net_platform_revenue' => 0,
            ],
        ]);
    }

    public function transactions(Request $request)
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

    public function earnings(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        return response()->json([
            'data' => [
                'contributions_received' => Contribution::sum('amount'),
                'book_sales' => 0,
                'platform_deductions' => 0,
                'available_balance' => 0,
                'pending_balance' => 0,
                'withdrawn_amount' => 0,
            ],
        ]);
    }
}
