<?php
namespace App\Services\Workflows\Backend\Selectors;

use App\Services\Workflows\Backend\Selectors\Contracts\SelectorAbstract;
use App\Services\Workflows\Backend\Selectors\Contracts\SelectorContract;
use Illuminate\Support\Facades\DB;

class TotalPurchasedAmount extends SelectorAbstract implements SelectorContract
{
    public function join($query, $values)
    {
        return $query->join(
            $this->getRawJoinQuery($values[0]['value'], $values[1]['value']),
            function ($join) {
                $join->on('users.id', '=', $this->getAlias('total_purchased_per_user') . '.user_id');
            }
        );
    }

    public function where($query, $value)
    {
        return $query;
    }

    private function getRawJoinQuery($operator, $amount)
    {
        return DB::raw('(
             SELECT user_courses.user_id, SUM(course_infusionsoft.price) as total_purchased FROM user_courses
             JOIN course_infusionsoft 
                ON user_courses.course_infusionsoft_id = course_infusionsoft.id
             GROUP BY user_courses.user_id   
             HAVING total_purchased '. $operator . ' '. $amount .'
        ) as ' . $this->getAlias('total_purchased_per_user'));
    }
}
