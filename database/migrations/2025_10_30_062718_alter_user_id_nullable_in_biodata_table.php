<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('biodata', function (Blueprint $table) {
            // Ubah kolom user_id menjadi nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('biodata', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};