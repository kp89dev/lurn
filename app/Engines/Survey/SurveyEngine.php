<?php
/**
 * Date: 3/22/18
 * Time: 9:30 AM
 */

namespace App\Engines\Survey;

use App\Engines\Survey\Traits\SurveyPresenter;
use App\Engines\Survey\Traits\SurveyQuestionRetriever;
use App\Engines\Survey\Traits\SurveyRetriever;
use App\Engines\Survey\Traits\SurveyTracker;
use App\Models\Survey;
use App\Models\User;

/**
 * Class SurveyCommander
 * @package App\Engines\Survey
 */
class SurveyEngine
{
    use SurveyRetriever,
        SurveyPresenter,
        SurveyQuestionRetriever,
        SurveyTracker;

    /**
     * @param User $user
     * @param array $types
     * @return Survey
     */
    public function nextTimedSurvey(User $user, array $types = [])
    {
        return $this->getNextUnansweredSurvey($user, ['timed'], $types);
    }

    /**
     * @param User $user
     * @param array $types
     * @return string
     */
    public function nextTimedSurveyQuestion(User $user, array $types = [])
    {
        return $this->presentCombinationQuestion(
            $this->getNextUnansweredSurveyQuestion($user, ['timed', 'event'], $types)
        );
    }

    /**
     * @param User $user
     * @param array $types
     * @return Survey
     */
    public function nextCombinationSurvey(User $user, array $types = [])
    {
        return $this->getNextUnansweredSurvey($user, ['timed', 'event'], $types);
    }

    /**
     * @param User $user
     * @param array $types
     * @return string
     */
    public function nextCombinationSurveyQuestion(User $user, array $types = [])
    {
        return $this->presentCombinationQuestion(
            $this->getNextUnansweredSurveyQuestion($user, ['timed', 'event'], $types)
        );
    }

    /**
     * @param User $user
     * @param string $event
     * @return Survey|null
     */
    public function eventSurvey(User $user, string $event)
    {
        return $this->getNextUnansweredEventSurvey($user, $event);
    }

    /**
     * @param User $user
     * @param string $event
     * @return string
     */
    public function eventSurveyQuestion(User $user, string $event)
    {
        return $this->presentEventQuestion($this->getNextUnansweredEventSurveyQuestion($user, $event));
    }

    /**
     * @param User $user
     * @param Survey $survey
     * @return string
     */
    public function standaloneSurveyQuestion(User $user, Survey $survey)
    {
        return $this->presentStandaloneQuestion($this->getNextUnansweredSurveyQuestion($user, [], [], $survey));
    }
}
