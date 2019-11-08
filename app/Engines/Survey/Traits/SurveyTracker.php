<?php
/**
 * Date: 3/22/18
 * Time: 9:26 AM
 */

namespace App\Engines\Survey\Traits;

use App\Models\Survey;
use App\Models\SurveyUserAnswer;
use App\Models\User;

trait SurveyTracker
{
    /**
     * @param User $user
     * @param Survey $survey
     * @return bool
     */
    public function isSurveyCompletedForUser(User $user, Survey $survey)
    {
        $questions = $survey->questions;
        $answers = $survey->answers->filter(function (SurveyUserAnswer $answer) use ($user) {
            return $answer->user->id === $user->id;
        });
        return $questions->count() === $answers->count();
    }

    /**
     * @param User $user
     * @param Survey $survey
     * @return bool
     */
    public function isSurveyIncompleteForUser(User $user, Survey $survey)
    {
        $questions = $survey->questions;
        $answers = $survey->answers->filter(function (SurveyUserAnswer $answer) use ($user) {
            return $answer->user->id === $user->id;
        });
        return $questions->count() !== $answers->count();
    }
}
