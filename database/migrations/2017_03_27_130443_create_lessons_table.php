<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('module_id')->default('0')->nullable();
            $table->enum('type', ['Lesson', 'Link'])->default('lesson');
            $table->text('title');
            $table->longText('description')->nullable();
            $table->string('link', 1024)->nullable();
            $table->integer('order');

            //$table->string('summary', 255)->nullable();
            //$table->integer('lesson_number')->nullable();
            //$table->text('lesson_materials')->nullable();

            $table->integer('status');
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
        Schema::drop('lessons');
    }
}
