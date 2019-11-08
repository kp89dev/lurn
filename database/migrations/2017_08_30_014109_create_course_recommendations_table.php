<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('recommended_course_id')->unsigned();
            $table->integer('order')->unsigned();
            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('recommended_course_id')->references('id')->on('courses');
            $table->unique(array('course_id','recommended_course_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_recommendations');
    }
}
