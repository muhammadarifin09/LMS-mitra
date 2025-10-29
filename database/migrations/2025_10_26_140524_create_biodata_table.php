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
            $table->string('kecamatan');
            $table->string('desa');
            $table->text('alamat');
            $table->string('username_sobat');
            $table->string('no_telepon');
            $table->string('foto_profil')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('biodata');
    }
};