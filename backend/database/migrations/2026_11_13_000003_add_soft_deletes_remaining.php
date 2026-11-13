<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'article_likes',
            'article_revisions',
            'bookmarks',
            'homepage_sections',
            'payment_attempts',
            'reading_history',
            'scheduled_publications',
        ] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'article_likes',
            'article_revisions',
            'bookmarks',
            'homepage_sections',
            'payment_attempts',
            'reading_history',
            'scheduled_publications',
        ] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};
