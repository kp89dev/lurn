<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Seeders\SurveyTriggerTypesTableSeeder;

class CreateSurveyTriggerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_trigger_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('display_name');
            $table->string('description');
            $table->timestamps();
        });

        app(SurveyTriggerTypesTableSeeder::class)->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_trigger_types');
    }
}
