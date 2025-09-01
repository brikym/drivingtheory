<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Změnit enum hodnoty pro status
        DB::statement("ALTER TABLE tests MODIFY COLUMN status ENUM('in_progress', 'completed', 'expired', 'cancelled') DEFAULT 'in_progress'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Vrátit původní enum hodnoty
        DB::statement("ALTER TABLE tests MODIFY COLUMN status ENUM('in_progress', 'completed', 'expired') DEFAULT 'in_progress'");
    }
};
