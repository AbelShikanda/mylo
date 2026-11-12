<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Identity\Transaction;
use App\Services\Finance\FinancialService;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function mpesa(Request $request)
    {
        $payload = $request->all();

        Log::info('M-Pesa webhook received', $payload);

        // Verify webhook signature (in production)
        // Process payment result
        $result = [
            'status' => $payload['status'] ?? 'completed',
            'provider_reference' => $payload['provider_reference'] ?? null,
            'verified' => true,
        ];

        if ($result['verified']) {
            $transaction = Transaction::where('provider_reference', $result['provider_reference'])
                ->orWhere('reference', $payload['transaction_reference'] ?? null)
                ->first();

            if ($transaction && $transaction->status !== 'completed') {
                $transaction->update(['status' => 'completed']);

                // Create ledger entry
                $this->financialService->createLedgerEntry(
                    'payment_verified',
                    $transaction->reference,
                    $transaction->amount,
                    $transaction->currency,
                    0,
                    $transaction->amount,
                    'Payment verified via M-Pesa webhook',
                    $transaction->writer_id
                );
            }
        }

        return response()->json(['status' => 'received']);
    }

    public function stripe(Request $request)
    {
        $payload = $request->all();

        Log::info('Stripe webhook received', $payload);

        // Verify webhook signature (in production)
        $result = [
            'status' => $payload['status'] ?? 'completed',
            'provider_reference' => $payload['provider_reference'] ?? null,
            'verified' => true,
        ];

        if ($result['verified']) {
            $transaction = Transaction::where('provider_reference', $result['provider_reference'])
                ->orWhere('reference', $payload['transaction_reference'] ?? null)
                ->first();

            if ($transaction && $transaction->status !== 'completed') {
                $transaction->update(['status' => 'completed']);

                // Create ledger entry
                $this->financialService->createLedgerEntry(
                    'payment_verified',
                    $transaction->reference,
                    $transaction->amount,
                    $transaction->currency,
                    0,
                    $transaction->amount,
                    'Payment verified via Stripe webhook',
                    $transaction->writer_id
                );
            }
        }

        return response()->json(['status' => 'received']);
    }
}
