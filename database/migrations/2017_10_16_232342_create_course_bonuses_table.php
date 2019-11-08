<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('bonus_course_id')->unsigned();
            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('bonus_course_id')->references('id')->on('courses');
            $table->unique(array('course_id','bonus_course_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_bonuses');
    }
}
