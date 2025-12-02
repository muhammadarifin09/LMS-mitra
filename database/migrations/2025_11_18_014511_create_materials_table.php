<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            // Kolom dasar dari migrasi pertama
            $table->id();
            $table->foreignId('course_id')->constrained('kursus')->onDelete('cascade');
            $table->string('title');
            $table->integer('order');
            $table->enum('type', ['pre_test', 'material', 'post_test', 'recap'])->default('material');
            $table->text('description')->nullable();
            
            // Kolom dari berbagai migrasi tambahan - HAPUS SEMUA 'AFTER'
            $table->integer('duration')->nullable();
            $table->json('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('duration_video')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Kolom untuk kehadiran dan tipe materi
            $table->boolean('attendance_required')->default(false);
            $table->string('material_type', 255)->nullable();
            $table->json('learning_objectives')->nullable();
            
            // Kolom untuk pretest
            $table->json('soal_pretest')->nullable();
            $table->integer('durasi_pretest')->nullable();
            $table->boolean('is_pretest')->default(false);
            
            // Kolom untuk posttest
            $table->json('soal_posttest')->nullable();
            $table->integer('durasi_posttest')->nullable();
            $table->boolean('is_posttest')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['course_id', 'order']);
            $table->index(['course_id', 'is_active']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};