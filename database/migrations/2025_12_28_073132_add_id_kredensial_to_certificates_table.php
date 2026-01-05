<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Kolom string dengan panjang tepat 8 karakter
            $table->string('id_kredensial', 8)
                  ->nullable() // sesuaikan: hapus ini jika wajib diisi
                  ->after('id') // atau 'nama_kolom_lain' - sesuaikan posisi
                  ->comment('Kode kredensial 8 karakter (huruf+angka)');
            
            // Optional: tambahkan index untuk pencarian yang cepat
            $table->index('id_kredensial');
            
            // Optional: jika kode harus unik
            // $table->unique('id_kredensial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Hapus index/unique terlebih dahulu
            $table->dropIndex(['id_kredensial']);
            // $table->dropUnique(['id_kredensial']);
            
            // Hapus kolom
            $table->dropColumn('id_kredensial');
        });
    }
};