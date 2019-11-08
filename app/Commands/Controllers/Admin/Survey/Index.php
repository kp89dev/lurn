<?php
/**
 * Date: 3/21/18
 * Time: 11:52 AM
 */

namespace App\Commands\Controllers\Admin\Survey;

use App\Models\Survey;

class Index extends SurveyBase
{
    public function process()
    {
        return [
            'surveys' => Survey::orderBy('id', 'DESC')->simplePaginate(20),
        ];
    }
}