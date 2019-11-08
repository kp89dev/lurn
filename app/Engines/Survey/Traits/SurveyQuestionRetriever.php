<?php
/**
 * Date: 3/22/18
 * Time: 3:33 PM
 */

namespace App\Engines\Survey\Traits;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;

trait SurveyQuestionRetriever
{
    /**
     * @param User $user
     * @param array $triggerTypes
     * @param array $types
     * @param Survey|null $survey
     * @return SurveyQuestion
     */
    public function getNextUnansweredSurveyQuestion(
        User $user,
        array $triggerTypes = [],
        array $types = [],
        Survey $survey = null
    ) {
        /** @var Survey $survey */
        $survey = $survey ?: $this->getNextUnansweredSurvey($user, $triggerTypes, $types);

        return $survey->questionOrderingName === 'ordered' ?
            $this->getNextOrderedSurveyUnansweredQuestion($user, $survey) :
            $this->getNextRandomSurveyUnansweredQuestion($user, $survey);
    }

    /**
     * @param User $user
     * @param Survey $survey
     * @return mixed
     */
    public function getNextOrderedSurveyUnansweredQuestion(User $user, Survey $survey)
    {
        return $survey->questions->filter(function (SurveyQuestion $question) use ($user) {
            return !$question->userAnswers->count();
        })->sortBy('order')->first();
    }

    /**
     * @param User $user
     * @param Survey $survey
     * @return mixed
     */
    public function getNextRandomSurveyUnansweredQuestion(User $user, Survey $survey)
    {
        return $survey->questions->filter(function (SurveyQuestion $question) use ($user) {
            return !$question->userAnswers->count();
        })->random(1)->first();
    }

    /**
     * @param User $user
     * @param $event
     * @return SurveyQuestion
     */
    public function getNextUnansweredEventSurveyQuestion(User $user, $event)
    {
        /** @var Survey|null $survey */
        $survey = $this->getNextUnansweredEventSurvey($user, $event);

        return $survey ? $this->getNextUnansweredSurveyQuestion($user, ['event'], $survey) : null;
    }
}