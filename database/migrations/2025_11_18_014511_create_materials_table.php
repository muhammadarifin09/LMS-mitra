<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('kursus')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['pre_test', 'material', 'post_test', 'recap'])->default('material');
            $table->integer('order');
            $table->string('material_type', 255)->nullable()->comment('theory, video, quiz');
            $table->text('description')->nullable();
            
            // Kolom konten
            $table->integer('duration')->nullable();
            $table->boolean('auto_duration')->default(true);
            $table->json('file_path')->nullable();
            $table->string('video_url')->nullable();
            
            // Video control columns
            $table->enum('video_type', ['youtube', 'hosted', 'local'])->default('youtube')
                  ->comment('youtube: YouTube video, hosted: Google Drive video, local: Local server video');
            $table->text('video_file')->nullable()->comment('JSON for Google Drive file info (for hosted type)');
            $table->boolean('allow_skip')->default(false);
            $table->json('player_config')->nullable();
            $table->boolean('has_video_questions')->default(false);
            $table->boolean('require_video_completion')->default(false);
            
            // Video questions stats
            $table->integer('question_count')->default(0);
            $table->integer('total_video_points')->default(0);
            
            // Kolom status
            $table->boolean('is_active')->default(true);
            $table->boolean('attendance_required')->default(false);
            
            // Kolom metadata
            $table->json('learning_objectives')->nullable();
            
            // Kolom untuk pretest
            $table->json('soal_pretest')->nullable();
            $table->integer('durasi_pretest')->nullable();
            $table->boolean('is_pretest')->default(false);
            
            // Kolom untuk posttest
            $table->json('soal_posttest')->nullable();
            $table->integer('durasi_posttest')->nullable();
            $table->boolean('is_posttest')->default(false);
            
            // Tracking columns
            $table->integer('total_views')->default(0);
            $table->integer('total_completions')->default(0);
            $table->decimal('avg_completion_time', 8, 2)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'order']);
            $table->index(['course_id', 'is_active']);
            $table->index(['course_id', 'type']);
            $table->index(['material_type']);
            $table->index(['video_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};