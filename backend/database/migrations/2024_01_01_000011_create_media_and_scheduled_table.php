<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->boolean('is_reported')->default(false);
            $table->morphs('owner');
            $table->morphs('uploader');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('scheduled_publications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->timestamp('publish_at');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_publications');
        Schema::dropIfExists('media_assets');
    }
};
