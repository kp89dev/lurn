<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCourseUpsell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_upsells', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_infusionsoft_id');
            $table->integer('succeeds_course_id');
            $table->longText('html');
            $table->text('css');
            $table->boolean('status')->defaults(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_upsells');
    }
}
