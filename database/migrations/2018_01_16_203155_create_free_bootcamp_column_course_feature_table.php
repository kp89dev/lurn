<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreeBootcampColumnCourseFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_features', function (Blueprint $table) {
            $table->boolean('free_bootcamp')->after('order');
            $table->dropForeign(['course_id']);
            $table->dropIndex('course_features_course_id_order_unique');

            $table->index(['course_id','order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_features', function (Blueprint $table) {
            $table->dropColumn('free_bootcamp');
        });
    }
}
