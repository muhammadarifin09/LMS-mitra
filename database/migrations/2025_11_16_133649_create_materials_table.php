<?php
// database/migrations/2024_..._create_materials_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('kursus'); // ← PERBAIKAN DI SINI
            $table->string('title');
            $table->integer('order');
            $table->enum('type', ['pre_test', 'material', 'post_test', 'recap']);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};