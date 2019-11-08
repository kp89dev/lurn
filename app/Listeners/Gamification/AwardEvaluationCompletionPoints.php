<?php
namespace App\Listeners\Gamification;

use App\Events\Onboarding\EvaluationCompleted;
use Gamification\Gamification;

class AwardEvaluationCompletionPoints
{
    public function handle(EvaluationCompleted $event)
    {
        $api = new Gamification;

        $api->finishEvaluation([
            'userId' => $event->user->id,
            'email' => $event->user->email,
            'details' => [
                'evaluationStage' => '1',
            ]
        ]);
    }
}
