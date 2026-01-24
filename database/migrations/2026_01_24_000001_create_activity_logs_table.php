<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Action details
            $table->string('action_type', 50); // login, logout, view, create, update, delete, print, bulk_import, etc.
            $table->string('action_group', 50)->nullable(); // auth, clients, files, users, settings, etc.

            // Subject (what was affected) - polymorphic
            $table->string('subject_type')->nullable(); // App\Models\Client, App\Models\File, etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_name')->nullable(); // Human readable name of subject

            // Description
            $table->string('description'); // Human readable: "طبع 80 باركود لـ 5 عملاء"

            // Properties (JSON for flexible data storage)
            $table->json('properties')->nullable(); // old_values, new_values, affected_ids, etc.

            // Batch grouping (for bulk operations)
            $table->uuid('batch_id')->nullable();
            $table->integer('batch_count')->nullable();

            // Request info
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes for filtering
            $table->index('action_type');
            $table->index('action_group');
            $table->index(['subject_type', 'subject_id']);
            $table->index('batch_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
