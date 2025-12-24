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
    public function up(): void
    {
        Schema::table('material_progress', function (Blueprint $table) {
            // Ubah dari ENUM ke VARCHAR atau perbaiki ENUM
            DB::statement("ALTER TABLE material_progress 
                MODIFY COLUMN video_status 
                ENUM('pending', 'in_progress', 'completed') 
                NOT NULL DEFAULT 'pending'");
            
            // Atau jika ingin VARCHAR:
            // $table->string('video_status', 20)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_progress', function (Blueprint $table) {
            DB::statement("ALTER TABLE material_progress 
                MODIFY COLUMN video_status 
                ENUM('pending', 'completed') 
                NOT NULL DEFAULT 'pending'");
        });
    }
};