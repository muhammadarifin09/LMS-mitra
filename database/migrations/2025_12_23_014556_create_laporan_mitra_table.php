<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laporan_mitra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID dari tabel users (M_User)
            $table->string('id_sobat'); // ID Sobat dari tabel biodata
            $table->string('periode', 7); // Format: YYYY-MM (misal: 2024-03)
            
            // Statistik utama
            $table->integer('total_kursus_diikuti')->default(0);
            $table->integer('kursus_selesai')->default(0);
            $table->decimal('rata_rata_progress', 5, 2)->default(0); // 100.00%
            $table->decimal('rata_rata_nilai', 5, 2)->nullable(); // 100.00
            
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes untuk performa
            $table->index('user_id');
            $table->index('id_sobat');
            $table->index('periode');
            $table->index(['user_id', 'periode']); // Composite index
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan_mitra');
    }
};