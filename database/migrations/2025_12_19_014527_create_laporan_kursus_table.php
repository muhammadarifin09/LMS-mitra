<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan_kursus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')
                ->constrained('kursus')
                ->cascadeOnDelete();

            $table->string('periode', 7); // contoh: 2025-01
            $table->integer('total_peserta');
            $table->integer('peserta_selesai');
            $table->decimal('rata_rata_progress', 5, 2);
            $table->decimal('rata_rata_nilai', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kursus');
    }
};

