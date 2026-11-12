<?php

namespace App\Services\Finance;

use App\Models\Identity\Contribution;
use App\Models\Identity\FinancialLedger;
use App\Models\Identity\Reader;
use App\Models\Identity\RecurringSupport;
use App\Models\Identity\Refund;
use App\Models\Identity\Transaction;
use App\Models\Identity\Writer;
use App\Models\Identity\WriterEarnings;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialService extends \App\Services\Shared\BaseService
{
    public function createContribution(array $data, Writer $writer, ?Reader $reader = null): array
    {
        return DB::transaction(function () use ($data, $writer, $reader) {
            $amount = $data['amount'];
            $currency = $data['currency'] ?? 'KES';
            $type = $data['type'] ?? 'one-time';
            $transactionReference = $data['transaction_reference'] ?? (string) Str::uuid();
            $idempotencyKey = $data['idempotency_key'] ?? (string) Str::uuid();

            // Check idempotency
            $existing = Transaction::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return [
                    'contribution' => $existing->contribution,
                    'transaction' => $existing,
                    'duplicate' => true,
                ];
            }

            // Create transaction
            $transaction = Transaction::create([
                'id' => (string) Str::uuid(),
                'reference' => $transactionReference,
                'customer_type' => 'reader',
                'customer_id' => $reader?->id,
                'writer_id' => $writer->id,
                'type' => 'contribution',
                'amount' => $amount,
                'currency' => $currency,
                'payment_method' => $data['payment_method'] ?? 'manual',
                'provider' => $data['provider'] ?? 'manual',
                'provider_reference' => $data['provider_reference'] ?? null,
                'status' => 'completed',
                'idempotency_key' => $idempotencyKey,
            ]);

            // Create contribution
            $contribution = Contribution::create([
                'id' => (string) Str::uuid(),
                'writer_id' => $writer->id,
                'reader_id' => $reader?->id,
                'amount' => $amount,
                'currency' => $currency,
                'type' => $type,
                'status' => 'completed',
                'transaction_reference' => $transactionReference,
            ]);

            // Create commission
            $commissionRate = config('platform.commission_rate', 0.05);
            $commissionAmount = $amount * $commissionRate;
            $writerEarningsAmount = $amount - $commissionAmount;

            \App\Models\Identity\Commission::create([
                'id' => (string) Str::uuid(),
                'transaction_id' => $transaction->id,
                'amount' => $commissionAmount,
                'rate' => $commissionRate,
            ]);

            // Update writer earnings
            $this->updateWriterEarnings($writer->id, $writerEarningsAmount, $commissionAmount, $amount);

            // Create ledger entries
            $ledgerRef1 = (string) Str::uuid();
            $ledgerRef2 = (string) Str::uuid();
            $ledgerRef3 = (string) Str::uuid();
            $this->createLedgerEntry('contribution_received', $ledgerRef1, $amount, $currency, 0, $amount, 'Contribution received from reader', $writer->id);
            $this->createLedgerEntry('commission_deducted', $ledgerRef2, -$commissionAmount, $currency, $amount, $amount - $commissionAmount, 'Platform commission deducted', $writer->id);
            $this->createLedgerEntry('writer_earnings_added', $ledgerRef3, $writerEarningsAmount, $currency, $amount - $commissionAmount, $amount, 'Writer earnings added', $writer->id);

            return [
                'contribution' => $contribution,
                'transaction' => $transaction,
                'duplicate' => false,
            ];
        });
    }

    public function createRecurringSupport(array $data, Writer $writer, Reader $reader): RecurringSupport
    {
        return RecurringSupport::create([
            'id' => (string) Str::uuid(),
            'writer_id' => $writer->id,
            'reader_id' => $reader->id,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'KES',
            'interval' => $data['interval'] ?? 'monthly',
            'next_due' => Carbon::parse($data['next_due'] ?? now()->addMonth()),
            'status' => 'active',
        ]);
    }

    public function cancelRecurringSupport(string $supportId, Writer $writer): void
    {
        $support = RecurringSupport::where('id', $supportId)
            ->where('writer_id', $writer->id)
            ->firstOrFail();

        $support->update(['status' => 'cancelled']);
    }

    public function getWriterEarnings(Writer $writer): array
    {
        $earnings = WriterEarnings::where('writer_id', $writer->id)->first();

        if (! $earnings) {
            $earnings = WriterEarnings::create([
                'id' => (string) Str::uuid(),
                'writer_id' => $writer->id,
                'contributions' => 0,
                'book_sales' => 0,
                'platform_deductions' => 0,
                'available_balance' => 0,
                'pending_balance' => 0,
                'withdrawn_amount' => 0,
            ]);
        }

        return [
            'contributions_received' => $earnings->contributions,
            'book_sales' => $earnings->book_sales,
            'platform_deductions' => $earnings->platform_deductions,
            'available_balance' => $earnings->available_balance,
            'pending_balance' => $earnings->pending_balance,
            'withdrawn_amount' => $earnings->withdrawn_amount,
        ];
    }

    public function createPayoutRequest(Writer $writer, float $amount): \App\Models\Identity\PayoutRequest
    {
        $earnings = WriterEarnings::where('writer_id', $writer->id)->first();

        if (! $earnings || $earnings->available_balance < $amount) {
            throw new \Exception('Insufficient available balance for payout');
        }

        $payout = PayoutRequest::create([
            'id' => (string) Str::uuid(),
            'writer_id' => $writer->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Move amount from available to pending
        $earnings->decrement('available_balance', $amount);
        $earnings->increment('pending_balance', $amount);

        return $payout;
    }

    public function approvePayout(\App\Models\Identity\PayoutRequest $payout, array $data): \App\Models\Identity\PayoutRecord
    {
        $payout->update([
            'status' => 'approved',
            'processed_at' => now(),
        ]);

        $record = PayoutRecord::create([
            'id' => (string) Str::uuid(),
            'payout_request_id' => $payout->id,
            'amount' => $payout->amount,
            'provider' => $data['provider'] ?? 'manual',
            'reference' => $data['reference'] ?? (string) Str::uuid(),
        ]);

        // Move from pending to withdrawn
        $writer = $payout->writer;
        $earnings = WriterEarnings::where('writer_id', $writer->id)->first();
        $earnings->decrement('pending_balance', $payout->amount);
        $earnings->increment('withdrawn_amount', $payout->amount);

        // Create ledger entry
        $this->createLedgerEntry('payout_processed', $payout->id, -$payout->amount, 'KES', 0, 0, 'Payout processed to writer', $writer->id);

        return $record;
    }

    public function rejectPayout(\App\Models\Identity\PayoutRequest $payout): void
    {
        $payout->update(['status' => 'rejected']);

        // Move amount back from pending to available
        $writer = $payout->writer;
        $earnings = WriterEarnings::where('writer_id', $writer->id)->first();
        $earnings->increment('available_balance', $payout->amount);
        $earnings->decrement('pending_balance', $payout->amount);
    }

    public function createRefund(array $data, \App\Models\Identity\Transaction $transaction): \App\Models\Identity\Refund
    {
        $refund = Refund::create([
            'id' => (string) Str::uuid(),
            'transaction_id' => $transaction->id,
            'amount' => $data['amount'],
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return $refund;
    }

    public function approveRefund(\App\Models\Identity\Refund $refund): void
    {
        $refund->update([
            'status' => 'approved',
            'processed_at' => now(),
        ]);

        // Create ledger entry
        $transaction = $refund->transaction;
        $this->createLedgerEntry('refund_issued', $refund->id, -$refund->amount, $transaction->currency, 0, 0, 'Refund issued for transaction ' . $transaction->reference, $transaction->writer_id);
    }

    public function rejectRefund(\App\Models\Identity\Refund $refund): void
    {
        $refund->update(['status' => 'rejected']);
    }

    public function getFinancialOverview(): array
    {
        return [
            'transaction_volume' => Transaction::sum('amount'),
            'writer_contributions' => Contribution::sum('amount'),
            'refunds' => Refund::sum('amount'),
        ];
    }

    public function getTransactions(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Transaction::with('writer')->orderBy('created_at', 'desc');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['writer_id'])) {
            $query->where('writer_id', $filters['writer_id']);
        }

        return $query->paginate(20);
    }

    public function getPayoutRequests(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = \App\Models\Identity\PayoutRequest::with('writer')->orderBy('created_at', 'desc');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(20);
    }

    public function getRefunds(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Refund::with('transaction')->orderBy('created_at', 'desc');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(20);
    }

    private function updateWriterEarnings(string $writerId, float $earningsAmount, float $commissionAmount, float $contributionAmount): void
    {
        $writerEarnings = WriterEarnings::where('writer_id', $writerId)->first();

        if ($writerEarnings) {
            $writerEarnings->increment('contributions', $contributionAmount);
            $writerEarnings->increment('platform_deductions', $commissionAmount);
            $writerEarnings->increment('available_balance', $earningsAmount);
        } else {
            WriterEarnings::create([
                'id' => (string) Str::uuid(),
                'writer_id' => $writerId,
                'contributions' => $contributionAmount,
                'book_sales' => 0,
                'platform_deductions' => $commissionAmount,
                'available_balance' => $earningsAmount,
                'pending_balance' => 0,
                'withdrawn_amount' => 0,
            ]);
        }
    }

    private function createLedgerEntry(string $entryType, string $reference, float $amount, string $currency, float $balanceBefore, float $balanceAfter, string $description, ?string $createdBy = null): void
    {
        FinancialLedger::create([
            'id' => (string) Str::uuid(),
            'entry_type' => $entryType,
            'reference' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'created_by' => $createdBy,
        ]);
    }
}
