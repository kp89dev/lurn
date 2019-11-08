<?php

namespace App\Models\QueryBuilder;

use App\Models\Onboarding\BaseScenario;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ScenarioCompletionCounters
{
    protected $scenario;
    protected $user;

    public function __construct(BaseScenario $scenario, User $user)
    {
        $this->scenario = $scenario;
        $this->user = $user;
    }

    public function get()
    {
        return DB::table('scenario_user')
            ->selectRaw('scenario_id, count(*) counter')
            ->whereUserId($this->user->id)
            ->groupBy('scenario_id')
            ->pluck('counter', 'scenario_id');
    }
}
