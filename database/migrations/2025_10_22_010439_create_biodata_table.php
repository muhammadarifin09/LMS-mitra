<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biodata', function (Blueprint $table) {
            $table->string('id_sobat')->primary();
            $table->string('nama');
            $table->string('username_sobat')->unique(); // email
            $table->string('no_hp');
            $table->string('kecamatan');
            $table->string('desa');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biodata');
    }
};
