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
        Schema::create('tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('tests_user_id_foreign');
            $table->enum('vehicle_type', ['automobil', 'motocykl']);
            $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('time_limit_minutes')->default(30);
            $table->unsignedTinyInteger('total_questions');
            $table->unsignedTinyInteger('total_points');
            $table->unsignedTinyInteger('earned_points')->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // např. 85.50
            $table->boolean('passed')->nullable();
            $table->boolean('time_expired')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
