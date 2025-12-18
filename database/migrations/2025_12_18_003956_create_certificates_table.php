<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->string('download_url')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'kursus_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};