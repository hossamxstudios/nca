<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->integer('from_page');
            $table->integer('to_page')->nullable();
            $table->timestamps();

            $table->index(['file_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_items');
    }
};
