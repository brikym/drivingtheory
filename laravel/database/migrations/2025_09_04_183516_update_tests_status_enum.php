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
        // Add 'cancelled' to the status enum
        DB::statement("ALTER TABLE tests MODIFY COLUMN status ENUM('in_progress', 'completed', 'expired', 'cancelled') NOT NULL DEFAULT 'in_progress'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'cancelled' from the status enum
        DB::statement("ALTER TABLE tests MODIFY COLUMN status ENUM('in_progress', 'completed', 'expired') NOT NULL DEFAULT 'in_progress'");
    }
};
