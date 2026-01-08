<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Isi dulu semua NULL dengan string kosong
        DB::table('biodata')->whereNull('posisi')->update(['posisi' => '']);
        DB::table('biodata')->whereNull('posisi_daftar')->update(['posisi_daftar' => '']);
        DB::table('biodata')->whereNull('alamat_prov')->update(['alamat_prov' => '']);
        DB::table('biodata')->whereNull('alamat_kab')->update(['alamat_kab' => '']);
        DB::table('biodata')->whereNull('tempat_tanggal_lahir')->update(['tempat_tanggal_lahir' => '']);
        DB::table('biodata')->whereNull('jenis_kelamin')->update(['jenis_kelamin' => '']);
        DB::table('biodata')->whereNull('pendidikan')->update(['pendidikan' => '']);
        DB::table('biodata')->whereNull('pekerjaan')->update(['pekerjaan' => '']);
        
        Schema::table('biodata', function (Blueprint $table) {
            $table->string('posisi',100)->default('')->change();
            $table->string('posisi_daftar',100)->default('')->change();
            $table->string('alamat_prov',100)->default('')->change();
            $table->string('alamat_kab',100)->default('')->change();
            $table->string('tempat_tanggal_lahir',150)->default('')->change();
            $table->string('jenis_kelamin',20)->default('')->change();
            $table->string('pendidikan',100)->default('')->change();
            $table->string('pekerjaan',100)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
