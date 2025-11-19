<?php
// database/migrations/2024_..._create_material_progress_table.php

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
            $table->enum('video_status', ['pending', 'watching', 'completed'])->default('pending');
            $table->json('quiz_answers')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Satu user hanya bisa memiliki satu progress per material
            $table->unique(['user_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_progress');
    }
};