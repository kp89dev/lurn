<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveySurveyCustomCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_survey_custom_code', function (Blueprint $table) {
            $table->unsignedInteger('survey_id');
            $table->unsignedInteger('survey_custom_code_id');
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('survey_custom_code_id')->references('id')->on('survey_custom_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_survey_custom_code');
    }
}
