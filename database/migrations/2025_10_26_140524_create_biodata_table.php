<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_biodata_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('biodata', function (Blueprint $table) {
            $table->string('id_sobat')->primary(); // Primary key sebagai string
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('no_telepon');
            $table->string('username_sobat')->unique();
            $table->string('foto_profil')->nullable();
            $table->string('pekerjaan');
            $table->string('instansi');
            $table->string('pendidikan_terakhir');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('biodata');
    }
};