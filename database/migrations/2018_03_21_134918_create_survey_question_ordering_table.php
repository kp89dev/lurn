<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Seeders\SurveyQuestionOrderingTableSeeder;

class CreateSurveyQuestionOrderingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_question_orderings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('display_name');
            $table->string('description');
            $table->timestamps();
        });

        app(SurveyQuestionOrderingTableSeeder::class)->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_question_orderings');
    }
}
