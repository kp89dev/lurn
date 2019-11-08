<?php
/**
 * Date: 3/21/18
 * Time: 9:44 AM
 */

namespace App\Engines\Survey\Traits;

use App\Models\Survey;
use App\Models\SurveyQuestion;

trait SurveyPresenter
{
    /**
     * @param Survey $survey
     * @return string
     */
    protected function presentSurvey(Survey $survey)
    {
        return 'test code';
    }

    /**
     * @param SurveyQuestion $surveyQuestion
     * @return string
     */
    protected function presentTimedQuestion(SurveyQuestion $surveyQuestion)
    {
        return 'test code';
    }

    /**
     * @param SurveyQuestion $surveyQuestion
     * @return string
     */
    protected function presentCombinationQuestion(SurveyQuestion $surveyQuestion)
    {
        return 'test code';
    }

    /**
     * @param SurveyQuestion $surveyQuestion
     * @return string
     */
    protected function presentEventQuestion(SurveyQuestion $surveyQuestion)
    {
        return 'test code';
    }

    /**
     * @param SurveyQuestion $surveyQuestion
     * @return string
     */
    protected function presentStandaloneQuestion(SurveyQuestion $surveyQuestion)
    {
        return 'test code';
    }
}
