<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('biodata', function (Blueprint $table) {

            // Data posisi
            $table->string('posisi', 100)->nullable()->after('username_sobat');
            $table->string('posisi_daftar', 100)->nullable()->after('posisi');

            // Data wilayah
            $table->string('alamat_prov', 100)->nullable()->after('alamat');
            $table->string('alamat_kab', 100)->nullable()->after('alamat_prov');

            // Data personal
            $table->string('tempat_tanggal_lahir', 150)->nullable()->after('alamat_kab');
            $table->string('jenis_kelamin', 20)->nullable()->after('tempat_tanggal_lahir');
            $table->string('pendidikan', 100)->nullable()->after('jenis_kelamin');
            $table->string('pekerjaan', 100)->nullable()->after('pendidikan');
            $table->text('deskripsi_pekerjaan_lain')->nullable()->after('pekerjaan');
        });
    }

    public function down()
    {
        Schema::table('biodata', function (Blueprint $table) {
            $table->dropColumn([
                'posisi',
                'posisi_daftar',
                'alamat_prov',
                'alamat_kab',
                'tempat_tanggal_lahir',
                'jenis_kelamin',
                'pendidikan',
                'pekerjaan',
                'deskripsi_pekerjaan_lain',
            ]);
        });
    }
};
