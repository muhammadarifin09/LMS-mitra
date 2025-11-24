<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Untuk siapa notifikasi ini
            $table->string('type'); // jenis: user_baru, pendaftaran_baru, dll
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('related_id')->nullable(); // ID data terkait
            $table->string('related_type')->nullable(); // Tabel data terkait
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};