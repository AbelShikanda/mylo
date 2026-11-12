<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('writer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('writer_id')->constrained('writers')->onDelete('cascade');
            $table->string('display_name')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->decimal('earnings_balance', 12, 2)->default(0);
            $table->decimal('pending_balance', 12, 2)->default(0);
            $table->decimal('withdrawn_amount', 12, 2)->default(0);
            $table->boolean('support_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('writer_profiles');
    }
};
