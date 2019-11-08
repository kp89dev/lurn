<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function(Blueprint $table){
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->string('title');
            $table->string('image');
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->text('style')->nullable();
            $table->longtext('body');
            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
