<?php

namespace App\Http\Controllers\Api\V1\Writer;

use App\Http\Controllers\Controller;
use App\Models\Identity\Writer;
use App\Services\Finance\FinancialService;
use Illuminate\Http\Request;

class FinancialSupportController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function activate(Request $request)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $writer->profile->update(['support_active' => true]);

        return response()->json(['message' => 'Financial support activated', 'support_active' => true]);
    }

    public function deactivate(Request $request)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $writer->profile->update(['support_active' => false]);

        return response()->json(['message' => 'Financial support deactivated', 'support_active' => false]);
    }

    public function createRecurring(Request $request, FinancialService $financialService)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $validated = $request->validate([
            'reader_id' => 'required|uuid|exists:readers,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'interval' => 'nullable|in:monthly,yearly',
            'next_due' => 'nullable|date',
        ]);

        $reader = \App\Models\Identity\Reader::findOrFail($validated['reader_id']);

        $support = $financialService->createRecurringSupport([
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'interval' => $validated['interval'] ?? 'monthly',
            'next_due' => $validated['next_due'] ?? now()->addMonth(),
        ], $writer, $reader);

        return response()->json(['data' => $support], 201);
    }

    public function cancelRecurring(Request $request, FinancialService $financialService, string $supportId)
    {
        /** @var Writer $writer */
        $writer = $request->user('writer');
        if (! $writer) {
            abort(404);
        }

        $financialService->cancelRecurringSupport($supportId, $writer);

        return response()->json(['message' => 'Recurring support cancelled']);
    }
}
