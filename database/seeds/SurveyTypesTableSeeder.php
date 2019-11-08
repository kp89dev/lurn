<?php

namespace Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class SurveyTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('survey-types') as $surveyTypes) {
            DB::table('survey_types')->insert(array_merge($surveyTypes, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}
