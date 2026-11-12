<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('writer_id')->constrained('writers')->onDelete('cascade');
            $table->uuid('product_category_id')->nullable()->constrained('product_categories');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('author');
            $table->string('cover_image')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('product_type')->default('ebook');
            $table->boolean('is_available')->default(true);
            $table->string('preview_file')->nullable();
            $table->string('digital_file')->nullable();
            $table->integer('inventory_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->decimal('total', 10, 2);
            $table->string('currency')->default('KES');
            $table->json('shipping_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id')->constrained('orders')->onDelete('cascade');
            $table->uuid('book_id')->constrained('books')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('digital_access', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->uuid('reader_id')->constrained('readers')->onDelete('cascade');
            $table->uuid('book_id')->constrained('books')->onDelete('cascade');
            $table->string('access_token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_access');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('books');
    }
};
