<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('imports', function (Blueprint $table) {
                $table->string('type')->default('full')->change();
            });
        } else {
            DB::statement("ALTER TABLE imports MODIFY COLUMN type ENUM('full', 'clients', 'lands', 'geographic', 'archive') DEFAULT 'full'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('imports', function (Blueprint $table) {
                $table->string('type')->default('full')->change();
            });
        } else {
            DB::statement("ALTER TABLE imports MODIFY COLUMN type ENUM('full', 'clients', 'lands', 'geographic') DEFAULT 'full'");
        }
    }
};
