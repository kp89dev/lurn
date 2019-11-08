<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->text('title');
            $table->text('description')->nullable()->default(null);
            $table->string('link', 1024)->nullable();
            $table->unsignedSmallInteger('order');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('hidden')->default(0);
            $table->enum('type', ['Module', 'Link']);

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
        Schema::drop('modules');
    }
}
