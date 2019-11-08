<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('survey_id');
            $table->integer('user_id')->nullable();
            $table->string('user_ip', 16)->nullable();
            $table->boolean('answered')->boolean(0);
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
        Schema::dropIfExists('survey_views');
    }
}
