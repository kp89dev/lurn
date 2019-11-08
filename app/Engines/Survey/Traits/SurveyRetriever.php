<?php
/**
 * Date: 3/22/18
 * Time: 9:26 AM
 */

namespace App\Engines\Survey\Traits;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait SurveyRetriever
{
    /**
     * @param User $user
     * @param array $triggerTypes
     * @param array $types
     * @return Survey
     */
    public function getNextUnansweredSurvey(User $user, array $triggerTypes = [], array $types = ['all'])
    {
        return $this->userSurveys($user, $triggerTypes, $types)->first();
    }

    /**
     * @param User $user
     * @param array $triggerTypes
     * @param array $types
     * @return Collection
     */
    public function userSurveys(User $user, array $triggerTypes, array $types)
    {
        return $this->queryForSurveys($triggerTypes, $types)->filter(function (Survey $survey) use ($user) {
            return $this->isSurveyIncompleteForUser($user, $survey);
        });
    }

    /**
     * @param User $user
     * @param $event
     * @return Survey|null
     */
    public function getNextUnansweredEventSurvey(User $user, $event)
    {
        /** @var Collection $surveys */
        $surveys = Survey::byEvent($event)->get();

        return $surveys->count() ? $surveys->filter(function (Survey $survey) use ($user) {
            return $this->isSurveyIncompleteForUser($user, $survey);
        })->first() : null;
    }

    /**
     * @param array $triggerTypes
     * @param array $types
     * @return Collection
     */
    public function queryForSurveys(array $triggerTypes = [], array $types = [])
    {
        /** @var Collection $surveys */
        $surveys = Survey::with([
            'questions',
            'questionAnswers',
            'answers',
            'surveyType',
            'surveyTriggerType',
            'surveyQuestionOrdering'
        ])
            ->whereHas('surveyType', function ($query) use ($types) {
                if (count($types) && !in_array('all', $types)) {
                    $query->whereIn('key', $types);
                }
            })
            ->whereHas('surveyTriggerType', function ($query) use ($triggerTypes) {
                if (count($triggerTypes)) {
                    $query->whereIn('key', $triggerTypes);
                }
            })
            ->orderBy('priority', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return $surveys;
    }
}
