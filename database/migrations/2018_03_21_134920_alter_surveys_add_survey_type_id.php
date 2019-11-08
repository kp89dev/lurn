<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSurveysAddSurveyTypeId extends Migration
{
    protected $columns = [
        'survey_type_id',
        'survey_question_ordering_id',
        'survey_trigger_type_id'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasColumns('surveys', $this->columns)) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->unsignedInteger('survey_type_id');
                $table->unsignedInteger('survey_question_ordering_id');
                $table->unsignedInteger('survey_trigger_type_id');

                $table->foreign('survey_type_id')->references('id')->on('survey_types');
                $table->foreign('survey_question_ordering_id')->references('id')->on('survey_question_orderings');
                $table->foreign('survey_trigger_type_id')->references('id')->on('survey_trigger_types');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('surveys', $this->columns)) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropColumn(['survey_type_id']);
                $table->dropColumn(['survey_question_ordering_id']);
                $table->dropColumn(['survey_trigger_type_id']);
            });
        }
    }
}
