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
        // First, change column to VARCHAR to allow data updates
        DB::statement("ALTER TABLE tests MODIFY COLUMN vehicle_type VARCHAR(10) NOT NULL");
        
        // Update existing data to map old values to new ones
        DB::table('tests')->where('vehicle_type', 'automobil')->update(['vehicle_type' => 'B']);
        DB::table('tests')->where('vehicle_type', 'motocykl')->update(['vehicle_type' => 'A']);
        
        // Then update the column back to enum with new values
        DB::statement("ALTER TABLE tests MODIFY COLUMN vehicle_type ENUM('A', 'B', 'C', 'D', 'B+E', 'C+E', 'D+E') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, change column to VARCHAR to allow data updates
        DB::statement("ALTER TABLE tests MODIFY COLUMN vehicle_type VARCHAR(10) NOT NULL");
        
        // Update data back to original values
        DB::table('tests')->where('vehicle_type', 'A')->update(['vehicle_type' => 'motocykl']);
        DB::table('tests')->where('vehicle_type', 'B')->update(['vehicle_type' => 'automobil']);
        
        // Then revert back to original enum values
        DB::statement("ALTER TABLE tests MODIFY COLUMN vehicle_type ENUM('automobil', 'motocykl') NOT NULL");
    }
};
