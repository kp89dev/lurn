<?php
/**
 * Date: 3/21/18
 * Time: 11:52 AM
 */

namespace App\Commands\Controllers\Admin\Survey;

use App\Models\Survey;
use Carbon\Carbon;

class Store extends SurveyBase
{
    public function process()
    {
        $entries = json_decode($this->httpRequest->question_data);

        $data = $this->fillData();

        $survey = Survey::create($data);

        $this->addSurveyQuestionsAndAnswers($entries, $survey);
    }

    /**
     * @param $entries
     * @param $survey
     */
    public function addSurveyQuestionsAndAnswers($entries, Survey $survey)
    {
        foreach ($entries->questions as $question) {
            $que = $this->surveyQuestion->create([
                'survey_id' => $survey->id,
                'title' => $question->title,
                'answer_choice' => $question->answer_choice,
                'order' => $question->order,
                'enabled' => $question->enabled,
            ]);

            $survey->questions()->save($que);

            foreach ($question->answers as $answer) {
                $this->surveyAnswer->create([
                    'survey_id' => $survey->id,
                    'question_id' => $que->id,
                    'title' => $answer->title,
                    'order' => $answer->order,
                    'enabled' => $answer->enabled
                ]);
            }
        }
    }

    /**
     * @return array
     */
    public function fillData()
    {
        $data = $this->httpRequest->only(
            'title',
            'description',
            'survey_type_id',
            'survey_trigger_type_id',
            'survey_question_ordering_id',
            'start_date',
            'end_date',
            'require_login',
            'enabled'
        );

        $data['start_date'] = Carbon::parse($data['start_date'] . ' 00:00:00');
        $data['end_date'] = Carbon::parse($data['end_date'] . ' 23:59:59');
        $data['enabled'] = $data['enabled'] ?: false;
        $data['require_login'] = $data['require_login'] ?: false;
        return $data;
    }
}