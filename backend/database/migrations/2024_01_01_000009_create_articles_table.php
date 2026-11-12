<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('writer_id')->constrained('writers')->onDelete('cascade');
            $table->uuid('category_id')->nullable()->constrained('categories');
            $table->uuid('subcategory_id')->nullable()->constrained('categories');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->json('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->integer('reading_time')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_reported')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('article_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->json('content');
            $table->uuid('changed_by')->nullable();
            $table->text('change_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('article_tags', function (Blueprint $table) {
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->uuid('tag_id')->constrained('tags')->onDelete('cascade');
            $table->primary(['article_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tags');
        Schema::dropIfExists('article_revisions');
        Schema::dropIfExists('articles');
    }
};
