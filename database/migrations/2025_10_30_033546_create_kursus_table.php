<?php
// database/migrations/2024_03_15_000001_create_kursus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKursusTable extends Migration
{
    public function up()
    {
        Schema::create('kursus', function (Blueprint $table) {
            $table->id();
            $table->string('judul_kursus');
            $table->text('deskripsi_kursus');
            $table->string('penerbit');
            $table->string('tingkat_kesulitan')->default('pemula'); // pemula, menengah, lanjutan
            $table->string('gambar_kursus')->nullable();
            $table->integer('durasi_jam')->default(0);
            $table->enum('status', ['draft', 'aktif', 'nonaktif'])->default('draft');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('output_pelatihan')->nullable();
            $table->text('persyaratan')->nullable();
            $table->text('fasilitas')->nullable();
            $table->integer('kuota_peserta')->nullable();
            $table->integer('peserta_terdaftar')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kursus');
    }
}