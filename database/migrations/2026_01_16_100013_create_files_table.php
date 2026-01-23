<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('land_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lane_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rack_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->string('original_name')->nullable();
            $table->integer('page_number')->nullable();
            $table->integer('pages_count')->default(1);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('client_id');
            $table->index('land_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
