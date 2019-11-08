<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveySurveyEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_survey_event', function (Blueprint $table) {
            $table->unsignedInteger('survey_id');
            $table->unsignedInteger('survey_event_id');
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('survey_event_id')->references('id')->on('survey_events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_survey_event');
    }
}
