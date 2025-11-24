<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToMaterialsTable extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            // ✅ TAMBAHKAN KOLOM YANG HILANG
            if (!Schema::hasColumn('materials', 'duration')) {
                $table->integer('duration')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('materials', 'file_path')) {
                $table->string('file_path')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('materials', 'video_url')) {
                $table->string('video_url')->nullable()->after('file_path');
            }
            
            if (!Schema::hasColumn('materials', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('video_url');
            }
            
            // Kolom untuk pretest
            if (!Schema::hasColumn('materials', 'soal_pretest')) {
                $table->json('soal_pretest')->nullable()->after('is_active');
            }
            
            if (!Schema::hasColumn('materials', 'durasi_pretest')) {
                $table->integer('durasi_pretest')->default(60)->after('soal_pretest');
            }
            
            if (!Schema::hasColumn('materials', 'passing_grade')) {
                $table->integer('passing_grade')->default(70)->after('durasi_pretest');
            }
            
            if (!Schema::hasColumn('materials', 'is_pretest')) {
                $table->boolean('is_pretest')->default(false)->after('passing_grade');
            }
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'duration',
                'file_path', 
                'video_url',
                'is_active',
                'soal_pretest',
                'durasi_pretest',
                'passing_grade',
                'is_pretest'
            ]);
        });
    }
}