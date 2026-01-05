<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->enum('attendance_status', ['pending', 'completed'])->default('pending');
            $table->enum('material_status', ['pending', 'downloaded', 'completed'])->default('pending');
            
            // Video status dengan ENUM yang sudah diperbaiki
            $table->enum('video_status', ['pending', 'in_progress', 'completed'])->default('pending');
            
            // Kolom untuk tracking download per file
            $table->json('downloaded_files')->nullable();
            $table->integer('total_files')->default(0);
            $table->boolean('all_files_downloaded')->default(false);
            
            $table->json('quiz_answers')->nullable();
            
            // Kolom untuk menyimpan hasil test
            $table->decimal('pretest_score', 5, 2)->nullable();
            $table->decimal('posttest_score', 5, 2)->nullable();
            $table->timestamp('pretest_completed_at')->nullable();
            $table->timestamp('posttest_completed_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->boolean('is_completed')->default(false);
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['user_id', 'material_id']);
            
            // Indexes
            $table->index(['user_id', 'is_completed']);
            $table->index(['material_id', 'is_completed']);
            $table->index(['user_id', 'material_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_progress');
    }
};