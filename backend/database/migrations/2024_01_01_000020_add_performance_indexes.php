<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Article indexes for performance
        Schema::table('articles', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'articles_status_published_idx');
            $table->index(['category_id', 'status'], 'articles_category_status_idx');
            $table->index('slug', 'articles_slug_idx');
            $table->index('writer_id', 'articles_writer_idx');
            $table->index('is_featured', 'articles_featured_idx');
        });

        // Category indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['parent_id', 'is_active'], 'categories_parent_active_idx');
            $table->index('slug', 'categories_slug_idx');
            $table->index('is_featured', 'categories_featured_idx');
        });

        // Tag indexes
        Schema::table('tags', function (Blueprint $table) {
            $table->index('slug', 'tags_slug_idx');
        });

        // Comment indexes
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['article_id', 'is_approved'], 'comments_article_approved_idx');
            $table->index('parent_id', 'comments_parent_idx');
            $table->index('reader_id', 'comments_reader_idx');
        });

        // Like indexes
        Schema::table('article_likes', function (Blueprint $table) {
            $table->index(['article_id', 'reader_id'], 'article_likes_article_reader_idx');
        });

        // Bookmark indexes
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->index(['reader_id', 'article_id'], 'bookmarks_reader_article_idx');
        });

        // Reading history indexes
        Schema::table('reading_history', function (Blueprint $table) {
            $table->index(['reader_id', 'article_id'], 'reading_history_reader_article_idx');
            $table->index('read_at', 'reading_history_read_at_idx');
        });

        // Transaction indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['writer_id', 'status'], 'transactions_writer_status_idx');
            $table->index(['customer_type', 'customer_id'], 'transactions_customer_idx');
            $table->index('idempotency_key', 'transactions_idempotency_idx');
            $table->index('reference', 'transactions_reference_idx');
            $table->index('created_at', 'transactions_created_idx');
        });

        // Order indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['reader_id', 'status'], 'orders_reader_status_idx');
            $table->index('created_at', 'orders_created_idx');
        });

        // Book indexes
        Schema::table('books', function (Blueprint $table) {
            $table->index(['product_type', 'is_available'], 'books_type_available_idx');
            $table->index(['product_category_id', 'is_available'], 'books_category_available_idx');
            $table->index('writer_id', 'books_writer_idx');
            $table->index('slug', 'books_slug_idx');
        });

        // Contribution indexes
        Schema::table('contributions', function (Blueprint $table) {
            $table->index(['writer_id', 'status'], 'contributions_writer_status_idx');
            $table->index(['reader_id', 'status'], 'contributions_reader_status_idx');
            $table->index('transaction_reference', 'contributions_reference_idx');
        });

        // Payout request indexes
        Schema::table('payout_requests', function (Blueprint $table) {
            $table->index(['writer_id', 'status'], 'payout_requests_writer_status_idx');
        });

        // Audit log indexes
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['administrator_id', 'created_at'], 'audit_logs_admin_created_idx');
            $table->index(['subject_type', 'subject_id'], 'audit_logs_subject_idx');
        });

        // Notification indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['recipient_type', 'recipient_id', 'read_at'], 'notifications_recipient_read_idx');
        });

        // Report indexes
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['reportable_type', 'reportable_id', 'status'], 'reports_subject_status_idx');
            $table->index('reporter_id', 'reports_reporter_idx');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_status_published_idx');
            $table->dropIndex('articles_category_status_idx');
            $table->dropIndex('articles_slug_idx');
            $table->dropIndex('articles_writer_idx');
            $table->dropIndex('articles_featured_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_parent_active_idx');
            $table->dropIndex('categories_slug_idx');
            $table->dropIndex('categories_featured_idx');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex('tags_slug_idx');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_article_approved_idx');
            $table->dropIndex('comments_parent_idx');
            $table->dropIndex('comments_reader_idx');
        });

        Schema::table('article_likes', function (Blueprint $table) {
            $table->dropIndex('article_likes_article_reader_idx');
        });

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropIndex('bookmarks_reader_article_idx');
        });

        Schema::table('reading_history', function (Blueprint $table) {
            $table->dropIndex('reading_history_reader_article_idx');
            $table->dropIndex('reading_history_read_at_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_writer_status_idx');
            $table->dropIndex('transactions_customer_idx');
            $table->dropIndex('transactions_idempotency_idx');
            $table->dropIndex('transactions_reference_idx');
            $table->dropIndex('transactions_created_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_reader_status_idx');
            $table->dropIndex('orders_created_idx');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('books_type_available_idx');
            $table->dropIndex('books_category_available_idx');
            $table->dropIndex('books_writer_idx');
            $table->dropIndex('books_slug_idx');
        });

        Schema::table('contributions', function (Blueprint $table) {
            $table->dropIndex('contributions_writer_status_idx');
            $table->dropIndex('contributions_reader_status_idx');
            $table->dropIndex('contributions_reference_idx');
        });

        Schema::table('payout_requests', function (Blueprint $table) {
            $table->dropIndex('payout_requests_writer_status_idx');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_admin_created_idx');
            $table->dropIndex('audit_logs_subject_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_recipient_read_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_subject_status_idx');
            $table->dropIndex('reports_reporter_idx');
        });
    }
};
