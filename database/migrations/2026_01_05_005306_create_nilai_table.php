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
        Schema::create('nilai', function (Blueprint $table) {
        $table->bigIncrements('id_nilai'); // ⬅️ primary key custom
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('kursus_id')->constrained('kursus')->cascadeOnDelete();
        $table->integer('nilai')->nullable(); // 0 - 100
        $table->enum('status', ['belum', 'lulus', 'tidak_lulus'])->default('belum');
        $table->timestamps();
        $table->unique(['user_id', 'kursus_id']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
