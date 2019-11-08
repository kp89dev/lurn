<?php

namespace App\Models\Onboarding;

use App\Models\Bonus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Mission
{
    private $user;
    private $points = 0;
    private $totalPoints = 0;

    public $scenarios = [];

    public function __construct(User $user)
    {
        $this->user = $user;

        $scenarios = DB::table('scenarios')->orderBy('id')->get();
        $handlers = [
            1 => ProfileCompleteScenario::class,
            2 => EvaluationCompleteScenario::class,
            3 => CourseEnrollmentScenario::class,
            4 => SocialSharingScenario::class,
            5 => ReferralScenario::class,
        ];

        foreach ($scenarios as $scenario) {
            if (! isset($handlers[$scenario->id])) {
                continue;
            }

            $handler = new $handlers[$scenario->id];

            $handler->id = $scenario->id;
            $handler->points = $scenario->points;
            $handler->message = $scenario->message;

            $handler->isCompleted($user) && $this->points += $scenario->points;
            $this->totalPoints += $scenario->points;

            $this->scenarios[$scenario->id] = $handler;
        }
    }

    public function display(User $user)
    {
        $display = '';

        foreach ($this->scenarios as $s) {
            $display .= $s->display($user) . ",\n";
        }

        return trim($display, "\n");
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function getTotalPoints()
    {
        return $this->totalPoints;
    }

    public function getCompletePercentage()
    {
        return max(0, min(100, round($this->totalPoints ? (int) $this->points / $this->totalPoints * 100 : 0)));
    }

    public function getNextBonus()
    {
        return Bonus::next($this->user->pointsEarned)->first();
    }

    public function getCurrentBonus()
    {
        return Bonus::current($this->user->pointsEarned)->first();
    }
}