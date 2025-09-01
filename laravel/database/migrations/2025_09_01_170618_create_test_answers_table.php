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
        Schema::create('test_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('test_id')->index('test_answers_test_id_foreign');
            $table->unsignedBigInteger('question_id')->index('test_answers_question_id_foreign');
            $table->unsignedBigInteger('selected_answer_id')->index('test_answers_selected_answer_id_foreign');
            $table->boolean('is_correct');
            $table->unsignedTinyInteger('points_earned');
            $table->unsignedTinyInteger('points_possible');
            $table->timestamp('answered_at');
            $table->timestamps();
            
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('selected_answer_id')->references('id')->on('answers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_answers');
    }
};
