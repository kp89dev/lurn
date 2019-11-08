<?php

namespace Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class SurveyQuestionOrderingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('survey-question-ordering') as $surveyQuestionOrder) {
            DB::table('survey_question_orderings')->insert(array_merge($surveyQuestionOrder, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}
