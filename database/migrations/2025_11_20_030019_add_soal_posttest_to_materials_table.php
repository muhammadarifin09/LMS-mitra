<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoalPosttestToMaterialsTable extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->json('soal_posttest')->nullable()->after('soal_pretest');
            $table->boolean('is_posttest')->default(false)->after('is_pretest');
            $table->integer('durasi_posttest')->nullable()->after('durasi_pretest');
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['soal_posttest', 'is_posttest', 'durasi_posttest']);
        });
    }
}