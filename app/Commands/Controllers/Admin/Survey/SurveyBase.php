<?php
/**
 * Date: 3/21/18
 * Time: 11:52 AM
 */

namespace App\Commands\Controllers\Admin\Survey;

use App\Commands\Base;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOrdering;
use App\Models\SurveyTriggerType;
use App\Models\SurveyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class SurveyBase extends Base
{
    /** @var Survey */
    protected $survey;

    /** @var SurveyType */
    protected $surveyType;

    /** @var SurveyTriggerType */
    protected $surveyTriggerType;

    /** @var SurveyQuestionOrdering */
    protected $surveyQuestionOrdering;

    /** @var Request */
    protected $httpRequest;

    /** @var SurveyQuestion */
    protected $surveyQuestion;

    /** @var SurveyAnswer */
    protected $surveyAnswer;

    /**
     * SurveyBase constructor.
     * @param SurveyType $surveyType
     * @param SurveyTriggerType $surveyTriggerType
     * @param SurveyQuestionOrdering $surveyQuestionOrdering
     * @param Request $httpRequest
     * @param SurveyQuestion $surveyQuestion
     * @param SurveyAnswer $surveyAnswer
     */
    public function __construct(
        SurveyType $surveyType,
        SurveyTriggerType $surveyTriggerType,
        SurveyQuestionOrdering $surveyQuestionOrdering,
        Request $httpRequest,
        SurveyQuestion $surveyQuestion,
        SurveyAnswer $surveyAnswer
    ) {
        $this->surveyType = $surveyType;
        $this->surveyTriggerType = $surveyTriggerType;
        $this->surveyQuestionOrdering = $surveyQuestionOrdering;
        $this->httpRequest = $httpRequest;
        $this->surveyQuestion = $surveyQuestion;
        $this->surveyAnswer = $surveyAnswer;
    }

    /**
     * @param $survey
     * @param $question
     * @return $this|Model
     */
    public function createSurveyQuestion($survey, $question)
    {
        $que = $this->surveyQuestion->create([
            'survey_id' => $survey->id,
            'title' => $question->title,
            'answer_choice' => $question->answer_choice,
            'order' => $question->order,
            'enabled' => $question->enabled,
        ]);
        return $que;
    }

    /**
     * @param $question
     * @param $survey
     * @return mixed|static
     */
    public function updateSurveyQuestion($question, $survey)
    {
        $que = $this->surveyQuestion->find($question->id);

        $que->survey_id = $survey->id;
        $que->title = $question->title;
        $que->answer_choice = $question->answer_choice;
        $que->order = $question->order;
        $que->enabled = $question->enabled;

        $que->save();
        return $que;
    }

    /**
     * @param $survey
     * @param $que
     * @param $answer
     * @return $this|Model
     */
    public function createSurveyAnswer($survey, $que, $answer)
    {
        $ans = $this->surveyAnswer->create([
            'survey_id' => $survey->id,
            'question_id' => $que->id,
            'title' => $answer->title,
            'order' => $answer->order,
            'enabled' => $answer->enabled
        ]);
        return $ans;
    }

    /**
     * @param $answer
     * @param $survey
     * @param $que
     */
    public function updateSurveyAnswer($answer, $survey, $que)
    {
        $ans = $this->surveyAnswer->find($answer->id);

        $ans->survey_id = $survey->id;
        $ans->question_id = $que->id;
        $ans->title = $answer->title;
        $ans->order = $answer->order;
        $ans->enabled = $answer->enabled;

        $ans->save();
    }

    /**
     * @param Survey $survey
     * @return $this
     */
    public function setSurvey(Survey $survey)
    {
        $this->survey = $survey;
        return $this;
    }
}
