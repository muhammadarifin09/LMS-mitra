<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->enum('status', ['in_progress', 'completed', 'dropped'])->default('in_progress');
            $table->integer('progress_percentage')->default(0);
            $table->integer('completed_activities')->default(0);
            $table->integer('total_activities')->default(0);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Unique constraint agar user tidak bisa enroll kursus yang sama dua kali
            $table->unique(['user_id', 'kursus_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};