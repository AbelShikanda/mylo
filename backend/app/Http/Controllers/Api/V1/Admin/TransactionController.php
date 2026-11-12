<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Transaction;
use App\Services\Finance\FinancialService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $filters = $request->only(['status', 'type', 'writer_id']);
        $transactions = $this->financialService->getTransactions($filters);

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
