<?php

namespace App\Services\Payment;

use App\Models\Identity\PaymentAttempt;
use App\Models\Identity\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class PaymentGatewayService
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    abstract public function initiatePayment(array $data): array;

    abstract public function verifyPayment(string $reference): array;

    abstract public function processWebhook(array $payload): array;

    protected function createPaymentAttempt(Transaction $transaction, array $rawResponse): PaymentAttempt
    {
        return PaymentAttempt::create([
            'id' => (string) Str::uuid(),
            'transaction_id' => $transaction->id,
            'provider' => static::providerName(),
            'reference' => $rawResponse['reference'] ?? null,
            'raw_response' => $rawResponse,
        ]);
    }

    public static function providerName(): string
    {
        return str_replace('Service', '', (new \ReflectionClass(static::class))->getShortName());
    }
}

class MpesaService extends PaymentGatewayService
{
    public function initiatePayment(array $data): array
    {
        $amount = $data['amount'];
        $phone = $data['phone_number'];
        $reference = $data['reference'];

        // M-Pesa STK Push simulation
        $response = [
            'reference' => 'MPESA' . Str::random(10),
            'status' => 'pending',
            'provider' => 'mpesa',
            'raw_response' => [
                'merchant_request_id' => 'MERCHANT' . Str::random(10),
                'checkout_request_id' => 'CHECKOUT' . Str::random(10),
                'response_code' => '0',
                'response_description' => 'Success. Request accepted for processing',
                'customer_message' => 'Success. Request accepted for processing',
            ],
        ];

        Log::info('M-Pesa payment initiated', [
            'reference' => $reference,
            'amount' => $amount,
            'phone' => $phone,
            'mpesa_reference' => $response['reference'],
        ]);

        return $response;
    }

    public function verifyPayment(string $reference): array
    {
        // Simulate M-Pesa payment verification
        return [
            'status' => 'completed',
            'provider_reference' => 'MPESA_VERIFIED_' . Str::random(8),
            'verified' => true,
        ];
    }

    public function processWebhook(array $payload): array
    {
        $status = $payload['status'] ?? 'completed';
        $providerReference = $payload['provider_reference'] ?? null;

        return [
            'status' => $status,
            'provider_reference' => $providerReference,
            'verified' => true,
            'raw_payload' => $payload,
        ];
    }
}

class StripeService extends PaymentGatewayService
{
    public function initiatePayment(array $data): array
    {
        $amount = $data['amount'];
        $currency = $data['currency'] ?? 'USD';
        $reference = $data['reference'];

        $response = [
            'reference' => 'STRIPE' . Str::random(10),
            'status' => 'pending',
            'provider' => 'stripe',
            'raw_response' => [
                'payment_intent_id' => 'pi_' . Str::random(24),
                'client_secret' => 'pi_' . Str::random(24) . '_secret_' . Str::random(24),
                'status' => 'requires_payment_method',
            ],
        ];

        Log::info('Stripe payment initiated', [
            'reference' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'stripe_reference' => $response['reference'],
        ]);

        return $response;
    }

    public function verifyPayment(string $reference): array
    {
        return [
            'status' => 'completed',
            'provider_reference' => 'STRIPE_VERIFIED_' . Str::random(8),
            'verified' => true,
        ];
    }

    public function processWebhook(array $payload): array
    {
        $status = $payload['status'] ?? 'completed';
        $providerReference = $payload['provider_reference'] ?? null;

        return [
            'status' => $status,
            'provider_reference' => $providerReference,
            'verified' => true,
            'raw_payload' => $payload,
        ];
    }
}

class PaymentGatewayFactory
{
    public static function make(string $provider, array $config = []): PaymentGatewayService
    {
        return match (strtolower($provider)) {
            'mpesa' => new MpesaService($config),
            'stripe' => new StripeService($config),
            default => throw new \Exception("Unsupported payment provider: {$provider}"),
        };
    }
}
