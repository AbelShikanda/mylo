<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'payout_records',
            'refunds',
            'financial_ledger',
            'commissions',
            'platform_config',
            'moderation_records',
            'transactions',
        ];

        foreach ($tables as $tbl) {
            Schema::table($tbl, function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'payout_records',
            'refunds',
            'financial_ledger',
            'commissions',
            'platform_config',
            'moderation_records',
            'transactions',
        ];

        foreach ($tables as $tbl) {
            Schema::table($tbl, function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};
