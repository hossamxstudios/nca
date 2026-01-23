<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('national_id', 14)->nullable()->unique();
            $table->string('client_code', 50)->nullable()->unique();
            $table->json('files_code')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('national_id');
            $table->index('client_code');
            $table->index('mobile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
