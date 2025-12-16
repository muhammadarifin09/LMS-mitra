<?php

// database/migrations/xxxx_xx_xx_create_user_video_progress_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->integer('last_watched_second')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_points_earned')->default(0);
            $table->json('watch_history')->nullable();
            $table->json('answered_questions')->nullable()->comment('Array question_id yang sudah dijawab');
            $table->timestamps();
            
            $table->unique(['user_id', 'material_id']);
            $table->index(['material_id', 'is_completed']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_video_progress');
    }
};