<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lands', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->after('area_id')->constrained()->nullOnDelete();
            $table->foreignId('lane_id')->nullable()->after('room_id')->constrained()->nullOnDelete();
            $table->foreignId('stand_id')->nullable()->after('lane_id')->constrained()->nullOnDelete();
            $table->foreignId('rack_id')->nullable()->after('stand_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lands', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['lane_id']);
            $table->dropForeign(['stand_id']);
            $table->dropForeign(['rack_id']);
            $table->dropColumn(['room_id', 'lane_id', 'stand_id', 'rack_id']);
        });
    }
};
