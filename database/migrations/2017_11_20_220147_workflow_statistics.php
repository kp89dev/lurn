<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkflowStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_statistics', function(Blueprint $table){
            $table->increments('id');
            $table->integer('workflow_id');
            $table->integer('step');
            $table->string('period');
            $table->integer('send');
            $table->integer('delivery');
            $table->integer('bounce');
            $table->integer('open');
            $table->integer('click');
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
        Schema::dropIfExists('workflow_statistics');
    }
}
