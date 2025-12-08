<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('material_progress', function (Blueprint $table) {
            // Tambahkan kolom untuk tracking download per file
            $table->json('downloaded_files')->nullable()->after('video_status');
            $table->integer('total_files')->default(0)->after('downloaded_files');
            $table->boolean('all_files_downloaded')->default(false)->after('total_files');
        });
    }

    public function down()
    {
        Schema::table('material_progress', function (Blueprint $table) {
            $table->dropColumn(['downloaded_files', 'total_files', 'all_files_downloaded']);
        });
    }
};