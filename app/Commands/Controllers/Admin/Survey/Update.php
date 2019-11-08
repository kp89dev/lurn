<?php
/**
 * Date: 3/21/18
 * Time: 11:52 AM
 */

namespace App\Commands\Controllers\Admin\Survey;

use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Update extends SurveyBase
{
    public function process()
    {
        $entries = json_decode($this->httpRequest->question_data);
        $data = $this->fillData();

        $survey = $this->survey->fill($data);
        $survey->save();

        $this->updateSurveyQuestionsAndAnswers($entries, $survey);
        $this->deleteDeletableSurveyQuestionsAndAnswers($entries);
    }

    /**
     * @return array
     */
    public function fillData()
    {
        /** @var Collection $requestData */
        $requestData = collect($this->httpRequest->only(
            'title',
            'description',
            'survey_type_id',
            'survey_trigger_type_id',
            'survey_question_ordering_id',
            'start_date',
            'end_date',
            'require_login',
            'enabled'
        ));

        $data = $requestData->toArray();

        $data['start_date'] = Carbon::parse($data['start_date'] . ' 00:00:00');
        $data['end_date'] = Carbon::parse($data['end_date'] . ' 23:59:59');
        $data['enabled'] = $requestData->get('enabled') ?: false;
        $data['require_login'] = $requestData->get('require_login') ?: false;
        return $data;
    }

    /**
     * @param $entries
     * @param $survey
     */
    public function updateSurveyQuestionsAndAnswers($entries, Survey $survey)
    {
        foreach ($entries->questions as $question) {
            $que = $this->handleQuestionsUpdate($survey, $question);

            $this->handleAnswerUpdate($survey, $question, $que);
        }
    }

    /**
     * @param $entries
     */
    public function deleteDeletableSurveyQuestionsAndAnswers($entries)
    {
        $this->surveyAnswer->destroy($entries->delete->answers);
        $this->surveyQuestion->destroy($entries->delete->questions);
    }

    /**
     * @param Survey $survey
     * @param $question
     * @return $this|\Illuminate\Database\Eloquent\Model|mixed|static
     */
    public function handleQuestionsUpdate(Survey $survey, $question)
    {
        if (empty($question->id)) {
            $que = $this->createSurveyQuestion($survey, $question);

            $survey->questions()->save($que);
        } else {
            $que = $this->updateSurveyQuestion($question, $survey);
        }
        return $que;
    }

    /**
     * @param Survey $survey
     * @param $question
     * @param $que
     */
    public function handleAnswerUpdate(Survey $survey, $question, $que)
    {
        foreach ($question->answers as $answer) {
            if (empty($answer->id)) {
                $this->createSurveyAnswer($survey, $que, $answer);
            } else {
                $this->updateSurveyAnswer($answer, $survey, $que);
            }
        }
    }
}
