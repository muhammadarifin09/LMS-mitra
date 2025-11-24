<?php
// database/migrations/xxxx_xx_xx_add_attendance_required_to_materials_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceRequiredToMaterialsTable extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->boolean('attendance_required')->default(false)->after('video_url');
            $table->enum('material_type', ['theory', 'video', 'practice', 'quiz'])->default('theory')->after('attendance_required');
            $table->text('learning_objectives')->nullable()->after('material_type');
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['attendance_required', 'material_type', 'learning_objectives']);
        });
    }
}