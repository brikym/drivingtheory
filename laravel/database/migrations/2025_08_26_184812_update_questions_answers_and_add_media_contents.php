<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {        
        Schema::table('questions', function (Blueprint $table) {
            $table->bigInteger('external_id')->nullable()->after('id');
            $table->integer('template_id')->nullable()->after('external_id');
            $table->integer('points_count')->nullable()->after('template_id');
            $table->dateTime('valid_from')->nullable()->after('points_count');
            $table->dateTime('valid_to')->nullable()->after('valid_from');
        });
     
        Schema::table('answers', function (Blueprint $table) {
            $table->bigInteger('external_id')->nullable()->after('id');            
        });

        // Nová tabulka media_contents
        Schema::create('media_contents', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('model_id'); // ID otázky nebo odpovědi
            $table->string('media_type')->nullable(); // image, video, audio, ...
            $table->string('media_url')->nullable();  // cesta k souboru            
            $table->timestamps();
            $table->index(['model_id']);
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'template_id', 'points_count', 'valid_from', 'valid_to']);
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn(['external_id']);
        });

        Schema::dropIfExists('media_contents');
    }
};
