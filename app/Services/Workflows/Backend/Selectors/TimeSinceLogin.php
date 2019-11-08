<?php
namespace App\Services\Workflows\Backend\Selectors;

use App\Services\Workflows\Backend\Selectors\Contracts\SelectorAbstract;
use App\Services\Workflows\Backend\Selectors\Contracts\SelectorContract;
use Illuminate\Support\Facades\DB;

class TimeSinceLogin extends SelectorAbstract implements SelectorContract
{
    public function join($query, $values)
    {
        return $query->join(
            $this->getRawQuery(
                $this->getTimestampFromDays($values[0]['value'], $values[1]['value'])
            ),
            function($join) {
                $join->on('users.id', '=', $this->getAlias('users_time_since_login') . '.user_id');
            }
        );
    }

    public function where($query, $values)
    {
        return $query;
    }

    private function getTimestampFromDays($number, $units)
    {
        return date('Y-m-d H:i:s', strtotime("-{$number} $units"));
    }

    private function getRawQuery($timestamp)
    {
        return DB::raw('(
             SELECT user_id, MAX(created_at) last_login_time
             FROM `user_logins` 
             GROUP BY user_id 
             HAVING last_login_time < "' . $timestamp . '"
        ) as ' . $this->getAlias('users_time_since_login'));
    }
}
