<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\Refund;
use App\Services\Finance\FinancialService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.refunds.manage'), 403);

        $filters = $request->only(['status']);
        $refunds = $this->financialService->getRefunds($filters);

        return response()->json([
            'data' => $refunds->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $refunds->currentPage(),
                    'last_page' => $refunds->lastPage(),
                    'per_page' => $refunds->perPage(),
                    'total' => $refunds->total(),
                ],
            ],
        ]);
    }

    public function approve(Request $request, Refund $refund, FinancialService $financialService)
    {
        abort_unless($request->user()->hasPermission('finance.refunds.manage'), 403);

        $financialService->approveRefund($refund);

        return response()->json(['message' => 'Refund approved']);
    }

    public function reject(Request $request, Refund $refund, FinancialService $financialService)
    {
        abort_unless($request->user()->hasPermission('finance.refunds.manage'), 403);

        $financialService->rejectRefund($refund);

        return response()->json(['message' => 'Refund rejected']);
    }
}
