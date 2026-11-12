<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\PayoutRequest;
use App\Models\Identity\Refund;
use App\Models\Identity\Transaction;
use App\Services\Finance\FinancialService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function overview(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $overview = $this->financialService->getFinancialOverview();

        return response()->json([
            'data' => $overview,
        ]);
    }

    public function transactions(Request $request)
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

    public function earnings(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $earnings = \App\Models\Identity\WriterEarnings::with('writer')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $earnings->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $earnings->currentPage(),
                    'last_page' => $earnings->lastPage(),
                    'per_page' => $earnings->perPage(),
                    'total' => $earnings->total(),
                ],
            ],
        ]);
    }
}
