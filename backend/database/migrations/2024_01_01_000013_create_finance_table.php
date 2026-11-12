<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('writer_id')->constrained('writers')->onDelete('cascade');
            $table->uuid('reader_id')->nullable()->constrained('readers')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('KES');
            $table->string('type')->default('one-time');
            $table->string('status')->default('completed');
            $table->string('transaction_reference')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('recurring_support', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('writer_id')->constrained('writers')->onDelete('cascade');
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('KES');
            $table->string('interval')->default('monthly');
            $table->timestamp('next_due');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->string('customer_type');
            $table->uuid('customer_id')->nullable();
            $table->uuid('writer_id')->nullable()->constrained('writers')->onDelete('set null');
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('KES');
            $table->string('payment_method');
            $table->string('provider');
            $table->string('provider_reference')->nullable();
            $table->string('status');
            $table->string('idempotency_key')->unique();
            $table->timestamps();
        });

        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->string('provider');
            $table->string('reference')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        Schema::create('writer_earnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('writer_id')->constrained('writers')->onDelete('cascade')->unique();
            $table->decimal('contributions', 12, 2)->default(0);
            $table->decimal('book_sales', 12, 2)->default(0);
            $table->decimal('platform_deductions', 12, 2)->default(0);
            $table->decimal('available_balance', 12, 2)->default(0);
            $table->decimal('pending_balance', 12, 2)->default(0);
            $table->decimal('withdrawn_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payout_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('writer_id')->constrained('writers')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payout_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payout_request_id')->constrained('payout_requests')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('provider');
            $table->string('reference');
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_ledger', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_type');
            $table->string('reference')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('KES');
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->text('description')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->decimal('rate', 5, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('financial_ledger');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payout_records');
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('writer_earnings');
        Schema::dropIfExists('payment_attempts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('recurring_support');
        Schema::dropIfExists('contributions');
    }
};
