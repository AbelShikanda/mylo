<?php

namespace App\Http\Controllers\Api\V1\Reader;

use App\Http\Controllers\Controller;
use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Services\Finance\FinancialService;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContributionController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $contributions = \App\Models\Identity\Contribution::where('reader_id', $reader->id)
            ->with('writer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $contributions->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $contributions->currentPage(),
                    'last_page' => $contributions->lastPage(),
                    'per_page' => $contributions->perPage(),
                    'total' => $contributions->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request, FinancialService $financialService)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $validated = $request->validate([
            'writer_id' => 'required|uuid|exists:writers,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'type' => 'nullable|in:one-time,recurring,article-specific',
            'payment_method' => 'nullable|string',
            'provider' => 'nullable|string',
            'idempotency_key' => 'nullable|string',
        ]);

        $writer = Writer::findOrFail($validated['writer_id']);

        $result = $financialService->createContribution([
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'type' => $validated['type'] ?? 'one-time',
            'payment_method' => $validated['payment_method'] ?? 'manual',
            'provider' => $validated['provider'] ?? 'manual',
            'idempotency_key' => $validated['idempotency_key'] ?? null,
        ], $writer, $reader);

        return response()->json([
            'data' => $result['contribution'],
            'duplicate' => $result['duplicate'] ?? false,
        ], $result['duplicate'] ?? false ? 200 : 201);
    }

    public function initiatePayment(Request $request)
    {
        /** @var Reader $reader */
        $reader = $request->user('reader');
        if (! $reader) {
            abort(404);
        }

        $validated = $request->validate([
            'writer_id' => 'required|uuid|exists:writers,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'provider' => 'required|string|in:mpesa,stripe',
            'phone_number' => 'nullable|string',
        ]);

        $gateway = PaymentGatewayFactory::make($validated['provider']);

        $paymentResult = $gateway->initiatePayment([
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'phone_number' => $validated['phone_number'] ?? null,
            'reference' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        return response()->json([
            'data' => $paymentResult,
        ]);
    }
}
