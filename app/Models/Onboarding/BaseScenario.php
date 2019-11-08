<?php

namespace App\Models\Onboarding;

use App\Models\QueryBuilder\ScenarioCompletionCounters;
use App\Models\UserPointActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

abstract class BaseScenario extends Model
{
    protected $table = 'scenarios';

    public abstract function isCompleted(User $user);

    public function complete(User $user, $details = null)
    {
        DB::table('scenario_user')->insert([
            'user_id'     => $user->id,
            'scenario_id' => $this->id,
            'created_at'  => Carbon::now(),
            'details'     => $details,
        ]);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function display(User $user)
    {
        $obj = new \StdClass();
        $obj->id = $this->id;
        $obj->done = $this->isCompleted($user);
        $obj->title = $this->message;
        $obj->points = $this->points;

        return json_encode($obj);
    }

    public function getCompletions(User $user = null)
    {
        $user = $user ?: user();

        return $this->getCompletionCounters($user);
    }

    public function getCompletionCounters(User $user)
    {
        global $completionCounters;

        if (isset($completionCounters[$user->id])) {
            return $completionCounters[$user->id];
        }

        $counters = (new ScenarioCompletionCounters($this, $user))->get();

        return $completionCounters[$user->id] = $counters[$this->id] ?? 0;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function awardPoints(User $user)
    {
        $this->complete($user);

        return true;
    }
}
