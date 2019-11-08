<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('course_container_id')->index();
            //$table->unsignedInteger('user_id');
            $table->text('title');
            $table->text('description');
            //$table->text('image')->nullable();
            $table->unsignedInteger('status');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() 
    {
        Schema::disableForeignKeyConstraints();
        Schema::drop('courses');
    }
}
