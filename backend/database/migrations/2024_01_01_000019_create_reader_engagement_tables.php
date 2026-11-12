<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['article_id', 'reader_id']);
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['reader_id', 'article_id']);
        });

        Schema::create('reading_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->integer('duration_seconds')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['reader_id', 'article_id']);
        });

        Schema::create('reader_category_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->uuid('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['reader_id', 'category_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('recipient_type');
            $table->uuid('recipient_id');
            $table->string('type');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['recipient_type', 'recipient_id']);
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reportable_type');
            $table->uuid('reportable_id');
            $table->string('reporter_type');
            $table->uuid('reporter_id');
            $table->string('reason');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reader_category_preferences');
        Schema::dropIfExists('reading_history');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('article_likes');
    }
};
