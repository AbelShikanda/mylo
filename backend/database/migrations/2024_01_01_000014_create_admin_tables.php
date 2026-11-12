<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('administrator_id')->nullable()->constrained('administrators')->onDelete('set null');
            $table->string('action');
            $table->string('subject_type');
            $table->uuid('subject_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_config', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string');
            $table->timestamps();
        });

        Schema::create('editorial_selections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('article_id')->constrained('articles')->onDelete('cascade');
            $table->string('section');
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('moderation_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('moderator_id')->nullable()->constrained('administrators')->onDelete('set null');
            $table->string('subject_type');
            $table->uuid('subject_id')->nullable();
            $table->string('action');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_records');
        Schema::dropIfExists('editorial_selections');
        Schema::dropIfExists('platform_config');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('audit_logs');
    }
};
