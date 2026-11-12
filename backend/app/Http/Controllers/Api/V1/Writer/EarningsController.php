<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Writer;
use App\Services\Finance\FinancialService;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request, FinancialService $financialService)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $earnings = $financialService->getWriterEarnings($writer);

        return response()->json(['data' => $earnings]);
    }

    public function requestPayout(Request $request, FinancialService $financialService)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $payout = $financialService->createPayoutRequest($writer, $validated['amount']);

        return response()->json(['data' => $payout], 201);
    }

    public function getPayouts(Request $request)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $payouts = \App\Models\Identity\PayoutRequest::where('writer_id', $writer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

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
}
