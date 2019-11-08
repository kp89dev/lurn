<?php
/**
 * Date: 3/21/18
 * Time: 11:52 AM
 */

namespace App\Commands\Controllers\Admin\Survey;

use App\Models\Survey;

class Create extends SurveyBase
{
    public function process()
    {
        return [
            'survey' => new Survey(),
            'action' => route('surveys.store'),
            'method' => '',
            'surveyTypes' => $this->surveyType->all()->pluck('display_name', 'id'),
            'surveyTriggerTypes' => $this->surveyTriggerType->all()->pluck('display_name', 'id'),
            'surveyQuestionOrderings' => $this->surveyQuestionOrdering->all()->pluck('display_name', 'id'),
        ];
    }
}