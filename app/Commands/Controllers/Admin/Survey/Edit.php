<?php
/**
 * Date: 3/21/18
 * Time: 11:52 AM
 */

namespace App\Commands\Controllers\Admin\Survey;

class Edit extends SurveyBase
{
    public function process()
    {
        return [
            'survey' => $this->survey,
            'action' => route('surveys.update', $this->survey->id),
            'method' => method_field('PUT'),
            'surveyTypes' => $this->surveyType->all()->pluck('display_name', 'id'),
            'surveyTriggerTypes' => $this->surveyTriggerType->all()->pluck('display_name', 'id'),
            'surveyQuestionOrderings' => $this->surveyQuestionOrdering->all()->pluck('display_name', 'id'),
        ];
    }
}