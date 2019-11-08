<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmlIdMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cml_id_map', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('old_id')->nullable();
            $table->unsignedInteger('new_id');
            $table->string('type');
            $table->string('connection');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cml_id_map');
    }
}
