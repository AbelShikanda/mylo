<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('admin_role_permissions', function (Blueprint $table) {
            $table->foreignUuid('admin_role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->foreignUuid('admin_permission_id')->constrained('admin_permissions')->onDelete('cascade');
            $table->primary(['admin_role_id', 'admin_permission_id']);
            $table->timestamps();
        });

        Schema::create('admin_user_roles', function (Blueprint $table) {
            $table->foreignUuid('administrator_id')->constrained('administrators')->onDelete('cascade');
            $table->foreignUuid('admin_role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->primary(['administrator_id', 'admin_role_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_user_roles');
        Schema::dropIfExists('admin_role_permissions');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('admin_roles');
    }
};
