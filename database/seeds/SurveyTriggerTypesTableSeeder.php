<?php

namespace Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class SurveyTriggerTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('survey-trigger-types') as $surveyTriggerTypes) {
            DB::table('survey_trigger_types')->insert(array_merge($surveyTriggerTypes, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}
