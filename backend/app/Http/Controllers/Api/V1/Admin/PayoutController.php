<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity\PayoutRequest;
use App\Services\Finance\FinancialService;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.payouts.manage'), 403);

        $filters = $request->only(['status']);
        $payouts = $this->financialService->getPayoutRequests($filters);

        return response()->json([
            'data' => $payouts->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $payouts->currentPage(),
                    'last_page' => $payouts->lastPage(),
                    'per_page' => $payouts->perPage(),
                    'total' => $payouts->total(),
                ],
            ],
        ]);
    }

    public function approve(Request $request, PayoutRequest $payout, FinancialService $financialService)
    {
        abort_unless($request->user()->hasPermission('finance.payouts.manage'), 403);

        $validated = $request->validate([
            'provider' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $record = $financialService->approvePayout($payout, $validated);

        return response()->json(['message' => 'Payout approved', 'data' => $record]);
    }

    public function reject(Request $request, PayoutRequest $payout, FinancialService $financialService)
    {
        abort_unless($request->user()->hasPermission('finance.payouts.manage'), 403);

        $financialService->rejectPayout($payout);

        return response()->json(['message' => 'Payout rejected']);
    }
}
