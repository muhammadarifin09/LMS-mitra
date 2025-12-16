<?php

// database/migrations/xxxx_xx_xx_create_video_questions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('time_in_seconds')->default(0)->comment('Waktu muncul (detik)');
            $table->text('question');
            $table->json('options')->comment('Array pilihan jawaban');
            $table->integer('correct_option')->default(0)->comment('Index jawaban benar (0-3)');
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
            $table->boolean('required_to_continue')->default(true);
            $table->timestamps();
            
            $table->index(['material_id', 'time_in_seconds']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_questions');
    }
};