<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateMaterialTypeColumnInMaterialsTable extends Migration
{
    public function up()
    {
        // Hapus kolom material_type yang lama
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('material_type');
        });

        // Tambahkan kolom material_type yang baru sebagai string
        Schema::table('materials', function (Blueprint $table) {
            $table->string('material_type', 255)->nullable()->after('type');
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('material_type');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->enum('material_type', ['theory', 'video', 'practice', 'quiz'])->nullable()->after('type');
        });
    }
}