<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('material_progress', function (Blueprint $table) {
            $table->decimal('pretest_score', 5, 2)->nullable();
            $table->decimal('posttest_score', 5, 2)->nullable();
            $table->timestamp('pretest_completed_at')->nullable();
            $table->timestamp('posttest_completed_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->boolean('is_completed')->default(false);
        });
    }

    public function down()
    {
        Schema::table('material_progress', function (Blueprint $table) {
            $table->dropColumn([
                'pretest_score',
                'posttest_score', 
                'pretest_completed_at',
                'posttest_completed_at',
                'attempts',
                'is_completed'
            ]);
        });
    }
};